<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\CustomFields;

if (!defined('ABSPATH')) exit;


use InvalidArgumentException;
use MailPoet\Entities\CustomFieldEntity;

class ApiDataSanitizer {

  const ERROR_MANDATORY_ARGUMENT_MISSING = 1001;
  const ERROR_MANDATORY_ARGUMENT_WRONG_TYPE = 1002;
  const ERROR_PARAMS_WRONG_TYPE = 1003;
  const ERROR_INVALID_TYPE = 1004;
  const ERROR_INVALID_VALIDATE = 1005;
  const ERROR_CHECKBOX_WRONG_VALUES_COUNT = 1006;
  const ERROR_INVALID_DATE_FORMAT = 1007;
  const ERROR_INVALID_DATE_TYPE = 1008;
  const ERROR_NO_VALUES = 1009;
  const ERROR_NO_VALUE = 1010;

  public function sanitize(array $data = []) {
    $this->checkMandatoryStringParameter($data, 'name');
    $this->checkMandatoryStringParameter($data, 'type');
    $this->checkParamsType($data);
    return [
      'name' => $data['name'],
      'type' => strtolower($data['type']),
      'params' => $this->sanitizeParams($data),
    ];
  }

  private function checkMandatoryStringParameter(array $data, $parameterName) {
    if (empty($data[$parameterName])) {
      // translators: %s is the name of the missing argument.
      throw new InvalidArgumentException(sprintf(__('Mandatory argument "%s" is missing', 'mailpoet'), $parameterName), self::ERROR_MANDATORY_ARGUMENT_MISSING);
    }
    if (!is_string($data[$parameterName])) {
      // translators: %s is the name of the malformed argument.
      throw new InvalidArgumentException(sprintf(__('Mandatory argument "%s" has to be string', 'mailpoet'), $parameterName), self::ERROR_MANDATORY_ARGUMENT_WRONG_TYPE);
    }
  }

  private function checkParamsType($data) {
    if (isset($data['params']) && !is_array($data['params'])) {
      throw new InvalidArgumentException(sprintf(__('Params has to be array', 'mailpoet')), self::ERROR_PARAMS_WRONG_TYPE);
    }
  }

  private function sanitizeParams($data) {
    $data['params'] = isset($data['params']) ? $data['params'] : [];
    $result = [];
    $result['required'] = $this->getRequired($data['params']);
    $result['label'] = $this->getLabel($data);
    return $result + $this->getExtraParams($data);
  }

  private function getLabel($data) {
    if (empty($data['params']['label'])) {
      return $data['name'];
    } else {
      return $data['params']['label'];
    }
  }

  private function getRequired($params) {
    if (isset($params['required']) && $params['required']) {
      return '1';
    }
    return '';
  }

  private function getExtraParams($data) {
    $type = strtolower($data['type']);
    if (in_array($type, [CustomFieldEntity::TYPE_TEXT, CustomFieldEntity::TYPE_TEXTAREA], true)) {
      return $this->getExtraParamsForText($data['params']);
    }

    if (in_array($type, [CustomFieldEntity::TYPE_RADIO, CustomFieldEntity::TYPE_SELECT], true)) {
      return $this->getExtraParamsForSelect($data['params']);
    }

    if ($type === CustomFieldEntity::TYPE_CHECKBOX) {
      return $this->getExtraParamsForCheckbox($data['params']);
    }

    if ($type === CustomFieldEntity::TYPE_DATE) {
      return $this->getExtraParamsForDate($data['params']);
    }

    // translators: %s is the name of the type.
    throw new InvalidArgumentException(sprintf(__('Invalid type "%s"', 'mailpoet'), $type), self::ERROR_INVALID_TYPE);
  }

  private function getExtraParamsForText($params) {
    if (isset($params['validate'])) {
      $validate = trim(strtolower($params['validate']));
      if (in_array($validate, ['number', 'alphanum', 'phone'], true)) {
        return ['validate' => $validate];
      }
      throw new InvalidArgumentException(__('Validate parameter is not valid', 'mailpoet'), self::ERROR_INVALID_VALIDATE);
    }
    return [];
  }

  private function getExtraParamsForCheckbox($params) {
    if (empty($params['values']) || count($params['values']) > 1) {
      throw new InvalidArgumentException(__('You need to pass exactly one value for checkbox', 'mailpoet'), self::ERROR_CHECKBOX_WRONG_VALUES_COUNT);
    }
    $value = reset($params['values']);
    return ['values' => [$this->sanitizeValue($value)]];
  }

  private function getExtraParamsForDate($params) {
    $dateType = (isset($params['date_type'])
      ? $params['date_type']
      : 'year_month_day'
    );
    $inputDateFormat = (isset($params['date_format'])
      ? $params['date_format']
      : ''
    );

    switch ($dateType) {
      case 'year_month_day':
        if (!in_array($inputDateFormat, ['MM/DD/YYYY', 'DD/MM/YYYY', 'YYYY/MM/DD'], true)) {
          throw new InvalidArgumentException(__('Invalid date_format for year_month_day', 'mailpoet'), self::ERROR_INVALID_DATE_FORMAT);
        }
        $dateFormat = $inputDateFormat;
        break;
      case 'year_month':
        if (!in_array($inputDateFormat, ['YYYY/MM', 'MM/YY'], true)) {
          throw new InvalidArgumentException(__('Invalid date_format for year_month', 'mailpoet'), self::ERROR_INVALID_DATE_FORMAT);
        }
        $dateFormat = $inputDateFormat;
        break;
      case 'month':
        $dateFormat = 'MM';
        break;
      case 'year':
        $dateFormat = 'YYYY';
        break;
      case 'day':
        $dateFormat = 'DD';
        break;
      default:
        throw new InvalidArgumentException(__('Invalid value for date_type', 'mailpoet'), self::ERROR_INVALID_DATE_TYPE);
    }
    return [
      'date_type' => $dateType,
      'date_format' => $dateFormat,
    ];
  }

  private function getExtraParamsForSelect($params) {
    if (empty($params['values'])) {
      throw new InvalidArgumentException(__('You need to pass some values for this type', 'mailpoet'), self::ERROR_NO_VALUES);
    }
    $values = [];
    foreach ($params['values'] as $value) {
      $values[] = $this->sanitizeValue($value);
    }
    return ['values' => $values];
  }

  private function sanitizeValue($value) {
    if (empty($value['value'])) {
      throw new InvalidArgumentException(__('Value cannot be empty', 'mailpoet'), self::ERROR_NO_VALUE);
    }
    $result = ['value' => $value['value']];
    if (isset($value['is_checked']) && $value['is_checked']) {
      $result['is_checked'] = '1';
    } else {
      $result['is_checked'] = '';
    }
    return $result;
  }
}
