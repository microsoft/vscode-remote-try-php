<?php declare(strict_types = 1);

namespace MailPoet\API\REST;

if (!defined('ABSPATH')) exit;


class ErrorResponse extends Response {
  public function __construct(
    int $status,
    string $message,
    string $code,
    array $errors = []
  ) {
    parent::__construct(null, $status);
    $this->set_data([
      'code' => $code,
      'message' => $message,
      'data' => [
        'status' => $status,
        'errors' => $errors,
      ],
    ]);
  }
}
