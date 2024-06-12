<?php declare(strict_types = 1);

namespace MailPoet\Mailer\Methods\ErrorMappers;

if (!defined('ABSPATH')) exit;


use MailPoet\Mailer\MailerError;
use MailPoet\Mailer\SubscriberError;

abstract class PHPMailerMapper {
  use ConnectionErrorMapperTrait;

  public function getErrorFromException(\Exception $e, $subscriber) {
    $level = MailerError::LEVEL_HARD;
    if (strpos($e->getMessage(), 'Invalid address') === 0) {
      $level = MailerError::LEVEL_SOFT;
    }

    $subscriberErrors = [new SubscriberError($subscriber, null)];
    return new MailerError(MailerError::OPERATION_SEND, $level, $e->getMessage(), null, $subscriberErrors);
  }

  public function getErrorForSubscriber($subscriber) {
    // translators: %s is the name of the method.
    $message = sprintf(__('%s has returned an unknown error.', 'mailpoet'), $this->getMethodName());
    $subscriberErrors = [new SubscriberError($subscriber, null)];
    return new MailerError(MailerError::OPERATION_SEND, MailerError::LEVEL_HARD, $message, null, $subscriberErrors);
  }

  abstract protected function getMethodName(): string;
}
