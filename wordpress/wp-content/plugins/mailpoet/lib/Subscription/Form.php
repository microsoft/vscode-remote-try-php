<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Subscription;

if (!defined('ABSPATH')) exit;


use MailPoet\API\JSON\API;
use MailPoet\API\JSON\Endpoint;
use MailPoet\API\JSON\Response as APIResponse;
use MailPoet\Util\Url as UrlHelper;

class Form {

  /** @var API */
  private $api;

  /** @var UrlHelper */
  private $urlHelper;

  public function __construct(
    API $api,
    UrlHelper $urlHelper
  ) {
    $this->api = $api;
    $this->urlHelper = $urlHelper;
  }

  public function onSubmit($requestData = false) {
    $requestData = ($requestData) ? $requestData : $_REQUEST;
    $this->api->setRequestData($requestData, Endpoint::TYPE_POST);
    $formId = (!empty($requestData['data']['form_id'])) ? (int)$requestData['data']['form_id'] : false;
    $response = $this->api->processRoute();
    if ($response->status !== APIResponse::STATUS_OK) {
      return (isset($response->meta['redirect_url'])) ?
      $this->urlHelper->redirectTo($response->meta['redirect_url']) :
      $this->urlHelper->redirectBack(
        [
          'mailpoet_error' => ($formId) ? $formId : true,
          'mailpoet_success' => null,
        ]
      );
    } else {
      return (isset($response->meta['redirect_url'])) ?
        $this->urlHelper->redirectTo($response->meta['redirect_url']) :
        $this->urlHelper->redirectBack(
          [
            'mailpoet_success' => $formId,
            'mailpoet_error' => null,
          ]
        );
    }
  }
}
