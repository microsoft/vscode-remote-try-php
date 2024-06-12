<?php declare(strict_types = 1);

namespace MailPoet\Mailer\Methods;

if (!defined('ABSPATH')) exit;


use MailPoet\Mailer\Mailer;
use MailPoet\Mailer\Methods\Common\BlacklistCheck;
use MailPoet\Mailer\WordPress\PHPMailerLoader;
use PHPMailer\PHPMailer\PHPMailer;

PHPMailerLoader::load();

abstract class PHPMailerMethod implements MailerMethod {
  /** @var string[] */
  public $sender;
  /** @var string[] */
  public $replyTo;
  /** @var string */
  public $returnPath;
  /** @var PHPMailer  */
  public $mailer;

  protected $errorMapper;
  /** @var BlacklistCheck */
  protected $blacklist;

  public function __construct(
    $sender,
    $replyTo,
    $returnPath,
    $errorMapper
  ) {
    $this->sender = $sender;
    $this->replyTo = $replyTo;
    $this->returnPath = $returnPath;
    $this->mailer = $this->buildMailer();
    $this->errorMapper = $errorMapper;
    $this->blacklist = new BlacklistCheck();
  }

  public function send($newsletter, $subscriber, $extraParams = []): array {
    if ($this->blacklist->isBlacklisted($subscriber)) {
      $error = $this->errorMapper->getBlacklistError($subscriber);
      return Mailer::formatMailerErrorResult($error);
    }
    try {
      $mailer = $this->configureMailerWithMessage($newsletter, $subscriber, $extraParams);
      $result = $mailer->send();
    } catch (\Exception $e) {
      return Mailer::formatMailerErrorResult($this->errorMapper->getErrorFromException($e, $subscriber));
    }
    if ($result === true) {
      return Mailer::formatMailerSendSuccessResult();
    } else {
      $error = $this->errorMapper->getErrorForSubscriber($subscriber);
      return Mailer::formatMailerErrorResult($error);
    }
  }

  abstract public function buildMailer(): PHPMailer;

  public function configureMailerWithMessage($newsletter, $subscriber, $extraParams = []) {
    $mailer = $this->mailer;
    $mailer->clearAddresses();
    $mailer->clearCustomHeaders();
    $mailer->isHTML(!empty($newsletter['body']['html']));
    $mailer->CharSet = 'UTF-8'; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    $mailer->setFrom($this->sender['from_email'], $this->sender['from_name'], false);
    $mailer->addReplyTo($this->replyTo['reply_to_email'], $this->replyTo['reply_to_name']);
    $subscriber = $this->processSubscriber($subscriber);
    $mailer->addAddress($subscriber['email'], $subscriber['name']);
    $mailer->Subject = (!empty($newsletter['subject'])) ? $newsletter['subject'] : ''; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    $mailer->Body = (!empty($newsletter['body']['html'])) ? $newsletter['body']['html'] : $newsletter['body']['text']; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    if ($mailer->ContentType !== 'text/plain') { // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
      $mailer->AltBody = (!empty($newsletter['body']['text'])) ? $newsletter['body']['text'] : ''; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    }
    $mailer->Sender = $this->returnPath; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    if (!empty($extraParams['unsubscribe_url'])) {
      $this->mailer->addCustomHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
      $this->mailer->addCustomHeader('List-Unsubscribe', '<' . $extraParams['unsubscribe_url'] . '>');
    }

    // Enforce base64 encoding when lines are too long, otherwise quoted-printable encoding
    // is automatically used which can occasionally break the email body.
    // Explanation:
    //   The bug occurs on Unix systems where mail() function passes email to a variation of
    //   sendmail command which expects only NL as line endings (POSIX). Since quoted-printable
    //   requires CRLF some of those commands convert LF to CRLF which can break the email body
    //   because it already (correctly) uses CRLF. Such CRLF then (wrongly) becomes CRCRLF.
    if (PHPMailer::hasLineLongerThanMax($mailer->Body)) { // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
      $mailer->Encoding = 'base64'; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    }

    return $mailer;
  }

  public function processSubscriber($subscriber) {
    preg_match('!(?P<name>.*?)\s<(?P<email>.*?)>!', $subscriber, $subscriberData);
    if (!isset($subscriberData['email'])) {
      $subscriberData = [
        'email' => $subscriber,
      ];
    }
    return [
      'email' => $subscriberData['email'],
      'name' => (isset($subscriberData['name'])) ? $subscriberData['name'] : '',
    ];
  }
}
