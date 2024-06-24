<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Mailer\WordPress;

if (!defined('ABSPATH')) exit;


use MailPoet\Mailer\MailerFactory;
use MailPoet\Mailer\MetaInfo;
use MailPoet\Settings\SettingsController;
use MailPoet\Subscribers\SubscribersRepository;

class WordpressMailerReplacer {

  /** @var MailerFactory */
  private $mailerFactory;

  /** @var MetaInfo */
  private $mailerMetaInfo;

  /** @var SettingsController */
  private $settings;

  /** @var SubscribersRepository */
  private $subscribersRepository;

  public function __construct(
    MailerFactory $mailerFactory,
    MetaInfo $mailerMetaInfo,
    SettingsController $settings,
    SubscribersRepository $subscribersRepository
  ) {
    $this->mailerFactory = $mailerFactory;
    $this->mailerMetaInfo = $mailerMetaInfo;
    $this->settings = $settings;
    $this->subscribersRepository = $subscribersRepository;
  }

  public function replaceWordPressMailer() {
    global $phpmailer;
    // This code needs to be wrapped because it has to run in an early stage of plugin initialisation
    // and in some cases on multisite instance it may run before DB migrator and settings table is not ready at that time
    try {
      $sendTransactional = $this->settings->get('send_transactional_emails', false);
    } catch (\Exception $e) {
      $sendTransactional = false;
    }
    if ($sendTransactional) {
      $phpmailer = new WordPressMailer(
        $this->mailerFactory,
        $this->mailerMetaInfo,
        $this->subscribersRepository
      );
    }
    return $phpmailer;
  }
}
