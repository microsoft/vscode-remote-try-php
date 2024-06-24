<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\MailPoet\Payloads;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Integration\Payload;
use MailPoet\Entities\NewsletterLinkEntity;

class NewsletterLinkPayload implements Payload {


  /** @var NewsletterLinkEntity */
  private $linkEntity;

  public function __construct(
    NewsletterLinkEntity $linkEntity
  ) {
    $this->linkEntity = $linkEntity;
  }

  public function getId(): ?int {
    return $this->linkEntity->getId();
  }

  public function getLink(): NewsletterLinkEntity {
    return $this->linkEntity;
  }
}
