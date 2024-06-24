<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\MailPoet\Hooks;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Data\Step;
use MailPoet\Automation\Engine\Hooks;
use MailPoet\Automation\Engine\Storage\AutomationStorage;
use MailPoet\Automation\Engine\WordPress;
use MailPoet\Newsletter\NewsletterDeleteController;
use MailPoet\Newsletter\NewslettersRepository;

class AutomationEditorLoadingHooks {

  /** @var WordPress */
  private $wp;

  /** @var AutomationStorage  */
  private $automationStorage;

  /** @var NewslettersRepository  */
  private $newslettersRepository;

  private NewsletterDeleteController $newsletterDeleteController;

  public function __construct(
    WordPress $wp,
    AutomationStorage $automationStorage,
    NewslettersRepository $newslettersRepository,
    NewsletterDeleteController $newsletterDeleteController
  ) {
    $this->wp = $wp;
    $this->automationStorage = $automationStorage;
    $this->newslettersRepository = $newslettersRepository;
    $this->newsletterDeleteController = $newsletterDeleteController;
  }

  public function init(): void {
    $this->wp->addAction(Hooks::EDITOR_BEFORE_LOAD, [$this, 'beforeEditorLoad']);
  }

  public function beforeEditorLoad(int $automationId): void {
    $automation = $this->automationStorage->getAutomation($automationId);
    if (!$automation) {
      return;
    }
    $this->disconnectEmptyEmailsFromSendEmailStep($automation);
  }

  private function disconnectEmptyEmailsFromSendEmailStep(Automation $automation): void {
    $sendEmailSteps = array_filter(
      $automation->getSteps(),
      function(Step $step): bool {
        return $step->getKey() === 'mailpoet:send-email';
      }
    );
    foreach ($sendEmailSteps as $step) {
      $emailId = $step->getArgs()['email_id'] ?? 0;
      if (!$emailId) {
        continue;
      }
      $newsletterEntity = $this->newslettersRepository->findOneById($emailId);
      if ($newsletterEntity && $newsletterEntity->getBody() !== null) {
        continue;
      }

      $this->newsletterDeleteController->bulkDelete([$emailId]);
      $args = $step->getArgs();
      unset($args['email_id']);
      $updatedStep = new Step(
        $step->getId(),
        $step->getType(),
        $step->getKey(),
        $args,
        $step->getNextSteps()
      );

      $steps = array_merge(
        $automation->getSteps(),
        [$updatedStep->getId() => $updatedStep]
      );
      $automation->setSteps($steps);

      //To be valid, an email would need to be associated to an active automation.
      if ($automation->getStatus() === Automation::STATUS_ACTIVE) {
        $automation->setStatus(Automation::STATUS_DRAFT);
      }
      $this->automationStorage->updateAutomation($automation);
    }
  }
}
