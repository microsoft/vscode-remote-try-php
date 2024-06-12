<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoetVendor\Sudzy;

if (!defined('ABSPATH')) exit;


class ValidationException extends \Exception
{
  protected $_validationErrors;

  public function __construct($errs) {
      $this->_validationErrors = $errs;
      parent::__construct(implode("\n", $errs));
  }

  public function getValidationErrors() {
      return $this->_validationErrors;
  }
}
