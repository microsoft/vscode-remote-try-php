<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoetVendor\Sudzy;

if (!defined('ABSPATH')) exit;


/**
 * Singleton valdation engine
 **/
class Engine
{
    /**
    * Validation methods are stored here so they can easily be overwritten
    */
  protected $_checks;

  public function __construct() {
      $this->_checks = [
          'required'  => [$this, '_required'],
          'minLength' => [$this, '_minLength'],
          'maxLength' => [$this, '_maxLength'],
          'isEmail'   => [$this, '_isEmail'],
          'isInteger'   => [$this, '_isInteger'],
          'isNumeric'   => [$this, '_isNumeric'],
      ];
  }

  public function __call($name, $args) {
      if (!isset($this->_checks[$name]))
          throw new \InvalidArgumentException("{$name} is not a valid validation function.");

      $val = array_shift($args);
      $args = array_shift($args);

      return call_user_func($this->_checks[$name], $val, $args);
  }

  public function executeOne($check, $val, $params=[]) {
    $callback = [$this, $check];
    if (is_callable($callback)) {
      return call_user_func($callback, $val, $params);
    }
  }

    /**
     * @param string $label label used to call function
     * @param Callable $function function with params (value, additional params as array)
     * @throws \Exception
     */
  public function addValidator($label, $function) {
      if (isset($this->_checks[$label])) throw new \Exception();
      $this->setValidator($label, $function);
  }

  public function setValidator($label, $function) {
      $this->_checks[$label] = $function;
  }

  public function removeValidator($label) {
      unset($this->_checks[$label]);
  }

    /**
    * @return array<int, int|string> The list of usable validator methods
    */
  public function getValidators() {
      return array_keys($this->_checks);
  }

    ///// Validator methods
  protected function _isEmail($val, $params) {
      return false !== filter_var($val, FILTER_VALIDATE_EMAIL);
  }

  protected function _isInteger($val, $params) {
      if (!is_numeric($val)) return false;
      return intval($val) == $val;
  }

  protected function _isNumeric($val, $params) {
      return is_numeric($val);
  }

  protected function _minLength($val, $params) {
      $len = array_shift($params);
      return strlen($val) >= $len;
  }

  protected function _maxLength($val, $params) {
      $len = array_shift($params);
      return strlen($val) <= $len;
  }

  protected function _required($val, $params=[]) {
      return !(($val === null) || ('' === trim($val)));
  }
}
