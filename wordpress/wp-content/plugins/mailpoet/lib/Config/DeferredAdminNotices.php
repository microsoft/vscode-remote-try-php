<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config;

if (!defined('ABSPATH')) exit;


use MailPoet\WP\Functions as WPFunctions;
use MailPoet\WP\Notice;

class DeferredAdminNotices {

  const OPTIONS_KEY_NAME = 'mailpoet_deferred_admin_notices';

  /**
   * @param string $message
   */
  public function addNetworkAdminNotice($message) {
    $notices = WPFunctions::get()->getOption(DeferredAdminNotices::OPTIONS_KEY_NAME, []);
    $notices[] = [
      "message" => $message,
      "networkAdmin" => true,// if we'll need to display the notice to anyone else
    ];
    WPFunctions::get()->updateOption(DeferredAdminNotices::OPTIONS_KEY_NAME, $notices);
  }

  public function printAndClean() {
    $notices = WPFunctions::get()->getOption(DeferredAdminNotices::OPTIONS_KEY_NAME, []);

    foreach ($notices as $notice) {
      $notice = new Notice(Notice::TYPE_WARNING, $notice["message"]);
      WPFunctions::get()->addAction('network_admin_notices', [$notice, 'displayWPNotice']);
    }

    if (!empty($notices)) {
      WPFunctions::get()->deleteOption(DeferredAdminNotices::OPTIONS_KEY_NAME);
    }
  }
}
