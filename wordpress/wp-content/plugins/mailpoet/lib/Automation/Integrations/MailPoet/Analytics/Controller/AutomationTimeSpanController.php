<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\MailPoet\Analytics\Controller;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Storage\AutomationStorage;
use MailPoet\Automation\Integrations\MailPoet\Actions\SendEmailAction;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Newsletter\NewslettersRepository;

class AutomationTimeSpanController {

  /** @var AutomationStorage */
  private $automationStorage;

  /** @var NewslettersRepository */
  private $newslettersRepository;

  public function __construct(
    AutomationStorage $automationStorage,
    NewslettersRepository $newslettersRepository
  ) {
    $this->automationStorage = $automationStorage;
    $this->newslettersRepository = $newslettersRepository;
  }

  public function getAutomationsInTimespan(Automation $automation, \DateTimeImmutable $after, \DateTimeImmutable $before): array {
    $automationVersions = $this->automationStorage->getAutomationVersionDates($automation->getId());
    usort(
      $automationVersions,
      function (array $a, array $b) {
        return $a['created_at'] <=> $b['created_at'];
      }
    );

    // Find all versions, which could have been active in the given time span
    $versionIds = [];
    foreach ($automationVersions as $automationVersion) {
      if ($automationVersion['created_at'] > $before) {
        // We are past the time span
        break;
      }
      if (!$versionIds || $automationVersion['created_at'] <= $after) {
        // This is the first version in the time span
        $versionIds = [(int)$automationVersion['id']];
        continue;
      }
      $versionIds[] = (int)$automationVersion['id'];
    }

    return count($versionIds) > 0 ? $this->automationStorage->getAutomationWithDifferentVersions($versionIds) : [];
  }

  /**
   * @param Automation $automation
   * @param \DateTimeImmutable $after
   * @param \DateTimeImmutable $before
   * @return NewsletterEntity[]
   */
  public function getAutomationEmailsInTimeSpan(Automation $automation, \DateTimeImmutable $after, \DateTimeImmutable $before): array {
    $automations = $this->getAutomationsInTimespan($automation, $after, $before);
    return count($automations) > 0 ? $this->getEmailsFromAutomations($automations) : [];
  }

  /**
   * @param Automation[] $automations
   * @return NewsletterEntity[]
   */
  public function getEmailsFromAutomations(array $automations): array {
    $emailSteps = [];
    foreach ($automations as $automation) {
      $emailSteps = array_merge(
        $emailSteps,
        array_values(
          array_filter(
            $automation->getSteps(),
            function($step) {
              return $step->getKey() === SendEmailAction::KEY;
            }
          )
        )
      );
    }
    $emailIds = array_unique(
      array_filter(
        array_map(
          function($step) {
            $args = $step->getArgs();
            return isset($args['email_id']) ? absint($args['email_id']) : null;
          },
          $emailSteps
        )
      )
    );

    return $this->newslettersRepository->findBy(['id' => $emailIds]);
  }
}
