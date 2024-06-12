<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Exceptions;

if (!defined('ABSPATH')) exit;


use Exception as PhpException;
use MailPoet\API\REST\Exception as RestException;
use Throwable;

/**
 * Frames all MailPoet Automation exceptions ("$e instanceof MailPoet\Automation\Exception").
 */
abstract class Exception extends PhpException implements RestException {
  /** @var int */
  protected $statusCode = 500;

  /** @var string  */
  protected $errorCode;

  /** @var string[] */
  protected $errors = [];

  final public function __construct(
    string $message = null,
    string $errorCode = null,
    Throwable $previous = null
  ) {
    parent::__construct($message ?? __('Unknown error.', 'mailpoet'), 0, $previous);
    $this->errorCode = $errorCode ?? 'mailpoet_automation_unknown_error';
  }

  /** @return static */
  public static function create(Throwable $previous = null) {
    return new static(null, null, $previous);
  }

  /** @return static */
  public function withStatusCode(int $statusCode) {
    $this->statusCode = $statusCode;
    return $this;
  }

  /** @return static */
  public function withError(string $id, string $error) {
    $this->errors[$id] = $error;
    return $this;
  }

  /** @return static */
  public function withErrorCode(string $errorCode) {
    $this->errorCode = $errorCode;
    return $this;
  }

  /** @return static */
  public function withMessage(string $message) {
    $this->message = $message;
    return $this;
  }

  /** @return static */
  public function withErrors(array $errors) {
    $this->errors = $errors;
    return $this;
  }

  public function getStatusCode(): int {
    return $this->statusCode;
  }

  public function getErrorCode(): string {
    return $this->errorCode;
  }

  public function getErrors(): array {
    return $this->errors;
  }
}
