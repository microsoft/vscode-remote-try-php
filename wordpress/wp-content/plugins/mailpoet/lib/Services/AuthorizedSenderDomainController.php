<?php declare(strict_types = 1);

namespace MailPoet\Services;

if (!defined('ABSPATH')) exit;


use MailPoet\Mailer\Mailer;
use MailPoet\Newsletter\Statistics\NewsletterStatisticsRepository;
use MailPoet\Services\Bridge\API;
use MailPoet\Settings\SettingsController;
use MailPoet\Util\License\Features\Subscribers;
use MailPoet\WP\Functions as WPFunctions;

class AuthorizedSenderDomainController {
  const DOMAIN_STATUS_VERIFIED = 'verified';
  const DOMAIN_STATUS_PARTIALLY_VERIFIED = 'partially-verified';
  const DOMAIN_STATUS_UNVERIFIED = 'unverified';

  const AUTHORIZED_SENDER_DOMAIN_ERROR_ALREADY_CREATED = 'Sender domain exist';
  const AUTHORIZED_SENDER_DOMAIN_ERROR_NOT_CREATED = 'Sender domain does not exist';
  const AUTHORIZED_SENDER_DOMAIN_ERROR_ALREADY_VERIFIED = 'Sender domain already verified';

  const LOWER_LIMIT = 100;
  const UPPER_LIMIT = 200;

  const INSTALLED_AFTER_NEW_RESTRICTIONS_OPTION = 'installed_after_new_domain_restrictions';

  const SENDER_DOMAINS_KEY = 'mailpoet_sender_domains';

  /** @var Bridge */
  private $bridge;

  /** @var NewsletterStatisticsRepository  */
  private $newsletterStatisticsRepository;

  /** @var SettingsController  */
  private $settingsController;

  /** @var null|array Cached response for with authorized domains */
  private $currentRecords = null;

  /** @var null|array */
  private $currentRawData = null;

  /** @var Subscribers */
  private $subscribers;

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    Bridge $bridge,
    NewsletterStatisticsRepository $newsletterStatisticsRepository,
    SettingsController $settingsController,
    Subscribers $subscribers,
    WPFunctions $wp
  ) {
    $this->bridge = $bridge;
    $this->newsletterStatisticsRepository = $newsletterStatisticsRepository;
    $this->settingsController = $settingsController;
    $this->subscribers = $subscribers;
    $this->wp = $wp;
  }

  /**
   * Get record of Bridge::getAuthorizedSenderDomains
   */
  public function getDomainRecords(string $domain = ''): array {
    $records = $this->getAllRecords();
    if ($domain) {
      return $records[$domain] ?? [];
    }
    return $records;
  }

  /**
   * Get all Authorized Sender Domains
   *
   * Note: This includes both verified and unverified domains
   */
  public function getAllSenderDomains(): array {
    return $this->returnAllDomains($this->getAllRecords());
  }

  /**
   * Get all Verified Sender Domains.
   *
   * Note: This includes partially or fully verified domains.
   */
  public function getVerifiedSenderDomains(): array {
    return $this->getFullyOrPartiallyVerifiedSenderDomains(true);
  }

  public function getVerifiedSenderDomainsIgnoringCache(): array {
    $this->reloadCache();
    return $this->getFullyOrPartiallyVerifiedSenderDomains(true);
  }

  /**
   * Create new Sender Domain
   *
   * Throws an InvalidArgumentException if domain already exist
   *
   * returns an Array of DNS response or array of error
   */
  public function createAuthorizedSenderDomain(string $domain): array {
    $allDomains = $this->getAllSenderDomains();

    $alreadyExist = in_array($domain, $allDomains);

    if ($alreadyExist) {
      // sender domain already created. skip making new request
      throw new \InvalidArgumentException(self::AUTHORIZED_SENDER_DOMAIN_ERROR_ALREADY_CREATED);
    }

    $response = $this->bridge->createAuthorizedSenderDomain($domain);

    if (isset($response['status']) && $response['status'] === API::RESPONSE_STATUS_ERROR) {
      throw new \InvalidArgumentException($response['message']);
    }

    // Reset cached value since a new domain was added
    $this->currentRecords = null;
    $this->reloadCache();

    return $response;
  }

  public function getRewrittenEmailAddress(string $email): string {
    return sprintf('%s@replies.sendingservice.net', str_replace('@', '=', $email));
  }

  /**
   * Verify Sender Domain
   *
   * Throws an InvalidArgumentException if domain does not exist or domain is already verified
   *
   * * returns [ok: bool, dns: array] if domain verification is successful
   * * or [ok: bool, error:  string, dns: array] if domain verification failed
   * * or [error: string, status: bool] for other errors
   */
  public function verifyAuthorizedSenderDomain(string $domain): array {
    $records = $this->bridge->getAuthorizedSenderDomains();

    $allDomains = $this->returnAllDomains($records);
    $alreadyExist = in_array($domain, $allDomains);

    if (!$alreadyExist) {
      // can't verify a domain that does not exist
      throw new \InvalidArgumentException(self::AUTHORIZED_SENDER_DOMAIN_ERROR_NOT_CREATED);
    }

    $verifiedDomains = $this->getFullyVerifiedSenderDomains(true);
    $alreadyVerified = in_array($domain, $verifiedDomains);

    if ($alreadyVerified) {
      // no need to reverify an already verified domain
      throw new \InvalidArgumentException(self::AUTHORIZED_SENDER_DOMAIN_ERROR_ALREADY_VERIFIED);
    }

    $response = $this->bridge->verifyAuthorizedSenderDomain($domain);

    // API response contains status, but we need to check that dns array is not included
    if ($response['status'] === API::RESPONSE_STATUS_ERROR && !isset($response['dns'])) {
      throw new \InvalidArgumentException($response['message']);
    }

    $this->currentRecords = null;
    $this->reloadCache();

    return $response;
  }

  public function getSenderDomainsByStatus(array $status): array {
    return array_filter($this->getAllRawData(), function(array $senderDomainData) use ($status) {
      return in_array($senderDomainData['domain_status'] ?? null, $status);
    });
  }

  /**
   * Returns sender domains that have all required records, including DMARC.
   */
  public function getFullyVerifiedSenderDomains($domainsOnly = false): array {
    $domainData = $this->getSenderDomainsByStatus([self::DOMAIN_STATUS_VERIFIED]);
    return $domainsOnly ? $this->extractDomains($domainData) : $domainData;
  }

  /**
   * Returns sender domains that were verified before DMARC record was required.
   */
  public function getPartiallyVerifiedSenderDomains($domainsOnly = false): array {
    $domainData = $this->getSenderDomainsByStatus([self::DOMAIN_STATUS_PARTIALLY_VERIFIED]);
    return $domainsOnly ? $this->extractDomains($domainData) : $domainData;
  }

  public function getUnverifiedSenderDomains($domainsOnly = false): array {
    $domainData = $this->getSenderDomainsByStatus([self::DOMAIN_STATUS_UNVERIFIED]);
    return $domainsOnly ? $this->extractDomains($domainData) : $domainData;
  }

  public function getFullyOrPartiallyVerifiedSenderDomains($domainsOnly = false): array {
    $domainData = $this->getSenderDomainsByStatus([self::DOMAIN_STATUS_PARTIALLY_VERIFIED, self::DOMAIN_STATUS_VERIFIED]);
    return $domainsOnly ? $this->extractDomains($domainData) : $domainData;
  }

  private function extractDomains(array $domainData): array {
    $extractedDomains = [];
    foreach ($domainData as $data) {
      $extractedDomains[] = $this->domainExtractor($data);
    }
    return $extractedDomains;
  }

  private function domainExtractor(array $domainData): string {
    return $domainData['domain'] ?? '';
  }

  public function getSenderDomainsGroupedByStatus(): array {
    $groupedDomains = [];
    foreach ($this->getAllRawData() as $senderDomainData) {
      $status = $senderDomainData['domain_status'] ?? 'unknown';
      if (!isset($groupedDomains[$status])) {
        $groupedDomains[$status] = [];
      }
      $groupedDomains[$status][] = $senderDomainData;
    }
    return $groupedDomains;
  }

  /**
   * Little helper function to return All Domains. alias to `array_keys`
   *
   * The domain is the key returned from the Bridge::getAuthorizedSenderDomains
   */
  private function returnAllDomains(array $records): array {
    $domains = array_keys($records);
    return $domains;
  }

  private function reloadCache() {
    $currentRawData = $this->bridge->getRawSenderDomainData();
    if (!$currentRawData) return; // Do not modify cache if there is no data from the API

    $this->currentRawData = $currentRawData;
    $this->wp->setTransient(self::SENDER_DOMAINS_KEY, $this->currentRawData, 60 * 60 * 24 * 7);
  }

  public function isCacheAvailable(): bool {
    return is_array($this->wp->getTransient(self::SENDER_DOMAINS_KEY));
  }

  private function getAllRawData(): array {
    if ($this->currentRawData === null) {
      $currentData = $this->wp->getTransient(self::SENDER_DOMAINS_KEY);
      if (is_array($currentData)) {
        $this->currentRawData = $currentData;
      } else {
        $this->reloadCache();
      }
    }
    return is_array($this->currentRawData) ? $this->currentRawData : [];
  }

  private function getAllRecords(): array {
    if ($this->currentRecords === null) {
      $this->currentRecords = $this->bridge->getAuthorizedSenderDomains();
    }
    return $this->currentRecords;
  }

  public function isNewUser(): bool {
    $installedVersion = $this->settingsController->get('version');

    // Setup wizard has not been completed
    if ($installedVersion === null) {
      return true;
    }

    $installedAfterNewDomainRestrictions = $this->settingsController->get(self::INSTALLED_AFTER_NEW_RESTRICTIONS_OPTION, false);

    if ($installedAfterNewDomainRestrictions) {
      return true;
    }

    return $this->newsletterStatisticsRepository->countBy([]) === 0;
  }

  public function isSmallSender(): bool {
    return $this->subscribers->getSubscribersCount() <= self::LOWER_LIMIT;
  }

  public function isBigSender(): bool {
    return $this->subscribers->getSubscribersCount() > self::UPPER_LIMIT;
  }

  public function isAuthorizedDomainRequiredForNewCampaigns(): bool {
    return $this->settingsController->get('mta.method') === Mailer::METHOD_MAILPOET && !$this->isSmallSender();
  }

  public function isAuthorizedDomainRequiredForExistingCampaigns(): bool {
    return $this->settingsController->get('mta.method') === Mailer::METHOD_MAILPOET && $this->isBigSender();
  }

  public function getContextData(): array {
    return [
      'verifiedSenderDomains' => $this->getFullyVerifiedSenderDomains(true),
      'partiallyVerifiedSenderDomains' => $this->getPartiallyVerifiedSenderDomains(true),
      'allSenderDomains' => $this->getAllSenderDomains(),
      'senderRestrictions' => [
        'lowerLimit' => self::LOWER_LIMIT,
        'alwaysRewrite' => false,
      ],
    ];
  }

  public function getContextDataForAutomations(): array {
    $data = $this->getContextData();
    $data['senderRestrictions']['alwaysRewrite'] = true;
    return $data;
  }
}
