<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Mailer\Methods\ErrorMappers;

if (!defined('ABSPATH')) exit;


use MailPoet\Mailer\Mailer;
use MailPoet\Mailer\MailerError;
use MailPoet\Mailer\SubscriberError;

class SendGridMapper {
  use BlacklistErrorMapperTrait;
  use ConnectionErrorMapperTrait;

  const METHOD = Mailer::METHOD_SENDGRID;

  public function getErrorFromResponse($response, $subscriber) {
    $response = (!empty($response['errors'][0])) ?
      $response['errors'][0] :
      // translators: %s is the name of the method.
      sprintf(__('%s has returned an unknown error.', 'mailpoet'), Mailer::METHOD_SENDGRID);

    $level = MailerError::LEVEL_HARD;
    if (strpos($response, 'Invalid email address') === 0) {
      $level = MailerError::LEVEL_SOFT;
    }
    $subscriberErrors = [new SubscriberError($subscriber, null)];
    return new MailerError(MailerError::OPERATION_SEND, $level, $response, null, $subscriberErrors);
  }
}
