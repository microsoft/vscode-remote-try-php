<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Subscribers\ImportExport\Import;

if (!defined('ABSPATH')) exit;


use MailPoet\CustomFields\CustomFieldsRepository;
use MailPoet\Entities\CustomFieldEntity;
use MailPoet\Entities\SubscriberCustomFieldEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Entities\SubscriberSegmentEntity;
use MailPoet\Entities\SubscriberTagEntity;
use MailPoet\Newsletter\Options\NewsletterOptionsRepository;
use MailPoet\Segments\WP;
use MailPoet\Services\Validator;
use MailPoet\Subscribers\ImportExport\ImportExportFactory;
use MailPoet\Subscribers\ImportExport\ImportExportRepository;
use MailPoet\Subscribers\Source;
use MailPoet\Subscribers\SubscribersRepository;
use MailPoet\Tags\TagRepository;
use MailPoet\Util\DateConverter;
use MailPoet\Util\Helpers;
use MailPoet\Util\Security;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Carbon\Carbon;

class Import {
  /** @var array */
  public $subscribersData;
  /** @var array */
  public $segmentsIds;
  /** @var string[] */
  public $tags;
  /** @var string */
  public $newSubscribersStatus;
  /** @var string */
  public $existingSubscribersStatus;
  /** @var bool */
  public $updateSubscribers;
  /** @var array */
  public $subscribersFields;
  /** @var array */
  public $subscribersCustomFields;
  /** @var int */
  public $subscribersCount;
  /** @var Carbon */
  public $createdAt;
  /** @var Carbon */
  public $updatedAt;
  /** @var array<string, mixed> */
  public $requiredSubscribersFields;
  const DB_QUERY_CHUNK_SIZE = 100;
  const STATUS_DONT_UPDATE = 'dont_update';

  public const ACTION_CREATE = 'create';
  public const ACTION_UPDATE = 'update';

  /** @var WP */
  private $wpSegment;

  /** @var CustomFieldsRepository */
  private $customFieldsRepository;

  /** @var ImportExportRepository */
  private $importExportRepository;

  /** @var NewsletterOptionsRepository */
  private $newsletterOptionsRepository;

  /** @var SubscribersRepository */
  private $subscriberRepository;

  /** @var TagRepository */
  private $tagRepository;

  /** @var Validator */
  private $validator;

  public function __construct(
    WP $wpSegment,
    CustomFieldsRepository $customFieldsRepository,
    ImportExportRepository $importExportRepository,
    NewsletterOptionsRepository $newsletterOptionsRepository,
    SubscribersRepository $subscriberRepository,
    TagRepository $tagRepository,
    Validator $validator,
    array $data
  ) {
    $this->wpSegment = $wpSegment;
    $this->customFieldsRepository = $customFieldsRepository;
    $this->importExportRepository = $importExportRepository;
    $this->newsletterOptionsRepository = $newsletterOptionsRepository;
    $this->subscriberRepository = $subscriberRepository;
    $this->tagRepository = $tagRepository;
    $this->validator = $validator;
    $this->validateImportData($data);
    $this->subscribersData = $this->transformSubscribersData(
      $data['subscribers'],
      $data['columns']
    );
    $this->segmentsIds = $data['segments'];
    $this->tags = $data['tags'];
    $this->newSubscribersStatus = $data['newSubscribersStatus'];
    $this->existingSubscribersStatus = $data['existingSubscribersStatus'];
    $this->updateSubscribers = $data['updateSubscribers'];
    $this->subscribersFields = $this->getSubscribersFields(
      array_keys($data['columns'])
    );
    $this->subscribersCustomFields = $this->getCustomSubscribersFields(
      array_keys($data['columns'])
    );
    $this->subscribersCount = (reset($this->subscribersData) === false) ? 0 : count(reset($this->subscribersData));
    $this->createdAt = Carbon::createFromTimestamp(WPFunctions::get()->currentTime('timestamp'));
    $this->updatedAt = Carbon::createFromTimestamp(WPFunctions::get()->currentTime('timestamp') + 1);
    $this->requiredSubscribersFields = [
      'status' => SubscriberEntity::STATUS_SUBSCRIBED,
      'first_name' => '',
      'last_name' => '',
      'created_at' => $this->createdAt,
    ];
  }

  public function validateImportData(array $data): void {
    $requiredDataFields = [
      'subscribers',
      'columns',
      'segments',
      'timestamp',
      'newSubscribersStatus',
      'existingSubscribersStatus',
      'updateSubscribers',
      'tags',
    ];
    // 1. data should contain all required fields
    // 2. column names should only contain alphanumeric & underscore characters
    if (
      count(array_intersect_key(array_flip($requiredDataFields), $data)) !== count($requiredDataFields) ||
      preg_grep('/[^a-zA-Z0-9_]/', array_keys($data['columns']))
    ) {
      throw new \Exception(__('Missing or invalid import data.', 'mailpoet'));
    }
  }

  /**
   * @return array{created: int, updated:int, segments: array, added_to_segment_with_welcome_notification:bool}
   * @throws \Exception
   */
  public function process(): array {
    // validate data based on field validation rules
    $subscribersData = $this->validateSubscribersData($this->subscribersData);
    if (!$subscribersData) {
      throw new \Exception(__('No valid subscribers were found.', 'mailpoet'));
    }
    // permanently trash deleted subscribers
    $this->deleteExistingTrashedSubscribers($subscribersData);

    // split subscribers into "existing" and "new" and free up memory
    $existingSubscribers = $newSubscribers = [
      'data' => [],
      'fields' => $this->subscribersFields,
    ];
    list($existingSubscribers['data'], $newSubscribers['data'], $wpUsers) =
      $this->splitSubscribersData($subscribersData);
    $subscribersData = null;

    // create or update subscribers
    $createdSubscribers = $updatedSubscribers = [];
    try {
      if ($newSubscribers['data']) {
        // add, if required, missing required fields to new subscribers
        $newSubscribers = $this->addMissingRequiredFields($newSubscribers);
        $newSubscribers = $this->setSubscriptionStatusToDefault($newSubscribers, $this->newSubscribersStatus);
        $newSubscribers = $this->setSource($newSubscribers);
        $newSubscribers = $this->setLinkToken($newSubscribers);
        $createdSubscribers =
          $this->createOrUpdateSubscribers(
            self::ACTION_CREATE,
            $newSubscribers,
            $this->subscribersCustomFields
          );
      }

      $updateExistingSubscribersStatus = false;

      if ($existingSubscribers['data']) {
        $allowedStatuses = [
          SubscriberEntity::STATUS_SUBSCRIBED,
          SubscriberEntity::STATUS_UNSUBSCRIBED,
          SubscriberEntity::STATUS_INACTIVE,
        ];
        if (in_array($this->existingSubscribersStatus, $allowedStatuses, true)) {
          $updateExistingSubscribersStatus = true;
          $existingSubscribers = $this->addField($existingSubscribers, 'status', $this->existingSubscribersStatus);
        }
        if ($this->updateSubscribers) {
          // Update existing subscribers' info (first_name, last_name etc.)
          // as well as status (optionally) if the status column was added above
          $updatedSubscribers =
            $this->createOrUpdateSubscribers(
              self::ACTION_UPDATE,
              $existingSubscribers,
              $this->subscribersCustomFields
            );
          if ($wpUsers) {
            $this->synchronizeWPUsers($wpUsers);
          }
        } elseif ($updateExistingSubscribersStatus) {
          // Only update existing subscribers' status
          // For this we need to remove all other fields except email and status
          $existingSubscribers['fields'] = array_intersect($existingSubscribers['fields'], ['email', 'status']);
          $existingSubscribers['data'] = array_intersect_key($existingSubscribers['data'], array_flip(['email', 'status']));
          $updatedSubscribers =
            $this->createOrUpdateSubscribers(
              self::ACTION_UPDATE,
              $existingSubscribers
            );
        }
      }
    } catch (\Exception $e) {
      throw new \Exception(__('Unable to save imported subscribers.', 'mailpoet'));
    }

    // check if any subscribers were added to segments that have welcome notifications configured
    $importFactory = new ImportExportFactory('import');
    $segments = $importFactory->getSegments();
    $welcomeNotificationsInSegments =
      ($createdSubscribers || $updatedSubscribers) ?
        $this->newsletterOptionsRepository->findWelcomeNotificationsForSegments($this->segmentsIds) :
        false;

    return [
      'created' => is_array($createdSubscribers) ? count($createdSubscribers) : 0,
      'updated' => is_array($updatedSubscribers) ? count($updatedSubscribers) : 0,
      'segments' => $segments,
      'added_to_segment_with_welcome_notification' =>
        ($welcomeNotificationsInSegments) ? true : false,
    ];
  }

  /**
   * @param array $subscribersData
   * @return false|array
   */
  public function validateSubscribersData(array $subscribersData) {
    $invalidRecords = [];
    foreach ($subscribersData as $column => &$data) {
      if ($column === 'email') {
        $data = array_map(
          function($index, $email) use(&$invalidRecords) {
            if (!$this->validator->validateNonRoleEmail($email)) {
              $invalidRecords[] = $index;
            }
            return strtolower($email);
          },
          array_keys($data),
          $data
        );
      }
      if (in_array($column, ['created_at', 'confirmed_at'], true)) {
        $data = $this->validateDateTime($data, $invalidRecords);
      }
      if (in_array($column, ['confirmed_ip', 'subscribed_ip'], true)) {
        $data = array_map(
          function($index, $ip) {
            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
              // if invalid or empty, we allow the import but remove the IP
              return null;
            }
            return $ip;
          },
          array_keys($data),
          $data
        );
      }
      // if this is a custom column
      if (in_array($column, $this->subscribersCustomFields)) {
        $customField = $this->customFieldsRepository->findOneById($column);
        if (!$customField instanceof CustomFieldEntity) {
          continue;
        }
        // validate date type
        if ($customField->getType() === CustomFieldEntity::TYPE_DATE) {
          $data = $this->validateDateTime($data, $invalidRecords);
        }
      }
    }
    if ($invalidRecords) {
      foreach ($subscribersData as $column => &$data) {
        $data = array_diff_key($data, array_flip($invalidRecords));
        $data = array_values($data);
      }
    }
    if (empty($subscribersData['email'])) return false;
    return $subscribersData;
  }

  private function validateDateTime(array $data, array &$invalidRecords): array {
    $siteUsesCustomFormat = WPFunctions::get()->getOption('date_format') === 'd/m/Y';
    if ($siteUsesCustomFormat) {
      return $this->validateDateTimeAttemptCustomFormat($data, $invalidRecords);
    }

    $validationRule = 'datetime';
    return array_map(
      function ($index, $date) use ($validationRule, &$invalidRecords) {
        if (empty($date)) return $date;
        $date = (new DateConverter())->convertDateToDatetime($date, $validationRule);
        if (!$date) {
          $invalidRecords[] = $index;
        }
        return $date;
      },
      array_keys($data),
      $data
    );
  }

  private function validateDateTimeAttemptCustomFormat(array $data, array &$invalidRecords): array {
    $validationRule = 'datetime';
    $dateTimeDates = $data;
    $dateTimeInvalidRecords = $invalidRecords;
    $datetimeErrorCount = 0;

    $validationRuleCustom = 'd/m/Y';
    $customFormatDates = $data;
    $customFormatInvalidRecords = $invalidRecords;
    $customFormatErrorCount = 0;

    // We attempt converting with both date formats
    foreach ($data as $index => $date) {
      if (empty($date)) {
        $dateTimeDates[$index] = $date;
        $customFormatDates[$index] = $date;
        continue;
      };
      $dateTimeDates[$index] = (new DateConverter())->convertDateToDatetime($date, $validationRule);
      if (!$dateTimeDates[$index]) {
        $datetimeErrorCount ++;
        $dateTimeInvalidRecords[] = $index;
      }
      $customFormatDates[$index] = (new DateConverter())->convertDateToDatetime($date, $validationRuleCustom);
      if (!$customFormatDates[$index]) {
        $customFormatErrorCount ++;
        $customFormatInvalidRecords[] = $index;
      }
    }

    if ($customFormatErrorCount < $datetimeErrorCount) {
      $invalidRecords = $customFormatInvalidRecords;
      return $customFormatDates;
    }

    $invalidRecords = $dateTimeInvalidRecords;
    return $dateTimeDates;
  }

  public function transformSubscribersData(array $subscribers, array $columns): array {
    $transformedSubscribers = [];
    foreach ($columns as $column => $data) {
      $transformedSubscribers[$column] = array_column($subscribers, $data['index']);
    }
    return $transformedSubscribers;
  }

  /**
   * @param array $subscribersData
   * @return array{array|false,array,array|false}
   */
  public function splitSubscribersData(array $subscribersData): array {
    // $subscribers_data is an two-dimensional associative array
    // of all subscribers being imported: [field => [value1, value2], field => [value1, value2], ...]
    $tempExistingSubscribers = [];
    foreach (array_chunk($subscribersData['email'], self::DB_QUERY_CHUNK_SIZE) as $subscribersEmails) {
      // create a two-dimensional indexed array of all existing subscribers
      // with just wp_user_id and email fields: [[wp_user_id, email], [wp_user_id, email], ...]
      $tempExistingSubscribers = array_merge(
        $tempExistingSubscribers,
        $this->subscriberRepository->findWpUserIdAndEmailByEmails($subscribersEmails)
      );
    }
    if (!$tempExistingSubscribers) {
      return [
        false, // existing subscribers
        $subscribersData, // new subscribers
        false, // WP users
      ];
    }
    // extract WP users ids into a simple indexed array: [wp_user_id_1, wp_user_id_2, ...]
    $wpUsers = array_filter(array_column($tempExistingSubscribers, 'wp_user_id'));
    // create a new two-dimensional associative array with existing subscribers ($existing_subscribers)
    // and reduce $subscribers_data to only new subscribers by removing existing subscribers
    $existingSubscribers = [];
    $subscribersEmails = array_flip($subscribersData['email']);
    foreach ($tempExistingSubscribers as $tempExistingSubscriber) {
      $existingSubscriberKey = $subscribersEmails[$tempExistingSubscriber['email']];
      foreach ($subscribersData as $field => &$value) {
        $existingSubscribers[$field][] = $value[$existingSubscriberKey];
        unset($value[$existingSubscriberKey]);
      }
    }
    $newSubscribers = $subscribersData;
    // reindex array after unsetting elements
    $newSubscribers = array_map('array_values', $newSubscribers);
    // remove empty values
    $newSubscribers = array_filter($newSubscribers);
    return [
      $existingSubscribers,
      $newSubscribers,
      $wpUsers,
    ];
  }

  public function deleteExistingTrashedSubscribers(array $subscribersData): void {
    $existingTrashedRecords = array_filter(
      array_map(function($subscriberEmails) {
        return $this->subscriberRepository->findIdsOfDeletedByEmails($subscriberEmails);
      }, array_chunk($subscribersData['email'], self::DB_QUERY_CHUNK_SIZE))
    );
    $existingTrashedRecords = Helpers::flattenArray($existingTrashedRecords);
    if (!$existingTrashedRecords) {
      return;
    }
    foreach (array_chunk($existingTrashedRecords, self::DB_QUERY_CHUNK_SIZE) as $subscriberIds) {
      $this->subscriberRepository->bulkDelete($subscriberIds);
    }
  }

  public function addMissingRequiredFields(array $subscribers): array {
    foreach (array_keys($this->requiredSubscribersFields) as $requiredField) {
      $subscribers = $this->addField($subscribers, $requiredField, $this->requiredSubscribersFields[$requiredField]);
    }
    return $subscribers;
  }

  /**
   * @param array $subscribers
   * @param string $fieldName
   * @param mixed $fieldValue
   * @return array
   */
  private function addField(array $subscribers, string $fieldName, $fieldValue): array {
    if (in_array($fieldName, $subscribers['fields'])) return $subscribers;

    $subscribersCount = count($subscribers['data'][key($subscribers['data'])]);
    $subscribers['data'][$fieldName] = array_fill(
      0,
      $subscribersCount,
      $fieldValue
    );
    $subscribers['fields'][] = $fieldName;

    return $subscribers;
  }

  private function setSubscriptionStatusToDefault(array $subscribersData, string $defaultStatus): array {
    if (!in_array('status', $subscribersData['fields'])) return $subscribersData;
    $subscribersData['data']['status'] = array_map(function() use ($defaultStatus) {
      return $defaultStatus;
    }, $subscribersData['data']['status']);

    if ($defaultStatus === SubscriberEntity::STATUS_SUBSCRIBED) {
      if (!in_array('last_subscribed_at', $subscribersData['fields'])) {
        $subscribersData['fields'][] = 'last_subscribed_at';
      }
      $subscribersData['data']['last_subscribed_at'] = array_map(function() {
        return $this->createdAt;
      }, $subscribersData['data']['status']);
    }
    return $subscribersData;
  }

  private function setSource(array $subscribersData): array {
    $subscribersCount = count($subscribersData['data'][key($subscribersData['data'])]);
    $subscribersData['fields'][] = 'source';
    $subscribersData['data']['source'] = array_fill(
      0,
      $subscribersCount,
      Source::IMPORTED
    );
    return $subscribersData;
  }

  private function setLinkToken(array $subscribersData): array {
    $subscribersCount = count($subscribersData['data'][key($subscribersData['data'])]);
    $subscribersData['fields'][] = 'link_token';
    $subscribersData['data']['link_token'] = array_map(
      function () {
        return Security::generateRandomString(SubscriberEntity::LINK_TOKEN_LENGTH);
      },
      array_fill(0, $subscribersCount, null)
    );
    return $subscribersData;
  }

  public function getSubscribersFields(array $subscribersFields): array {
    return array_values(
      array_filter(
        array_map(function($field) {
          if (!is_int($field)) return $field;
        }, $subscribersFields)
      )
    );
  }

  /**
   * @param array $subscribersFields
   * @return int[]
   */
  public function getCustomSubscribersFields(array $subscribersFields): array {
    return array_values(
      array_filter(
        array_map(function($field) {
          if (is_int($field)) return $field;
        }, $subscribersFields)
      )
    );
  }

  public function createOrUpdateSubscribers(
    string $action,
    array $subscribersData,
    array $subscribersCustomFields = []
  ): ?array {
    $subscribersCount = count($subscribersData['data'][key($subscribersData['data'])]);
    $subscribers = array_map(function($index) use ($subscribersData) {
      return array_map(function($field) use ($index, $subscribersData) {
        return $subscribersData['data'][$field][$index];
      }, $subscribersData['fields']);
    }, range(0, $subscribersCount - 1));
    foreach (array_chunk($subscribers, self::DB_QUERY_CHUNK_SIZE) as $data) {
      if ($action === self::ACTION_CREATE) {
        $this->importExportRepository->insertMultiple(
          SubscriberEntity::class,
          $subscribersData['fields'],
          $data
        );
      } elseif ($action === self::ACTION_UPDATE) {
        $this->importExportRepository->updateMultiple(
          SubscriberEntity::class,
          $subscribersData['fields'],
          $data,
          $this->updatedAt
        );
      }
    }
    $createdOrUpdatedSubscribers = [];
    foreach (array_chunk($subscribersData['data']['email'], self::DB_QUERY_CHUNK_SIZE) as $emails) {
      foreach ($this->subscriberRepository->findIdAndEmailByEmails($emails) as $createdOrUpdatedSubscriber) {
        // ensure emails loaded from the DB are lowercased (imported emails are lowercased as well)
        $createdOrUpdatedSubscriber['email'] = mb_strtolower($createdOrUpdatedSubscriber['email']);
        $createdOrUpdatedSubscribers[] = $createdOrUpdatedSubscriber;
      }
    }
    if (empty($createdOrUpdatedSubscribers)) return null;

    $this->subscriberRepository->invalidateTotalSubscribersCache();
    $createdOrUpdatedSubscribersIds = array_column($createdOrUpdatedSubscribers, 'id');
    if ($subscribersCustomFields) {
      $this->createOrUpdateCustomFields(
        $action,
        $createdOrUpdatedSubscribers,
        $subscribersData,
        $subscribersCustomFields
      );
    }
    $this->addSubscribersToSegments(
      $createdOrUpdatedSubscribersIds,
      $this->segmentsIds
    );
    $this->addTagsToSubscribers(
      $createdOrUpdatedSubscribersIds,
      $this->tags
    );
    return $createdOrUpdatedSubscribers;
  }

  public function createOrUpdateCustomFields(
    string $action,
    array $createdOrUpdatedSubscribers,
    array $subscribersData,
    array $subscribersCustomFieldsIds
  ): void {
    // check if custom fields exist in the database
    $subscribersCustomFieldsIds = array_map(function(CustomFieldEntity $customField): int {
      return (int)$customField->getId();
    }, $this->customFieldsRepository->findBy(['id' => $subscribersCustomFieldsIds]));
    if (!$subscribersCustomFieldsIds) {
      return;
    }
    // assemble a two-dimensional array: [[custom_field_id, subscriber_id, value], [custom_field_id, subscriber_id, value], ...]
    $subscribersCustomFieldsData = [];
    $subscribersEmails = array_flip($subscribersData['data']['email']);
    foreach ($createdOrUpdatedSubscribers as $createdOrUpdatedSubscriber) {
      $subscriberIndex = $subscribersEmails[$createdOrUpdatedSubscriber['email']];
      foreach ($subscribersData['data'] as $field => $values) {
        // exclude non-custom fields
        if (!is_int($field)) continue;
        $subscribersCustomFieldsData[] = [
          (int)$field,
          $createdOrUpdatedSubscriber['id'],
          $values[$subscriberIndex],
          $this->createdAt,
        ];
      }
    }
    $columns = [
      'custom_field_id',
      'subscriber_id',
      'value',
      'created_at',
    ];
    $customFieldCount = count($subscribersCustomFieldsIds);
    $customFieldBatchSize = (int)(round(self::DB_QUERY_CHUNK_SIZE / $customFieldCount) * $customFieldCount);
    $customFieldBatchSize = ($customFieldBatchSize > 0) ? $customFieldBatchSize : 1;
    foreach (array_chunk($subscribersCustomFieldsData, $customFieldBatchSize) as $subscribersCustomFieldsDataChunk) {
      $this->importExportRepository->insertMultiple(
        SubscriberCustomFieldEntity::class,
        $columns,
        $subscribersCustomFieldsDataChunk
      );
      if ($action === self::ACTION_UPDATE) {
        $this->importExportRepository->updateMultiple(
          SubscriberCustomFieldEntity::class,
          $columns,
          $subscribersCustomFieldsDataChunk,
          $this->updatedAt
        );
      }
    }
  }

  /**
   * @param int[] $wpUsers
   * @return array
   */
  public function synchronizeWPUsers(array $wpUsers): array {
    $users = array_map([$this->wpSegment, 'synchronizeUser'], $wpUsers);
    $this->subscriberRepository->invalidateTotalSubscribersCache();
    return $users;
  }

  public function addSubscribersToSegments(array $subscribersIds, array $segmentsIds): void {
    $columns = [
      'subscriber_id',
      'segment_id',
      'created_at',
    ];
    foreach ($segmentsIds as $segmentId) {
      foreach (array_chunk($subscribersIds, self::DB_QUERY_CHUNK_SIZE) as $subscriberIdsChunk) {
        $data = [];
        $data = array_merge($data, array_map(function ($subscriberId) use ($segmentId): array {
          return [
            $subscriberId,
            $segmentId,
            $this->createdAt,
          ];
        }, $subscriberIdsChunk));

        $this->importExportRepository->insertMultiple(
          SubscriberSegmentEntity::class,
          $columns,
          $data
        );
      }
    }
  }

  /**
   * @param int[] $subscribersIds
   * @param string[] $tagNames
   */
  public function addTagsToSubscribers(array $subscribersIds, array $tagNames): void {
    $tagIds = [];
    foreach ($tagNames as $tagName) {
      $tag = $this->tagRepository->findOneBy(['name' => $tagName]);
      if (!$tag) {
        $tag = $this->tagRepository->createOrUpdate(['name' => $tagName]);
      }
      $tagIds[] = $tag->getId();
    }

    $columns = [
      'subscriber_id',
      'tag_id',
      'created_at',
    ];
    foreach ($tagIds as $tagId) {
      foreach (array_chunk($subscribersIds, self::DB_QUERY_CHUNK_SIZE) as $subscriberIdsChunk) {
        $data = [];
        $data = array_merge($data, array_map(function ($subscriberId) use ($tagId): array {
          return [
            $subscriberId,
            $tagId,
            $this->createdAt,
          ];
        }, $subscriberIdsChunk));

        $this->importExportRepository->insertMultiple(
          SubscriberTagEntity::class,
          $columns,
          $data
        );
      }
    }
  }
}
