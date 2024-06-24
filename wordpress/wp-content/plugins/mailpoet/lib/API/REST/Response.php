<?php declare(strict_types = 1);

namespace MailPoet\API\REST;

if (!defined('ABSPATH')) exit;


use WP_REST_Response;

class Response extends WP_REST_Response {
  public function __construct(
    array $data = null,
    int $status = 200
  ) {
    parent::__construct(['data' => $data], $status);
  }
}
