<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Doctrine\Validator;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Symfony\Component\Validator\ConstraintViolationInterface;
use MailPoetVendor\Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends \RuntimeException {
  /** @var string */
  private $resourceName;

  /** @var ConstraintViolationListInterface|ConstraintViolationInterface[] */
  private $violations;

  public function __construct(
    $resourceName,
    ConstraintViolationListInterface $violations
  ) {
    $this->resourceName = $resourceName;
    $this->violations = $violations;

    $linePrefix = '  ';
    $message = "Validation failed for '$resourceName'.\nDetails:\n";
    $message .= $linePrefix . implode("\n$linePrefix", $this->getErrors());
    parent::__construct($message);
  }

  /** @return string */
  public function getResourceName() {
    return $this->resourceName;
  }

  /** @return ConstraintViolationListInterface|ConstraintViolationInterface[] */
  public function getViolations() {
    return $this->violations;
  }

  /** @return string[] */
  public function getErrors() {
    $messages = [];
    foreach ($this->violations as $violation) {
      $messages[] = $this->formatError($violation);
    }
    sort($messages);
    return $messages;
  }

  private function formatError(ConstraintViolationInterface $violation) {
    return '[' . $violation->getPropertyPath() . '] ' . $violation->getMessage();
  }
}
