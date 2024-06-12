<?php declare(strict_types = 1);

namespace MailPoet\Services;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\Renderer;
use MailPoet\Mailer\MailerFactory;
use MailPoet\Mailer\MetaInfo;

class CongratulatoryMssEmailController {
  /** @var MailerFactory */
  private $mailerFactory;

  /** @var MetaInfo */
  private $mailerMetaInfo;

  /** @var Renderer */
  private $renderer;

  public function __construct(
    MailerFactory $mailerFactory,
    MetaInfo $mailerMetaInfo,
    Renderer $renderer
  ) {
    $this->mailerFactory = $mailerFactory;
    $this->mailerMetaInfo = $mailerMetaInfo;
    $this->renderer = $renderer;
  }

  public function sendCongratulatoryEmail(string $toEmailAddress) {
    $renderedNewsletter = [
      'subject' => _x('Sending with MailPoet works!', 'Subject of an email confirming that MailPoet Sending Service works', 'mailpoet'),
      'body' => [
        'html' => $this->renderer->render('emails/congratulatoryMssEmail.html'),
        'text' => $this->renderer->render('emails/congratulatoryMssEmail.txt'),
      ],
    ];

    $extraParams = [
      'meta' => $this->mailerMetaInfo->getSendingTestMetaInfo(),
    ];
    $this->mailerFactory->getDefaultMailer()->send($renderedNewsletter, $toEmailAddress, $extraParams);
  }
}
