<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Cron\Workers\SendingQueue\Tasks;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Mailer\Mailer as MailerInstance;
use MailPoet\Mailer\MailerFactory;
use MailPoet\Mailer\MailerLog;
use MailPoet\Mailer\Methods\MailPoet;

class Mailer {
  /** @var MailerFactory */
  private $mailerFactory;

  /** @var MailerInstance */
  private $mailer;

  public function __construct(
    MailerFactory $mailerFactory
  ) {
    $this->mailerFactory = $mailerFactory;
    $this->mailer = $this->configureMailer();
  }

  public function configureMailer(NewsletterEntity $newsletter = null) {
    $sender['address'] = ($newsletter && !empty($newsletter->getSenderAddress())) ?
      $newsletter->getSenderAddress() :
      null;
    $sender['name'] = ($newsletter && !empty($newsletter->getSenderName())) ?
      $newsletter->getSenderName() :
      null;
    $replyTo['address'] = ($newsletter && !empty($newsletter->getReplyToAddress())) ?
      $newsletter->getReplyToAddress() :
      null;
    $replyTo['name'] = ($newsletter && !empty($newsletter->getReplyToName())) ?
      $newsletter->getReplyToName() :
      null;
    if (!$sender['address']) {
      $sender = null;
    }
    if (!$replyTo['address']) {
      $replyTo = null;
    }
    $this->mailer = $this->mailerFactory->buildMailer(null, $sender, $replyTo);
    return $this->mailer;
  }

  public function getMailerLog() {
    return MailerLog::getMailerLog();
  }

  public function updateSentCount() {
    return MailerLog::incrementSentCount();
  }

  public function getProcessingMethod() {
    return ($this->mailer->mailerMethod instanceof MailPoet) ?
      'bulk' :
      'individual';
  }

  public function prepareSubscriberForSending(SubscriberEntity $subscriber) {
    return $this->mailer->formatSubscriberNameAndEmailAddress($subscriber);
  }

  public function sendBulk($preparedNewsletters, $preparedSubscribers, $extraParams = []) {
    if ($this->getProcessingMethod() === 'individual') {
      throw new \LogicException('Trying to send a batch with individual processing method');
    }
    return $this->mailer->mailerMethod->send(
      $preparedNewsletters,
      $preparedSubscribers,
      $extraParams
    );
  }

  public function send($preparedNewsletter, $preparedSubscriber, $extraParams = []) {
    if ($this->getProcessingMethod() === 'bulk') {
      throw new \LogicException('Trying to send an individual email with a bulk processing method');
    }
    return $this->mailer->mailerMethod->send(
      $preparedNewsletter,
      $preparedSubscriber,
      $extraParams
    );
  }
}
