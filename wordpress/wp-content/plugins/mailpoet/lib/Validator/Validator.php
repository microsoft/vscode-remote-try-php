<?php declare(strict_types = 1);

namespace MailPoet\Validator;

if (!defined('ABSPATH')) exit;


use JsonSerializable;
use MailPoet\WP\Functions as WPFunctions;
use stdClass;
use WP_Error;

class Validator {
  /** @var WPFunctions */
  private $wp;

  public function __construct(
    WPFunctions $wp
  ) {
    $this->wp = $wp;
  }

  /**
   * Strict validation & sanitization implementation.
   * It only coerces int to float (e.g. 5 to 5.0).
   *
   * @param mixed $value
   * @return mixed
   */
  public function validate(Schema $schema, $value, string $paramName = 'value') {
    return $this->validateSchemaArray($schema->toArray(), $value, $paramName);
  }

  /**
   * Strict validation & sanitization implementation.
   * It only coerces int to float (e.g. 5 to 5.0).
   *
   * @param array $schema. The array must follow the format, which is returned from Schema::toArray().
   * @param mixed $value
   * @return mixed
   */
  public function validateSchemaArray(array $schema, $value, string $paramName = 'value') {
    $result = $this->validateAndSanitizeValueFromSchema($value, $schema, $paramName);
    if ($result instanceof WP_Error) {
      throw ValidationException::createFromWpError($result);
    }
    return $result;
  }

  /**
   * Mirrors rest_validate_value_from_schema() and rest_sanitize_value_from_schema().
   *
   * @param mixed $value
   * @param array $schema
   * @param string $paramName
   * @return mixed|WP_Error
   */
  private function validateAndSanitizeValueFromSchema($value, array $schema, string $paramName) {
    // nullable
    $fullType = $schema['type'] ?? null;
    if (is_array($fullType) && in_array('null', $fullType, true) && $value === null) {
      return null;
    }

    // anyOf, oneOf
    if (isset($schema['anyOf'])) {
      return $this->validateAndSanitizeAnyOf($value, $schema, $paramName);
    } elseif (isset($schema['oneOf'])) {
      return $this->validateAndSanitizeOneOf($value, $schema, $paramName);
    }

    // make types strict
    $type = is_array($fullType) ? $fullType[0] : $fullType;
    switch ($type) {
      case 'number':
        if (!is_float($value) && !is_int($value)) {
          return $this->getTypeError($paramName, $fullType);
        }
        break;
      case 'integer':
        if (!is_int($value)) {
          return $this->getTypeError($paramName, $fullType);
        }
        break;
      case 'boolean':
        if (!is_bool($value)) {
          return $this->getTypeError($paramName, $fullType);
        }
        break;
      case 'array':
        if (!is_array($value)) {
          return $this->getTypeError($paramName, $fullType);
        }

        if (isset($schema['items'])) {
          foreach ($value as $i => $v) {
            $result = $this->validateAndSanitizeValueFromSchema($v, $schema['items'], $paramName . '[' . $i . ']');
            if ($this->wp->isWpError($result)) {
              return $result;
            }
          }
        }
        break;
      case 'object':
        if (!is_array($value) && !$value instanceof stdClass && !$value instanceof JsonSerializable) {
          return $this->getTypeError($paramName, $fullType);
        }

        // ensure string keys
        $value = (array)($value instanceof JsonSerializable ? $value->jsonSerialize() : $value);
        if (count(array_filter(array_keys($value), 'is_string')) !== count($value)) {
          return $this->getTypeError($paramName, $fullType);
        }

        // validate object properties
        foreach ($value as $k => $v) {
          if (isset($schema['properties'][$k])) {
            $result = $this->validateAndSanitizeValueFromSchema($v, $schema['properties'][$k], $paramName . '[' . $k . ']');
            if ($this->wp->isWpError($result)) {
              return $result;
            }
            continue;
          }

          $patternPropertySchema = $this->wp->restFindMatchingPatternPropertySchema($k, $schema);
          if ($patternPropertySchema) {
            $result = $this->validateAndSanitizeValueFromSchema($v, $patternPropertySchema, $paramName . '[' . $k . ']');
            if ($this->wp->isWpError($result)) {
              return $result;
            }
            continue;
          }

          if (isset($schema['additionalProperties']) && is_array($schema['additionalProperties'])) {
            $result = $this->validateAndSanitizeValueFromSchema($v, $schema['additionalProperties'], $paramName . '[' . $k . ']');
            if ($this->wp->isWpError($result)) {
              return $result;
            }
          }
        }
        break;
    }

    $result = $this->wp->restValidateValueFromSchema($value, $schema, $paramName);
    if ($this->wp->isWpError($result)) {
      return $result;
    }
    return $this->wp->restSanitizeValueFromSchema($value, $schema, $paramName);
  }

  /**
   * Mirrors rest_find_any_matching_schema().
   *
   * @param mixed $value
   * @return mixed|WP_Error
   */
  private function validateAndSanitizeAnyOf($value, array $anyOfSchema, string $paramName) {
    $errors = [];
    foreach ($anyOfSchema['anyOf'] as $index => $schema) {
      $result = $this->validateAndSanitizeValueFromSchema($value, $schema, $paramName);
      if (!$this->wp->isWpError($result)) {
        return $result;
      }
      $errors[] = ['error_object' => $result, 'schema' => $schema, 'index' => $index];
    }
    return $this->wp->restGetCombiningOperationError($value, $paramName, $errors);
  }

  /**
   * Mirrors rest_find_one_matching_schema().
   *
   * @param mixed $value
   * @return mixed|WP_Error
   */
  private function validateAndSanitizeOneOf($value, array $oneOfSchema, string $paramName) {
    $matchingSchemas = [];
    $errors = [];
    $data = null;
    foreach ($oneOfSchema['oneOf'] as $index => $schema) {
      $result = $this->validateAndSanitizeValueFromSchema($value, $schema, $paramName);
      if ($this->wp->isWpError($result)) {
        $errors[] = ['error_object' => $result, 'schema' => $schema, 'index' => $index];
      } else {
        $data = $result;
        $matchingSchemas[$index] = $schema;
      }
    }

    if (!$matchingSchemas) {
      return $this->wp->restGetCombiningOperationError($value, $paramName, $errors);
    }

    if (count($matchingSchemas) > 1) {
      // reuse WP method to generate detailed error
      $invalidSchema = ['type' => []];
      $oneOf = array_replace(array_fill(0, count($oneOfSchema['oneOf']), $invalidSchema), $matchingSchemas);
      return $this->wp->restFindOneMatchingSchema($value, ['oneOf' => $oneOf], $paramName);
    }
    return $data;
  }

  /** @param string|string[] $type */
  private function getTypeError(string $param, $type): WP_Error {
    $type = is_array($type) ? $type : [$type];
    return new WP_Error(
      'rest_invalid_type',
      // translators: %1$s is the current parameter and %2$s a comma-separated list of the allowed types.
      sprintf(__('%1$s is not of type %2$s.', 'mailpoet'), $param, implode(',', $type)),
      ['param' => $param]
    );
  }
}
