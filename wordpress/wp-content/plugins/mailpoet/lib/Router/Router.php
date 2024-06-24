<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Router;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\AccessControl;
use MailPoet\Util\Helpers;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Psr\Container\ContainerInterface;

class Router {
  public $apiRequest;
  public $endpoint;
  public $action;
  public $data;
  public $endpointAction;
  public $accessControl;
  /** @var ContainerInterface */
  private $container;
  const NAME = 'mailpoet_router';
  const RESPONSE_ERROR = 404;
  const RESPONE_FORBIDDEN = 403;

  public function __construct(
    AccessControl $accessControl,
    ContainerInterface $container,
    $apiData = false
  ) {
    $apiData = ($apiData) ? $apiData : $_GET;
    $this->apiRequest = is_array($apiData) && array_key_exists(self::NAME, $apiData);
    $this->endpoint = isset($apiData['endpoint']) ?
      Helpers::underscoreToCamelCase($apiData['endpoint']) :
      false;
    $this->endpointAction = isset($apiData['action']) ?
      Helpers::underscoreToCamelCase($apiData['action']) :
      false;
    $this->data = isset($apiData['data']) ?
      self::decodeRequestData($apiData['data']) :
      [];
    $this->accessControl = $accessControl;
    $this->container = $container;
  }

  public function init() {
    if (!$this->apiRequest) return;
    $endpointClass = __NAMESPACE__ . "\\Endpoints\\" . ucfirst($this->endpoint);

    if (!$this->endpoint || !class_exists($endpointClass)) {
      return $this->terminateRequest(self::RESPONSE_ERROR, __('Invalid router endpoint', 'mailpoet'));
    }

    $endpoint = $this->container->get($endpointClass);

    if (!method_exists($endpoint, $this->endpointAction) || !in_array($this->endpointAction, $endpoint->allowedActions)) {
      return $this->terminateRequest(self::RESPONSE_ERROR, __('Invalid router endpoint action', 'mailpoet'));
    }
    if (!$this->validatePermissions($this->endpointAction, $endpoint->permissions)) {
      return $this->terminateRequest(self::RESPONE_FORBIDDEN, __('You do not have the required permissions.', 'mailpoet'));
    }
    WPFunctions::get()->doAction('mailpoet_conflict_resolver_router_url_query_parameters');
    $callback = [
      $endpoint,
      $this->endpointAction,
    ];
    if (is_callable($callback)) {
      return call_user_func($callback, $this->data);
    }
  }

  public static function decodeRequestData($data) {
    $data = !is_array($data) ? json_decode(base64_decode($data), true) : [];
    if (!is_array($data)) {
      $data = [];
    }
    return $data;
  }

  public static function encodeRequestData($data) {
    $jsonEncoded = json_encode($data);
    if ($jsonEncoded === false) {
      return '';
    }
    return rtrim(base64_encode($jsonEncoded), '=');
  }

  public static function buildRequest($endpoint, $action, $data = false) {
    $params = [
      self::NAME => '',
      'endpoint' => $endpoint,
      'action' => $action,
    ];
    if ($data) {
      $params['data'] = self::encodeRequestData($data);
    }
    return WPFunctions::get()->addQueryArg($params, WPFunctions::get()->homeUrl());
  }

  public function terminateRequest($code, $message) {
    WPFunctions::get()->statusHeader($code, $message);
    exit;
  }

  public function validatePermissions($endpointAction, $permissions) {
    // validate action permission if defined, otherwise validate global permission
    return(!empty($permissions['actions'][$endpointAction])) ?
      $this->accessControl->validatePermission($permissions['actions'][$endpointAction]) :
      $this->accessControl->validatePermission($permissions['global']);
  }
}
