<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Mailer\Methods;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\ServicesChecker;
use MailPoet\Mailer\Mailer;
use MailPoet\Mailer\Methods\Common\BlacklistCheck;
use MailPoet\Mailer\Methods\ErrorMappers\MailPoetMapper;
use MailPoet\Services\AuthorizedEmailsController;
use MailPoet\Services\Bridge;
use MailPoet\Services\Bridge\API;
use MailPoet\Util\Url;

class MailPoet implements MailerMethod {
  public $api;
  public $sender;
  public $replyTo;
  public $servicesChecker;

  /** @var AuthorizedEmailsController */
  private $authorizedEmailsController;

  /** @var MailPoetMapper */
  private $errorMapper;

  /** @var BlacklistCheck */
  private $blacklist;

  /*** @var Url */
  private $url;

  /** @var Bridge */
  private $bridge;

  public function __construct(
    $apiKey,
    $sender,
    $replyTo,
    MailPoetMapper $errorMapper,
    AuthorizedEmailsController $authorizedEmailsController,
    Bridge $bridge,
    Url $url
  ) {
    $this->api = new API($apiKey);
    $this->sender = $sender;
    $this->replyTo = $replyTo;
    $this->servicesChecker = new ServicesChecker();
    $this->errorMapper = $errorMapper;
    $this->bridge = $bridge;
    $this->authorizedEmailsController = $authorizedEmailsController;
    $this->blacklist = new BlacklistCheck();
    $this->url = $url;
  }

  public function send($newsletter, $subscriber, $extraParams = []): array {
    if ($this->servicesChecker->isMailPoetAPIKeyValid() === false) {
      return Mailer::formatMailerErrorResult($this->errorMapper->getInvalidApiKeyError());
    }

    $subscribersForBlacklistCheck = is_array($subscriber) ? $subscriber : [$subscriber];
    foreach ($subscribersForBlacklistCheck as $sub) {
      if ($this->blacklist->isBlacklisted($sub)) {
        $error = $this->errorMapper->getBlacklistError($sub);
        return Mailer::formatMailerErrorResult($error);
      }
    }

    $messageBody = $this->getBody($newsletter, $subscriber, $extraParams);
    $result = $this->api->sendMessages($messageBody);

    switch ($result['status']) {
      case API::SENDING_STATUS_CONNECTION_ERROR:
        $error = $this->errorMapper->getConnectionError($result['message']);
        return Mailer::formatMailerErrorResult($error);
      case API::SENDING_STATUS_SEND_ERROR:
        $error = $this->processSendError($result, $subscriber, $newsletter);
        return Mailer::formatMailerErrorResult($error);
      case API::RESPONSE_STATUS_OK:
      default:
        return Mailer::formatMailerSendSuccessResult();
    }
  }

  public function processSendError($result, $subscriber, $newsletter) {
    if (empty($result['code'])) {
      return $this->errorMapper->getErrorForResult($result, $subscriber, $this->sender, $newsletter);
    }

    switch ($result['code']) {
      case API::RESPONSE_CODE_KEY_INVALID:
        $this->bridge->invalidateMssKey();
        break;

      case API::RESPONSE_CODE_CAN_NOT_SEND:
        if ($result['error'] === API::ERROR_MESSAGE_INVALID_FROM) {
          $this->authorizedEmailsController->checkAuthorizedEmailAddresses();
        }
        break;

      case API::RESPONSE_CODE_PAYLOAD_ERROR:
        if (!empty($result['error']) && $result['error'] === API::ERROR_MESSAGE_BULK_EMAIL_FORBIDDEN) {
          $this->authorizedEmailsController->checkAuthorizedEmailAddresses();
        }
        break;
    }

    return $this->errorMapper->getErrorForResult($result, $subscriber, $this->sender, $newsletter);
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

  public function getBody($newsletter, $subscriber, $extraParams = []) {
    if (is_array($newsletter) && is_array($subscriber)) {
      $body = [];
      for ($record = 0; $record < count($newsletter); $record++) {
        $body[] = $this->composeBody(
          $newsletter[$record],
          $this->processSubscriber($subscriber[$record]),
          (!empty($extraParams['unsubscribe_url'][$record])) ? $extraParams['unsubscribe_url'][$record] : false,
          (!empty($extraParams['one_click_unsubscribe'][$record])) ? $extraParams['one_click_unsubscribe'][$record] : false,
          (!empty($extraParams['meta'][$record])) ? $extraParams['meta'][$record] : false
        );
      }
    } else {
      $body[] = $this->composeBody(
        $newsletter,
        $this->processSubscriber($subscriber),
        (!empty($extraParams['unsubscribe_url'])) ? $extraParams['unsubscribe_url'] : false,
        (!empty($extraParams['one_click_unsubscribe'])) ? $extraParams['one_click_unsubscribe'] : false,
        (!empty($extraParams['meta'])) ? $extraParams['meta'] : false
      );
    }
    return $body;
  }

  private function composeBody($newsletter, $subscriber, $unsubscribeUrl, $oneClickUnsubscribeUrl, $meta): array {
    $body = [
      'to' => ([
        'address' => $subscriber['email'],
        'name' => $subscriber['name'],
      ]),
      'from' => ([
        'address' => $this->sender['from_email'],
        'name' => $this->sender['from_name'],
      ]),
      'reply_to' => ([
        'address' => $this->replyTo['reply_to_email'],
      ]),
      'subject' => $newsletter['subject'],
    ];
    if (!empty($this->replyTo['reply_to_name'])) {
      $body['reply_to']['name'] = $this->replyTo['reply_to_name'];
    }
    if (!empty($newsletter['body']['html'])) {
      $body['html'] = $newsletter['body']['html'];
    }
    if (!empty($newsletter['body']['text'])) {
      $body['text'] = $newsletter['body']['text'];
    }
    if ($unsubscribeUrl) {
      $isHttps = $this->url->isUsingHttps($unsubscribeUrl);
      $body['unsubscribe'] = [
        'url' => $isHttps && $oneClickUnsubscribeUrl ? $oneClickUnsubscribeUrl : $unsubscribeUrl,
        'post' => $isHttps,
      ];
    }
    if ($meta) {
      $body['meta'] = $meta;
    }
    return $body;
  }
}
