<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\API\JSON\v1;

if (!defined('ABSPATH')) exit;


use MailPoet\API\JSON\Endpoint as APIEndpoint;
use MailPoet\API\JSON\Error as APIError;
use MailPoet\Config\AccessControl;
use MailPoet\Features\FeatureFlagsController;
use MailPoet\Features\FeaturesController;

class FeatureFlags extends APIEndpoint {

  public $permissions = [
    'global' => AccessControl::PERMISSION_MANAGE_FEATURES,
  ];

  /** @var FeaturesController */
  private $featuresController;

  /** @var FeatureFlagsController */
  private $featureFlagsController;

  public function __construct(
    FeaturesController $featuresController,
    FeatureFlagsController $featureFlags
  ) {
    $this->featuresController = $featuresController;
    $this->featureFlagsController = $featureFlags;
  }

  public function getAll() {
    $featureFlags = $this->featureFlagsController->getAll();
    return $this->successResponse($featureFlags);
  }

  public function set(array $flags) {
    foreach ($flags as $name => $value) {
      if (!$this->featuresController->exists($name)) {
        return $this->badRequest([
          APIError::BAD_REQUEST => "Feature '$name' does not exist'",
        ]);
      }
    }

    foreach ($flags as $name => $value) {
      $this->featureFlagsController->set($name, (bool)$value);
    }
    return $this->successResponse([]);
  }
}
