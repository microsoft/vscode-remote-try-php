<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\API\JSON;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\AccessControl;
use MailPoet\Exception;
use MailPoet\Logging\LoggerFactory;
use MailPoet\Settings\SettingsController;
use MailPoet\Subscription\Captcha\CaptchaConstants;
use MailPoet\Tracy\ApiPanel\ApiPanel;
use MailPoet\Tracy\DIPanel\DIPanel;
use MailPoet\Util\Helpers;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Psr\Container\ContainerInterface;
use Throwable;
use Tracy\Debugger;
use Tracy\ILogger;

class API {
  private $requestApiVersion;
  private $requestEndpoint;
  private $requestMethod;
  private $requestToken;
  private $requestType;
  private $requestEndpointClass;
  private $requestData = [];
  private $endpointNamespaces = [];
  private $availableApiVersions = [
      'v1',
  ];
  /** @var ContainerInterface */
  private $container;

  /** @var AccessControl */
  private $accessControl;

  /** @var ErrorHandler */
  private $errorHandler;

  /** @var WPFunctions */
  private $wp;

  /** @var SettingsController */
  private $settings;

  /** @var LoggerFactory */
  private $loggerFactory;

  const CURRENT_VERSION = 'v1';

  public function __construct(
    ContainerInterface $container,
    AccessControl $accessControl,
    ErrorHandler $errorHandler,
    SettingsController $settings,
    LoggerFactory $loggerFactory,
    WPFunctions $wp
  ) {
    $this->container = $container;
    $this->accessControl = $accessControl;
    $this->errorHandler = $errorHandler;
    $this->settings = $settings;
    $this->wp = $wp;
    foreach ($this->availableApiVersions as $availableApiVersion) {
      $this->addEndpointNamespace(
        sprintf('%s\%s', __NAMESPACE__, $availableApiVersion),
        $availableApiVersion
      );
    }
    $this->loggerFactory = $loggerFactory;
  }

  public function init() {
     // admin security token and API version
    WPFunctions::get()->addAction(
      'admin_head',
      [$this, 'setTokenAndAPIVersion']
    );

    // ajax (logged in users)
    WPFunctions::get()->addAction(
      'wp_ajax_mailpoet',
      [$this, 'setupAjax']
    );

    // ajax (logged out users)
    WPFunctions::get()->addAction(
      'wp_ajax_nopriv_mailpoet',
      [$this, 'setupAjax']
    );

    // nonce refreshing via heartbeats
    WPFunctions::get()->addAction(
      'wp_refresh_nonces',
      [$this, 'addTokenToHeartbeatResponse']
    );
  }

  public function setupAjax() {
    $this->wp->doAction('mailpoet_api_setup', [$this]);

    if (isset($_POST['api_version'])) {
      $this->setRequestData($_POST, Endpoint::TYPE_POST);
    } else {
      $this->setRequestData($_GET, Endpoint::TYPE_GET);
    }

    $ignoreToken = (
      $this->settings->get('captcha.type') != CaptchaConstants::TYPE_DISABLED &&
      $this->requestEndpoint === 'subscribers' &&
      $this->requestMethod === 'subscribe'
    );

    if (!$ignoreToken && $this->wp->wpVerifyNonce($this->requestToken, 'mailpoet_token') === false) {
      $errorMessage = __("Sorry, but we couldn't connect to the MailPoet server. Please refresh the web page and try again.", 'mailpoet');
      $errorResponse = $this->createErrorResponse(Error::UNAUTHORIZED, $errorMessage, Response::STATUS_UNAUTHORIZED);
      return $errorResponse->send();
    }

    $response = $this->processRoute();
    $response->send();
  }

  public function setRequestData($data, $requestType) {
    $this->requestApiVersion = !empty($data['api_version']) ? $data['api_version'] : false;

    $this->requestEndpoint = isset($data['endpoint'])
      ? Helpers::underscoreToCamelCase(trim($data['endpoint']))
      : null;

    // JS part of /wp-admin/customize.php does not like a 'method' field in a form widget
    $methodParamName = isset($data['mailpoet_method']) ? 'mailpoet_method' : 'method';
    $this->requestMethod = isset($data[$methodParamName])
      ? Helpers::underscoreToCamelCase(trim($data[$methodParamName]))
      : null;
    $this->requestType = $requestType;

    $this->requestToken = isset($data['token'])
      ? trim($data['token'])
      : null;

    if (!$this->requestEndpoint || !$this->requestMethod || !$this->requestApiVersion) {
      $errorMessage = __('Invalid API request.', 'mailpoet');
      $errorResponse = $this->createErrorResponse(Error::BAD_REQUEST, $errorMessage, Response::STATUS_BAD_REQUEST);
      return $errorResponse;
    } else if (!empty($this->endpointNamespaces[$this->requestApiVersion])) {
      foreach ($this->endpointNamespaces[$this->requestApiVersion] as $namespace) {
        $endpointClass = sprintf(
          '%s\%s',
          $namespace,
          ucfirst($this->requestEndpoint)
        );
        if ($this->container->has($endpointClass)) {
          $this->requestEndpointClass = $endpointClass;
          break;
        }
      }
      $this->requestData = isset($data['data'])
        ? WPFunctions::get()->stripslashesDeep($data['data'])
        : [];

      // remove reserved keywords from data
      if (is_array($this->requestData) && !empty($this->requestData)) {
        // filter out reserved keywords from data
        $reservedKeywords = [
          'token',
          'endpoint',
          'method',
          'api_version',
          'mailpoet_method', // alias of 'method'
          'mailpoet_redirect',
        ];
        $this->requestData = array_diff_key(
          $this->requestData,
          array_flip($reservedKeywords)
        );
      }
    }
  }

  public function processRoute() {
    try {
      if (
        empty($this->requestEndpointClass) ||
        !$this->container->has($this->requestEndpointClass)
      ) {
        throw new \Exception(__('Invalid API endpoint.', 'mailpoet'));
      }

      $endpoint = $this->container->get($this->requestEndpointClass);
      if (!method_exists($endpoint, $this->requestMethod)) {
        throw new \Exception(__('Invalid API endpoint method.', 'mailpoet'));
      }

      if (!$endpoint->isMethodAllowed($this->requestMethod, $this->requestType)) {
        throw new \Exception(__('HTTP request method not allowed.', 'mailpoet'));
      }

      if (
        class_exists(Debugger::class)
        && class_exists(DIPanel::class)
        && class_exists(ApiPanel::class)
      ) {
        ApiPanel::init($endpoint, $this->requestMethod, $this->requestData);
        DIPanel::init();
      }

      // check the accessibility of the requested endpoint's action
      // by default, an endpoint's action is considered "private"
      if (!$this->validatePermissions($this->requestMethod, $endpoint->permissions)) {
        $errorMessage = __('You do not have the required permissions.', 'mailpoet');
        $errorResponse = $this->createErrorResponse(Error::FORBIDDEN, $errorMessage, Response::STATUS_FORBIDDEN);
        return $errorResponse;
      }
      $response = $endpoint->{$this->requestMethod}($this->requestData);
      return $response;
    } catch (Exception $e) {
      $this->logError($e);
      return $this->errorHandler->convertToResponse($e);
    } catch (Throwable $e) {
      if (class_exists(Debugger::class) && Debugger::$logDirectory) {
        Debugger::log($e, ILogger::EXCEPTION);
      }
      $this->logError($e);
      $errorMessage = $e->getMessage();
      $errorResponse = $this->createErrorResponse(Error::BAD_REQUEST, $errorMessage, Response::STATUS_BAD_REQUEST);
      return $errorResponse;
    }
  }

  public function validatePermissions($requestMethod, $permissions) {
    // validate method permission if defined, otherwise validate global permission
    return(!empty($permissions['methods'][$requestMethod])) ?
      $this->accessControl->validatePermission($permissions['methods'][$requestMethod]) :
      $this->accessControl->validatePermission($permissions['global']);
  }

  public function setTokenAndAPIVersion() {
    echo sprintf(
      '<script type="text/javascript">' .
      'var mailpoet_token = "%s";' .
      'var mailpoet_api_version = "%s";' .
      '</script>',
      esc_js($this->wp->wpCreateNonce('mailpoet_token')),
      esc_js(self::CURRENT_VERSION)
    );
  }

  public function addTokenToHeartbeatResponse($response) {
    $response['mailpoet_token'] = $this->wp->wpCreateNonce('mailpoet_token');
    return $response;
  }

  public function addEndpointNamespace($namespace, $version) {
    if (!empty($this->endpointNamespaces[$version][$namespace])) return;
    $this->endpointNamespaces[$version][] = $namespace;
  }

  public function getEndpointNamespaces() {
    return $this->endpointNamespaces;
  }

  public function getRequestedEndpointClass() {
    return $this->requestEndpointClass;
  }

  public function getRequestedAPIVersion() {
    return $this->requestApiVersion;
  }

  public function createErrorResponse($errorType, $errorMessage, $responseStatus) {
    $errorResponse = new ErrorResponse(
      [
        $errorType => $errorMessage,
      ],
      [],
      $responseStatus
    );
    return $errorResponse;
  }

  private function logError(Throwable $e): void {
    // logging to the php log
    if (function_exists('error_log')) {
      error_log((string)$e); // phpcs:ignore Squiz.PHP.DiscouragedFunctions
    }
    // logging to the MailPoet table
    $this->loggerFactory->getLogger(LoggerFactory::TOPIC_API)->warning($e->getMessage(), [
      'requestMethod' => $this->requestMethod,
      'requestEndpoint' => $this->requestEndpoint,
      'exceptionMessage' => $e->getMessage(),
      'exceptionTrace' => $e->getTrace(),
    ]);
  }
}
