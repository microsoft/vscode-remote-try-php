<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\API\JSON\v1;

if (!defined('ABSPATH')) exit;


use MailPoet\API\JSON\Endpoint as APIEndpoint;
use MailPoet\API\JSON\Error as APIError;
use MailPoet\API\JSON\Response;
use MailPoet\API\JSON\ResponseBuilders\CustomFieldsResponseBuilder;
use MailPoet\Config\AccessControl;
use MailPoet\CustomFields\CustomFieldsRepository;
use MailPoet\Entities\CustomFieldEntity;
use MailPoet\Form\ApiDataSanitizer;

class CustomFields extends APIEndpoint {
  public $permissions = [
    'global' => AccessControl::PERMISSION_MANAGE_FORMS,
  ];

  /** @var CustomFieldsRepository */
  private $customFieldsRepository;

  /** @var CustomFieldsResponseBuilder */
  private $customFieldsResponseBuilder;

  /** @var ApiDataSanitizer */
  private $dataSanitizer;

  public function __construct(
    CustomFieldsRepository $customFieldsRepository,
    CustomFieldsResponseBuilder $customFieldsResponseBuilder,
    ApiDataSanitizer $dataSanitizer
  ) {
    $this->customFieldsRepository = $customFieldsRepository;
    $this->customFieldsResponseBuilder = $customFieldsResponseBuilder;
    $this->dataSanitizer = $dataSanitizer;
  }

  public function getAll() {
    $collection = $this->customFieldsRepository->findBy([], ['createdAt' => 'asc']);
    return $this->successResponse($this->customFieldsResponseBuilder->buildBatch($collection));
  }

  public function delete($data = []) {
    $id = (isset($data['id']) ? (int)$data['id'] : null);
    $customField = $this->customFieldsRepository->findOneById($id);
    if ($customField instanceof CustomFieldEntity) {
      $this->customFieldsRepository->remove($customField);
      $this->customFieldsRepository->flush();

      return $this->successResponse($this->customFieldsResponseBuilder->build($customField));
    } else {
      return $this->errorResponse([
        APIError::NOT_FOUND => __('This custom field does not exist.', 'mailpoet'),
      ]);
    }
  }

  public function save($data = []) {
    try {
      $data = $this->dataSanitizer->sanitizeBlock($data);
      $customField = $this->customFieldsRepository->createOrUpdate($data);
      $customField = $this->customFieldsRepository->findOneById($customField->getId());
      if(!$customField instanceof CustomFieldEntity) return $this->errorResponse();
      return $this->successResponse($this->customFieldsResponseBuilder->build($customField));
    } catch (\Exception $e) {
      return $this->errorResponse($errors = [], $meta = [], $status = Response::STATUS_BAD_REQUEST);
    }
  }

  public function get($data = []) {
    $id = (isset($data['id']) ? (int)$data['id'] : null);
    $customField = $this->customFieldsRepository->findOneById($id);
    if ($customField instanceof CustomFieldEntity) {
      return $this->successResponse($this->customFieldsResponseBuilder->build($customField));
    }
    return $this->errorResponse([
      APIError::NOT_FOUND => __('This custom field does not exist.', 'mailpoet'),
    ]);
  }
}
