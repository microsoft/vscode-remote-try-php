<?php declare(strict_types = 1);

namespace MailPoet\WooCommerce;

if (!defined('ABSPATH')) exit;


use Automattic\WooCommerce\Admin\Features\OnboardingTasks\Task;
use MailPoet\Config\Menu;
use MailPoet\DI\ContainerWrapper;
use MailPoet\Settings\SettingsController;

/**
 * MailPoet task that is added to the WooCommerce homepage.
 */
class MailPoetTask extends Task {
  public function get_id(): string { // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    return 'mailpoet_task';
  }

  public function get_title(): string { // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    if ($this->is_complete()) {
      return esc_html__('MailPoet is ready to send marketing emails from your store', 'mailpoet');
    }

    return esc_html__('Set up email marketing with MailPoet', 'mailpoet');
  }

  public function get_content(): string { // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    return '';
  }

  /**
   * String that is displayed below the title of the task indicating the estimated completion time.
   */
  public function get_time(): string { // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    return '';
  }

  /**
   * Link used when the user clicks on the title of the task.
   */
  public function get_action_url(): string { // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    if ($this->is_complete()) {
      return admin_url('admin.php?page=' . Menu::MAIN_PAGE_SLUG);
    }

    return admin_url('admin.php?page=' . Menu::WELCOME_WIZARD_PAGE_SLUG . '&mailpoet_wizard_loaded_via_woocommerce');
  }

  /**
   * Whether the task is completed.
   * If the setting 'version' is not null it means the welcome wizard
   * was already completed so we mark this task as completed as well.
   */
  public function is_complete(): bool { // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    $settings = ContainerWrapper::getInstance()->get(SettingsController::class);
    $version = $settings->get('version');

    return $version !== null;
  }
}
