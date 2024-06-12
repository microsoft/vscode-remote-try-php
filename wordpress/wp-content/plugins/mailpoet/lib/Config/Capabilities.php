<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config;

if (!defined('ABSPATH')) exit;


use MailPoet\WP\Functions as WPFunctions;
use WP_Role;

class Capabilities {
  const MEMBERS_CAP_GROUP_NAME = 'mailpoet';

  private $renderer = null;
  /** @var WPFunctions  */
  private $wp;
  /** @var AccessControl */
  private $accessControl;

  public function __construct(
    $renderer = null,
    WPFunctions $wp = null
  ) {
    if ($renderer !== null) {
      $this->renderer = $renderer;
    }
    if ($wp == null) {
      $wp = new WPFunctions;
    }
    $this->wp = $wp;
    $this->accessControl = new AccessControl;
  }

  public function init() {
    $this->setupMembersCapabilities();
  }

  public function setupWPCapabilities() {
    $permissions = $this->accessControl->getDefaultPermissions();
    $roleObjects = [];
    foreach ($permissions as $name => $roles) {
      foreach ($roles as $role) {
        if (!isset($roleObjects[$role])) {
          $roleObjects[$role] = WPFunctions::get()->getRole($role);
        }
        if (!$roleObjects[$role] instanceof WP_Role) continue;
        $roleObjects[$role]->add_cap($name);
      }
    }
  }

  public function removeWPCapabilities() {
    $permissions = $this->accessControl->getDefaultPermissions();
    $roleObjects = [];
    foreach ($permissions as $name => $roles) {
      foreach ($roles as $role) {
        if (!isset($roleObjects[$role])) {
          $roleObjects[$role] = WPFunctions::get()->getRole($role);
        }
        if (!$roleObjects[$role] instanceof WP_Role) continue;
        $roleObjects[$role]->remove_cap($name);
      }
    }
  }

  public function setupMembersCapabilities() {
    $this->wp->addAction('admin_enqueue_scripts', [$this, 'enqueueMembersStyles']);
    $this->wp->addAction('members_register_cap_groups', [$this, 'registerMembersCapGroup']);
    $this->wp->addAction('members_register_caps', [$this, 'registerMembersCapabilities']);
  }

  public function enqueueMembersStyles() {
    WPFunctions::get()->wpEnqueueStyle(
      'mailpoet-admin-global',
      Env::$assetsUrl . '/dist/css/' . $this->renderer->getCssAsset('mailpoet-admin.css')
    );
  }

  public function registerMembersCapGroup() {
    members_register_cap_group(
      self::MEMBERS_CAP_GROUP_NAME,
      [
        'label' => __('MailPoet', 'mailpoet'),
        'caps' => [],
        'icon' => 'mailpoet-icon-logo',
        'priority' => 30,
      ]
    );
  }

  public function registerMembersCapabilities() {
    $permissions = $this->accessControl->getPermissionLabels();
    foreach ($permissions as $name => $label) {
      $this->registerMembersCapability($name, $label);
    }
  }

  public function registerMembersCapability($name, $label) {
    members_register_cap(
      $name,
      [
        'label' => $label,
        'group' => self::MEMBERS_CAP_GROUP_NAME,
      ]
    );
  }
}
