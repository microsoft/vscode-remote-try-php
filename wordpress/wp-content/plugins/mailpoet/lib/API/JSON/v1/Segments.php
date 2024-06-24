<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\API\JSON\v1;

if (!defined('ABSPATH')) exit;


use Exception;
use MailPoet\API\JSON\Endpoint as APIEndpoint;
use MailPoet\API\JSON\Error as APIError;
use MailPoet\API\JSON\Response;
use MailPoet\API\JSON\ResponseBuilders\SegmentsResponseBuilder;
use MailPoet\Config\AccessControl;
use MailPoet\ConflictException;
use MailPoet\Cron\CronWorkerScheduler;
use MailPoet\Cron\Workers\WooCommerceSync;
use MailPoet\Doctrine\Validator\ValidationException;
use MailPoet\Entities\SegmentEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Form\FormsRepository;
use MailPoet\Listing;
use MailPoet\Newsletter\Segment\NewsletterSegmentRepository;
use MailPoet\Segments\SegmentListingRepository;
use MailPoet\Segments\SegmentSaveController;
use MailPoet\Segments\SegmentsRepository;
use MailPoet\Segments\SegmentSubscribersRepository;
use MailPoet\Segments\WooCommerce;
use MailPoet\Segments\WP;
use MailPoet\Subscribers\SubscribersRepository;
use MailPoet\UnexpectedValueException;

class Segments extends APIEndpoint {
  public $permissions = [
    'global' => AccessControl::PERMISSION_MANAGE_SEGMENTS,
  ];

  /** @var Listing\Handler */
  private $listingHandler;

  /** @var SegmentsRepository */
  private $segmentsRepository;

  /** @var SegmentsResponseBuilder */
  private $segmentsResponseBuilder;

  /** @var SegmentSaveController */
  private $segmentSavecontroller;

  /** @var SubscribersRepository */
  private $subscribersRepository;

  /** @var WooCommerce */
  private $wooCommerceSync;

  /** @var WP */
  private $wpSegment;

  /** @var SegmentListingRepository */
  private $segmentListingRepository;

  /** @var NewsletterSegmentRepository */
  private $newsletterSegmentRepository;

  /** @var CronWorkerScheduler */
  private $cronWorkerScheduler;

  /** @var FormsRepository */
  private $formsRepository;

  /** @var SegmentSubscribersRepository */
  private $segmentSubscribersRepository;

  public function __construct(
    Listing\Handler $listingHandler,
    SegmentsRepository $segmentsRepository,
    SegmentListingRepository $segmentListingRepository,
    SegmentsResponseBuilder $segmentsResponseBuilder,
    SegmentSaveController $segmentSavecontroller,
    SegmentSubscribersRepository $segmentSubscribersRepository,
    SubscribersRepository $subscribersRepository,
    WooCommerce $wooCommerce,
    WP $wpSegment,
    NewsletterSegmentRepository $newsletterSegmentRepository,
    CronWorkerScheduler $cronWorkerScheduler,
    FormsRepository $formsRepository
  ) {
    $this->listingHandler = $listingHandler;
    $this->wooCommerceSync = $wooCommerce;
    $this->segmentsRepository = $segmentsRepository;
    $this->segmentsResponseBuilder = $segmentsResponseBuilder;
    $this->segmentSavecontroller = $segmentSavecontroller;
    $this->subscribersRepository = $subscribersRepository;
    $this->wpSegment = $wpSegment;
    $this->segmentListingRepository = $segmentListingRepository;
    $this->newsletterSegmentRepository = $newsletterSegmentRepository;
    $this->cronWorkerScheduler = $cronWorkerScheduler;
    $this->formsRepository = $formsRepository;
    $this->segmentSubscribersRepository = $segmentSubscribersRepository;
  }

  public function get($data = []) {
    $id = (isset($data['id']) ? (int)$data['id'] : false);
    $segment = $this->segmentsRepository->findOneById($id);
    if ($segment instanceof SegmentEntity) {
      return $this->successResponse($this->segmentsResponseBuilder->build($segment));
    } else {
      return $this->errorResponse([
        APIError::NOT_FOUND => __('This list does not exist.', 'mailpoet'),
      ]);
    }
  }

  public function listing($data = []) {
    $data['params'] = $data['params'] ?? ['lists']; // Dummy param to apply constraints properly
    $definition = $this->listingHandler->getListingDefinition($data);
    $items = $this->segmentListingRepository->getData($definition);
    $count = $this->segmentListingRepository->getCount($definition);
    $filters = $this->segmentListingRepository->getFilters($definition);
    $groups = $this->segmentListingRepository->getGroups($definition);
    $segments = $this->segmentsResponseBuilder->buildForListing($items);

    return $this->successResponse($segments, [
      'count' => $count,
      'filters' => $filters,
      'groups' => $groups,
    ]);
  }

  public function save($data = []) {
    try {
      $data['name'] = isset($data['name']) ? sanitize_text_field($data['name']) : '';
      $data['description'] = isset($data['description']) ? sanitize_textarea_field($data['description']) : '';
      $segment = $this->segmentSavecontroller->save($data);
    } catch (ValidationException $exception) {
      return $this->badRequest([
        APIError::BAD_REQUEST => __('Please specify a name.', 'mailpoet'),
      ]);
    } catch (ConflictException $exception) {
      return $this->badRequest([
        APIError::BAD_REQUEST => __('Another record already exists. Please specify a different "name".', 'mailpoet'),
      ]);
    }
    $response = $this->segmentsResponseBuilder->build($segment);
    return $this->successResponse($response);
  }

  public function restore($data = []) {
    $segment = $this->getSegment($data);
    if ($segment instanceof SegmentEntity) {
      if (!$this->isTrashOrRestoreAllowed($segment)) {
        return $this->errorResponse([
          APIError::FORBIDDEN => __('This list cannot be moved to trash.', 'mailpoet'),
        ]);
      }
      // When the segment is of type WP_USERS we want to restore all its subscribers
      if ($segment->getType() === SegmentEntity::TYPE_WP_USERS) {
        $subscribers = $this->subscribersRepository->findBySegment((int)$segment->getId());
        $subscriberIds = array_map(function (SubscriberEntity $subscriberEntity): int {
          return (int)$subscriberEntity->getId();
        }, $subscribers);
        $this->subscribersRepository->bulkRestore($subscriberIds);
      }

      $this->segmentsRepository->bulkRestore([$segment->getId()], $segment->getType());
      $this->segmentsRepository->refresh($segment);
      return $this->successResponse(
        $this->segmentsResponseBuilder->build($segment),
        ['count' => 1]
      );
    } else {
      return $this->errorResponse([
        APIError::NOT_FOUND => __('This list does not exist.', 'mailpoet'),
      ]);
    }
  }

  public function trash($data = []) {
    $segment = $this->getSegment($data);
    if (!$segment instanceof SegmentEntity) {

      return $this->errorResponse([
        APIError::NOT_FOUND => __('This list does not exist.', 'mailpoet'),
      ]);
    }

    if (!$this->isTrashOrRestoreAllowed($segment)) {
      return $this->errorResponse([
        APIError::FORBIDDEN => __('This list cannot be moved to trash.', 'mailpoet'),
      ]);
    }

    $activelyUsedNewslettersSubjects = $this->newsletterSegmentRepository->getSubjectsOfActivelyUsedEmailsForSegments([$segment->getId()]);
    if (isset($activelyUsedNewslettersSubjects[$segment->getId()])) {
      return $this->badRequest([
        APIError::BAD_REQUEST => str_replace(
          '%1$s',
          "'" . join("', '", $activelyUsedNewslettersSubjects[$segment->getId()]) . "'",
          // translators: %1$s is a comma-seperated list of emails for which the segment is used.
          _x('List cannot be deleted because it’s used for %1$s email', 'Alert shown when trying to delete segment, which is assigned to any automatic emails.', 'mailpoet')
        ),
      ]);
    }

    $activelyUsedFormNames = $this->formsRepository->getNamesOfFormsForSegments();
    if (isset($activelyUsedFormNames[$segment->getId()])) {
      return $this->badRequest([
        APIError::BAD_REQUEST => str_replace(
          '%1$s',
          "'" . join("', '", $activelyUsedFormNames[$segment->getId()]) . "'",
          // translators: %1$s is a comma-seperated list of forms for which the segment is used.
          _nx(
            'List cannot be deleted because it’s used for %1$s form',
            'List cannot be deleted because it’s used for %1$s forms',
            count($activelyUsedFormNames[$segment->getId()]),
            'Alert shown when trying to delete segment, when it is assigned to a form.',
            'mailpoet'
          )
        ),
      ]);
    }

    // When the segment is of type WP_USERS we want to trash all subscribers who aren't subscribed in another list
    if ($segment->getType() === SegmentEntity::TYPE_WP_USERS) {
      $subscribers = $this->subscribersRepository->findExclusiveSubscribersBySegment((int)$segment->getId());
      $subscriberIds = array_map(function (SubscriberEntity $subscriberEntity): int {
        return (int)$subscriberEntity->getId();
      }, $subscribers);
      $this->subscribersRepository->bulkTrash($subscriberIds);
    }

    $this->segmentsRepository->doTrash([$segment->getId()], $segment->getType());
    $this->segmentsRepository->refresh($segment);
    return $this->successResponse(
      $this->segmentsResponseBuilder->build($segment),
      ['count' => 1]
    );
  }

  public function delete($data = []) {
    $segment = $this->getSegment($data);
    if ($segment instanceof SegmentEntity) {
      $this->segmentsRepository->bulkDelete([$segment->getId()]);
      return $this->successResponse(null, ['count' => 1]);
    } else {
      return $this->errorResponse([
        APIError::NOT_FOUND => __('This list does not exist.', 'mailpoet'),
      ]);
    }
  }

  public function duplicate($data = []) {
    $segment = $this->getSegment($data);

    if ($segment instanceof SegmentEntity) {
      try {
        $duplicate = $this->segmentSavecontroller->duplicate($segment);
      } catch (Exception $e) {
        return $this->errorResponse([
          APIError::UNKNOWN => __('Duplicating of segment failed.', 'mailpoet'),
        ], [], Response::STATUS_UNKNOWN);
      }
      return $this->successResponse(
        $this->segmentsResponseBuilder->build($duplicate),
        ['count' => 1]
      );
    } else {
      return $this->errorResponse([
        APIError::NOT_FOUND => __('This list does not exist.', 'mailpoet'),
      ]);
    }
  }

  public function synchronize($data) {
    try {
      if ($data['type'] === SegmentEntity::TYPE_WC_USERS) {
        $this->cronWorkerScheduler->scheduleImmediatelyIfNotRunning(WooCommerceSync::TASK_TYPE);
      } else {
        $this->wpSegment->synchronizeUsers();
      }
    } catch (\Exception $e) {
      return $this->errorResponse([
        $e->getCode() => $e->getMessage(),
      ]);
    }

    return $this->successResponse(null);
  }

  public function bulkAction($data = []) {
    $definition = $this->listingHandler->getListingDefinition($data['listing']);
    $ids = $this->segmentListingRepository->getActionableIds($definition);
    $count = 0;
    if ($data['action'] === 'trash') {
      $count = $this->segmentsRepository->bulkTrash($ids);
    } elseif ($data['action'] === 'restore') {
      $count = $this->segmentsRepository->bulkRestore($ids);
    } elseif ($data['action'] === 'delete') {
      $count = $this->segmentsRepository->bulkDelete($ids);
    } else {
      throw UnexpectedValueException::create()
        ->withErrors([APIError::BAD_REQUEST => "Invalid bulk action '{$data['action']}' provided."]);
    }
    return $this->successResponse(null, ['count' => $count]);
  }

  public function subscriberCount($data = []) {
    $segmentIds = $data['segmentIds'] ?? [];
    if (empty($segmentIds)) {
      return $this->errorResponse([
        APIError::BAD_REQUEST => __('No segment IDs provided.', 'mailpoet'),
      ]);
    }
    $filterSegmentId = $data['filterSegmentId'] ?? null;
    $status = $data['status'] ?? SubscriberEntity::STATUS_SUBSCRIBED;
    $response['count'] = $this->segmentSubscribersRepository->getSubscribersCountBySegmentIds($segmentIds, $status, $filterSegmentId);

    return $this->successResponse($response);
  }

  private function isTrashOrRestoreAllowed(SegmentEntity $segment): bool {
    $allowedSegmentTypes = [
      SegmentEntity::TYPE_DEFAULT,
      SegmentEntity::TYPE_WP_USERS,
    ];
    if (in_array($segment->getType(), $allowedSegmentTypes, true)) {
      return true;
    }

    return false;
  }

  private function getSegment(array $data): ?SegmentEntity {
    return isset($data['id'])
      ? $this->segmentsRepository->findOneById((int)$data['id'])
      : null;
  }
}
