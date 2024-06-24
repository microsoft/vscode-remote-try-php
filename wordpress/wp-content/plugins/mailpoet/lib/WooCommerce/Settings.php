<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\WooCommerce;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\Renderer;
use MailPoet\Settings\SettingsController;

class Settings {

  /** @var Renderer */
  private $renderer;

  /** @var SettingsController */
  private $settings;

  public function __construct(
    Renderer $renderer,
    SettingsController $settings
  ) {
    $this->renderer = $renderer;
    $this->settings = $settings;
  }

  public function disableWooCommerceSettings() {
    if (
      !isset($_GET['tab'])
      || $_GET['tab'] !== 'email'
      || isset($_GET['section'])
    ) {
      return;
    }

    //The templates are in our control and the inputs are sanitized.
    //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, WordPressDotOrg.sniffs.OutputEscaping.UnescapedOutputParameter
    echo $this->renderer->render('woocommerce/settings_button.html', [
      'woocommerce_template_id' => (int)$this->settings->get(TransactionalEmails::SETTING_EMAIL_ID),
    ]);
    if (!(bool)$this->settings->get('woocommerce.use_mailpoet_editor')) {
      return;
    }
    // The templates are in our control and the inputs are sanitized.
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped,WordPressDotOrg.sniffs.OutputEscaping.UnescapedOutputParameter
    echo $this->renderer->render('woocommerce/settings_overlay.html', [
      'woocommerce_template_id' => (int)$this->settings->get(TransactionalEmails::SETTING_EMAIL_ID),
    ]);
  }
}
