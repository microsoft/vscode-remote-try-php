<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing
namespace MailPoetVendor\Sudzy;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Paris\Model;

/**
 * @method static static|bool create($data=null)
 */
abstract class ValidModel extends Model {
  protected $_validator = null; // Reference to Sudzy validator object
  protected $_validations = []; // Array of validations
  protected $_validationErrors = []; // Array of error messages
  protected $_validationOptions = [
    'indexedErrors' => false, // If True getValidationErrors will return an array with the index
                              // being the field name and the value the error. If multiple errors
                              // are triggered for a field only the first will be kept.
    'throw' => self::ON_SAVE,  // One of self::ON_SET|ON_SAVE|NEVER.
                              //  + ON_SET throws immediately when field is set()
                              //  + ON_SAVE throws on save()
                              //  + NEVER means an exception is never thrown; check for ->getValidationErrors()
  ];

  const ON_SET = 'set';
  const ON_SAVE = 'save';
  const NEVER = null;

  public function __construct($validatorInstance = null) {
    $this->_validator = $validatorInstance;
  }

  public function setValidationOptions($options) {
    $this->_validationOptions = array_merge($this->_validationOptions, $options);
  }

  public function addValidation($field, $validation, $message) {
    if (!isset($this->_validations[$field])) {
      $this->_validations[$field] = [];
    }
    $this->_validations[$field][] = [
      'validation' => $validation,
      'message'     => $message,
    ];
  }

  public function addValidations($field, $validators) {
    foreach ($validators as $validation => $message) {
      $this->addValidation($field, $validation, $message);
    }
  }

  // /**
  // * Checks, without throwing exceptions, model fields with validations
  // *
  // * @return bool If false, running $this->doValidationError() will respond appropriately
  // */
  // public function validate()
  // {
  //     $fields = array_keys($this->_validations);
  //     $success = true;
  //     foreach ($fields as $f) {
  //         $success = $success && $this->validateField($f, $this->$f);
  //     }
  //     return $success;
  // }

  /**
  * @return bool Will set a message if returning false
  **/
  public function validateField($field, $value) {
    $this->setupValidationEngine();

    if (!isset($this->_validations[$field])) {
      return true; // No validations, return true by default
    }

    $success = true;
    foreach ($this->_validations[$field] as $v) {
      $checks = explode(' ', $v['validation']);
      foreach ($checks as $check) {
        $params = explode('|', $check);
        $check  = array_shift($params);

        if (!$this->_validator->executeOne($check, $value, $params)) {
          $this->addValidationError($v['message'], $field);
          $success = false;
        }
      }
    }
    return $success;
  }

  public function getValidationErrors() {
    return $this->_validationErrors;
  }

  public function resetValidationErrors() {
    $this->_validationErrors = [];
  }

  ///////////////////
  // Overloaded methods

  /**
  * Overload __set to call validateAndSet
  */
  public function __set($name, $value) {
    $this->validateAndSet($name, $value);
  }

  /**
  * Overload save; checks if errors exist before saving
  */
  public function save() {
    if ($this->isNew()) { //Fields populated by create() or hydrate() don't pass through set()
      foreach (array_keys($this->_validations) as $field) {
        $this->validateField($field, $this->$field);
      }
    }

    $errs = $this->getValidationErrors();
    if (!empty($errs)) {
      $this->doValidationError(self::ON_SAVE);
    }

    parent::save();
  }

  /**
  * Overload set; to call validateAndSet
  */
  public function set($key, $value = null) {
    if (is_array($key)) {
      // multiple values
      foreach ($key as $field => $value) {
        $this->validateAndSet($field, $value);
      }
    } else {
      $this->validateAndSet($key, $value);
    }
    // we should return $this to not break Idiorm's fluent interface:
    // $model->set('property', 'foo')->save();
    return $this;
  }


  ////////////////////
  // Protected methods
  protected function doValidationError($context) {
    if ($context == $this->_validationOptions['throw']) {
      throw new \MailPoetVendor\Sudzy\ValidationException($this->_validationErrors);
    }
  }

  protected function addValidationError($msg, $field = null) {
    if ($this->_validationOptions['indexedErrors'] && $field !== null) {
      // Only keep the first error found on a field
      if (!isset($this->_validationErrors[$field])) {
        $this->_validationErrors[$field] = $msg;
      }
    } else {
      $this->_validationErrors[] = $msg;
    }
  }

  /**
  * Overload set; to call validateAndSet
  */
  protected function validateAndSet($name, $value) {
    if (!$this->validateField($name, $value)) $this->doValidationError(self::ON_SET);
    parent::set($name, $value);
  }

  protected function setupValidationEngine() {
    if (null == $this->_validator) $this->_validator = new \MailPoetVendor\Sudzy\Engine(); // Is lazy setup worth it?
  }
}
