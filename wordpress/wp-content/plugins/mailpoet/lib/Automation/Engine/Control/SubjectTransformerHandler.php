<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Control;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Data\Step as StepData;
use MailPoet\Automation\Engine\Data\Subject;
use MailPoet\Automation\Engine\Integration\SubjectTransformer;
use MailPoet\Automation\Engine\Integration\Trigger;
use MailPoet\Automation\Engine\Registry;

class SubjectTransformerHandler {

  /* @var Registry */
  private $registry;

  public function __construct(
    Registry $registry
  ) {
    $this->registry = $registry;
  }

  public function getSubjectKeysForAutomation(Automation $automation): array {
    $triggerData = array_values(array_filter(
      $automation->getSteps(),
      function (StepData $step): bool {
        return $step->getType() === StepData::TYPE_TRIGGER;
      }
    ));

    $triggers = array_filter(array_map(
      function (StepData $step): ?Trigger {
        return $this->registry->getTrigger($step->getKey());
      },
      $triggerData
    ));
    $all = [];
    foreach ($triggers as $trigger) {
      $all[] = $this->getSubjectKeysForTrigger($trigger);
    }
    $all = count($all) > 1 ? array_intersect(...$all) : $all[0] ?? [];
    return array_values(array_unique($all));
  }

  public function getSubjectKeysForTrigger(Trigger $trigger): array {
    $transformerMap = $this->getTransformerMap();
    $all = $trigger->getSubjectKeys();
    $queue = $all;
    while ($key = array_shift($queue)) {
      foreach ($transformerMap[$key] ?? [] as $transformer) {
        $newKey = $transformer->returns();
        if (!in_array($newKey, $all, true)) {
          $all[] = $newKey;
          $queue[] = $newKey;
        }
      }
    }
    sort($all);
    return $all;
  }

  /**
   * @param Subject[] $subjects
   * @return Subject[]
   */
  public function getAllSubjects(array $subjects): array {
    $transformerMap = $this->getTransformerMap();
    $all = [];
    foreach ($subjects as $subject) {
      $all[$subject->getKey()] = $subject;
    }

    $queue = array_keys($all);
    while ($key = array_shift($queue)) {
      foreach ($transformerMap[$key] ?? [] as $transformer) {
          $newKey = $transformer->returns();
        if (!isset($all[$newKey])) {
          $newSubject = $transformer->transform($all[$key]);
          if (!$newSubject) {
            continue;
          }
          $all[$newKey] = $newSubject;
          $queue[] = $newKey;
        }
      }
    }
    return array_values($all);
  }

  /**
   * @return SubjectTransformer[][]
   */
  private function getTransformerMap(): array {
    $transformerMap = [];
    foreach ($this->registry->getSubjectTransformers() as $transformer) {
      $transformerMap[$transformer->accepts()] = array_merge($transformerMap[$transformer->accepts()] ?? [], [$transformer]);
    }
    return $transformerMap;
  }
}
