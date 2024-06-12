<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Cron\Workers\SendingQueue;

if (!defined('ABSPATH')) exit;


use MailPoet\Logging\LoggerFactory;
use MailPoet\Settings\SettingsController;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Monolog\Logger;

class SendingThrottlingHandler {
  public const BATCH_SIZE = 20;
  public const SETTINGS_KEY = 'mta_throttling';
  public const SUCCESS_THRESHOLD_TO_INCREASE = 10;

  /** @var Logger */
  private $logger;

  /** @var SettingsController */
  private $settings;

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    LoggerFactory $loggerFactory,
    SettingsController $settings,
    WPFunctions $wp
  ) {
    $this->logger = $loggerFactory->getLogger(LoggerFactory::TOPIC_SENDING);
    $this->settings = $settings;
    $this->wp = $wp;
  }

  public function getBatchSize(): int {
    $throttlingSettings = $this->loadSettings();
    if (isset($throttlingSettings['batch_size'])) {
      return $throttlingSettings['batch_size'];
    }
    return $this->getMaxBatchSize();
  }

  private function getMaxBatchSize(): int {
    return $this->wp->applyFilters('mailpoet_cron_worker_sending_queue_batch_size', self::BATCH_SIZE);
  }

  public function throttleBatchSize(): int {
    $batchSize = $this->getBatchSize();
    if ($batchSize > 1) {
      $batchSize = (int)ceil($this->getBatchSize() / 2);
      $throttlingSettings = $this->loadSettings();
      $throttlingSettings['batch_size'] = $batchSize;
      unset($throttlingSettings['success_count']);
      $this->logger->error("MailPoet throttling: decrease batch_size to: {$batchSize}");
      $this->saveSettings($throttlingSettings);
    }

    return $batchSize;
  }

  public function processSuccess(): void {
    $throttlingSettings = $this->loadSettings();
    if (!isset($throttlingSettings['batch_size'])) {
      return;
    }
    $throttlingSettings['success_count'] = isset($throttlingSettings['success_count']) ? ++$throttlingSettings['success_count'] : 1;
    $this->logger->info("MailPoet throttling: increase success_count to: {$throttlingSettings['success_count']}");
    if ($throttlingSettings['success_count'] >= self::SUCCESS_THRESHOLD_TO_INCREASE) {
      unset($throttlingSettings['success_count']);
      $throttlingSettings['batch_size'] = min($this->getMaxBatchSize(), $throttlingSettings['batch_size'] * 2);
      $this->logger->info("MailPoet throttling: increase batch_size to: {$throttlingSettings['batch_size']}");
      if ($this->getMaxBatchSize() === $throttlingSettings['batch_size']) {
        unset($throttlingSettings['batch_size']);
      }
    }
    $this->saveSettings($throttlingSettings);
  }

  private function loadSettings(): ?array {
    return $this->settings->get(self::SETTINGS_KEY);
  }

  private function saveSettings(array $settings): void {
    $this->settings->set(self::SETTINGS_KEY, $settings);
  }
}
