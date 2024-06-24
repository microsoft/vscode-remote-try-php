<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\MailPoet;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\ServicesChecker;
use MailPoet\Segments\SegmentsRepository;
use MailPoet\Services\AuthorizedSenderDomainController;
use MailPoet\Services\Bridge;

class ContextFactory {
  /** @var SegmentsRepository */
  private $segmentsRepository;

  /** @var Bridge */
  private $bridge;

  /** @var ServicesChecker */
  private $servicesChecker;

  /** @var AuthorizedSenderDomainController */
  private $authorizedSenderDomainController;

  public function __construct(
    SegmentsRepository $segmentsRepository,
    Bridge $bridge,
    ServicesChecker $servicesChecker,
    AuthorizedSenderDomainController $authorizedSenderDomainController
  ) {
    $this->segmentsRepository = $segmentsRepository;
    $this->servicesChecker = $servicesChecker;
    $this->bridge = $bridge;
    $this->authorizedSenderDomainController = $authorizedSenderDomainController;
  }

  /** @return mixed[] */
  public function getContextData(): array {
    $data = [
      'segments' => $this->getSegments(),
      'userRoles' => $this->getUserRoles(),
    ];

    if ($this->isMSSEnabled()) {
      $data['senderDomainsConfig'] = $this->getSenderDomainsConfig();
    }

    return $data;
  }

  private function getSenderDomainsConfig(): array {
    $senderDomainsConfig = $this->authorizedSenderDomainController->getContextDataForAutomations();
    $senderDomainsConfig['authorizedEmails'] = $this->bridge->getAuthorizedEmailAddresses();
    return $senderDomainsConfig;
  }

  private function isMSSEnabled(): bool {
    $mpApiKeyValid = $this->servicesChecker->isMailPoetAPIKeyValid(false, true);
    return $mpApiKeyValid && $this->bridge->isMailpoetSendingServiceEnabled();
  }

  private function getSegments(): array {
    $segments = [];
    foreach ($this->segmentsRepository->findAll() as $segment) {
      $segments[] = [
        'id' => $segment->getId(),
        'name' => $segment->getName(),
        'type' => $segment->getType(),
      ];
    }
    return $segments;
  }

  private function getUserRoles(): array {
    $userRoles = [];
    foreach (wp_roles()->roles as $role => $details) {
      $userRoles[] = [
        'id' => $role,
        'name' => $details['name'],
      ];
    }
    return $userRoles;
  }
}
