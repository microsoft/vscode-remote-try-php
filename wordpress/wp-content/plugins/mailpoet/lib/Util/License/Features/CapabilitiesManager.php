<?php declare(strict_types = 1);

namespace MailPoet\Util\License\Features;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\ServicesChecker;
use MailPoet\Settings\SettingsController;
use MailPoet\Util\License\Features\Data\Capability;

class CapabilitiesManager {
  // Settings mapping
  const MSS_TIER_SETTING_KEY = 'mta.mailpoet_api_key_state.data.tier';
  const MSS_MAILPOET_LOGO_IN_EMAILS_SETTING_KEY = 'mta.mailpoet_api_key_state.data.mailpoet_logo_in_emails';
  const MSS_DETAILED_ANALYTICS_SETTING_KEY = 'mta.mailpoet_api_key_state.data.detailed_analytics';
  const MSS_AUTOMATION_STEPS_SETTING_KEY = 'mta.mailpoet_api_key_state.data.automation_steps';
  const MSS_SEGMENT_FILTERS_SETTING_KEY = 'mta.mailpoet_api_key_state.data.segment_filters';
  // Product capabilities mapping
  const MIN_TIER_LOGO_NOT_REQUIRED = 1;
  const MIN_TIER_ANALYTICS_ENABLED = 1;
  const MIN_TIER_NO_UPGRADE_PAGE = 2;
  const MIN_TIER_UNLIMITED_AUTOMATION_STEPS = 2;
  const MIN_TIER_UNLIMITED_SEGMENT_FILTERS = 2;

  private SettingsController $settings;
  private ServicesChecker $servicesChecker;
  private Subscribers $subscribersFeature;
  private ?int $tier;
  private bool $isKeyValid = false;

  /** @var array<string,Capability>|null */
  private ?array $capabilities = null;

  public function __construct(
    SettingsController $settings,
    ServicesChecker $servicesChecker,
    Subscribers $subscribersFeature
  ) {
    $this->settings = $settings;
    $this->servicesChecker = $servicesChecker;
    $this->subscribersFeature = $subscribersFeature;
  }

  public function getTier(): ?int {
    $tier = $this->settings->get(self::MSS_TIER_SETTING_KEY);
    return isset($tier) ? (int)$tier : null;
  }

  private function isMailpoetLogoInEmailsRequired(): bool {
    $mailpoetLogoInEmails = $this->settings->get(self::MSS_MAILPOET_LOGO_IN_EMAILS_SETTING_KEY);

    if (!isset($this->tier) && !isset($mailpoetLogoInEmails)) {
      return !$this->servicesChecker->isUserActivelyPaying(); // Backward compatibility
    }

    if (!$this->isKeyValid) {
      return true;
    }

    // Allow for less restrictive individual capability to take precedence over tier
    if (isset($mailpoetLogoInEmails) && (bool)$mailpoetLogoInEmails === false) {
      return false;
    }

    return !isset($this->tier) || $this->tier < self::MIN_TIER_LOGO_NOT_REQUIRED;
  }

  private function isDetailedAnalyticsEnabled(): bool {
    // Preconditions
    if (!$this->subscribersFeature->hasValidPremiumKey() || $this->subscribersFeature->check() || !$this->servicesChecker->isPremiumPluginActive()) {
      return false;
    }

    $detailedAnalytics = $this->settings->get(self::MSS_DETAILED_ANALYTICS_SETTING_KEY);

    if (!isset($this->tier) && !isset($detailedAnalytics)) {
      return true; // Backward compatibility is true when preconditions have been met
    }

    // Allow for less restrictive individual capability to take precedence
    if (isset($detailedAnalytics) && (bool)$detailedAnalytics === true) {
      return true;
    }

    return (isset($this->tier) && $this->tier >= self::MIN_TIER_ANALYTICS_ENABLED);
  }

  private function getLimit(string $settingKey, int $minTierForUnlimited): int {
    $capabilityValue = $this->settings->get($settingKey);

    if (!isset($this->tier) && !isset($capabilityValue)) {
      return 0; // Backward compatibility
    }

    $limitFromTier = isset($this->tier) && $this->tier >= $minTierForUnlimited ? 0 : 1; // 0 is unlimited

    if ($limitFromTier === 0) {
      return 0;
    }

    // Allow for less restrictive individual capability to take precedence
    return (isset($capabilityValue) && ((int)$capabilityValue === 0 || (int)$capabilityValue > $limitFromTier)) ? (int)$capabilityValue : $limitFromTier;
  }

  private function getAutomationStepsLimit(): int {
    return $this->getLimit(self::MSS_AUTOMATION_STEPS_SETTING_KEY, self::MIN_TIER_UNLIMITED_AUTOMATION_STEPS);
  }

  private function getSegmentFiltersLimit(): int {
    return $this->getLimit(self::MSS_SEGMENT_FILTERS_SETTING_KEY, self::MIN_TIER_UNLIMITED_SEGMENT_FILTERS);
  }

  public function initCapabilities(): void {
    $this->tier = $this->getTier();
    $isPremiumKeyValid = $this->servicesChecker->isPremiumKeyValid(false);
    $this->isKeyValid = $isPremiumKeyValid || $this->servicesChecker->isMailPoetAPIKeyValid(false);
    $automationSteps = $this->getAutomationStepsLimit();
    $segmentFilters = $this->getSegmentFiltersLimit();
    $this->capabilities = [
      'mailpoetLogoInEmails' => new Capability('mailpoetLogoInEmails', Capability::TYPE_BOOLEAN, $this->isMailpoetLogoInEmailsRequired()),
      'detailedAnalytics' => new Capability('detailedAnalytics', Capability::TYPE_BOOLEAN, !$this->isDetailedAnalyticsEnabled()),
      'automationSteps' => new Capability('automationSteps', Capability::TYPE_NUMBER, $automationSteps > 0, $automationSteps),
      'segmentFilters' => new Capability('segmentFilters', Capability::TYPE_NUMBER, $segmentFilters > 0, $segmentFilters),
    ];
  }

  /** @return array<string,Capability> */
  public function getCapabilities(): array {
    if ($this->capabilities === null) {
      $this->initCapabilities();
    }
    return $this->capabilities ?? [];
  }

  public function getCapability(string $name): ?Capability {
    return $this->getCapabilities()[$name] ?? null;
  }

  /**
   * Returns true if there is no valid premium key or the product tier can be upgraded
   */
  public function showUpgradePage(): bool {
    $tier = $this->getTier();
    if (!$this->subscribersFeature->hasValidPremiumKey() || (isset($tier) && $tier < self::MIN_TIER_NO_UPGRADE_PAGE)) {
      return true;
    }
    return false;
  }

  /**
   * Returns true if there is a valid key for a tier that can be upgraded
   * @todo remove after the a/b test is finished and keep only one page
   */
  public function showNewUpgradePage(): bool {
    $tier = $this->getTier();
    $isKeyValid = $this->subscribersFeature->hasValidPremiumKey() || $this->servicesChecker->isMailPoetAPIKeyValid(false);
    if ($isKeyValid && isset($tier) && $tier < self::MIN_TIER_NO_UPGRADE_PAGE) {
      return true;
    }
    return false;
  }
}
