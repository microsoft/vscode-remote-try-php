<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Util\Notices;

if (!defined('ABSPATH')) exit;


use MailPoet\Cron\Workers\SendingQueue\SendingQueue;
use MailPoet\WP\Functions as WPFunctions;
use MailPoet\WP\Notice;

class EmailWithInvalidSegmentNotice {
  const OPTION_NAME = SendingQueue::EMAIL_WITH_INVALID_SEGMENT_OPTION;

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    WPFunctions $wp
  ) {
    $this->wp = $wp;
  }

  public function init($shouldDisplay) {
    if (!$shouldDisplay || !$this->wp->getTransient(self::OPTION_NAME)) {
      return;
    }

    return $this->display($this->wp->getTransient(self::OPTION_NAME));
  }

  public function disable() {
    $this->wp->deleteTransient(self::OPTION_NAME);
  }

  private function display($newsletterSubject) {
    $notice = sprintf(
      // translators: %s is the subject of the newsletter.
      __('You are sending “%s“ to the deleted list. To continue sending, please restore the list. Alternatively, delete the newsletter if you no longer want to keep sending it.', 'mailpoet'),
      $this->wp->escHtml($newsletterSubject)
    );
    $extraClasses = 'mailpoet-dismissible-notice is-dismissible';

    Notice::displayError($notice, $extraClasses, self::OPTION_NAME, true);
    return $notice;
  }
}
