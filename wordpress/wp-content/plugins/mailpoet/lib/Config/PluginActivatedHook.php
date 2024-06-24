<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config;

if (!defined('ABSPATH')) exit;


use MailPoet\WP\Functions as WPFunctions;

class PluginActivatedHook {
  private $deferredAdminNotices;

  public function __construct(
    DeferredAdminNotices $deferredAdminNotices
  ) {
    $this->deferredAdminNotices = $deferredAdminNotices;
  }

  public function action($plugin, $networkWide) {
    if ($plugin === WPFunctions::get()->pluginBasename(Env::$file) && $networkWide) {
      $this->deferredAdminNotices->addNetworkAdminNotice(__("We noticed that you're using an unsupported environment. While MailPoet might work within a MultiSite environment, we don’t support it.", 'mailpoet'));
    }
  }
}
