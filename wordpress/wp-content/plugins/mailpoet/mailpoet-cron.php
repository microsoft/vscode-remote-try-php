<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

/**
 * This file may be used by MailPoet to run a cron daemon that sends emails and perform other periodic tasks. It is
 * disabled by default, and it only works on the command line (it is not accessible via the web browser in any way).
 * It is used by a small subset of users that are not able to use the recommended method to send MailPoet emails.
 * This is the case for sites that have their WP-Cron jobs broken by 3rd party plugins during cron processing, which
 * prevents MailPoet from functioning. To work around this problem, this script loads only MailPoet and WordPress,
 * without any other plugins. That is why it needs to require wp-load.php directly.
 */

ini_set("display_errors", "1");
error_reporting(E_ALL);

if (!isset($argv[1]) || !$argv[1]) {
  echo 'You need to pass a WordPress root as an argument.';
  exit(1);
}

$wpLoadFile = $argv[1] . '/wp-load.php';
if (!file_exists($wpLoadFile)) {
  echo 'WordPress root argument is not valid.';
  exit(1);
}

if (!defined('ABSPATH')) {
  /** Set up WordPress environment */
  require_once($wpLoadFile);
}

if (!is_plugin_active('mailpoet/mailpoet.php')) {
  echo 'MailPoet plugin is not active';
  exit(1);
}

if (wp_is_maintenance_mode()) {
  echo 'WordPress site in maintenance mode.';
  exit(1);
}

// Check for minimum supported PHP version
if (version_compare(phpversion(), '7.4.0', '<')) {
  echo 'MailPoet requires PHP version 7.4 or newer (version 8.1 recommended).';
  exit(1);
}

if (strpos(@ini_get('disable_functions'), 'set_time_limit') === false) {
  set_time_limit(0);
}

$container = \MailPoet\DI\ContainerWrapper::getInstance(WP_DEBUG);

// Check if Linux Cron method is set in plugin settings
$settings = $container->get(\MailPoet\Settings\SettingsController::class);
if ($settings->get('cron_trigger.method') !== \MailPoet\Cron\CronTrigger::METHOD_LINUX_CRON) {
  echo 'You attempt to run MailPoets "Server side cron (Linux cron)."' . PHP_EOL .
    'But in your settings, you have defined a different method for the Newsletter task scheduler.' . PHP_EOL .
    'If you want to use the "Server side cron", please go to MailPoet > Settings > Advanced and choose the correct Newsletter task scheduler.' . PHP_EOL;
  exit(1);
}

// Run Cron Daemon
$cronHelper = $container->get(\MailPoet\Cron\CronHelper::class);
$data = $cronHelper->createDaemon(null);
$trigger = $container->get(\MailPoet\Cron\Daemon::class);
$trigger->run($data);
