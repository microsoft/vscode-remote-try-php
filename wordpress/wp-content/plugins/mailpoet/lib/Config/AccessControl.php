<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Engine;
use MailPoet\WP\Functions as WPFunctions;

class AccessControl {
  const PERMISSION_ACCESS_PLUGIN_ADMIN = 'mailpoet_access_plugin_admin';
  const PERMISSION_MANAGE_SETTINGS = 'mailpoet_manage_settings';
  const PERMISSION_MANAGE_FEATURES = 'mailpoet_manage_features';
  const PERMISSION_MANAGE_EMAILS = 'mailpoet_manage_emails';
  const PERMISSION_MANAGE_SUBSCRIBERS = 'mailpoet_manage_subscribers';
  const PERMISSION_MANAGE_FORMS = 'mailpoet_manage_forms';
  const PERMISSION_MANAGE_SEGMENTS = 'mailpoet_manage_segments';
  const PERMISSION_MANAGE_AUTOMATIONS = Engine::CAPABILITY_MANAGE_AUTOMATIONS;
  const PERMISSION_MANAGE_HELP = 'mailpoet_manage_help';
  const NO_ACCESS_RESTRICTION = 'mailpoet_no_access_restriction';
  const ALL_ROLES_ACCESS = 'mailpoet_all_roles_access';

  public function getDefaultPermissions() {
    return [
      self::PERMISSION_ACCESS_PLUGIN_ADMIN => WPFunctions::get()->applyFilters(
        'mailpoet_permission_access_plugin_admin',
        [
          'administrator',
          'editor',
        ]
      ),
      self::PERMISSION_MANAGE_SETTINGS => WPFunctions::get()->applyFilters(
        'mailpoet_permission_manage_settings',
        [
          'administrator',
        ]
      ),
      self::PERMISSION_MANAGE_FEATURES => WPFunctions::get()->applyFilters(
        'mailpoet_permission_manage_features',
        [
          'administrator',
        ]
      ),
      self::PERMISSION_MANAGE_EMAILS => WPFunctions::get()->applyFilters(
        'mailpoet_permission_manage_emails',
        [
          'administrator',
          'editor',
        ]
      ),
      self::PERMISSION_MANAGE_SUBSCRIBERS => WPFunctions::get()->applyFilters(
        'mailpoet_permission_manage_subscribers',
        [
          'administrator',
        ]
      ),
      self::PERMISSION_MANAGE_FORMS => WPFunctions::get()->applyFilters(
        'mailpoet_permission_manage_forms',
        [
          'administrator',
        ]
      ),
      self::PERMISSION_MANAGE_SEGMENTS => WPFunctions::get()->applyFilters(
        'mailpoet_permission_manage_segments',
        [
          'administrator',
        ]
      ),
      self::PERMISSION_MANAGE_AUTOMATIONS => WPFunctions::get()->applyFilters(
        'mailpoet_permission_manage_automations',
        [
          'administrator',
          'editor',
        ]
      ),
      self::PERMISSION_MANAGE_HELP => WPFunctions::get()->applyFilters(
        'mailpoet_permission_manage_help',
        [
          'administrator',
          'editor',
        ]
      ),
    ];
  }

  public function getPermissionLabels() {
    return [
      self::PERMISSION_ACCESS_PLUGIN_ADMIN => __('Admin menu item', 'mailpoet'),
      self::PERMISSION_MANAGE_SETTINGS => __('Manage settings', 'mailpoet'),
      self::PERMISSION_MANAGE_FEATURES => __('Manage features', 'mailpoet'),
      self::PERMISSION_MANAGE_EMAILS => __('Manage emails', 'mailpoet'),
      self::PERMISSION_MANAGE_SUBSCRIBERS => __('Manage subscribers', 'mailpoet'),
      self::PERMISSION_MANAGE_FORMS => __('Manage forms', 'mailpoet'),
      self::PERMISSION_MANAGE_SEGMENTS => __('Manage segments', 'mailpoet'),
      self::PERMISSION_MANAGE_AUTOMATIONS => __('Manage automations', 'mailpoet'),
      self::PERMISSION_MANAGE_HELP => __('Manage help', 'mailpoet'),
    ];
  }

  public function validatePermission($permission) {
    if ($permission === self::NO_ACCESS_RESTRICTION) return true;
    if ($permission === self::ALL_ROLES_ACCESS) {
      $capabilities = array_keys($this->getDefaultPermissions());
      foreach ($capabilities as $capability) {
        if (WPFunctions::get()->currentUserCan($capability)) {
          return true;
        }
      }
      return false;
    }
    return WPFunctions::get()->currentUserCan($permission);
  }
}
