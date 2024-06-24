<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Subscribers\Statistics;

if (!defined('ABSPATH')) exit;


use MailPoet\Newsletter\Statistics\WooCommerceRevenue;

class SubscriberStatistics {

  /** @var int */
  private $clickCount;

  /** @var int */
  private $openCount;

  /** @var int */
  private $machineOpenCount;

  /** @var int */
  private $totalSentCount;

  /** @var WooCommerceRevenue|null */
  private $wooCommerceRevenue;

  public function __construct(
    $clickCount,
    $openCount,
    $machineOpenCount,
    $totalSentCount,
    $wooCommerceRevenue = null
  ) {
    $this->clickCount = $clickCount;
    $this->openCount = $openCount;
    $this->machineOpenCount = $machineOpenCount;
    $this->totalSentCount = $totalSentCount;
    $this->wooCommerceRevenue = $wooCommerceRevenue;
  }

  public function getClickCount(): int {
    return $this->clickCount;
  }

  public function getOpenCount(): int {
    return $this->openCount;
  }

  public function getMachineOpenCount(): int {
    return $this->machineOpenCount;
  }

  public function getTotalSentCount(): int {
    return $this->totalSentCount;
  }

  /**
   * @return WooCommerceRevenue|null
   */
  public function getWooCommerceRevenue() {
    return $this->wooCommerceRevenue;
  }
}
