<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\API\JSON\ResponseBuilders;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\CustomFieldEntity;

class CustomFieldsResponseBuilder {
  /**
   * @param CustomFieldEntity[] $customFields
   * @return array
   */
  public function buildBatch(array $customFields) {
    return array_map([$this, 'build'], $customFields);
  }

  /**
   * @param CustomFieldEntity $customField
   * @return array
   */
  public function build(CustomFieldEntity $customField) {
    return [
      'id' => $customField->getId(),
      'name' => $customField->getName(),
      'type' => $customField->getType(),
      'params' => $customField->getParams(),
      'created_at' => ($createdAt = $customField->getCreatedAt()) ? $createdAt->format('Y-m-d H:i:s') : null,
      'updated_at' => $customField->getUpdatedAt()->format('Y-m-d H:i:s'),
    ];
  }
}
