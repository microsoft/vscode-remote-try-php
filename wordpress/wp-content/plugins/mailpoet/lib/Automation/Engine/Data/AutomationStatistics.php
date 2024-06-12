<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Data;

if (!defined('ABSPATH')) exit;


class AutomationStatistics {

  private $automationId;
  private $versionId;
  private $entered;
  private $inProgress;

  public function __construct(
    int $automationId,
    int $entered = 0,
    int $inProcess = 0,
    ?int $versionId = null
  ) {
    $this->automationId = $automationId;
    $this->entered = $entered;
    $this->inProgress = $inProcess;
    $this->versionId = $versionId;
  }

  public function getAutomationId(): int {
    return $this->automationId;
  }

  public function getVersionId(): ?int {
    return $this->versionId;
  }

  public function getEntered(): int {
    return $this->entered;
  }

  public function getInProgress(): int {
    return $this->inProgress;
  }

  public function getExited(): int {
    return $this->getEntered() - $this->getInProgress();
  }

  public function toArray(): array {
    return [
      'automation_id' => $this->getAutomationId(),
      'totals' => [
        'entered' => $this->getEntered(),
        'in_progress' => $this->getInProgress(),
        'exited' => $this->getExited(),
      ],
    ];
  }
}
