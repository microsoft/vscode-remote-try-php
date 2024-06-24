<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Mailer\Methods\ErrorMappers;

if (!defined('ABSPATH')) exit;


use MailPoet\Mailer\MailerError;
use MailPoet\Mailer\SubscriberError;

trait BlacklistErrorMapperTrait {
  public function getBlacklistError($subscriber) {
    // translators: %s is the name of the method.
    $message = sprintf(__('%s has returned an unknown error.', 'mailpoet'), self::METHOD);
    $subscriberErrors = [new SubscriberError($subscriber, null)];
    return new MailerError(MailerError::OPERATION_SEND, MailerError::LEVEL_SOFT, $message, null, $subscriberErrors);
  }
}
