<?php declare(strict_types = 1);

namespace MailPoet\API\JSON\v1;

if (!defined('ABSPATH')) exit;


use MailPoet\API\JSON\Endpoint as APIEndpoint;
use MailPoet\API\JSON\Error as APIError;
use MailPoet\API\JSON\ErrorResponse;
use MailPoet\API\JSON\Response;
use MailPoet\API\JSON\ResponseBuilders\SubscribersResponseBuilder;
use MailPoet\API\JSON\SuccessResponse;
use MailPoet\Config\AccessControl;
use MailPoet\ConflictException;
use MailPoet\Doctrine\Validator\ValidationException;
use MailPoet\Entities\SegmentEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Entities\TagEntity;
use MailPoet\Exception;
use MailPoet\Listing;
use MailPoet\Segments\SegmentsRepository;
use MailPoet\Settings\SettingsController;
use MailPoet\Subscribers\ConfirmationEmailMailer;
use MailPoet\Subscribers\SubscriberListingRepository;
use MailPoet\Subscribers\SubscriberSaveController;
use MailPoet\Subscribers\SubscribersRepository;
use MailPoet\Subscribers\SubscriberSubscribeController;
use MailPoet\Tags\TagRepository;
use MailPoet\UnexpectedValueException;
use MailPoet\Util\Helpers;

class Subscribers extends APIEndpoint {
  const SUBSCRIPTION_LIMIT_COOLDOWN = 60;

  public $permissions = [
    'global' => AccessControl::PERMISSION_MANAGE_SUBSCRIBERS,
    'methods' => ['subscribe' => AccessControl::NO_ACCESS_RESTRICTION],
  ];

  /** @var Listing\Handler */
  private $listingHandler;

  /** @var ConfirmationEmailMailer; */
  private $confirmationEmailMailer;

  /** @var SubscribersRepository */
  private $subscribersRepository;

  /** @var SubscribersResponseBuilder */
  private $subscribersResponseBuilder;

  /** @var SubscriberListingRepository */
  private $subscriberListingRepository;

  /** @var SegmentsRepository */
  private $segmentsRepository;

  /** @var TagRepository */
  private $tagRepository;

  /** @var SubscriberSaveController */
  private $saveController;

  /** @var SubscriberSubscribeController */
  private $subscribeController;

  /** @var SettingsController */
  private $settings;

  public function __construct(
    Listing\Handler $listingHandler,
    ConfirmationEmailMailer $confirmationEmailMailer,
    SubscribersRepository $subscribersRepository,
    SubscribersResponseBuilder $subscribersResponseBuilder,
    SubscriberListingRepository $subscriberListingRepository,
    SegmentsRepository $segmentsRepository,
    TagRepository $tagRepository,
    SubscriberSaveController $saveController,
    SubscriberSubscribeController $subscribeController,
    SettingsController $settings
  ) {
    $this->listingHandler = $listingHandler;
    $this->confirmationEmailMailer = $confirmationEmailMailer;
    $this->subscribersRepository = $subscribersRepository;
    $this->subscribersResponseBuilder = $subscribersResponseBuilder;
    $this->subscriberListingRepository = $subscriberListingRepository;
    $this->segmentsRepository = $segmentsRepository;
    $this->tagRepository = $tagRepository;
    $this->saveController = $saveController;
    $this->subscribeController = $subscribeController;
    $this->settings = $settings;
  }

  public function get($data = []) {
    $subscriber = $this->getSubscriber($data);
    if (!$subscriber instanceof SubscriberEntity) {
      return $this->errorResponse([
        APIError::NOT_FOUND => __('This subscriber does not exist.', 'mailpoet'),
      ]);
    }
    $result = $this->subscribersResponseBuilder->build($subscriber);
    return $this->successResponse($result);
  }

  public function listing($data = []) {
    $definition = $this->listingHandler->getListingDefinition($data);
    $items = $this->subscriberListingRepository->getData($definition);
    $count = $this->subscriberListingRepository->getCount($definition);
    $filters = $this->subscriberListingRepository->getFilters($definition);
    $groups = $this->subscriberListingRepository->getGroups($definition);
    $subscribers = $this->subscribersResponseBuilder->buildForListing($items);
    if ($data['filter']['segment'] ?? false) {
      foreach ($subscribers as $key => $subscriber) {
        $subscribers[$key] = $this->preferUnsubscribedStatusFromSegment($subscriber, $data['filter']['segment']);
      }
    }
    return $this->successResponse($subscribers, [
      'count' => $count,
      'filters' => $filters,
      'groups' => $groups,
    ]);
  }

  private function preferUnsubscribedStatusFromSegment(array $subscriber, $segmentId) {
    $segmentStatus = $this->findSegmentStatus($subscriber, $segmentId);

    if ($segmentStatus === SubscriberEntity::STATUS_UNSUBSCRIBED) {
      $subscriber['status'] = $segmentStatus;
    }
    return $subscriber;
  }

  private function findSegmentStatus(array $subscriber, $segmentId) {
    foreach ($subscriber['subscriptions'] as $segment) {
      if ($segment['segment_id'] === $segmentId) {
        return $segment['status'];
      }
    }
  }

  public function subscribe($data = []) {
    try {
      $meta = $this->subscribeController->subscribe($data);
    } catch (Exception $exception) {
      return $this->badRequest([$exception->getMessage()]);
    }

    if (!empty($meta['error'])) {
      $errorMessage = $meta['error'];
      unset($meta['error']);
      return $this->badRequest([APIError::BAD_REQUEST => $errorMessage], $meta);
    }

    return $this->successResponse(
      [],
      $meta
    );
  }

  /**
   * @param array $data
   * @return ErrorResponse|SuccessResponse
   * @throws \Exception
   */
  public function save(array $data = []) {
    try {
      $subscriber = $this->saveController->save($data);
    } catch (ValidationException $validationException) {
      return $this->badRequest([$this->getErrorMessage($validationException)]);
    } catch (ConflictException $conflictException) {
      return $this->badRequest([
        APIError::BAD_REQUEST => $conflictException->getMessage(),
      ]);
    };

    return $this->successResponse(
      $this->subscribersResponseBuilder->build($subscriber)
    );
  }

  public function restore($data = []) {
    $subscriber = $this->getSubscriber($data);
    if ($subscriber instanceof SubscriberEntity) {
      $this->subscribersRepository->bulkRestore([$subscriber->getId()]);
      $this->subscribersRepository->refresh($subscriber);
      return $this->successResponse(
        $this->subscribersResponseBuilder->build($subscriber),
        ['count' => 1]
      );
    } else {
      return $this->errorResponse([
        APIError::NOT_FOUND => __('This subscriber does not exist.', 'mailpoet'),
      ]);
    }
  }

  public function trash($data = []) {
    $subscriber = $this->getSubscriber($data);
    if ($subscriber instanceof SubscriberEntity) {
      $this->subscribersRepository->bulkTrash([$subscriber->getId()]);
      $this->subscribersRepository->refresh($subscriber);
      return $this->successResponse(
        $this->subscribersResponseBuilder->build($subscriber),
        ['count' => 1]
      );
    } else {
      return $this->errorResponse([
        APIError::NOT_FOUND => __('This subscriber does not exist.', 'mailpoet'),
      ]);
    }
  }

  public function delete($data = []) {
    $subscriber = $this->getSubscriber($data);
    if ($subscriber instanceof SubscriberEntity) {
      $count = $this->subscribersRepository->bulkDelete([$subscriber->getId()]);
      return $this->successResponse(null, ['count' => $count]);
    } else {
      return $this->errorResponse([
        APIError::NOT_FOUND => __('This subscriber does not exist.', 'mailpoet'),
      ]);
    }
  }

  public function sendConfirmationEmail($data = []) {
    if (!(bool)$this->settings->get('signup_confirmation.enabled', true)) {
      $errorMessage = __('Sign-up confirmation is disabled in your [link]MailPoet settings[/link]. Please enable it to resend confirmation emails or update your subscriber’s status manually.', 'mailpoet');
      $errorMessage = Helpers::replaceLinkTags($errorMessage, 'admin.php?page=mailpoet-settings#/signup');
      return $this->errorResponse([APIError::BAD_REQUEST => $errorMessage], [], Response::STATUS_BAD_REQUEST);
    }

    $id = (isset($data['id']) ? (int)$data['id'] : false);
    $subscriber = $this->subscribersRepository->findOneById($id);
    if ($subscriber instanceof SubscriberEntity) {
      try {
        if ($this->confirmationEmailMailer->sendConfirmationEmail($subscriber)) {
          return $this->successResponse();
        } else {
          return $this->errorResponse([
            APIError::UNKNOWN => __('There was a problem with your sending method. Please check if your sending method is properly configured.', 'mailpoet'),
          ]);
        }
      } catch (\Exception $e) {
        return $this->errorResponse([
          APIError::UNKNOWN => __('There was a problem with your sending method. Please check if your sending method is properly configured.', 'mailpoet'),
        ]);
      }
    } else {
      return $this->errorResponse([
        APIError::NOT_FOUND => __('This subscriber does not exist.', 'mailpoet'),
      ]);
    }
  }

  public function bulkAction($data = []) {
    $definition = $this->listingHandler->getListingDefinition($data['listing']);
    $ids = $this->subscriberListingRepository->getActionableIds($definition);

    $count = 0;
    $segment = null;
    if (isset($data['segment_id'])) {
      $segment = $this->getSegment($data);
      if (!$segment) {
        return $this->errorResponse([
          APIError::NOT_FOUND => __('This segment does not exist.', 'mailpoet'),
        ]);
      }
    }

    $tag = null;
    if (isset($data['tag_id'])) {
      $tag = $this->getTag($data);
      if (!$tag) {
        return $this->errorResponse([
          APIError::NOT_FOUND => __('This tag does not exist.', 'mailpoet'),
        ]);
      }
    }

    if ($data['action'] === 'trash') {
      $count = $this->subscribersRepository->bulkTrash($ids);
    } elseif ($data['action'] === 'restore') {
      $count = $this->subscribersRepository->bulkRestore($ids);
    } elseif ($data['action'] === 'delete') {
      $count = $this->subscribersRepository->bulkDelete($ids);
    } elseif ($data['action'] === 'removeFromAllLists') {
      $count = $this->subscribersRepository->bulkRemoveFromAllSegments($ids);
    } elseif ($data['action'] === 'removeFromList' && $segment instanceof SegmentEntity) {
      $count = $this->subscribersRepository->bulkRemoveFromSegment($segment, $ids);
    } elseif ($data['action'] === 'addToList' && $segment instanceof SegmentEntity) {
      $count = $this->subscribersRepository->bulkAddToSegment($segment, $ids);
    } elseif ($data['action'] === 'moveToList' && $segment instanceof SegmentEntity) {
      $count = $this->subscribersRepository->bulkMoveToSegment($segment, $ids);
    } elseif ($data['action'] === 'unsubscribe') {
      $count = $this->subscribersRepository->bulkUnsubscribe($ids);
    } elseif ($data['action'] === 'addTag' && $tag instanceof TagEntity) {
      $count = $this->subscribersRepository->bulkAddTag($tag, $ids);
    } elseif ($data['action'] === 'removeTag' && $tag instanceof TagEntity) {
      $count = $this->subscribersRepository->bulkRemoveTag($tag, $ids);
    } else {
      throw UnexpectedValueException::create()
        ->withErrors([APIError::BAD_REQUEST => "Invalid bulk action '{$data['action']}' provided."]);
    }
    $meta = [
      'count' => $count,
    ];

    if ($segment) {
      $meta['segment'] = $segment->getName();
    }
    if ($tag) {
      $meta['tag'] = $tag->getName();
    }
    return $this->successResponse(null, $meta);
  }

  /**
   * @param array $data
   * @return SubscriberEntity|null
   */
  private function getSubscriber($data) {
    return isset($data['id'])
      ? $this->subscribersRepository->findOneById((int)$data['id'])
      : null;
  }

  private function getSegment(array $data): ?SegmentEntity {
    return isset($data['segment_id'])
      ? $this->segmentsRepository->findOneById((int)$data['segment_id'])
      : null;
  }

  private function getTag(array $data): ?TagEntity {
    return isset($data['tag_id'])
      ? $this->tagRepository->findOneById((int)$data['tag_id'])
      : null;
  }

  private function getErrorMessage(ValidationException $exception): string {
    $exceptionMessage = $exception->getMessage();
    if (strpos($exceptionMessage, 'This value should not be blank.') !== false) {
      return __('Please enter your email address', 'mailpoet');
    } elseif (strpos($exceptionMessage, 'This value is not a valid email address.') !== false) {
      return __('Your email address is invalid!', 'mailpoet');
    }

    return __('Unexpected error.', 'mailpoet');
  }
}
