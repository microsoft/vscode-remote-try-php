<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Cron\Workers;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Settings\SettingsController;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Carbon\Carbon;

class Beamer extends SimpleWorker {
  const TASK_TYPE = 'beamer';
  const API_URL = 'https://api.getbeamer.com/v0';
  const API_KEY = 'b_neUUX8kIYVEYZqQzSnwhmVggVLA6lT+GzDQOW7hrP38=';

  /** @var SettingsController */
  private $settings;

  public function __construct(
    SettingsController $settings,
    WPFunctions $wp
  ) {
    parent::__construct($wp);
    $this->settings = $settings;
  }

  public function processTaskStrategy(ScheduledTaskEntity $task, $timer) {
    if (!$this->isBeamerEnabled()) {
      return false;
    }
    return $this->setLastAnnouncementDate();
  }

  private function isBeamerEnabled(): bool {
    return $this->settings->get('3rd_party_libs.enabled') === '1';
  }

  public function setLastAnnouncementDate() {
    $response = $this->wp->wpRemoteGet(self::API_URL . '/posts?published=true&maxResults=1', [
      'headers' => [
        'Beamer-Api-Key' => self::API_KEY,
      ],
    ]);
    $posts = $this->wp->wpRemoteRetrieveBody($response);
    if (empty($posts)) return false;
    $posts = json_decode($posts);
    /** @var \stdClass[] $posts */
    if (empty($posts) || empty($posts[0]->date)) return false;
    $this->settings->set('last_announcement_date', Carbon::createFromTimeString($posts[0]->date)->getTimestamp());
    return true;
  }

  public function getNextRunDate() {
    // once every two weeks on a random day of the week, random time of the day
    $date = Carbon::createFromTimestamp($this->wp->currentTime('timestamp'));
    return $date
      ->next(Carbon::MONDAY)
      ->startOfDay()
      ->addDays(rand(7, 13))
      ->addHours(rand(0, 23))
      ->addMinutes(rand(0, 59));
  }
}
