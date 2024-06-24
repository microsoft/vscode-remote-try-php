<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Cron;

if (!defined('ABSPATH')) exit;


use MailPoet\Router\Endpoints\CronDaemon as CronDaemonEndpoint;
use MailPoet\Router\Router;
use MailPoet\Settings\SettingsController;
use MailPoet\Util\Security;
use MailPoet\WP\Functions as WPFunctions;

class CronHelper {
  const DAEMON_EXECUTION_LIMIT = 20; // seconds
  const DAEMON_REQUEST_TIMEOUT = 5; // seconds
  const DAEMON_SETTING = 'cron_daemon';
  const DAEMON_STATUS_ACTIVE = 'active';
  const DAEMON_STATUS_INACTIVE = 'inactive';

  // Error codes
  const DAEMON_EXECUTION_LIMIT_REACHED = 1001;

  /** @var SettingsController */
  private $settings;

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    SettingsController $settings,
    WPFunctions $wp
  ) {
    $this->settings = $settings;
    $this->wp = $wp;
  }

  public function getDaemonExecutionLimit() {
    $limit = $this->wp->applyFilters('mailpoet_cron_get_execution_limit', self::DAEMON_EXECUTION_LIMIT);
    return $limit;
  }

  public function getDaemonExecutionTimeout() {
    $limit = $this->getDaemonExecutionLimit();
    $timeout = $limit * 1.75;
    return $this->wp->applyFilters('mailpoet_cron_get_execution_timeout', $timeout);
  }

  public function createDaemon($token) {
    $daemon = [
      'token' => $token,
      'status' => self::DAEMON_STATUS_ACTIVE,
      'run_accessed_at' => null,
      'run_started_at' => null,
      'run_completed_at' => null,
      'last_error' => null,
      'last_error_date' => null,
    ];
    $this->saveDaemon($daemon);
    return $daemon;
  }

  public function restartDaemon($token) {
    return $this->createDaemon($token);
  }

  public function getDaemon() {
    return $this->settings->fetch(self::DAEMON_SETTING);
  }

  public function saveDaemonLastError($error) {
    $daemon = $this->getDaemon();
    if ($daemon) {
      $daemon['last_error'] = $error;
      $daemon['last_error_date'] = time();
      $this->saveDaemon($daemon);
    }
  }

  public function saveDaemonRunCompleted($runCompletedAt) {
    $daemon = $this->getDaemon();
    if ($daemon) {
      $daemon['run_completed_at'] = $runCompletedAt;
      $this->saveDaemon($daemon);
    }
  }

  public function saveDaemon($daemon) {
    $daemon['updated_at'] = time();
    $this->settings->set(
      self::DAEMON_SETTING,
      $daemon
    );
  }

  public function deactivateDaemon($daemon) {
    // We do not need to deactivate an inactive daemon
    if (isset($daemon['status']) && $daemon['status'] === self::DAEMON_STATUS_INACTIVE) {
      return;
    }
    $daemon['status'] = self::DAEMON_STATUS_INACTIVE;
    $this->settings->set(
      self::DAEMON_SETTING,
      $daemon
    );
  }

  public function createToken() {
    return Security::generateRandomString();
  }

  public function pingDaemon() {
    $url = $this->getCronUrl(
      CronDaemonEndpoint::ACTION_PING_RESPONSE
    );
    $result = $this->queryCronUrl($url);
    if (is_wp_error($result)) return $result->get_error_message();
    $response = $this->wp->wpRemoteRetrieveBody($result);
    $response = substr(trim($response), -strlen(DaemonHttpRunner::PING_SUCCESS_RESPONSE)) === DaemonHttpRunner::PING_SUCCESS_RESPONSE ?
      DaemonHttpRunner::PING_SUCCESS_RESPONSE :
      $response;
    return $response;
  }

  public function validatePingResponse($response) {
    return $response === DaemonHttpRunner::PING_SUCCESS_RESPONSE;
  }

  public function accessDaemon($token) {
    $data = ['token' => $token];
    $url = $this->getCronUrl(
      CronDaemonEndpoint::ACTION_RUN,
      $data
    );
    $daemon = $this->getDaemon();
    if (!$daemon) {
      throw new \LogicException('Daemon does not exist.');
    }
    $daemon['run_accessed_at'] = time();
    $this->saveDaemon($daemon);
    $result = $this->queryCronUrl($url);
    return $this->wp->wpRemoteRetrieveBody($result);
  }

  /**
   * @return bool|null
   */
  public function isDaemonAccessible() {
    $daemon = $this->getDaemon();
    if (!$daemon || !isset($daemon['run_accessed_at'])) {
      return null;
    }
    if ($daemon['run_accessed_at'] <= (int)$daemon['run_started_at']) {
      return true;
    }
    if (
      $daemon['run_accessed_at'] + self::DAEMON_REQUEST_TIMEOUT < time() &&
      $daemon['run_accessed_at'] > (int)$daemon['run_started_at']
    ) {
        return false;
    }
    return null;
  }

  public function queryCronUrl($url) {
    $args = $this->wp->applyFilters(
      'mailpoet_cron_request_args',
      [
        'blocking' => true,
        'sslverify' => false,
        'timeout' => self::DAEMON_REQUEST_TIMEOUT,
        'user-agent' => 'MailPoet Cron',
      ]
    );
    return $this->wp->wpRemotePost($url, $args);
  }

  public function getCronUrl($action, $data = false) {
    $url = Router::buildRequest(
      CronDaemonEndpoint::ENDPOINT,
      $action,
      $data
    );
    $customCronUrl = $this->wp->applyFilters('mailpoet_cron_request_url', $url);
    return ($customCronUrl === $url) ?
      str_replace(home_url(), $this->getSiteUrl(), $url) :
      $customCronUrl;
  }

  public function getSiteUrl($siteUrl = false) {
    // additional check for some sites running inside a virtual machine or behind
    // proxy where there could be different ports (e.g., host:8080 => guest:80)
    if (!$siteUrl) {
      $siteUrl = defined('MAILPOET_CRON_SITE_URL') ? MAILPOET_CRON_SITE_URL : $this->wp->homeUrl();
    }
    $parsedUrl = parse_url($siteUrl);
    if (!is_array($parsedUrl)) {
      throw new \Exception(__('Site URL is unreachable.', 'mailpoet'));
    }

    $callScheme = '';
    if (isset($parsedUrl['scheme']) && ($parsedUrl['scheme'] === 'https')) {
      $callScheme = 'ssl://';
    }

    // 1. if site URL does not contain a port, return the URL
    if (!isset($parsedUrl['port']) || empty($parsedUrl['port'])) return $siteUrl;
    // 2. if site URL contains valid port, try connecting to it
    $urlHost = $parsedUrl['host'] ?? '';
    $fp = @fsockopen($callScheme . $urlHost, $parsedUrl['port'], $errno, $errstr, 1);
    if ($fp) return $siteUrl;
    // 3. if connection fails, attempt to connect the standard port derived from URL
    // schema
    $urlScheme = $parsedUrl['scheme'] ?? '';
    $port = (strtolower($urlScheme) === 'http') ? 80 : 443;
    $fp = @fsockopen($callScheme . $urlHost, $port, $errno, $errstr, 1);
    if ($fp) return sprintf('%s://%s', $urlScheme, $urlHost);
    // 4. throw an error if all connection attempts failed
    throw new \Exception(__('Site URL is unreachable.', 'mailpoet'));
  }

  public function enforceExecutionLimit($timer) {
    $elapsedTime = microtime(true) - $timer;
    $limit = $this->getDaemonExecutionLimit();
    if ($elapsedTime >= $limit) {
      throw new \Exception(
        sprintf(
          // translators: %1$d is the number of seconds the daemon is allowed to run, %2$d is how many more seconds the daemon did run.
          __(
            'The maximum execution time of %1$d seconds was exceeded by %2$d seconds. This task will resume during the next run.',
            'mailpoet'
          ),
          (int)round($limit),
          (int)round($elapsedTime - $limit)
        ),
        self::DAEMON_EXECUTION_LIMIT_REACHED
      );
    }
  }
}
