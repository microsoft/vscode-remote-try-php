<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\API\JSON\v1;

if (!defined('ABSPATH')) exit;


use MailPoet\API\JSON\Endpoint as APIEndpoint;
use MailPoet\API\JSON\Error as APIError;
use MailPoet\Config\AccessControl;
use MailPoet\Config\ServicesChecker;
use MailPoet\WP\Functions as WPFunctions;
use MailPoet\WPCOM\DotcomHelperFunctions;
use WP_Error;

class Premium extends APIEndpoint {
  const PREMIUM_PLUGIN_SLUG = 'mailpoet-premium';
  const PREMIUM_PLUGIN_PATH = 'mailpoet-premium/mailpoet-premium.php';
  // This is the path to the managed plugin on Dotcom platform. It is relative to WP_PLUGIN_DIR.
  const DOTCOM_SYMLINK_PATH = '../../../../wordpress/plugins/mailpoet-premium/latest';

  public $permissions = [
    'global' => AccessControl::PERMISSION_MANAGE_SETTINGS,
  ];

  /** @var ServicesChecker */
  private $servicesChecker;

  /** @var WPFunctions */
  private $wp;

  /** @var DotcomHelperFunctions */
  private $dotcomHelperFunctions;

  public function __construct(
    ServicesChecker $servicesChecker,
    WPFunctions $wp,
    DotcomHelperFunctions $dotcomHelperFunctions
  ) {
    $this->servicesChecker = $servicesChecker;
    $this->wp = $wp;
    $this->dotcomHelperFunctions = $dotcomHelperFunctions;
  }

  public function installPlugin() {
    $premiumKeyValid = $this->servicesChecker->isPremiumKeyValid(false);
    if (!$premiumKeyValid) {
      return $this->error(__('Premium key is not valid.', 'mailpoet'));
    }

    $pluginInfo = $this->wp->pluginsApi('plugin_information', [
      'slug' => self::PREMIUM_PLUGIN_SLUG,
    ]);

    if (!$pluginInfo || $pluginInfo instanceof WP_Error) {
      return $this->error(__('Error when installing MailPoet Premium plugin.', 'mailpoet'));
    }

    $pluginInfo = (array)$pluginInfo;

    // If we are in Dotcom platform, we try to symlink the plugin instead of downloading it
    try {
      if ($this->dotcomHelperFunctions->isDotcom()) {
        $result = symlink(self::DOTCOM_SYMLINK_PATH, WP_PLUGIN_DIR . '/' . self::PREMIUM_PLUGIN_SLUG);
        if ($result === true) {
          return $this->successResponse();
        }
      }
    } catch (\Exception $e) {
      // Do nothing and continue with a regular installation
    }

    $result = $this->wp->installPlugin($pluginInfo['download_link']);
    if ($result !== true) {
      return $this->error(__('Error when installing MailPoet Premium plugin.', 'mailpoet'));
    }
    return $this->successResponse();
  }

  public function activatePlugin() {
    $premiumKeyValid = $this->servicesChecker->isPremiumKeyValid(false);
    if (!$premiumKeyValid) {
      return $this->error(__('Premium key is not valid.', 'mailpoet'));
    }

    $result = $this->wp->activatePlugin(self::PREMIUM_PLUGIN_PATH);
    if ($result !== null) {
      return $this->error(__('Error when activating MailPoet Premium plugin.', 'mailpoet'));
    }
    return $this->successResponse();
  }

  private function error($message) {
    return $this->badRequest([
      APIError::BAD_REQUEST => $message,
    ]);
  }
}
