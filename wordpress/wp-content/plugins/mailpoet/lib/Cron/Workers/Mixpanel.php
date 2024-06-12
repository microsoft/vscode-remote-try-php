<?php declare(strict_types = 1);

namespace MailPoet\Cron\Workers;

if (!defined('ABSPATH')) exit;


use MailPoet\Analytics\Analytics;
use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\WP\Functions;
use Mixpanel as MixpanelLibrary;

class Mixpanel extends SimpleWorker {

  const PRODUCTION_PROJECT_ID = '8cce373b255e5a76fb22d57b85db0c92';

  /** @var Analytics */
  private $analytics;

  const TASK_TYPE = 'mixpanel';

  /** @var MixpanelLibrary */
  private $mixpanel;

  public function __construct(
    Analytics $analytics,
    Functions $wp
  ) {
    parent::__construct($wp);
    $this->analytics = $analytics;
    $this->mixpanel = MixpanelLibrary::getInstance(self::PRODUCTION_PROJECT_ID);
    $this->mixpanel->register('Platform', 'Plugin');
  }

  public function processTaskStrategy(ScheduledTaskEntity $task, $timer) {
    return $this->maybeReportAnalyticsToMixpanel();
  }

  public function maybeReportAnalyticsToMixpanel(): bool {
    if (!$this->analytics->shouldSend()) {
      return true;
    }
    return $this->reportAnalyticsToMixpanel();
  }

  public function reportAnalyticsToMixpanel(): bool {
    $publicId = $this->analytics->getPublicId();

    if (strlen($publicId) < 1) {
      return true;
    }

    $data = $this->analytics->getAnalyticsData();

    $this->mixpanel->identify($publicId);
    $this->mixpanel->people->set($publicId, $data);
    $this->mixpanel->track('User Properties', $data);

    $this->analytics->recordDataSent();

    return true;
  }

  public function getNextRunDate() {
    return $this->analytics->getNextSendDate()->addMinutes(rand(0, 59));
  }
}
