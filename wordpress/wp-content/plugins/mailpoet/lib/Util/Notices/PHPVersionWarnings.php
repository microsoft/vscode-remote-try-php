<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Util\Notices;

if (!defined('ABSPATH')) exit;


use MailPoet\Util\Helpers;
use MailPoet\WP\Functions as WPFunctions;
use MailPoet\WP\Notice;

class PHPVersionWarnings {

  const DISMISS_NOTICE_TIMEOUT_SECONDS = 2592000; // 30 days
  const OPTION_NAME = 'dismissed-php-version-outdated-notice';

  public function init($phpVersion, $shouldDisplay) {
    if ($shouldDisplay && $this->isOutdatedPHPVersion($phpVersion)) {
      return $this->display($phpVersion);
    }
  }

  public function isOutdatedPHPVersion($phpVersion) {
    return version_compare($phpVersion, '8.0', '<') && !get_transient(self::OPTION_NAME);
  }

  public function display($phpVersion) {
    // translators: %s is the PHP version
    $errorString = __('Your website is running an outdated version of PHP (%1$s), on which MailPoet might stop working in the future. We recommend upgrading to %2$s or greater. Read our [link]simple PHP upgrade guide.[/link]', 'mailpoet');
    $errorString = sprintf($errorString, $phpVersion, '8.1');
    $error = Helpers::replaceLinkTags($errorString, 'https://kb.mailpoet.com/article/251-upgrading-the-websites-php-version', [
      'target' => '_blank',
    ]);

    $extraClasses = 'mailpoet-dismissible-notice is-dismissible';

    return Notice::displayWarning($error, $extraClasses, self::OPTION_NAME);
  }

  public function disable() {
    WPFunctions::get()->setTransient(self::OPTION_NAME, true, self::DISMISS_NOTICE_TIMEOUT_SECONDS);
  }
}
