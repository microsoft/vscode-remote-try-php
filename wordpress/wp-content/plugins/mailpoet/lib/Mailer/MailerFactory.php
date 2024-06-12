<?php declare(strict_types = 1);

namespace MailPoet\Mailer;

if (!defined('ABSPATH')) exit;


use MailPoet\DI\ContainerWrapper;
use MailPoet\InvalidStateException;
use MailPoet\Mailer\Methods\AmazonSES;
use MailPoet\Mailer\Methods\ErrorMappers\AmazonSESMapper;
use MailPoet\Mailer\Methods\ErrorMappers\MailPoetMapper;
use MailPoet\Mailer\Methods\ErrorMappers\PHPMailMapper;
use MailPoet\Mailer\Methods\ErrorMappers\SendGridMapper;
use MailPoet\Mailer\Methods\ErrorMappers\SMTPMapper;
use MailPoet\Mailer\Methods\MailPoet;
use MailPoet\Mailer\Methods\PHPMail;
use MailPoet\Mailer\Methods\SendGrid;
use MailPoet\Mailer\Methods\SMTP;
use MailPoet\Services\AuthorizedEmailsController;
use MailPoet\Services\Bridge;
use MailPoet\Settings\SettingsController;
use MailPoet\Util\Url;
use MailPoet\WP\Functions as WPFunctions;

class MailerFactory {
  /** @var SettingsController */
  private $settings;

  /** @var WPFunctions */
  private $wp;

  /** @var Mailer */
  private $defaultMailer;

  public function __construct(
    SettingsController $settings,
    WPFunctions $wp
  ) {
    $this->settings = $settings;
    $this->wp = $wp;
  }

  public function getDefaultMailer(): Mailer {
    if ($this->defaultMailer === null) {
      $this->defaultMailer = $this->buildMailer();
    }
    return $this->defaultMailer;
  }

  public function buildMailer(array $mailerConfig = null, array $sender = null, array $replyTo = null, string $returnPath = null): Mailer {
    $sender = $this->getSenderNameAndAddress($sender);
    $replyTo = $this->getReplyToNameAndAddress($sender, $replyTo);
    $mailerConfig = $mailerConfig ?? $this->getMailerConfig();
    $returnPath = $returnPath ?? $this->getReturnPathAddress($sender);
    switch ($mailerConfig['method']) {
      case Mailer::METHOD_AMAZONSES:
        $mailerMethod = new AmazonSES(
          $mailerConfig['region'],
          $mailerConfig['access_key'],
          $mailerConfig['secret_key'],
          $sender,
          $replyTo,
          $returnPath,
          new AmazonSESMapper(),
          $this->wp
        );
        break;
      case Mailer::METHOD_MAILPOET:
        $mailerMethod = new MailPoet(
          $mailerConfig['mailpoet_api_key'],
          $sender,
          $replyTo,
          ContainerWrapper::getInstance()->get(MailPoetMapper::class),
          ContainerWrapper::getInstance()->get(AuthorizedEmailsController::class),
          ContainerWrapper::getInstance()->get(Bridge::class),
          ContainerWrapper::getInstance()->get(Url::class)
        );
        break;
      case Mailer::METHOD_SENDGRID:
        $mailerMethod = new SendGrid(
          $mailerConfig['api_key'],
          $sender,
          $replyTo,
          new SendGridMapper()
        );
        break;
      case Mailer::METHOD_PHPMAIL:
        $mailerMethod = new PHPMail(
          $sender,
          $replyTo,
          $returnPath,
          new PHPMailMapper()
        );
        break;
      case Mailer::METHOD_SMTP:
        $mailerMethod = new SMTP(
          $mailerConfig['host'],
          $mailerConfig['port'],
          (int)$mailerConfig['authentication'],
          $mailerConfig['encryption'],
          $sender,
          $replyTo,
          $returnPath,
          new SMTPMapper(),
          $mailerConfig['login'],
          $mailerConfig['password']
        );
        break;
      default:
        throw new InvalidStateException(__('Mailing method does not exist.', 'mailpoet'));
    }
    return new Mailer($mailerMethod);
  }

  private function getMailerConfig(): array {
    $config = $this->settings->get(Mailer::MAILER_CONFIG_SETTING_NAME);
    if (!$config || !isset($config['method'])) throw new InvalidStateException(__('Mailer is not configured.', 'mailpoet'));
    return $config;
  }

  private function getSenderNameAndAddress(array $sender = null): array {
    if (empty($sender)) {
      $sender = $this->settings->get('sender', []);
      if (empty($sender['address'])) throw new InvalidStateException(__('Sender name and email are not configured.', 'mailpoet'));
    }
    $fromName = $this->encodeAddressNamePart($sender['name'] ?? '');
    return [
      'from_name' => $fromName,
      'from_email' => $sender['address'],
      'from_name_email' => sprintf('%s <%s>', $fromName, $sender['address']),
    ];
  }

  private function getReplyToNameAndAddress(array $sender, array $replyTo = null): array {
    if (!$replyTo) {
      $replyTo = $this->settings->get('reply_to');
      $replyTo['name'] = (!empty($replyTo['name'])) ?
        $replyTo['name'] :
        $sender['from_name'];
      $replyTo['address'] = (!empty($replyTo['address'])) ?
        $replyTo['address'] :
        $sender['from_email'];
    }
    if (empty($replyTo['address'])) {
      $replyTo['address'] = $sender['from_email'];
    }
    $replyToName = $this->encodeAddressNamePart($replyTo['name'] ?? '');
    return [
      'reply_to_name' => $replyToName,
      'reply_to_email' => $replyTo['address'],
      'reply_to_name_email' => sprintf('%s <%s>', $replyToName, $replyTo['address']),
    ];
  }

  private function getReturnPathAddress(array $sender): ?string {
    $bounceAddress = (string)$this->settings->get('bounce.address');
    return $this->wp->isEmail($bounceAddress) ? $bounceAddress : $sender['from_email'];
  }

  private function encodeAddressNamePart($name): string {
    if (mb_detect_encoding($name) === 'ASCII') return $name;
    // encode non-ASCII string as per RFC 2047 (https://www.ietf.org/rfc/rfc2047.txt)
    return sprintf('=?utf-8?B?%s?=', base64_encode($name));
  }
}
