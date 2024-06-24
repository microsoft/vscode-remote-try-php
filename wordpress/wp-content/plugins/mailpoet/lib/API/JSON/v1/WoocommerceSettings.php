<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\API\JSON\v1;

if (!defined('ABSPATH')) exit;


use MailPoet\API\JSON\Endpoint as APIEndpoint;
use MailPoet\Config\AccessControl;
use MailPoet\WP\Functions as WPFunctions;

class WoocommerceSettings extends APIEndpoint {
  public $permissions = [
    'global' => AccessControl::PERMISSION_MANAGE_EMAILS,
  ];

  private $allowedSettings = [
    'woocommerce_email_base_color',
  ];

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    WPFunctions $wp
  ) {
    $this->wp = $wp;
  }

  public function set($data = []) {
    foreach ($data as $option => $value) {
      if (in_array($option, $this->allowedSettings)) {
        $this->wp->updateOption($option, $value);
      }
    }
    return $this->successResponse([]);
  }
}
