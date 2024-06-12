<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\MailPoet\Fields;

if (!defined('ABSPATH')) exit;


use DateTimeImmutable;
use MailPoet\Automation\Engine\Data\Field;
use MailPoet\Automation\Engine\Exceptions\InvalidStateException;
use MailPoet\Automation\Engine\WordPress;
use MailPoet\Automation\Integrations\MailPoet\Payloads\SubscriberPayload;
use MailPoet\CustomFields\CustomFieldsRepository;
use MailPoet\Entities\CustomFieldEntity;
use MailPoet\Entities\SubscriberCustomFieldEntity;
use MailPoet\Util\DateConverter;

class SubscriberCustomFieldsFactory {
  /** @var CustomFieldsRepository */
  private $customFieldsRepository;

  /** @var WordPress */
  private $wordPress;

  public function __construct(
    CustomFieldsRepository $customFieldsRepository,
    WordPress $wordPress
  ) {
    $this->customFieldsRepository = $customFieldsRepository;
    $this->wordPress = $wordPress;
  }

  /** @return Field[] */
  public function getFields(): array {
    return array_map(function (CustomFieldEntity $customField) {
      return $this->getField($customField);
    }, $this->customFieldsRepository->findAll());
  }

  private function getField(CustomFieldEntity $customField): Field {
    switch ($customField->getType()) {
      case CustomFieldEntity::TYPE_TEXT:
      case CustomFieldEntity::TYPE_TEXTAREA:
        $validate = $customField->getParams()['validate'] ?? null;
        return $validate === 'number'
          ? $this->createNumberField($customField)
          : $this->createStringField($customField);
      case CustomFieldEntity::TYPE_CHECKBOX:
        return $this->createBooleanField($customField);
      case CustomFieldEntity::TYPE_RADIO:
      case CustomFieldEntity::TYPE_SELECT:
        return $this->createEnumField($customField);
      case CustomFieldEntity::TYPE_DATE:
        $type = $customField->getParams()['date_type'] ?? null;
        if ($type === 'year_month_day' || $type === 'year_month') {
          return $this->createDateTimeField($customField);
        } elseif ($type === 'year') {
          return $this->createYearField($customField);
        } elseif ($type === 'month') {
          return $this->createMonthField($customField);
        } elseif ($type === 'day') {
          return $this->createDayField($customField);
        } else {
          throw new InvalidStateException(sprintf('Unknown date type "%s"', $type));
        }
      default:
        throw new InvalidStateException(sprintf('Unknown custom field type "%s"', $customField->getType()));
    }
  }

  private function createStringField(CustomFieldEntity $customField): Field {
    $factory = function (SubscriberPayload $payload) use ($customField) {
      return $this->getCustomFieldValue($payload, $customField);
    };
    return $this->createField($customField, Field::TYPE_STRING, $factory);
  }

  private function createNumberField(CustomFieldEntity $customField): Field {
    $factory = function (SubscriberPayload $payload) use ($customField) {
      $value = $this->getCustomFieldValue($payload, $customField);
      return is_numeric($value) ? (float)$value : null;
    };
    return $this->createField($customField, Field::TYPE_NUMBER, $factory);
  }

  private function createBooleanField(CustomFieldEntity $customField): Field {
    $factory = function (SubscriberPayload $payload) use ($customField) {
      $value = $this->getCustomFieldValue($payload, $customField);
      return $value === null ? null : (bool)$value;
    };
    return $this->createField($customField, Field::TYPE_BOOLEAN, $factory);
  }

  private function createEnumField(CustomFieldEntity $customField): Field {
    $factory = function (SubscriberPayload $payload) use ($customField) {
      $value = $this->getCustomFieldValue($payload, $customField);
      return $value === null ? null : $value;
    };
    return $this->createField($customField, Field::TYPE_ENUM, $factory, [
      'options' => array_map(function (array $value) {
        return ['id' => $value['value'], 'name' => $value['value']];
      }, $customField->getParams()['values'] ?? []),
    ]);
  }

  private function createDateTimeField(CustomFieldEntity $customField): Field {
    $factory = function (SubscriberPayload $payload) use ($customField) {
      return $this->getDateTimeValue($customField, $this->getCustomFieldValue($payload, $customField) ?? '');
    };
    return $this->createField($customField, Field::TYPE_DATETIME, $factory);
  }

  private function createYearField(CustomFieldEntity $customField): Field {
    $factory = function (SubscriberPayload $payload) use ($customField) {
      $value = $this->getDateTimeValue($customField, $this->getCustomFieldValue($payload, $customField) ?? '');
      return $value ? (int)$value->format('Y') : null;
    };
    return $this->createField($customField, Field::TYPE_INTEGER, $factory);
  }

  private function createMonthField(CustomFieldEntity $customField): Field {
    $factory = function (SubscriberPayload $payload) use ($customField) {
      $value = $this->getDateTimeValue($customField, $this->getCustomFieldValue($payload, $customField) ?? '');
      return $value ? (int)$value->format('n') : null;
    };
    return $this->createField($customField, Field::TYPE_ENUM, $factory, [
      'options' => array_map(function (int $value) {
        return ['id' => $value, 'name' => $this->wordPress->getWpLocale()->get_month($value)];
      }, range(1, 12)),
    ]);
  }

  private function createDayField(CustomFieldEntity $customField): Field {
    $factory = function (SubscriberPayload $payload) use ($customField) {
      $value = $this->getDateTimeValue($customField, $this->getCustomFieldValue($payload, $customField) ?? '');
      return $value ? (int)$value->format('j') : null;
    };
    return $this->createField($customField, Field::TYPE_ENUM, $factory, [
      'options' => array_map(function (int $value) {
        return ['id' => $value, 'name' => "$value"];
      }, range(1, 31)),
    ]);
  }

  private function getCustomFieldValue(SubscriberPayload $payload, CustomFieldEntity $customField): ?string {
    $subscriberCustomField = $payload->getSubscriber()->getSubscriberCustomFields()->filter(
      function (SubscriberCustomFieldEntity $subscriberCustomField = null) use ($customField) {
        return $subscriberCustomField && $subscriberCustomField->getCustomField() === $customField;
      }
    )->first() ?: null;
    return $subscriberCustomField ? $subscriberCustomField->getValue() : null;
  }

  private function createField(CustomFieldEntity $customField, string $type, callable $factory, array $args = []): Field {
    $key = 'mailpoet:subscriber:custom-field:' . $customField->getName();
    $name = sprintf(
      // translators: %s is the name of the custom field
      __('Custom field: %s', 'mailpoet'),
      $customField->getParams()['label'] ?? $customField->getName()
    );
    return new Field($key, $type, $name, $factory, $args);
  }

  private function getDateTimeValue(CustomFieldEntity $customField, ?string $value): ?DateTimeImmutable {
    $dateFormat = $customField->getParams()['date_format'] ?? null;
    if (!$dateFormat || !$value) {
      return null;
    }
    $dateString = (new DateConverter())->convertDateToDatetime($value, $dateFormat) ?: null;
    return $dateString ? new DateTimeImmutable($dateString, $this->wordPress->wpTimezone()) : null;
  }
}
