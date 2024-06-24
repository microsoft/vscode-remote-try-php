<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Settings;

if (!defined('ABSPATH')) exit;


use MailPoet\Cron\Workers\InactiveSubscribers;
use MailPoet\Cron\Workers\WooCommerceSync;
use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Mailer\Mailer;
use MailPoet\Newsletter\Sending\ScheduledTasksRepository;
use MailPoet\Services\Bridge;
use MailPoet\Services\SubscribersCountReporter;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Carbon\Carbon;

class SettingsChangeHandler {

  /** @var ScheduledTasksRepository */
  private $scheduledTasksRepository;

  /** @var SettingsController */
  private $settingsController;

  /** @var Bridge */
  private $bridge;

  /** @var SubscribersCountReporter */
  private $subscribersCountReporter;

  public function __construct(
    ScheduledTasksRepository $scheduledTasksRepository,
    SettingsController $settingsController,
    Bridge $bridge,
    SubscribersCountReporter $subscribersCountReporter
  ) {
    $this->scheduledTasksRepository = $scheduledTasksRepository;
    $this->settingsController = $settingsController;
    $this->bridge = $bridge;
    $this->subscribersCountReporter = $subscribersCountReporter;
  }

  public function onSubscribeOldWoocommerceCustomersChange(): void {
    $task = $this->scheduledTasksRepository->findOneBy([
      'type' => WooCommerceSync::TASK_TYPE,
      'status' => ScheduledTaskEntity::STATUS_SCHEDULED,
      'deletedAt' => null,
    ], ['createdAt' => 'DESC']);
    if (!($task instanceof ScheduledTaskEntity)) {
      $task = $this->createScheduledTask(WooCommerceSync::TASK_TYPE);
    }
    $datetime = Carbon::createFromTimestamp((int)WPFunctions::get()->currentTime('timestamp'));
    $task->setScheduledAt($datetime->subMinute());
    $this->scheduledTasksRepository->persist($task);
    $this->scheduledTasksRepository->flush();
  }

  public function onInactiveSubscribersIntervalChange(): void {
    $task = $this->scheduledTasksRepository->findOneBy([
      'type' => InactiveSubscribers::TASK_TYPE,
      'status' => ScheduledTaskEntity::STATUS_SCHEDULED,
      'deletedAt' => null,
    ], ['createdAt' => 'DESC']);
    if (!($task instanceof ScheduledTaskEntity)) {
      $task = $this->createScheduledTask(InactiveSubscribers::TASK_TYPE);
    }
    $datetime = Carbon::createFromTimestamp((int)WPFunctions::get()->currentTime('timestamp'));
    $task->setScheduledAt($datetime->subMinute());
    $this->scheduledTasksRepository->persist($task);
    $this->scheduledTasksRepository->flush();
  }

  public function onMSSActivate($newSettings) {
    // see mailpoet/assets/js/src/wizard/create_sender_settings.jsx:freeAddress
    $httpHost = isset($_SERVER['HTTP_HOST']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_HOST'])) : '';
    $domain = str_replace('www.', '', $httpHost);
    if (
      isset($newSettings['sender']['address'])
      && !empty($newSettings['reply_to']['address'])
      && ($newSettings['sender']['address'] === ('wordpress@' . $domain))
    ) {
      $sender = [
        'name' => $newSettings['reply_to']['name'] ?? '',
        'address' => $newSettings['reply_to']['address'],
      ];
      $this->settingsController->set('sender', $sender);
      $this->settingsController->set('reply_to', null);
    }
  }

  private function createScheduledTask(string $type): ScheduledTaskEntity {
    $task = new ScheduledTaskEntity();
    $task->setType($type);
    $task->setStatus(ScheduledTaskEntity::STATUS_SCHEDULED);
    return $task;
  }

  public function updateApiKeyState($settings) {
    $apiKey = $settings[Mailer::MAILER_CONFIG_SETTING_NAME]['mailpoet_api_key'] ?? null;
    $premiumKey = $settings['premium']['premium_key'] ?? null;
    if (!empty($apiKey)) {
      $apiKeyState = $this->bridge->checkMSSKey($apiKey);
      $this->bridge->storeMSSKeyAndState($apiKey, $apiKeyState);
    }
    if (!empty($premiumKey)) {
      $premiumState = $this->bridge->checkPremiumKey($premiumKey);
      $this->bridge->storePremiumKeyAndState($premiumKey, $premiumState);
    }
    if ($apiKey && !empty($apiKeyState) && in_array($apiKeyState['state'], [Bridge::KEY_VALID, Bridge::KEY_VALID_UNDERPRIVILEGED], true)) {
      return $this->subscribersCountReporter->report($apiKey);
    }
    if ($premiumKey && !empty($premiumState) && in_array($premiumState['state'], [Bridge::KEY_VALID, Bridge::KEY_VALID_UNDERPRIVILEGED], true)) {
      return $this->subscribersCountReporter->report($premiumKey);
    }
  }
}
