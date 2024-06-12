<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Services;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\NewsletterEntity;
use MailPoet\Mailer\Mailer;
use MailPoet\Mailer\MailerError;
use MailPoet\Mailer\MailerLog;
use MailPoet\Newsletter\NewslettersRepository;
use MailPoet\Services\Bridge\API;
use MailPoet\Settings\SettingsController;
use MailPoet\Util\Helpers;

class AuthorizedEmailsController {
  const AUTHORIZED_EMAIL_ADDRESSES_ERROR_SETTING = 'authorized_emails_addresses_check';

  const AUTHORIZED_EMAIL_ADDRESSES_API_TYPE_AUTHORIZED = 'authorized';
  const AUTHORIZED_EMAIL_ADDRESSES_API_TYPE_PENDING = 'pending';
  const AUTHORIZED_EMAIL_ADDRESSES_API_TYPE_ALL = 'all';
  const AUTHORIZED_EMAIL_ERROR_ALREADY_AUTHORIZED = 'Email address is already authorized';
  const AUTHORIZED_EMAIL_ERROR_PENDING_CONFIRMATION = 'Email address is pending confirmation';

  /** @var Bridge */
  private $bridge;

  /** @var SettingsController */
  private $settings;

  /** @var NewslettersRepository */
  private $newslettersRepository;

  /** @var AuthorizedSenderDomainController */
  private $senderDomainController;

  private $automaticEmailTypes = [
    NewsletterEntity::TYPE_WELCOME,
    NewsletterEntity::TYPE_NOTIFICATION,
    NewsletterEntity::TYPE_AUTOMATIC,
  ];

  public function __construct(
    SettingsController $settingsController,
    Bridge $bridge,
    NewslettersRepository $newslettersRepository,
    AuthorizedSenderDomainController $senderDomainController
  ) {
    $this->settings = $settingsController;
    $this->bridge = $bridge;
    $this->newslettersRepository = $newslettersRepository;
    $this->senderDomainController = $senderDomainController;
  }

  public function setFromEmailAddress(string $address) {
    $authorizedEmails = $this->bridge->getAuthorizedEmailAddresses() ?: [];
    $verifiedDomains = $this->senderDomainController->getVerifiedSenderDomainsIgnoringCache();
    $isAuthorized = $this->validateAuthorizedEmail($authorizedEmails, $address);

    $emailDomainIsVerified = $this->validateEmailDomainIsVerified($verifiedDomains, $address);

    if (!$emailDomainIsVerified && !$isAuthorized) {
      throw new \InvalidArgumentException("Email address '$address' is not authorized");
    }

    // update FROM address in settings & all scheduled and active emails
    $this->settings->set('sender.address', $address);
    $result = $this->validateAddressesInScheduledAndAutomaticEmails($authorizedEmails, $verifiedDomains);
    foreach ($result['invalid_senders_in_newsletters'] ?? [] as $item) {
      $newsletter = $this->newslettersRepository->findOneById((int)$item['newsletter_id']);
      if ($newsletter) {
        $newsletter->setSenderAddress($address);
      }
    }
    $this->newslettersRepository->flush();
    $this->settings->set(self::AUTHORIZED_EMAIL_ADDRESSES_ERROR_SETTING, null);
  }

  public function getAllAuthorizedEmailAddress(): array {
    return $this->bridge->getAuthorizedEmailAddresses(self::AUTHORIZED_EMAIL_ADDRESSES_API_TYPE_ALL);
  }

  public function createAuthorizedEmailAddress(string $email): array {
    $allEmails = $this->getAllAuthorizedEmailAddress();

    $authorizedEmails = isset($allEmails[self::AUTHORIZED_EMAIL_ADDRESSES_API_TYPE_AUTHORIZED]) ? $allEmails[self::AUTHORIZED_EMAIL_ADDRESSES_API_TYPE_AUTHORIZED] : [];
    $isAuthorized = $this->validateAuthorizedEmail($authorizedEmails, $email);

    if ($isAuthorized) {
      throw new \InvalidArgumentException(self::AUTHORIZED_EMAIL_ERROR_ALREADY_AUTHORIZED);
    }

    $pendingEmails = isset($allEmails[self::AUTHORIZED_EMAIL_ADDRESSES_API_TYPE_PENDING]) ? $allEmails[self::AUTHORIZED_EMAIL_ADDRESSES_API_TYPE_PENDING] : [];
    $isPending = $this->validateAuthorizedEmail($pendingEmails, $email);

    if ($isPending) {
      throw new \InvalidArgumentException(self::AUTHORIZED_EMAIL_ERROR_PENDING_CONFIRMATION);
    }

    $response = $this->bridge->createAuthorizedEmailAddress($email);
    if ($response['status'] === API::RESPONSE_STATUS_ERROR) {
      throw new \InvalidArgumentException($response['message']);
    }

    return $response;
  }

  public function isEmailAddressAuthorized(string $email): bool {
    $authorizedEmails = $this->bridge->getAuthorizedEmailAddresses() ?: [];
    return $this->validateAuthorizedEmail($authorizedEmails, $email);
  }

  public function checkAuthorizedEmailAddresses() {
    if (!Bridge::isMPSendingServiceEnabled()) {
      $this->settings->set(self::AUTHORIZED_EMAIL_ADDRESSES_ERROR_SETTING, null);
      $this->updateMailerLog();
      return null;
    }

    $authorizedEmails = $this->bridge->getAuthorizedEmailAddresses();
    // Keep previous check result for an invalid response from API
    if (!$authorizedEmails) {
      return null;
    }
    $authorizedEmails = array_map('strtolower', $authorizedEmails);

    $verifiedDomains = $this->senderDomainController->getVerifiedSenderDomainsIgnoringCache();

    $result = [];
    $result = $this->validateAddressesInSettings($authorizedEmails, $verifiedDomains, $result);
    $result = $this->validateAddressesInScheduledAndAutomaticEmails($authorizedEmails, $verifiedDomains, $result);
    $this->settings->set(self::AUTHORIZED_EMAIL_ADDRESSES_ERROR_SETTING, $result ?: null);
    $this->updateMailerLog($result);
    return $result;
  }

  public function onSettingsSave($settings): ?array {
    $senderAddressSet = !empty($settings['sender']['address']);
    $mailpoetSendingMethodSet = ($settings[Mailer::MAILER_CONFIG_SETTING_NAME]['method'] ?? null) === Mailer::METHOD_MAILPOET;
    if ($senderAddressSet || $mailpoetSendingMethodSet) {
      return $this->checkAuthorizedEmailAddresses();
    }
    return null;
  }

  public function onNewsletterSenderAddressUpdate(NewsletterEntity $newsletter, string $oldSenderAddress = null) {
    if ($newsletter->getSenderAddress() === $oldSenderAddress) {
      return;
    }
    if ($newsletter->getType() === NewsletterEntity::TYPE_STANDARD && $newsletter->getStatus() === NewsletterEntity::STATUS_SCHEDULED) {
      $this->checkAuthorizedEmailAddresses();
    }
    if (in_array($newsletter->getType(), $this->automaticEmailTypes, true) && $newsletter->getStatus() === NewsletterEntity::STATUS_ACTIVE) {
      $this->checkAuthorizedEmailAddresses();
    }
  }

  public function isSenderAddressValid(NewsletterEntity $newsletter, string $context = 'activation'): bool {
    if (!in_array($newsletter->getType(), NewsletterEntity::CAMPAIGN_TYPES)) {
      return true;
    }

    $isAuthorizedDomainRequired = $context === 'activation'
      ? $this->senderDomainController->isAuthorizedDomainRequiredForNewCampaigns()
      : $this->senderDomainController->isAuthorizedDomainRequiredForExistingCampaigns();

    if (!$isAuthorizedDomainRequired) {
      return true;
    }

    $verifiedDomains = $context === 'activation'
      ? $this->senderDomainController->getVerifiedSenderDomainsIgnoringCache()
      : $this->senderDomainController->getVerifiedSenderDomains();

    // The shop is not returning data, so we allow sending and let the Sending Service block the campaign if needed.
    if ($context === 'sending' && empty($verifiedDomains) && !$this->senderDomainController->isCacheAvailable()) {
      return true;
    }

    return $this->validateEmailDomainIsVerified($verifiedDomains, $newsletter->getSenderAddress());
  }

  private function validateAddressesInSettings($authorizedEmails, $verifiedDomains, $result = []) {
    $defaultSenderAddress = $this->settings->get('sender.address');

    if ($this->validateEmailDomainIsVerified($verifiedDomains, $defaultSenderAddress)) {
      // allow sending from any email address in a verified domain
      return $result;
    }

    if (!$this->validateAuthorizedEmail($authorizedEmails, $defaultSenderAddress)) {
      $result['invalid_sender_address'] = $defaultSenderAddress;
    }

    return $result;
  }

  private function validateAddressesInScheduledAndAutomaticEmails($authorizedEmails, $verifiedDomains, $result = []) {
    $newsletters = $this->newslettersRepository->getScheduledStandardEmailsAndActiveAutomaticEmails($this->automaticEmailTypes);

    $invalidSendersInNewsletters = [];
    foreach ($newsletters as $newsletter) {
      if ($this->validateAuthorizedEmail($authorizedEmails, $newsletter->getSenderAddress())) {
        continue;
      }
      if ($this->validateEmailDomainIsVerified($verifiedDomains, $newsletter->getSenderAddress())) {
        // allow sending from any email address in a verified domain
        continue;
      }
      $invalidSendersInNewsletters[] = [
        'newsletter_id' => $newsletter->getId(),
        'subject' => $newsletter->getSubject(),
        'sender_address' => $newsletter->getSenderAddress(),
      ];
    }

    if (!count($invalidSendersInNewsletters)) {
      return $result;
    }

    $result['invalid_senders_in_newsletters'] = $invalidSendersInNewsletters;
    return $result;
  }

  /**
   * @param array|null $error
   */
  private function updateMailerLog(array $error = null) {
    if ($error) {
      return;
    }
    $mailerLogError = MailerLog::getError();
    if ($mailerLogError && $mailerLogError['operation'] === MailerError::OPERATION_AUTHORIZATION) {
      MailerLog::resumeSending();
    }
  }

  private function validateAuthorizedEmail($authorizedEmails = [], $email = '') {
    $lowercaseAuthorizedEmails = array_map('strtolower', $authorizedEmails);
    return in_array(strtolower($email), $lowercaseAuthorizedEmails, true);
  }

  private function validateEmailDomainIsVerified(array $verifiedDomains = [], string $email = ''): bool {
    $lowercaseVerifiedDomains = array_map('strtolower', $verifiedDomains);
    $emailDomain = Helpers::extractEmailDomain($email);
    return in_array($emailDomain, $lowercaseVerifiedDomains, true);
  }
}
