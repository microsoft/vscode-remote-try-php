<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\API\JSON;

if (!defined('ABSPATH')) exit;


class ErrorResponse extends Response {
  public $errors;

  public function __construct(
    $errors = [],
    $meta = [],
    $status = self::STATUS_NOT_FOUND
  ) {
    parent::__construct($status, $meta);
    $this->errors = $this->formatErrors($errors);
  }

  public function getData() {
    return (empty($this->errors)) ? null : ['errors' => $this->errors];
  }

  public function formatErrors($errors = []) {
    return array_map(function($error, $message) {
      // sanitize SQL error
      if (preg_match('/^SQLSTATE/i', $message)) {
        $message = __('An unknown error occurred.', 'mailpoet');
      }
      return [
        'error' => $error,
        'message' => $message,
      ];
    }, array_keys($errors), array_values($errors));
  }
}
