<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Subscribers;

if (!defined('ABSPATH')) exit;


use MailPoet\ConflictException;
use MailPoet\CustomFields\CustomFieldsRepository;
use MailPoet\Doctrine\Validator\ValidationException;
use MailPoet\Entities\StatisticsUnsubscribeEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Entities\SubscriberSegmentEntity;
use MailPoet\Entities\SubscriberTagEntity;
use MailPoet\Newsletter\Scheduler\WelcomeScheduler;
use MailPoet\Segments\SegmentsRepository;
use MailPoet\Settings\SettingsController;
use MailPoet\Statistics\Track\Unsubscribes;
use MailPoet\Tags\TagRepository;
use MailPoet\Util\Security;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Carbon\Carbon;

class SubscriberSaveController {
  /** @var CustomFieldsRepository */
  private $customFieldsRepository;

  /** @var Security */
  private $security;

  /** @var SettingsController */
  private $settings;

  /** @var SegmentsRepository */
  private $segmentsRepository;

  /** @var SubscriberCustomFieldRepository */
  private $subscriberCustomFieldRepository;

  /** @var SubscribersRepository */
  private $subscribersRepository;

  /** @var SubscriberSegmentRepository */
  private $subscriberSegmentRepository;

  /** @var SubscriberTagRepository */
  private $subscriberTagRepository;

  /** @var TagRepository */
  private $tagRepository;

  /** @var Unsubscribes */
  private $unsubscribesTracker;

  /** @var WelcomeScheduler */
  private $welcomeScheduler;

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    CustomFieldsRepository $customFieldsRepository,
    Security $security,
    SettingsController $settings,
    SegmentsRepository $segmentsRepository,
    SubscriberCustomFieldRepository $subscriberCustomFieldRepository,
    SubscribersRepository $subscribersRepository,
    SubscriberSegmentRepository $subscriberSegmentRepository,
    SubscriberTagRepository $subscriberTagRepository,
    TagRepository $tagRepository,
    Unsubscribes $unsubscribesTracker,
    WelcomeScheduler $welcomeScheduler,
    WPFunctions $wp
  ) {
    $this->customFieldsRepository = $customFieldsRepository;
    $this->security = $security;
    $this->settings = $settings;
    $this->segmentsRepository = $segmentsRepository;
    $this->subscriberSegmentRepository = $subscriberSegmentRepository;
    $this->subscribersRepository = $subscribersRepository;
    $this->subscriberCustomFieldRepository = $subscriberCustomFieldRepository;
    $this->tagRepository = $tagRepository;
    $this->unsubscribesTracker = $unsubscribesTracker;
    $this->welcomeScheduler = $welcomeScheduler;
    $this->wp = $wp;
    $this->subscriberTagRepository = $subscriberTagRepository;
  }

  public function filterOutReservedColumns(array $subscriberData): array {
    $reservedColumns = [
      'id',
      'wp_user_id',
      'is_woocommerce_user',
      'status',
      'subscribed_ip',
      'confirmed_ip',
      'confirmed_at',
      'created_at',
      'updated_at',
      'deleted_at',
      'unconfirmed_data',
    ];
    return array_diff_key(
      $subscriberData,
      array_flip($reservedColumns)
    );
  }

  /**
   * @throws ConflictException
   * @throws ValidationException
   * @throws \Exception
   */
  public function save(array $data): SubscriberEntity {
    if (!empty($data)) {
      $data = $this->wp->stripslashesDeep($data);
    }

    if (empty($data['segments'])) {
      $data['segments'] = [];
    }
    $data['segments'] = array_merge($data['segments'], $this->getNonDefaultSubscribedSegments($data));
    $newSegments = $this->findNewSegments($data);

    if (empty($data['tags'])) {
      $data['tags'] = [];
    }

    $oldSubscriber = $this->findSubscriber($data);
    $oldStatus = $oldSubscriber ? $oldSubscriber->getStatus() : null;
    if (
      $oldSubscriber instanceof SubscriberEntity
      && isset($data['status'])
      && ($data['status'] === SubscriberEntity::STATUS_UNSUBSCRIBED)
      && ($oldSubscriber->getStatus() !== SubscriberEntity::STATUS_UNSUBSCRIBED)
    ) {
      $currentUser = $this->wp->wpGetCurrentUser();
      $this->unsubscribesTracker->track(
        (int)$oldSubscriber->getId(),
        StatisticsUnsubscribeEntity::SOURCE_ADMINISTRATOR,
        null,
        $currentUser->display_name // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
      );
    }

    if (isset($data['email']) && $this->isNewEmail($data['email'], $oldSubscriber)) {
      $this->verifyEmailIsUnique($data['email']);
    }

    $subscriber = $this->createOrUpdate($data, $oldSubscriber);

    $this->updateCustomFields($data, $subscriber);
    $this->updateTags($data, $subscriber);

    $segments = isset($data['segments']) ? $this->findSegments($data['segments']) : null;
    // check for status change
    if (
      $oldStatus === SubscriberEntity::STATUS_SUBSCRIBED
      && $subscriber->getStatus() === SubscriberEntity::STATUS_UNSUBSCRIBED
    ) {
      // make sure we unsubscribe the user from all segments
      $this->subscriberSegmentRepository->unsubscribeFromSegments($subscriber);
    } elseif ($segments !== null) {
      $this->subscriberSegmentRepository->resetSubscriptions($subscriber, $segments);
    }

    if (!empty($newSegments)) {
      $this->welcomeScheduler->scheduleSubscriberWelcomeNotification($subscriber->getId(), $newSegments);
    }

    // when global status changes to subscribed, fire subscribed hook for all subscribed segments
    if (
      $subscriber->getStatus() === SubscriberEntity::STATUS_SUBSCRIBED
      && $oldStatus !== null // don't trigger for new subscribers (handled in subscriber segments repository)
      && $oldStatus !== SubscriberEntity::STATUS_SUBSCRIBED
    ) {
      $segments = $subscriber->getSubscriberSegments();
      foreach ($segments as $subscriberSegment) {
        if ($subscriberSegment->getStatus() === SubscriberEntity::STATUS_SUBSCRIBED) {
          $this->wp->doAction('mailpoet_segment_subscribed', $subscriberSegment);
        }
      }
    }

    return $subscriber;
  }

  private function getNonDefaultSubscribedSegments(array $data): array {
    if (!isset($data['id']) || (int)$data['id'] <= 0) {
      return [];
    }

    $subscribedSegments = $this->subscriberSegmentRepository->getNonDefaultSubscribedSegments($data['id']);
    return array_filter(array_map(function(SubscriberSegmentEntity $subscriberSegment): int {
      $segment = $subscriberSegment->getSegment();
      if (!$segment) {
        return 0;
      }
      return (int)$segment->getId();
    }, $subscribedSegments));
  }

  private function findSegments(array $segmentIds): array {
    return $this->segmentsRepository->findBy(['id' => $segmentIds]);
  }

  private function findNewSegments(array $data): array {
    $oldSegmentIds = [];
    if (isset($data['id']) && (int)$data['id'] > 0) {
      $subscribersSegments = $this->subscriberSegmentRepository->findBy(['subscriber' => $data['id']]);
      foreach ($subscribersSegments as $subscribersSegment) {
        $segment = $subscribersSegment->getSegment();
        if (!$segment) {
          continue;
        }
        $oldSegmentIds[] = (int)$segment->getId();
      }
    }

    return array_diff($data['segments'], $oldSegmentIds);
  }

  /**
   * @throws ValidationException
   */
  public function createOrUpdate(array $data, ?SubscriberEntity $subscriber): SubscriberEntity {
    if (!$subscriber) {
      $subscriber = $this->createSubscriber();
      if (!isset($data['source'])) $data['source'] = Source::ADMINISTRATOR;
    }

    if (isset($data['email'])) $subscriber->setEmail($data['email']);
    if (isset($data['first_name'])) $subscriber->setFirstName($data['first_name']);
    if (isset($data['last_name'])) $subscriber->setLastName($data['last_name']);
    if (isset($data['status'])) $subscriber->setStatus($data['status']);
    if (isset($data['source'])) $subscriber->setSource($data['source']);
    if (isset($data['wp_user_id'])) $subscriber->setWpUserId($data['wp_user_id']);
    if (isset($data['subscribed_ip'])) $subscriber->setSubscribedIp($data['subscribed_ip']);
    if (isset($data['confirmed_ip'])) $subscriber->setConfirmedIp($data['confirmed_ip']);
    if (isset($data['is_woocommerce_user'])) $subscriber->setIsWoocommerceUser((bool)$data['is_woocommerce_user']);
    $createdAt = isset($data['created_at']) ? Carbon::createFromFormat('Y-m-d H:i:s', $data['created_at']) : null;
    if ($createdAt) $subscriber->setCreatedAt($createdAt);
    $confirmedAt = isset($data['confirmed_at']) ? Carbon::createFromFormat('Y-m-d H:i:s', $data['confirmed_at']) : null;
    if ($confirmedAt) $subscriber->setConfirmedAt($confirmedAt);

    // wipe any unconfirmed data at this point
    $subscriber->setUnconfirmedData(null);

    // Validate the email (Saving group) + everything else (Default group)
    $subscriber->setValidationGroups(['Saving', 'Default']);

    try {
      $this->subscribersRepository->persist($subscriber);
      $this->subscribersRepository->flush();
    } catch (ValidationException $exception) {
      // detach invalid entity because it can block another work with doctrine
      $this->subscribersRepository->detach($subscriber);
      throw $exception;
    }

    return $subscriber;
  }

  private function isNewEmail(string $email, ?SubscriberEntity $subscriber): bool {
    if ($subscriber && ($subscriber->getEmail() === $email)) return false;
    return true;
  }

  /**
   * @throws ConflictException
   */
  private function verifyEmailIsUnique(string $email): void {
    $existingSubscriber = $this->subscribersRepository->findOneBy(['email' => $email]);
    if ($existingSubscriber) {
      // translators: %s is email address which already exists.
      $exceptionMessage = sprintf(__('A subscriber with E-mail "%s" already exists.', 'mailpoet'), $email);
      throw new ConflictException($exceptionMessage);
    }
  }

  private function createSubscriber(): SubscriberEntity {
    $subscriber = new SubscriberEntity();
    $subscriber->setUnsubscribeToken($this->security->generateUnsubscribeTokenByEntity($subscriber));
    $subscriber->setLinkToken(Security::generateHash(SubscriberEntity::LINK_TOKEN_LENGTH));
    $subscriber->setStatus(!$this->settings->get('signup_confirmation.enabled') ? SubscriberEntity::STATUS_SUBSCRIBED : SubscriberEntity::STATUS_UNCONFIRMED);

    return $subscriber;
  }

  private function findSubscriber(array &$data): ?SubscriberEntity {
    $subscriber = null;
    if (isset($data['id']) && (int)$data['id'] > 0) {
      $subscriber = $this->subscribersRepository->findOneById(((int)$data['id']));
      unset($data['id']);
    }

    if (!$subscriber && !empty($data['email'])) {
      $subscriber = $this->subscribersRepository->findOneBy(['email' => $data['email']]);
      if ($subscriber) {
        unset($data['email']);
      }
    }

    return $subscriber;
  }

  public function updateCustomFields(array $data, SubscriberEntity $subscriber): void {
    $customFieldsMap = [];
    foreach ($data as $key => $value) {
      if (strpos($key, 'cf_') === 0) {
        $customFieldsMap[(int)substr($key, 3)] = $value;
      }
    }

    if (empty($customFieldsMap)) {
      return;
    }

    $customFields = $this->customFieldsRepository->findBy(['id' => array_keys($customFieldsMap)]);
    foreach ($customFields as $customField) {
      $this->subscriberCustomFieldRepository->createOrUpdate($subscriber, $customField, $customFieldsMap[$customField->getId()]);
    }
  }

  private function updateTags(array $data, SubscriberEntity $subscriber): void {
    $removedTags = [];

    /**
     * $data['tags'] is either an array of arrays containing name, id etc. of the tag or an array of strings - the names
     * of the tag.
     *
     * Therefore we map it to be only an array of strings, containing the names of the tag.
     */
    $tags = array_map(
      function($tag): string {
        if (is_array($tag)) {
          return array_key_exists('name', $tag) ? (string)$tag['name'] : '';
        }
        return (string)$tag;
      },
      (array)$data['tags']
    );
    foreach ($subscriber->getSubscriberTags() as $subscriberTag) {
      $tag = $subscriberTag->getTag();
      if (!$tag || !in_array($tag->getName(), $tags, true)) {
        $subscriber->getSubscriberTags()->removeElement($subscriberTag);
        $removedTags[] = $subscriberTag;
      }
    }

    $newlyAddedTags = [];
    foreach ($tags as $tagName) {
      $tag = $this->tagRepository->createOrUpdate(['name' => $tagName]);
      $subscriberTag = $subscriber->getSubscriberTag($tag);
      if (!$subscriberTag) {
        $subscriberTag = new SubscriberTagEntity($tag, $subscriber);
        $subscriber->getSubscriberTags()->add($subscriberTag);
        $this->subscriberTagRepository->persist($subscriberTag);
        $newlyAddedTags[] = $subscriberTag;
      }
    }
    $this->subscriberTagRepository->flush();
    foreach ($newlyAddedTags as $subscriberTag) {
      $this->wp->doAction('mailpoet_subscriber_tag_added', $subscriberTag);
    }
    foreach ($removedTags as $subscriberTag) {
      $this->wp->doAction('mailpoet_subscriber_tag_removed', $subscriberTag);
    }
  }
}
