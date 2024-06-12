<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Builder;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Data\NextStep;
use MailPoet\Automation\Engine\Data\Step;
use MailPoet\Automation\Engine\Exceptions;
use MailPoet\Automation\Engine\Exceptions\InvalidStateException;
use MailPoet\Automation\Engine\Storage\AutomationStorage;
use MailPoet\Automation\Engine\WordPress;
use MailPoet\Util\Security;

class DuplicateAutomationController {
  /** @var WordPress */
  private $wordPress;

  /** @var AutomationStorage */
  private $automationStorage;

  public function __construct(
    WordPress $wordPress,
    AutomationStorage $automationStorage
  ) {
    $this->wordPress = $wordPress;
    $this->automationStorage = $automationStorage;
  }

  public function duplicateAutomation(int $id): Automation {
    $automation = $this->automationStorage->getAutomation($id);
    if (!$automation) {
      throw Exceptions::automationNotFound($id);
    }

    $duplicate = new Automation(
      $this->getName($automation->getName()),
      $this->getSteps($automation->getSteps()),
      $this->wordPress->wpGetCurrentUser()
    );
    $duplicate->setStatus(Automation::STATUS_DRAFT);

    $automationId = $this->automationStorage->createAutomation($duplicate);
    $savedAutomation = $this->automationStorage->getAutomation($automationId);
    if (!$savedAutomation) {
      throw new InvalidStateException('Automation not found.');
    }
    return $savedAutomation;
  }

  private function getName(string $name): string {
    // translators: %s is the original automation name.
    $newName = sprintf(__('Copy of %s', 'mailpoet'), $name);
    $maxLength = $this->automationStorage->getNameColumnLength();
    if (strlen($newName) > $maxLength) {
      $append = '…';
      return substr($newName, 0, $maxLength - strlen($append)) . $append;
    }
    return $newName;
  }

  /**
   * @param Step[] $steps
   * @return Step[]
  */
  private function getSteps(array $steps): array {
    $newIds = [];
    foreach ($steps as $step) {
      $id = $step->getId();
      $newIds[$id] = $id === 'root' ? 'root' : $this->getId();
    }

    $newSteps = [];
    foreach ($steps as $step) {
      $newId = $newIds[$step->getId()];
      $newSteps[$newId] = new Step(
        $newId,
        $step->getType(),
        $step->getKey(),
        $step->getArgs(),
        array_map(function (NextStep $nextStep) use ($newIds): NextStep {
          $nextStepId = $nextStep->getId();
          return new NextStep($nextStepId ? $newIds[$nextStepId] : null);
        }, $step->getNextSteps())
      );
    }
    return $newSteps;
  }

  private function getId(): string {
    return Security::generateRandomString(16);
  }
}
