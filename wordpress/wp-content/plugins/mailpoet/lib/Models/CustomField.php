<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Models;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\CustomFieldEntity;
use MailPoet\Util\DateConverter;

/**
 * @property string $name
 * @property string $type
 * @property string|array|null $params
 *
 * @deprecated This model is deprecated. Use \MailPoet\CustomFields\CustomFieldsRepository and
 * \MailPoet\Entities\CustomFieldEntity instead. This class can be removed after 2024-05-30.
 */
class CustomField extends Model {
  public static $_table = MP_CUSTOM_FIELDS_TABLE; // phpcs:ignore PSR2.Classes.PropertyDeclaration
  const TYPE_DATE = CustomFieldEntity::TYPE_DATE;
  const TYPE_TEXT = CustomFieldEntity::TYPE_TEXT;
  const TYPE_TEXTAREA = CustomFieldEntity::TYPE_TEXTAREA;
  const TYPE_RADIO = CustomFieldEntity::TYPE_RADIO;
  const TYPE_CHECKBOX = CustomFieldEntity::TYPE_CHECKBOX;
  const TYPE_SELECT = CustomFieldEntity::TYPE_SELECT;

  /**
   * @deprecated
   */
  public function __construct() {
    self::deprecationError(__METHOD__);
    parent::__construct();
    $this->addValidations('name', [
      'required' => __('Please specify a name.', 'mailpoet'),
    ]);
    $this->addValidations('type', [
      'required' => __('Please specify a type.', 'mailpoet'),
    ]);
  }

  /**
   * @deprecated
   */
  public function asArray() {
    self::deprecationError(__METHOD__);
    $model = parent::asArray();

    if (isset($model['params'])) {
      $model['params'] = (is_array($this->params))
        ? $this->params
        : ($this->params !== null ? unserialize($this->params) : false);
    }
    return $model;
  }

  /**
   * @deprecated
   */
  public function save() {
    self::deprecationError(__METHOD__);
    if (is_null($this->params)) {
      $this->params = [];
    }
    $this->set('params', (
      is_array($this->params)
      ? serialize($this->params)
      : $this->params
    ));
    return parent::save();
  }

  /**
   * @deprecated
   */
  public function formatValue($value = null) {
    self::deprecationError(__METHOD__);
    // format custom field data depending on type
    if (is_array($value) && $this->type === self::TYPE_DATE) {
      $customFieldData = $this->asArray();
      $dateFormat = $customFieldData['params']['date_format'];
      $dateType = (isset($customFieldData['params']['date_type'])
        ? $customFieldData['params']['date_type']
        : 'year_month_day'
      );
      $dateParts = explode('_', $dateType);
      switch ($dateType) {
        case 'year_month_day':
          $value = str_replace(
            ['DD', 'MM', 'YYYY'],
            [$value['day'], $value['month'], $value['year']],
            $dateFormat
          );
          break;

        case 'year_month':
          $value = str_replace(
            ['MM', 'YYYY'],
            [$value['month'], $value['year']],
            $dateFormat
          );
          break;

        case 'month':
          if ((int)$value['month'] === 0) {
            $value = '';
          } else {
            $value = sprintf(
              '%s',
              $value['month']
            );
          }
          break;

        case 'day':
          if ((int)$value['day'] === 0) {
            $value = '';
          } else {
            $value = sprintf(
              '%s',
              $value['day']
            );
          }
          break;

        case 'year':
          if ((int)$value['year'] === 0) {
            $value = '';
          } else {
            $value = sprintf(
              '%04d',
              $value['year']
            );
          }
          break;
      }

      if (!empty($value) && is_string($value)) {
        $value = (new DateConverter())->convertDateToDatetime($value, $dateFormat);
      }
    }

    return $value;
  }

  /**
   * @deprecated
   */
  public function subscribers() {
    self::deprecationError(__METHOD__);
    return $this->hasManyThrough(
      __NAMESPACE__ . '\Subscriber',
      __NAMESPACE__ . '\SubscriberCustomField',
      'custom_field_id',
      'subscriber_id'
    )->selectExpr(MP_SUBSCRIBER_CUSTOM_FIELD_TABLE . '.value');
  }

  /**
   * @deprecated
   */
  public static function createOrUpdate($data = []) {
    self::deprecationError(__METHOD__);
    // set name as label by default
    if (empty($data['params']['label']) && isset($data['name'])) {
      $data['params']['label'] = $data['name'];
    }
    return parent::_createOrUpdate($data);
  }

  /**
   * @deprecated This is here for displaying the deprecation warning for properties.
   */
  public function __get($key) {
    self::deprecationError('property "' . $key . '"');
    return parent::__get($key);
  }

  /**
   * @deprecated This is here for displaying the deprecation warning for static calls.
   */
  public static function __callStatic($name, $arguments) {
    self::deprecationError($name);
    return parent::__callStatic($name, $arguments);
  }

  private static function deprecationError($methodName) {
    trigger_error(
      'Calling ' . esc_html($methodName) . ' is deprecated and will be removed. Use \MailPoet\CustomFields\CustomFieldsRepository and \MailPoet\Entities\CustomFieldEntity instead.',
      E_USER_DEPRECATED
    );
  }
}
