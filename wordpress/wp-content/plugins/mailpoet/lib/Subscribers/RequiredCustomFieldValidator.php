<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Subscribers;

if (!defined('ABSPATH')) exit;


use Exception;
use MailPoet\CustomFields\CustomFieldsRepository;
use MailPoet\Entities\FormEntity;

class RequiredCustomFieldValidator {
  /** @var CustomFieldsRepository */
  private $customFieldRepository;

  public function __construct(
    CustomFieldsRepository $customFieldRepository
  ) {
    $this->customFieldRepository = $customFieldRepository;
  }

  /**
   * @param array $data
   * @param FormEntity|null $form
   *
   * @throws Exception
   */
  public function validate(array $data, FormEntity $form = null) {
    $allCustomFields = $this->getCustomFields($form);
    foreach ($allCustomFields as $customFieldId => $customFieldName) {
      if ($this->isCustomFieldMissing($customFieldId, $data)) {
        throw new Exception(
          // translators: %s is the name of the custom field.
          sprintf(__('Missing value for custom field "%s"', 'mailpoet'), $customFieldName)
        );
      }
    }
  }

  private function isCustomFieldMissing(int $customFieldId, array $data): bool {
    if (!array_key_exists($customFieldId, $data) && !array_key_exists('cf_' . $customFieldId, $data)) {
      return true;
    }
    if (isset($data[$customFieldId]) && !$data[$customFieldId]) {
      return true;
    }
    if (isset($data['cf_' . $customFieldId]) && !$data['cf_' . $customFieldId]) {
      return true;
    }
    return false;
  }

  private function getCustomFields(FormEntity $form = null): array {
    $result = [];

    if ($form) {
      $ids = $this->getFormCustomFieldIds($form);
      if (!$ids) {
        return [];
      }
      $requiredCustomFields = $this->customFieldRepository->findBy(['id' => $ids]);
    } else {
      $requiredCustomFields = $this->customFieldRepository->findAll();
    }

    foreach ($requiredCustomFields as $customField) {
      $params = $customField->getParams();
      if (is_array($params) && isset($params['required']) && $params['required']) {
        $result[$customField->getId()] = $customField->getName();
      }
    }

    return $result;
  }

  /**
   * @return int[]
   */
  private function getFormCustomFieldIds(FormEntity $form): array {
    $formFields = $form->getBlocksByTypes(FormEntity::FORM_FIELD_TYPES);
    $customFieldIds = [];
    foreach ($formFields as $formField) {
      if (isset($formField['id']) && is_numeric($formField['id'])) {
        $customFieldIds[] = (int)$formField['id'];
      }
    }
    return $customFieldIds;
  }
}
