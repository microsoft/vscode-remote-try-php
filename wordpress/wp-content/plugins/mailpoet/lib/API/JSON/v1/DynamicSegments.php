<?php declare(strict_types = 1);

namespace MailPoet\API\JSON\v1;

if (!defined('ABSPATH')) exit;


use MailPoet\API\JSON\Endpoint as APIEndpoint;
use MailPoet\API\JSON\Error;
use MailPoet\API\JSON\Response;
use MailPoet\API\JSON\ResponseBuilders\DynamicSegmentsResponseBuilder;
use MailPoet\Config\AccessControl;
use MailPoet\ConflictException;
use MailPoet\Doctrine\Validator\ValidationException;
use MailPoet\Entities\SegmentEntity;
use MailPoet\Listing\Handler;
use MailPoet\Newsletter\Segment\NewsletterSegmentRepository;
use MailPoet\Segments\DynamicSegments\DynamicSegmentsListingRepository;
use MailPoet\Segments\DynamicSegments\Exceptions\InvalidFilterException;
use MailPoet\Segments\DynamicSegments\FilterDataMapper;
use MailPoet\Segments\DynamicSegments\SegmentSaveController;
use MailPoet\Segments\SegmentsRepository;
use MailPoet\Segments\SegmentSubscribersRepository;
use MailPoet\UnexpectedValueException;
use Throwable;

class DynamicSegments extends APIEndpoint {

  public $permissions = [
    'global' => AccessControl::PERMISSION_MANAGE_SEGMENTS,
  ];

  /** @var Handler */
  private $listingHandler;

  /** @var DynamicSegmentsListingRepository */
  private $dynamicSegmentsListingRepository;

  /** @var SegmentsRepository */
  private $segmentsRepository;

  /** @var DynamicSegmentsResponseBuilder */
  private $segmentsResponseBuilder;

  /** @var SegmentSaveController */
  private $saveController;

  /** @var SegmentSubscribersRepository */
  private $segmentSubscribersRepository;

  /** @var FilterDataMapper */
  private $filterDataMapper;

  /** @var NewsletterSegmentRepository */
  private $newsletterSegmentRepository;

  public function __construct(
    Handler $handler,
    DynamicSegmentsListingRepository $dynamicSegmentsListingRepository,
    DynamicSegmentsResponseBuilder $segmentsResponseBuilder,
    SegmentsRepository $segmentsRepository,
    SegmentSubscribersRepository $segmentSubscribersRepository,
    FilterDataMapper $filterDataMapper,
    SegmentSaveController $saveController,
    NewsletterSegmentRepository $newsletterSegmentRepository
  ) {
    $this->listingHandler = $handler;
    $this->dynamicSegmentsListingRepository = $dynamicSegmentsListingRepository;
    $this->segmentsResponseBuilder = $segmentsResponseBuilder;
    $this->segmentsRepository = $segmentsRepository;
    $this->saveController = $saveController;
    $this->segmentSubscribersRepository = $segmentSubscribersRepository;
    $this->filterDataMapper = $filterDataMapper;
    $this->newsletterSegmentRepository = $newsletterSegmentRepository;
  }

  public function get($data = []) {
    if (isset($data['id'])) {
      $id = (int)$data['id'];
    } else {
      return $this->errorResponse([
        Error::BAD_REQUEST => __('Missing mandatory argument `id`.', 'mailpoet'),
      ]);
    }

    $segment = $this->segmentsRepository->findOneById($id);
    if (!$segment instanceof SegmentEntity) {
      return $this->errorResponse([
        Error::NOT_FOUND => __('This segment does not exist.', 'mailpoet'),
      ]);
    }

    return $this->successResponse($this->segmentsResponseBuilder->build($segment));
  }

  public function getCount($data = []) {
    try {
      $filterData = $this->filterDataMapper->map($data);
      $count = $this->segmentSubscribersRepository->getDynamicSubscribersCount($filterData);
      return $this->successResponse([
        'count' => $count,
      ]);
    } catch (InvalidFilterException $e) {
      return $this->errorResponse([
        Error::BAD_REQUEST => $this->getErrorString($e),
      ], [], Response::STATUS_BAD_REQUEST);
    }
  }

  public function save($data) {
    try {
      $data['name'] = isset($data['name']) ? sanitize_text_field($data['name']) : '';
      $data['description'] = isset($data['description']) ? sanitize_textarea_field($data['description']) : '';
      $segment = $this->saveController->save($data);
      return $this->successResponse($this->segmentsResponseBuilder->build($segment));
    } catch (InvalidFilterException $e) {
      return $this->errorResponse([
        Error::BAD_REQUEST => $this->getErrorString($e),
      ], [], Response::STATUS_BAD_REQUEST);
    } catch (ConflictException $e) {
      return $this->badRequest([
        Error::BAD_REQUEST => __('Another record already exists. Please specify a different "name".', 'mailpoet'),
      ]);
    } catch (ValidationException $exception) {
      return $this->badRequest([
        Error::BAD_REQUEST => __('Please specify a name.', 'mailpoet'),
      ]);
    }
  }

  public function duplicate($data = []) {
    $segment = $this->getSegment($data);

    if ($segment instanceof SegmentEntity) {
      try {
        $duplicate = $this->saveController->duplicate($segment);
      } catch (Throwable $e) {
        return $this->errorResponse([
          // translators: %s is the error message
          Error::UNKNOWN => sprintf(__('Duplicating of segment failed: %s', 'mailpoet'), $e->getMessage()),
        ], [], Response::STATUS_UNKNOWN);
      }
      return $this->successResponse(
        $this->segmentsResponseBuilder->build($duplicate),
        ['count' => 1]
      );
    } else {
      return $this->errorResponse([
        Error::NOT_FOUND => __('This segment does not exist.', 'mailpoet'),
      ]);
    }
  }

  private function getErrorString(InvalidFilterException $e) {
    switch ($e->getCode()) {
      case InvalidFilterException::MISSING_TYPE:
        return __('The segment type is missing.', 'mailpoet');
      case InvalidFilterException::INVALID_TYPE:
        return __('The segment type is unknown.', 'mailpoet');
      case InvalidFilterException::MISSING_ROLE:
        return __('Please select a user role.', 'mailpoet');
      case InvalidFilterException::MISSING_ACTION:
      case InvalidFilterException::INVALID_EMAIL_ACTION:
        return __('Please select an email action.', 'mailpoet');
      case InvalidFilterException::MISSING_NEWSLETTER_ID:
        return __('Please select an email.', 'mailpoet');
      case InvalidFilterException::MISSING_PRODUCT_ID:
        return __('Please select a product.', 'mailpoet');
      case InvalidFilterException::MISSING_COUNTRY:
        return __('Please select a country.', 'mailpoet');
      case InvalidFilterException::MISSING_CATEGORY_ID:
        return __('Please select a category.', 'mailpoet');
      case InvalidFilterException::MISSING_VALUE:
        return __('Please fill all required values.', 'mailpoet');
      case InvalidFilterException::MISSING_NUMBER_OF_ORDERS_FIELDS:
        return __('Please select a type for the comparison, a number of orders and a number of days.', 'mailpoet');
      case InvalidFilterException::MISSING_TOTAL_SPENT_FIELDS:
      case InvalidFilterException::MISSING_SINGLE_ORDER_VALUE_FIELDS:
      case InvalidFilterException::MISSING_AVERAGE_SPENT_FIELDS:
        return __('Please select a type for the comparison, an amount and a number of days.', 'mailpoet');
      case InvalidFilterException::MISSING_FILTER:
        return __('Please add at least one condition for filtering.', 'mailpoet');
      case InvalidFilterException::MISSING_OPERATOR:
        return __('Please select a type for the comparison.', 'mailpoet');
      default:
        return __('An error occurred while saving data.', 'mailpoet');
    }
  }

  public function trash($data = []) {
    if (!isset($data['id'])) {
      return $this->errorResponse([
        Error::BAD_REQUEST => __('Missing mandatory argument `id`.', 'mailpoet'),
      ]);
    }

    $segment = $this->getSegment($data);
    if ($segment === null) {
      return $this->errorResponse([
        Error::NOT_FOUND => __('This segment does not exist.', 'mailpoet'),
      ]);
    }

    $activelyUsedErrors = $this->getErrorMessagesForSegmentsUsedInActiveNewsletters([$segment->getId()]);
    if (count($activelyUsedErrors) > 0) {
      return $this->badRequest($activelyUsedErrors);
    }

    $this->segmentsRepository->bulkTrash([$segment->getId()], SegmentEntity::TYPE_DYNAMIC);
    return $this->successResponse(
      $this->segmentsResponseBuilder->build($segment),
      ['count' => 1]
    );
  }

  public function getErrorMessagesForSegmentsUsedInActiveNewsletters(array $segmentIds): array {
    $errors = [];
    $activelyUsedNewslettersSubjects = $this->newsletterSegmentRepository->getSubjectsOfActivelyUsedEmailsForSegments($segmentIds);
    foreach ($segmentIds as $segmentId) {
      if (isset($activelyUsedNewslettersSubjects[$segmentId])) {
        $segment = $this->getSegment(['id' => $segmentId]);
        if ($segment) {
          $errors[] = sprintf(
            // translators: %1$s is the name of the segment, %2$s is a comma-seperated list of emails for which the segment is used.
            _x('Segment \'%1$s\' cannot be deleted because it’s used for \'%2$s\' email', 'Alert shown when trying to delete segment, which is assigned to any automatic emails.', 'mailpoet'),
            $segment->getName(),
            join("', '", $activelyUsedNewslettersSubjects[$segmentId])
          );
        }
      }
    }

    return $errors;
  }

  public function restore($data = []) {
    if (!isset($data['id'])) {
      return $this->errorResponse([
        Error::BAD_REQUEST => __('Missing mandatory argument `id`.', 'mailpoet'),
      ]);
    }

    $segment = $this->getSegment($data);
    if ($segment === null) {
      return $this->errorResponse([
        Error::NOT_FOUND => __('This segment does not exist.', 'mailpoet'),
      ]);
    }

    $this->segmentsRepository->bulkRestore([$segment->getId()], SegmentEntity::TYPE_DYNAMIC);
    return $this->successResponse(
      $this->segmentsResponseBuilder->build($segment),
      ['count' => 1]
    );
  }

  public function delete($data = []) {
    if (!isset($data['id'])) {
      return $this->errorResponse([
        Error::BAD_REQUEST => __('Missing mandatory argument `id`.', 'mailpoet'),
      ]);
    }

    $segment = $this->getSegment($data);
    if ($segment === null) {
      return $this->errorResponse([
        Error::NOT_FOUND => __('This segment does not exist.', 'mailpoet'),
      ]);
    }

    $this->segmentsRepository->bulkDelete([$segment->getId()], SegmentEntity::TYPE_DYNAMIC);
    return $this->successResponse(null, ['count' => 1]);
  }

  public function listing($data = []) {
    $data['params'] = $data['params'] ?? ['segments']; // Dummy param to apply constraints properly
    $definition = $this->listingHandler->getListingDefinition($data);
    $items = $this->dynamicSegmentsListingRepository->getData($definition);
    $count = $this->dynamicSegmentsListingRepository->getCount($definition);
    $filters = $this->dynamicSegmentsListingRepository->getFilters($definition);
    $groups = $this->dynamicSegmentsListingRepository->getGroups($definition);
    $segments = $this->segmentsResponseBuilder->buildForListing($items);

    return $this->successResponse($segments, [
      'count' => $count,
      'filters' => $filters,
      'groups' => $groups,
    ]);
  }

  public function bulkAction($data = []) {
    $definition = $this->listingHandler->getListingDefinition($data['listing']);
    $ids = $this->dynamicSegmentsListingRepository->getActionableIds($definition);
    $meta = [];
    if ($data['action'] === 'trash') {
      $errors = $this->getErrorMessagesForSegmentsUsedInActiveNewsletters($ids);
      if (count($errors) > 0) {
        $meta['errors'] = $errors;
      }
      $meta['count'] = $this->segmentsRepository->bulkTrash($ids, SegmentEntity::TYPE_DYNAMIC);
    } elseif ($data['action'] === 'restore') {
      $meta['count'] = $this->segmentsRepository->bulkRestore($ids, SegmentEntity::TYPE_DYNAMIC);
    } elseif ($data['action'] === 'delete') {
      $meta['count'] = $this->segmentsRepository->bulkDelete($ids, SegmentEntity::TYPE_DYNAMIC);
    } else {
      throw UnexpectedValueException::create()
        ->withErrors([Error::BAD_REQUEST => "Invalid bulk action '{$data['action']}' provided."]);
    }
    return $this->successResponse(null, $meta);
  }

  private function getSegment(array $data): ?SegmentEntity {
    return isset($data['id'])
      ? $this->segmentsRepository->findOneById((int)$data['id'])
      : null;
  }
}
