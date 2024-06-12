<?php declare(strict_types = 1);

namespace MailPoet\API\REST;

if (!defined('ABSPATH')) exit;


use WP_REST_Request;

class Request {
  /** @var WP_REST_Request */
  private $wpRequest;

  public function __construct(
    WP_REST_Request $wpRequest
  ) {
    $this->wpRequest = $wpRequest;
  }

  public function getHeader(string $key): ?string {
    return $this->wpRequest->get_header($key);
  }

  public function getParams(): array {
    return $this->wpRequest->get_params();
  }

  /** @return mixed */
  public function getParam(string $name) {
    return $this->wpRequest->get_param($name);
  }
}
