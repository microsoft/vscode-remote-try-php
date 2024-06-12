<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Util\Notices;

if (!defined('ABSPATH')) exit;


use MailPoet\Util\Helpers;
use MailPoet\WP\Functions as WPFunctions;
use MailPoet\WP\Notice;

class ChangedTrackingNotice {
  const OPTION_NAME = 'mailpoet-changed-tracking-settings-notice';

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    WPFunctions $wp
  ) {
    $this->wp = $wp;
  }

  public function init($shouldDisplay) {
    if ($shouldDisplay && $this->wp->getTransient(self::OPTION_NAME)) {
      return $this->display();
    }
    return null;
  }

  public function display() {
    $text = __('Email open and click tracking is now enabled. You can change how MailPoet tracks your subscribers in [link]Settings[/link]', 'mailpoet');
    $text = Helpers::replaceLinkTags($text, 'admin.php?page=mailpoet-settings#advanced');
    $extraClasses = 'mailpoet-dismissible-notice is-dismissible';

    return Notice::displayWarning($text, $extraClasses, self::OPTION_NAME);
  }

  public function disable() {
    $this->wp->deleteTransient(self::OPTION_NAME);
  }
}
