<?php declare(strict_types = 1);

namespace MailPoet\WooCommerce\MultichannelMarketing;

if (!defined('ABSPATH')) exit;


class MPMarketingChannelController {

  /**
   * @var MPMarketingChannelDataController
   */
  private $channelDataController;

  public function __construct(
    MPMarketingChannelDataController $channelDataController
  ) {
    $this->channelDataController = $channelDataController;
  }

  public function registerMarketingChannel($registeredMarketingChannels): array {
    return array_merge($registeredMarketingChannels, [
      new MPMarketingChannel(
        $this->channelDataController
      ),
    ]);
  }
}
