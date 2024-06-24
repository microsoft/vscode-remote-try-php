<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Segments;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\DynamicSegmentFilterData;
use MailPoet\Entities\DynamicSegmentFilterEntity;
use MailPoet\Entities\SegmentEntity;
use MailPoet\Segments\DynamicSegments\Filters\SubscriberTag;
use MailPoet\Util\License\Features\Subscribers as SubscribersFeature;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Doctrine\Common\Collections\Collection;

class SegmentDependencyValidator {
  private const MAILPOET_PREMIUM_PLUGIN = [
    'id' => 'mailpoet-premium/mailpoet-premium.php',
    'name' => 'MailPoet Premium',
  ];

  private const WOOCOMMERCE_PLUGIN = [
    'id' => 'woocommerce/woocommerce.php',
    'name' => 'WooCommerce',
  ];

  private const WOOCOMMERCE_MEMBERSHIPS_PLUGIN = [
    'id' => 'woocommerce-memberships/woocommerce-memberships.php',
    'name' => 'WooCommerce Memberships',
  ];

  private const WOOCOMMERCE_SUBSCRIPTIONS_PLUGIN = [
    'id' => 'woocommerce-subscriptions/woocommerce-subscriptions.php',
    'name' => 'WooCommerce Subscriptions',
  ];

  private const REQUIRED_PLUGINS_BY_TYPE = [
    DynamicSegmentFilterData::TYPE_WOOCOMMERCE => [
      self::WOOCOMMERCE_PLUGIN,
    ],
    DynamicSegmentFilterData::TYPE_WOOCOMMERCE_MEMBERSHIP => [
      self::WOOCOMMERCE_MEMBERSHIPS_PLUGIN,
      self::WOOCOMMERCE_PLUGIN,
    ],
    DynamicSegmentFilterData::TYPE_WOOCOMMERCE_SUBSCRIPTION => [
      self::WOOCOMMERCE_SUBSCRIPTIONS_PLUGIN,
      self::WOOCOMMERCE_PLUGIN,
    ],
  ];

  private const REQUIRED_PLUGINS_BY_TYPE_AND_ACTION = [
    DynamicSegmentFilterData::TYPE_USER_ROLE => [
      SubscriberTag::TYPE => [
        self::MAILPOET_PREMIUM_PLUGIN,
      ],
    ],
  ];

  /** @var SubscribersFeature */
  private $subscribersFeature;

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    SubscribersFeature $subscribersFeature,
    WPFunctions $wp
  ) {
    $this->subscribersFeature = $subscribersFeature;
    $this->wp = $wp;
  }

  /**
   * @return string[]
   */
  public function getMissingPluginsBySegment(SegmentEntity $segment): array {
    $dynamicFilters = $segment->getDynamicFilters();
    $missingPluginNames = $this->getMissingPluginsByAllFilters($dynamicFilters);
    foreach ($dynamicFilters as $dynamicFilter) {
      $missingPlugins = $this->getMissingPluginsByFilter($dynamicFilter);
      if (!$missingPlugins) {
        continue;
      }
      foreach ($missingPlugins as $plugin) {
        $missingPluginNames[] = $plugin['name'];
      }
    }
    return array_unique($missingPluginNames);
  }

  /**
   * @param Collection<int, DynamicSegmentFilterEntity> $dynamicFilters
   */
  public function getMissingPluginsByAllFilters(Collection $dynamicFilters): array {
    $missingPluginNames = [];
    if (
      count($dynamicFilters) > 1
      && (!$this->wp->isPluginActive(self::MAILPOET_PREMIUM_PLUGIN['id'])
        || !$this->subscribersFeature->hasValidPremiumKey()
        || $this->subscribersFeature->check())
    ) {
      $missingPluginNames[] = self::MAILPOET_PREMIUM_PLUGIN['name'];
    }
    return $missingPluginNames;
  }

  public function getMissingPluginsByFilter(DynamicSegmentFilterEntity $dynamicSegmentFilter): array {
    $config = $this->getRequiredPluginsConfig(
      $dynamicSegmentFilter->getFilterData()->getFilterType() ?? '',
      $dynamicSegmentFilter->getFilterData()->getAction()
    );
    return $this->getMissingPlugins($config);
  }

  public function canUseDynamicFilterType(string $type): bool {
    $config = $this->getRequiredPluginsConfig($type);
    return empty($this->getMissingPlugins($config));
  }

  private function getRequiredPluginsConfig(string $type, ?string $action = null): array {
    $requiredPlugins = [];
    if (isset(self::REQUIRED_PLUGINS_BY_TYPE[$type])) {
      $requiredPlugins = self::REQUIRED_PLUGINS_BY_TYPE[$type];
    }
    if (isset(self::REQUIRED_PLUGINS_BY_TYPE_AND_ACTION[$type][$action])) {
      $requiredPlugins = array_merge($requiredPlugins, self::REQUIRED_PLUGINS_BY_TYPE_AND_ACTION[$type][$action]);
    }
    return $requiredPlugins;
  }

  private function getMissingPlugins(array $config): array {
    $missingPlugins = [];
    foreach ($config as $requiredPlugin) {
      if (isset($requiredPlugin['id']) && !$this->wp->isPluginActive($requiredPlugin['id'])) {
        $missingPlugins[] = $requiredPlugin;
      }
    }
    return $missingPlugins;
  }

  public function getCustomErrorMessage($missingPlugin) {
    if (
      $missingPlugin === self::MAILPOET_PREMIUM_PLUGIN['name']
      && $this->wp->isPluginActive(self::MAILPOET_PREMIUM_PLUGIN['id'])
      && (!$this->subscribersFeature->hasValidPremiumKey() || $this->subscribersFeature->check())
    ) {
      return [
        'message' => __('Your current MailPoet plan does not support advanced segments. Please [link]upgrade to a MailPoet Premium plan[/link] to reactivate this segment.', 'mailpoet'),
        'link' => 'https://account.mailpoet.com',
      ];
    }
    return false;
  }
}
