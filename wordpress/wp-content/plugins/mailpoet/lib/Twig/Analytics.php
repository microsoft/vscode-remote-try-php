<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Twig;

if (!defined('ABSPATH')) exit;


use MailPoet\Analytics\Analytics as AnalyticsGenerator;
use MailPoet\DI\ContainerWrapper;
use MailPoet\InvalidStateException;
use MailPoetVendor\Twig\Extension\AbstractExtension;
use MailPoetVendor\Twig\TwigFunction;

class Analytics extends AbstractExtension {

  /** @var  AnalyticsGenerator */
  private $analytics;

  public function getFunctions() {
    return [
      new TwigFunction(
        'is_analytics_enabled',
        [$this, 'isEnabled'],
        ['is_safe' => ['all']]
      ),
      new TwigFunction(
        'get_analytics_public_id',
        [$this, 'getPublicId'],
        ['is_safe' => ['all']]
      ),
      new TwigFunction(
        'is_analytics_public_id_new',
        [$this, 'isPublicIdNew'],
        ['is_safe' => ['all']]
      ),
    ];
  }

  private function getAnalytics() {

    if ($this->analytics === null) {
      $this->analytics = ContainerWrapper::getInstance()->get(AnalyticsGenerator::class);
    }
    if (!$this->analytics instanceof AnalyticsGenerator) {
      throw new InvalidStateException('AnalyticsGenerator service was not registered!');
    }
    return $this->analytics;
  }

  public function isEnabled() {
    return $this->getAnalytics()->isEnabled();
  }

  public function getPublicId() {
    return $this->getAnalytics()->getPublicId();
  }

  public function isPublicIdNew() {
    return $this->getAnalytics()->isPublicIdNew();
  }
}
