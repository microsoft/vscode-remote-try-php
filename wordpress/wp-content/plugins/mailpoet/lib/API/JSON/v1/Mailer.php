<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\API\JSON\v1;

if (!defined('ABSPATH')) exit;


use MailPoet\API\JSON\Endpoint as APIEndpoint;
use MailPoet\API\JSON\Error as APIError;
use MailPoet\Config\AccessControl;
use MailPoet\Mailer\MailerFactory;
use MailPoet\Mailer\MailerLog;
use MailPoet\Mailer\MetaInfo;
use MailPoet\Services\AuthorizedEmailsController;
use MailPoet\Services\AuthorizedSenderDomainController;
use MailPoet\Services\Bridge;
use MailPoet\Settings\SettingsController;

class Mailer extends APIEndpoint {

  /** @var AuthorizedEmailsController */
  private $authorizedEmailsController;

  /** @var Bridge */
  private $bridge;

  /** @var SettingsController */
  private $settings;

  /** @var MetaInfo */
  private $mailerMetaInfo;

  /** @var MailerFactory */
  private $mailerFactory;

  /** @var AuthorizedSenderDomainController */
  private $senderDomainController;

  public $permissions = [
    'global' => AccessControl::PERMISSION_MANAGE_EMAILS,
  ];

  public function __construct(
    AuthorizedEmailsController $authorizedEmailsController,
    SettingsController $settings,
    Bridge $bridge,
    MailerFactory $mailerFactory,
    MetaInfo $mailerMetaInfo,
    AuthorizedSenderDomainController $senderDomainController
  ) {
    $this->authorizedEmailsController = $authorizedEmailsController;
    $this->settings = $settings;
    $this->bridge = $bridge;
    $this->mailerFactory = $mailerFactory;
    $this->mailerMetaInfo = $mailerMetaInfo;
    $this->senderDomainController = $senderDomainController;
  }

  public function send($data = []) {
    try {
      $mailer = $this->mailerFactory->buildMailer(
        $data['mailer'] ?? null,
        $data['sender'] ?? null,
        $data['reply_to'] ?? null
      );
      // report this as 'sending_test' in metadata since this endpoint is only used to test sending methods for now
      $extraParams = [
        'meta' => $this->mailerMetaInfo->getSendingTestMetaInfo(),
      ];
      $result = $mailer->send($data['newsletter'], $data['subscriber'], $extraParams);
    } catch (\Exception $e) {
      return $this->errorResponse([
        $e->getCode() => $e->getMessage(),
      ]);
    }

    if ($result['response'] === false) {
      $error = sprintf(
        // translators: %s is the error message.
        __('The email could not be sent: %s', 'mailpoet'),
        $result['error']->getMessage()
      );
      return $this->errorResponse([APIError::BAD_REQUEST => $error]);
    } else {
      return $this->successResponse(null);
    }
  }

  public function resumeSending() {
    if ($this->settings->get(AuthorizedEmailsController::AUTHORIZED_EMAIL_ADDRESSES_ERROR_SETTING)) {
      $this->authorizedEmailsController->checkAuthorizedEmailAddresses();
    }
    MailerLog::resumeSending();
    return $this->successResponse(null);
  }

  public function getAuthorizedEmailAddresses() {
    $authorizedEmails = $this->bridge->getAuthorizedEmailAddresses();
    return $this->successResponse($authorizedEmails);
  }

  public function getVerifiedSenderDomains() {
    $verifiedDomains = $this->senderDomainController->getVerifiedSenderDomains();
    return $this->successResponse($verifiedDomains);
  }
}
