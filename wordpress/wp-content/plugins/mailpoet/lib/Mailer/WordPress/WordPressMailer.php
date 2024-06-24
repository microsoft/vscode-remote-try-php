<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Mailer\WordPress;

if (!defined('ABSPATH')) exit;


use MailPoet\Mailer\MailerFactory;
use MailPoet\Mailer\MetaInfo;
use MailPoet\Subscribers\SubscribersRepository;
use MailPoetVendor\Html2Text\Html2Text;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;

PHPMailerLoader::load();

class WordPressMailer extends PHPMailer {
  /** @var MailerFactory */
  private $mailerFactory;

  /** @var MetaInfo */
  private $mailerMetaInfo;

  /** @var SubscribersRepository */
  private $subscribersRepository;

  public function __construct(
    MailerFactory $mailerFactory,
    MetaInfo $mailerMetaInfo,
    SubscribersRepository $subscribersRepository
  ) {
    parent::__construct(true);
    $this->mailerFactory = $mailerFactory;
    $this->mailerMetaInfo = $mailerMetaInfo;
    $this->subscribersRepository = $subscribersRepository;
  }

  public function send() {
    // We need this so that the PHPMailer class will correctly prepare all the headers.
    $originalMailer = $this->Mailer; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    $this->Mailer = 'mail'; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

    // Prepare everything (including the message) for sending.
    $this->preSend();

    $email = $this->getEmail();
    $address = $this->formatAddress($this->getToAddresses());
    $subscriber = $this->subscribersRepository->findOneBy(['email' => $address]);
    $extraParams = [
      'meta' => $this->mailerMetaInfo->getWordPressTransactionalMetaInfo($subscriber),
    ];

    try {
      // we need to build fresh mailer for every single WP e-mail to make sure reply-to is set
      $replyTo = $this->getReplyToAddress();
      $mailer = $this->mailerFactory->buildMailer(null, null, $replyTo);
      $result = $mailer->send($email, $address, $extraParams);
      if (!$result['response']) {
        throw new \Exception($result['error']->getMessage());
      }
    } catch (\Exception $ePrimary) {
      // In case the sending using MailPoet's mailer fails continue with sending using original parent PHPMailer::sent method.
      // But if anything fails we still want tho throw the error from the primary MailPoet mailer.
      try {
        // Restore original settings for mailer. Some sites may use SMTP and we needed to reset it to mail
        $this->Mailer = $originalMailer; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        return parent::send();
      } catch (\Exception $eFallback) {
        throw new PHPMailerException($ePrimary->getMessage(), $ePrimary->getCode(), $ePrimary);
      }
    }
    return true;
  }

  private function getEmail() {
    // phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    $email = [
      'subject' => $this->Subject,
      'body' => [],
    ];

    if (strpos($this->ContentType, 'text/plain') === 0) {
      $email['body']['text'] = $this->Body;
    } elseif (strpos($this->ContentType, 'text/html') === 0) {
      $text = @Html2Text::convert(strtolower($this->CharSet) === 'utf-8' ? $this->Body : mb_convert_encoding($this->Body, 'UTF-8', mb_list_encodings()));
      $email['body']['text'] = $text;
      $email['body']['html'] = $this->Body;
    } elseif (strpos($this->ContentType, 'multipart/alternative') === 0) {
      $email['body']['text'] = $this->AltBody;
      $email['body']['html'] = $this->Body;
    } else {
      throw new PHPMailerException('Unsupported email content type has been used. Please use only text or HTML emails.');
    }
    return $email;
    // phpcs:enable
  }

  private function formatAddress($wordpressAddress) {
    $data = $wordpressAddress[0];
    $result = [
      'address' => $data[0],
    ];
    if (!empty($data[1])) {
      $result['full_name'] = $data[1];
    }
    return $result;
  }

  private function getReplyToAddress(): ?array {
    $replyToAddress = null;
    $addresses = $this->getReplyToAddresses();

    if (!empty($addresses)) {
      // only one reply-to address supported by \MailPoet\Mailer
      $address = array_shift($addresses);
      $replyToAddress = [];

      if ($address[1]) {
        $replyToAddress['name'] = $address[1];
      }

      if ($address[0]) {
        $replyToAddress['address'] = $address[0];
      }
    }

    return $replyToAddress;
  }
}
