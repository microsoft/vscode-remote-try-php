<?php declare(strict_types = 1);

namespace MailPoet\PostEditorBlocks;

if (!defined('ABSPATH')) exit;


use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;
use MailPoet\Config\Env;
use MailPoet\WP\Functions as WPFunctions;

/**
 * Class MarketingOptinBlock
 *
 * Class for integrating marketing optin block with WooCommerce Checkout.
 */
class MarketingOptinBlock implements IntegrationInterface {
  /** @var array */
  private $options;

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    array $options,
    WPFunctions $wp
  ) {
    $this->options = $options;
    $this->wp = $wp;
  }

  public function get_name(): string { // phpcs:ignore PSR1.Methods.CamelCapsMethodName
    return 'mailpoet';
  }

  /**
   * Register block scripts and assets.
   */
  public function initialize() {
    $script_asset_path = Env::$assetsPath . '/dist/js/marketing-optin-block/marketing-optin-block-frontend.asset.php';
    $script_asset = file_exists($script_asset_path)
      ? require $script_asset_path
      : [
        'dependencies' => [],
        'version' => Env::$version,
      ];
    $this->wp->wpRegisterScript(
      'mailpoet-marketing-optin-block-frontend',
      Env::$assetsUrl . '/dist/js/marketing-optin-block/marketing-optin-block-frontend.js',
      $script_asset['dependencies'],
      $script_asset['version'],
      true
    );
  }

  /**
   * Returns an array of script handles to enqueue in the frontend context.
   *
   * @return string[]
   */
  public function get_script_handles() { // phpcs:ignore
    return ['mailpoet-marketing-optin-block-frontend'];
  }

  /**
   * Returns an array of script handles to enqueue in the editor context.
   *
   * @return string[]
   */
  public function get_editor_script_handles() { // phpcs:ignore
    return [];
  }

  /**
   * An array of key, value pairs of data made available to the block on the client side.
   *
   * @return array
   */
  public function get_script_data() { // phpcs:ignore
    return $this->options;
  }
}
