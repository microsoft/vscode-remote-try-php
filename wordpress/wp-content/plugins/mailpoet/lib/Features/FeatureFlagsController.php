<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Features;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\FeatureFlagEntity;

class FeatureFlagsController {

  /** @var FeaturesController */
  private $featuresController;

  /** @var FeatureFlagsRepository */
  private $featureFlagsRepository;

  public function __construct(
    FeaturesController $featuresController,
    FeatureFlagsRepository $featureFlagsRepository
  ) {
    $this->featuresController = $featuresController;
    $this->featureFlagsRepository = $featureFlagsRepository;
  }

  public function set($name, $value) {
    if (!$this->featuresController->exists($name)) {
      throw new \RuntimeException("Feature '$name' does not exist'");
    }

    $this->featureFlagsRepository->createOrUpdate(['name' => $name, 'value' => $value]);
  }

  public function getAll() {
    $flags = $this->featureFlagsRepository->findAll();
    $flagsMap = array_combine(
      array_map(
        function (FeatureFlagEntity $flag) {
          return $flag->getName();
        },
        $flags
      ),
      $flags
    );

    $output = [];
    foreach ($this->featuresController->getDefaults() as $name => $default) {
      $output[] = [
        'name' => $name,
        'value' => isset($flagsMap[$name]) ? (bool)$flagsMap[$name]->getValue() : $default,
        'default' => $default,
      ];
    }
    return $output;
  }
}
