<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Cron;

if (!defined('ABSPATH')) exit;


use MailPoet\Cron\Triggers\WordPress;
use MailPoet\Settings\SettingsController;
use MailPoet\WP\Functions as WPFunctions;
use Tracy\Debugger;

class DaemonHttpRunner {
  public $settingsDaemonData;
  public $timer;
  public $token;

  /** @var Daemon|null */
  private $daemon;

  /** @var CronHelper */
  private $cronHelper;

  /** @var SettingsController */
  private $settings;

  const PING_SUCCESS_RESPONSE = 'pong';

  /** @var WordPress */
  private $wordpressTrigger;

  public function __construct(
    Daemon $daemon = null,
    CronHelper $cronHelper,
    SettingsController $settings,
    WordPress $wordpressTrigger
  ) {
    $this->cronHelper = $cronHelper;
    $this->settingsDaemonData = $this->cronHelper->getDaemon();
    $this->token = $this->cronHelper->createToken();
    $this->timer = microtime(true);
    $this->daemon = $daemon;
    $this->settings = $settings;
    $this->wordpressTrigger = $wordpressTrigger;
  }

  public function ping() {
    // if Tracy enabled & called by 'MailPoet Cron' user agent, disable Tracy Bar
    // (happens in CronHelperTest because it's not a real integration test - calls other WP instance)
    $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ?
      sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT']))
      : null;
    if (class_exists(Debugger::class) && $userAgent === 'MailPoet Cron') {
      Debugger::$showBar = false;
    }
    $this->addCacheHeaders();
    $this->terminateRequest(self::PING_SUCCESS_RESPONSE);
  }

  public function run($requestData) {
    ignore_user_abort(true);
    if (strpos((string)@ini_get('disable_functions'), 'set_time_limit') === false) {
      set_time_limit(0);
    }
    $this->addCacheHeaders();
    if (!$requestData) {
      $error = __('Invalid or missing request data.', 'mailpoet');
    } else {
      if (!$this->settingsDaemonData) {
        $error = __('Daemon does not exist.', 'mailpoet');
      } else {
        if (
          !isset($requestData['token']) ||
          $requestData['token'] !== $this->settingsDaemonData['token']
        ) {
          $error = 'Invalid or missing token.';
        }
      }
    }
    if (!empty($error)) {
      return $this->abortWithError($error);
    }
    if ($this->daemon === null) {
      return $this->abortWithError(__('Daemon does not set correctly.', 'mailpoet'));
    }
    $this->settingsDaemonData['token'] = $this->token;
    $this->daemon->run($this->settingsDaemonData);
    // If we're using the WordPress trigger, check the conditions to stop cron if necessary
    $enableCronSelfDeactivation = WPFunctions::get()->applyFilters('mailpoet_cron_enable_self_deactivation', false);
    if (
      $enableCronSelfDeactivation
      && $this->isCronTriggerMethodWordPress()
      && !$this->checkWPTriggerExecutionRequirements()
    ) {
      $this->stopCron();
    } else {
      // if workers took less time to execute than the daemon execution limit,
      // pause daemon execution to ensure that daemon runs only once every X seconds
      $elapsedTime = microtime(true) - $this->timer;
      if ($elapsedTime < $this->cronHelper->getDaemonExecutionLimit()) {
        $this->pauseExecution((int)ceil($this->cronHelper->getDaemonExecutionLimit() - $elapsedTime));
      }
    }
    // after each execution, re-read daemon data in case it changed
    $settingsDaemonData = $this->cronHelper->getDaemon();
    if ($this->shouldTerminateExecution($settingsDaemonData)) {
      return $this->terminateRequest();
    }
    return $this->callSelf();
  }

  public function pauseExecution(int $pauseTime) {
    return sleep($pauseTime);
  }

  public function callSelf() {
    $this->cronHelper->accessDaemon($this->token);
    $this->terminateRequest();
  }

  public function abortWithError($message) {
    WPFunctions::get()->statusHeader(404, $message);
    exit;
  }

  public function terminateRequest($message = false) {
    echo esc_html($message);
    die();
  }

  public function isCronTriggerMethodWordPress() {
    return $this->settings->get(CronTrigger::SETTING_NAME . '.method') === CronTrigger::METHOD_WORDPRESS;
  }

  public function checkWPTriggerExecutionRequirements() {
    return $this->wordpressTrigger->checkExecutionRequirements();
  }

  public function stopCron() {
    return $this->wordpressTrigger->stop();
  }

  /**
   * @param array|null $settingsDaemonData
   *
   * @return bool
   */
  private function shouldTerminateExecution(array $settingsDaemonData = null) {
    return !$settingsDaemonData ||
       $settingsDaemonData['token'] !== $this->token ||
       (isset($settingsDaemonData['status']) && $settingsDaemonData['status'] !== CronHelper::DAEMON_STATUS_ACTIVE);
  }

  private function addCacheHeaders() {
    if (headers_sent()) {
      return;
    }
    // Common Cache Control header. Should be respected by cache proxies and CDNs.
    header('Cache-Control: no-cache');
    // Mark as blacklisted for SG Optimizer for sites hosted on SiteGround.
    header('X-Cache-Enabled: False');
    // Set caching header for LiteSpeed server.
    header('X-LiteSpeed-Cache-Control: no-cache');
  }
}
