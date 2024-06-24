<?php declare (strict_types = 1);

namespace MailPoet\Logging;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\Env;
use MailPoetVendor\Monolog\Processor\ProcessorInterface;

class PluginVersionProcessor implements ProcessorInterface {
  public function __invoke(array $record): array {
    $record['extra']['free_plugin_version'] = Env::$version;
    $record['extra']['premium_plugin_version'] = defined('MAILPOET_PREMIUM_VERSION') ? MAILPOET_PREMIUM_VERSION : 'premium not installed';
    return $record;
  }
}
