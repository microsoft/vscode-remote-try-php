<?php declare(strict_types = 1);

namespace MailPoet\Util\Notices;

if (!defined('ABSPATH')) exit;


use MailPoet\Util\Helpers;
use MailPoet\WP\Functions as WPFunctions;
use MailPoet\WP\Notice;

/**
 * This can be removed after 2022-12-01
 */
class DeprecatedFilterNotice {
  const DISMISS_NOTICE_TIMEOUT_SECONDS = 15552000; // 6 months
  const OPTION_NAME = 'dismissed-deprecated-filter-notice';

  const DEPRECATED_FILTER_NAME = 'mailpoet_mailer_smtp_transport_agent';
  const NEW_FILTER_NAME = 'mailpoet_mailer_smtp_options';

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    WPFunctions $wp
  ) {
    $this->wp = $wp;
  }

  public function init($shouldDisplay): ?Notice {
    if ($shouldDisplay && !$this->wp->getTransient(self::OPTION_NAME) && $this->wp->hasFilter('mailpoet_mailer_smtp_transport_agent')) {
      return $this->display();
    }
    return null;
  }

  public function display(): Notice {
    $message = Helpers::replaceLinkTags(
      __('The <i>mailpoet_mailer_smtp_transport_agent</i> filter no longer works. Please replace it with <i>mailpoet_mailer_smtp_options</i>. Read more in [link]documentation[/link].', 'mailpoet'),
      'https://kb.mailpoet.com/article/193-tls-encryption-does-not-work',
      ['target' => '_blank']
    );
    $extraClasses = 'mailpoet-dismissible-notice is-dismissible';

    return Notice::displayWarning($message, $extraClasses, self::OPTION_NAME);
  }

  public function disable(): void {
    $this->wp->setTransient(self::OPTION_NAME, true, self::DISMISS_NOTICE_TIMEOUT_SECONDS);
  }
}
