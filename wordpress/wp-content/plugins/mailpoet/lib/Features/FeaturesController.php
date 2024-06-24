<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Features;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Doctrine\DBAL\Exception\TableNotFoundException;

class FeaturesController {
  const FEATURE_BRAND_TEMPLATES = 'brand_templates';
  const GUTENBERG_EMAIL_EDITOR = 'gutenberg_email_editor';

  // Define feature defaults in the array below in the following form:
  //   self::FEATURE_NAME_OF_FEATURE => true,
  private $defaults = [
    self::FEATURE_BRAND_TEMPLATES => false,
    self::GUTENBERG_EMAIL_EDITOR => false,
  ];

  /** @var array|null */
  private $flags;

  /** @var FeatureFlagsRepository */
  private $featureFlagsRepository;

  public function __construct(
    FeatureFlagsRepository $featureFlagsRepository
  ) {
    $this->featureFlagsRepository = $featureFlagsRepository;
  }

  /** @return bool */
  public function isSupported($feature) {
    if (!$this->exists($feature)) {
      throw new \RuntimeException("Unknown feature '$feature'");
    }
    // ensure controller works even if used before migrator, return default value in such case
    try {
      $this->ensureFlagsLoaded();
    } catch (TableNotFoundException $e) {
      return $this->defaults[$feature];
    }
    return ($this->flags ?? [])[$feature];
  }

  /** @return bool */
  public function exists($feature) {
    return array_key_exists($feature, $this->defaults);
  }

  /** @return array */
  public function getDefaults() {
    return $this->defaults;
  }

  /** @return array */
  public function getAllFlags() {
    $this->ensureFlagsLoaded();
    return $this->flags ?? [];
  }

  public function resetCache(): void {
    $this->flags = null;
  }

  private function ensureFlagsLoaded() {
    if ($this->flags !== null) {
      return;
    }

    $flagsMap = $this->getValueMap();
    $this->flags = [];
    foreach ($this->defaults as $name => $default) {
      $this->flags[$name] = isset($flagsMap[$name]) ? $flagsMap[$name] : $default;
    }
  }

  private function getValueMap() {
    $features = $this->featureFlagsRepository->findAll();
    $featuresMap = [];
    foreach ($features as $feature) {
      $featuresMap[$feature->getName()] = (bool)$feature->getValue();
    }
    return $featuresMap;
  }
}
