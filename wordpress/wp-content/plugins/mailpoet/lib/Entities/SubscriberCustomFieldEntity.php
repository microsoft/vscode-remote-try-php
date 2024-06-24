<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Entities;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\EntityTraits\AutoincrementedIdTrait;
use MailPoet\Doctrine\EntityTraits\CreatedAtTrait;
use MailPoet\Doctrine\EntityTraits\SafeToOneAssociationLoadTrait;
use MailPoet\Doctrine\EntityTraits\UpdatedAtTrait;
use MailPoet\InvalidStateException;
use MailPoet\Util\DateConverter;
use MailPoetVendor\Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="subscriber_custom_field")
 */
class SubscriberCustomFieldEntity {
  use AutoincrementedIdTrait;
  use CreatedAtTrait;
  use UpdatedAtTrait;
  use SafeToOneAssociationLoadTrait;

  /**
   * @ORM\ManyToOne(targetEntity="MailPoet\Entities\SubscriberEntity")
   * @var SubscriberEntity|null
   */
  private $subscriber;

  /**
   * @ORM\ManyToOne(targetEntity="MailPoet\Entities\CustomFieldEntity")
   * @var CustomFieldEntity|null
   */
  private $customField;

  /**
   * @ORM\Column(type="string")
   * @var string
   */
  private $value;

  /**
   * @param string|array|null $value
   */
  public function __construct(
    SubscriberEntity $subscriber,
    CustomFieldEntity $customField,
    $value
  ) {
    $this->subscriber = $subscriber;
    $this->customField = $customField;
    $this->setValue($value);
  }

  /**
   * @return SubscriberEntity|null
   */
  public function getSubscriber() {
    $this->safelyLoadToOneAssociation('subscriber');
    return $this->subscriber;
  }

  public function getValue(): string {
    return $this->value;
  }

  /**
   * @return CustomFieldEntity|null
   */
  public function getCustomField() {
    return $this->customField;
  }

  /**
   * @param string|array|null $value
   */
  public function setValue($value): void {
    $customField = $this->getCustomField();
    if (!$customField instanceof CustomFieldEntity) {
      throw new InvalidStateException('CustomField has to be set');
    }

    // format custom field data depending on type
    if (is_array($value) && $customField->getType() === CustomFieldEntity::TYPE_DATE) {
      $customFieldParams = $customField->getParams();
      $dateFormat = $customFieldParams['date_format'] ?? null;
      $dateType = isset($customFieldParams['date_type']) ? $customFieldParams['date_type'] : 'year_month_day';
      switch ($dateType) {
        case 'year_month_day':
          $value = str_replace(['DD', 'MM', 'YYYY'], [$value['day'], $value['month'], $value['year']], $dateFormat);
          break;

        case 'year_month':
          $value = str_replace(['MM', 'YYYY'], [$value['month'], $value['year']], $dateFormat);
          break;

        case 'month':
          $value = (int)$value['month'] === 0 ? '' : sprintf('%s', $value['month']);
          break;

        case 'day':
          $value = (int)$value['day'] === 0 ? '' : sprintf('%s', $value['day']);
          break;

        case 'year':
          $value = (int)$value['year'] === 0 ? '' : sprintf('%04d', $value['year']);
          break;
      }

      if (!empty($value) && is_string($value)) {
        $value = (new DateConverter())->convertDateToDatetime($value, $dateFormat);
      }
    }

    if (is_array($value)) {
      throw new InvalidStateException('Final value has to be string');
    }
    $this->value = (string)$value;
  }
}
