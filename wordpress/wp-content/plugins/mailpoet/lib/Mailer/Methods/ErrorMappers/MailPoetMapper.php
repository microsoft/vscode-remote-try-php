<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Mailer\Methods\ErrorMappers;

if (!defined('ABSPATH')) exit;


use InvalidArgumentException;
use MailPoet\Config\ServicesChecker;
use MailPoet\Mailer\Mailer;
use MailPoet\Mailer\MailerError;
use MailPoet\Mailer\SubscriberError;
use MailPoet\Services\Bridge\API;
use MailPoet\Util\Helpers;
use MailPoet\Util\License\Features\Subscribers as SubscribersFeature;
use MailPoet\Util\Notices\PendingApprovalNotice;
use MailPoet\Util\Notices\UnauthorizedEmailNotice;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Carbon\Carbon;

class MailPoetMapper {
  use BlacklistErrorMapperTrait;
  use ConnectionErrorMapperTrait;

  const METHOD = Mailer::METHOD_MAILPOET;

  const TEMPORARY_UNAVAILABLE_RETRY_INTERVAL = 300; // seconds

  /** @var ServicesChecker */
  private $servicesChecker;

  /** @var SubscribersFeature */
  private $subscribersFeature;

  /** @var WPFunctions */
  private $wp;

  /** @var PendingApprovalNotice */
  private $pendingApprovalNotice;

  public function __construct(
    ServicesChecker $servicesChecker,
    SubscribersFeature $subscribers,
    WPFunctions $wp,
    PendingApprovalNotice $pendingApprovalNotice
  ) {
    $this->servicesChecker = $servicesChecker;
    $this->subscribersFeature = $subscribers;
    $this->wp = $wp;
    $this->pendingApprovalNotice = $pendingApprovalNotice;
  }

  public function getInvalidApiKeyError() {
    return new MailerError(
      MailerError::OPERATION_SEND,
      MailerError::LEVEL_HARD,
      __('MailPoet API key is invalid!', 'mailpoet')
    );
  }

  public function getErrorForResult(array $result, $subscribers, $sender = null, $newsletter = null) {
    $level = MailerError::LEVEL_HARD;
    $operation = MailerError::OPERATION_SEND;
    $retryInterval = null;
    $subscribersErrors = [];
    $resultCode = !empty($result['code']) ? $result['code'] : null;

    switch ($resultCode) {
      case API::RESPONSE_CODE_NOT_ARRAY:
        $message = __('JSON input is not an array', 'mailpoet');
        break;
      case API::RESPONSE_CODE_PAYLOAD_ERROR:
        $resultParsed = json_decode($result['message'], true);
        $message = __('Error while sending.', 'mailpoet');

        if (is_array($resultParsed)) {
          try {
            $subscribersErrors = $this->getSubscribersErrors($resultParsed, $subscribers);
            $level = MailerError::LEVEL_SOFT;
          } catch (InvalidArgumentException $e) {
            $message .= ' ' . $e->getMessage();
          }
          break;
        }

        $appendedMessage = ' ' . $result['message'];
        if (isset($result['error']) && in_array($result['error'], [API::ERROR_MESSAGE_DMRAC, API::ERROR_MESSAGE_BULK_EMAIL_FORBIDDEN])) {
            $appendedMessage = $this->getDmarcMessage($result, $sender);

          if ($result['error'] === API::ERROR_MESSAGE_BULK_EMAIL_FORBIDDEN) {
            $operation = MailerError::OPERATION_DOMAIN_AUTHORIZATION;
            $level = MailerError::LEVEL_SOFT;
          }
        }
        $message .= $appendedMessage;
        break;
      case API::RESPONSE_CODE_INTERNAL_SERVER_ERROR:
      case API::RESPONSE_CODE_BAD_GATEWAY:
      case API::RESPONSE_CODE_TEMPORARY_UNAVAILABLE:
      case API::RESPONSE_CODE_GATEWAY_TIMEOUT:
        $message = __('Email service is temporarily not available, please try again in a few minutes.', 'mailpoet');
        $retryInterval = self::TEMPORARY_UNAVAILABLE_RETRY_INTERVAL;
        break;
      case API::RESPONSE_CODE_CAN_NOT_SEND:
        [$operation, $message] = $this->getCanNotSendError($result, $sender);
        break;
      case API::RESPONSE_CODE_KEY_INVALID:
      case API::RESPONSE_CODE_PAYLOAD_TOO_BIG:
      default:
        $message = $result['message'];
    }
    return new MailerError($operation, $level, $message, $retryInterval, $subscribersErrors);
  }

  private function getSubscribersErrors($resultParsed, $subscribers) {
    $errors = [];
    foreach ($resultParsed as $resultError) {
      if (!is_array($resultError) || !isset($resultError['index']) || !isset($subscribers[$resultError['index']])) {
        throw new InvalidArgumentException(__('Invalid MSS response format.', 'mailpoet'));
      }
      $subscriberErrors = [];
      if (isset($resultError['errors']) && is_array($resultError['errors'])) {
        array_walk_recursive($resultError['errors'], function($item) use (&$subscriberErrors) {
          $subscriberErrors[] = $item;
        });
      }
      $message = join(', ', $subscriberErrors);
      $errors[] = new SubscriberError($subscribers[$resultError['index']], $message);
    }
    return $errors;
  }

  private function getUnauthorizedEmailMessage($sender) {
    $email = $sender ? $sender['from_email'] : __('Unknown address', 'mailpoet');
    $validationError = ['invalid_sender_address' => $email];
    $notice = new UnauthorizedEmailNotice($this->wp, null);
    $message = $notice->getMessage($validationError);
    return $message;
  }

  private function getSubscribersLimitReachedMessage(): string {
    $message = __('You have reached the subscriber limit of your plan. Please [link1]upgrade your plan[/link1], or [link2]contact our support team[/link2] if you have any questions.', 'mailpoet');
    $message = Helpers::replaceLinkTags(
      $message,
      'https://account.mailpoet.com/account/',
      [
        'target' => '_blank',
        'rel' => 'noopener noreferrer',
      ],
      'link1'
    );
    $message = Helpers::replaceLinkTags(
      $message,
      'https://www.mailpoet.com/support/',
      [
        'target' => '_blank',
        'rel' => 'noopener noreferrer',
      ],
      'link2'
    );

    return "{$message}<br/>";
  }

  private function getAccountBannedMessage(): string {
    $message = __('MailPoet Sending Service has been temporarily suspended for your site due to [link1]degraded email deliverability[/link1]. Please [link2]contact our support team[/link2] to resolve the issue.', 'mailpoet');
    $message = Helpers::replaceLinkTags(
      $message,
      'https://kb.mailpoet.com/article/231-sending-does-not-work#suspended',
      [
        'target' => '_blank',
        'rel' => 'noopener noreferrer',
      ],
      'link1'
    );
    $message = Helpers::replaceLinkTags(
      $message,
      'https://www.mailpoet.com/support-for-banned-users/',
      [
        'target' => '_blank',
        'rel' => 'noopener noreferrer',
      ],
      'link2'
    );

    return "{$message}<br/>";
  }

  private function getDmarcMessage($result, $sender): string {
    $messageToAppend = __('[link1]Click here to start the authentication[/link1].', 'mailpoet');
    $senderEmail = $sender['from_email'] ?? '';

    $appendMessage = Helpers::replaceLinkTags(
      $messageToAppend,
      '#',
      [
        'class' => 'mailpoet-js-button-authorize-email-and-sender-domain',
        'data-email' => $senderEmail,
        'data-type' => 'domain',
        'rel' => 'noopener noreferrer',
      ],
      'link1'
    );
    $final = ' ' . $result['message'] . ' ' . $appendMessage;
    return $final;
  }

  private function getEmailVolumeLimitReachedMessage(): string {
    $partialApiKey = $this->servicesChecker->generatePartialApiKey();
    $emailVolumeLimit = $this->subscribersFeature->getEmailVolumeLimit();
    $date = Carbon::now()->startOfMonth()->addMonth();
    if ($emailVolumeLimit) {
      $message = sprintf(
      // translators: %1$s is email volume limit and %2$s the date when you can resume sending.
        __('You have sent more emails this month than your MailPoet plan includes (%1$s), and sending has been temporarily paused. To continue sending with MailPoet Sending Service please [link]upgrade your plan[/link], or wait until sending is automatically resumed on %2$s.', 'mailpoet'),
        $emailVolumeLimit,
        $this->wp->dateI18n($this->wp->getOption('date_format'), $date->getTimestamp())
      );
    } else {
      $message = sprintf(
        // translators: %1$s the date when you can resume sending.
        __('You have sent more emails this month than your MailPoet plan includes, and sending has been temporarily paused. To continue sending with MailPoet Sending Service please [link]upgrade your plan[/link], or wait until sending is automatically resumed on %1$s.', 'mailpoet'),
        $this->wp->dateI18n($this->wp->getOption('date_format'), $date->getTimestamp())
      );
    }

    $message = Helpers::replaceLinkTags(
      $message,
      "https://account.mailpoet.com/orders/upgrade/{$partialApiKey}",
      [
        'target' => '_blank',
        'rel' => 'noopener noreferrer',
      ]
    );

    return "{$message}<br/>";
  }

  /**
   * Returns error $message and $operation for API::RESPONSE_CODE_CAN_NOT_SEND
   */
  private function getCanNotSendError(array $result, $sender): array {
    if ($result['error'] === API::ERROR_MESSAGE_PENDING_APPROVAL) {
      $operation = MailerError::OPERATION_PENDING_APPROVAL;
      $message = $this->pendingApprovalNotice->getPendingApprovalMessage() . '<br/>';
      return [$operation, $message];
    }

    // Backward compatibility for older blocked keys.
    // Exceeded subscribers limit used to use the same error message as insufficient privileges.
    // We can change the message to "Insufficient privileges" like wording a couple of months after releasing SHOP-1228
    if ($result['error'] === API::ERROR_MESSAGE_INSUFFICIENT_PRIVILEGES) {
      $operation = MailerError::OPERATION_INSUFFICIENT_PRIVILEGES;
      $message = $this->getSubscribersLimitReachedMessage();
      return [$operation, $message];
    }

    if ($result['error'] === API::ERROR_MESSAGE_SUBSCRIBERS_LIMIT_REACHED) {
      $operation = MailerError::OPERATION_SUBSCRIBER_LIMIT_REACHED;
      $message = $this->getSubscribersLimitReachedMessage();
      return [$operation, $message];
    }

    if ($result['error'] === API::ERROR_MESSAGE_EMAIL_VOLUME_LIMIT_REACHED) {
      $operation = MailerError::OPERATION_EMAIL_LIMIT_REACHED;
      $message = $this->getEmailVolumeLimitReachedMessage();
      return [$operation, $message];
    }

    if ($result['error'] === API::ERROR_MESSAGE_INVALID_FROM) {
      $operation = MailerError::OPERATION_AUTHORIZATION;
      $message = $this->getUnauthorizedEmailMessage($sender);
      return [$operation, $message];
    }

    $message = $this->getAccountBannedMessage();
    $operation = MailerError::OPERATION_SEND;
    return [$operation, $message];
  }
}
