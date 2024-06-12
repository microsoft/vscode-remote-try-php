<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\MailPoet\Fields;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Field;
use MailPoet\Automation\Integrations\MailPoet\Payloads\NewsletterLinkPayload;

class NewsletterLinkFieldsFactory {
  public function getFields(): array {
    return [
      new Field(
        'mailpoet:email-link:url',
        Field::TYPE_STRING,
        __('Link URL', 'mailpoet'),
        function(NewsletterLinkPayload $payload) {
          return $payload->getLink()->getUrl();
        }
      ),
      new Field(
        'mailpoet:email-link:created',
        Field::TYPE_DATETIME,
        __('Created', 'mailpoet'),
        function(NewsletterLinkPayload $payload) {
          return $payload->getLink()->getCreatedAt();
        }
      ),
      new Field(
        'mailpoet:email-link:id',
        Field::TYPE_INTEGER,
        __('Link ID', 'mailpoet'),
        function(NewsletterLinkPayload $payload) {
          return $payload->getLink()->getId();
        }
      ),
    ];
  }
}
