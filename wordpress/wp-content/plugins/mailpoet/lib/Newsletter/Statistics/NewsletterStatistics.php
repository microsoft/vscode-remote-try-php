<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter\Statistics;

if (!defined('ABSPATH')) exit;


class NewsletterStatistics {

  /** @var int */
  private $clickCount;

  /** @var int */
  private $openCount;

  /** @var int */
  private $machineOpenCount;

  /** @var int */
  private $unsubscribeCount;

  /** @var int */
  private $bounceCount;

  /** @var int */
  private $totalSentCount;

  /** @var WooCommerceRevenue|null */
  private $wooCommerceRevenue;

  public function __construct(
    $clickCount,
    $openCount,
    $unsubscribeCount,
    $bounceCount,
    $totalSentCount,
    $wooCommerceRevenue
  ) {
    $this->clickCount = $clickCount;
    $this->openCount = $openCount;
    $this->unsubscribeCount = $unsubscribeCount;
    $this->bounceCount = $bounceCount;
    $this->totalSentCount = $totalSentCount;
    $this->wooCommerceRevenue = $wooCommerceRevenue;
  }

  public function getClickCount(): int {
    return $this->clickCount;
  }

  public function getOpenCount(): int {
    return $this->openCount;
  }

  public function getUnsubscribeCount(): int {
    return $this->unsubscribeCount;
  }

  public function getBounceCount(): int {
    return $this->bounceCount;
  }

  public function getTotalSentCount(): int {
    return $this->totalSentCount;
  }

  public function getWooCommerceRevenue(): ?WooCommerceRevenue {
    return $this->wooCommerceRevenue;
  }

  public function setMachineOpenCount(int $machineOpenCount): void {
    $this->machineOpenCount = $machineOpenCount;
  }

  public function getMachineOpenCount(): int {
    return $this->machineOpenCount;
  }

  public function asArray(): array {
    return [
      'clicked' => $this->clickCount,
      'opened' => $this->openCount,
      'machineOpened' => $this->machineOpenCount,
      'unsubscribed' => $this->unsubscribeCount,
      'bounced' => $this->bounceCount,
      'revenue' => empty($this->wooCommerceRevenue) ? null : $this->wooCommerceRevenue->asArray(),
    ];
  }
}
