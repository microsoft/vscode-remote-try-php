<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\API\JSON;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\AccessControl;

abstract class Endpoint {
  const TYPE_POST = 'POST';
  const TYPE_GET = 'GET';

  public $permissions = [
    'global' => AccessControl::PERMISSION_MANAGE_SETTINGS,
    'methods' => [],
  ];

  protected static $getMethods = [];

  public function successResponse(
    $data = [], $meta = [], $status = Response::STATUS_OK
  ) {
    return new SuccessResponse($data, $meta, $status);
  }

  public function errorResponse(
    $errors = [], $meta = [], $status = Response::STATUS_NOT_FOUND
  ) {
    if (empty($errors)) {
      $errors = [
        Error::UNKNOWN => __('An unknown error occurred.', 'mailpoet'),
      ];
    }
    return new ErrorResponse($errors, $meta, $status);
  }

  public function badRequest($errors = [], $meta = []) {
    if (empty($errors)) {
      $errors = [
        Error::BAD_REQUEST => __('Invalid request parameters', 'mailpoet'),
      ];
    }
    return new ErrorResponse($errors, $meta, Response::STATUS_BAD_REQUEST);
  }

  public function isMethodAllowed($name, $type) {
    // Block GET requests on POST endpoints, but allow POST requests on GET endpoints (some plugins
    // change REQUEST_METHOD to POST on GET requests, which caused them to be blocked)
    if ($type === self::TYPE_GET && !in_array($name, static::$getMethods)) {
      return false;
    }
    return true;
  }
}
