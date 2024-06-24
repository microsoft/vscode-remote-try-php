<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Validation\AutomationRules;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Control\SubjectTransformerHandler;
use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Data\Filter;
use MailPoet\Automation\Engine\Integration\Payload;
use MailPoet\Automation\Engine\Integration\Subject;
use MailPoet\Automation\Engine\Registry;
use MailPoet\Automation\Engine\Validation\AutomationGraph\AutomationNode;
use MailPoet\Automation\Engine\Validation\AutomationGraph\AutomationNodeVisitor;
use MailPoet\Validator\ValidationException;
use MailPoet\Validator\Validator;

class ValidStepFiltersRule implements AutomationNodeVisitor {
  /** @var Registry */
  private $registry;

  /** @var SubjectTransformerHandler */
  private $subjectTransformerHandler;

  /** @var Validator */
  private $validator;

  public function __construct(
    Registry $registry,
    SubjectTransformerHandler $subjectTransformerHandler,
    Validator $validator
  ) {
    $this->registry = $registry;
    $this->subjectTransformerHandler = $subjectTransformerHandler;
    $this->validator = $validator;
  }

  public function initialize(Automation $automation): void {
  }

  public function visitNode(Automation $automation, AutomationNode $node): void {
    $filters = $node->getStep()->getFilters();
    $groups = $filters ? $filters->getGroups() : [];
    $errors = [];
    foreach ($groups as $group) {
      foreach ($group->getFilters() as $filter) {
        $registryFilter = $this->registry->getFilter($filter->getFieldType());
        if (!$registryFilter) {
          continue;
        }
        try {
          $this->validator->validate($registryFilter->getArgsSchema($filter->getCondition()), $filter->getArgs());
        } catch (ValidationException $e) {
          $errors[$filter->getId()] = $e->getWpError()->get_error_code();
          continue;
        }

        // ensure that the field is available with the provided subjects
        $subjectKeys = $this->subjectTransformerHandler->getSubjectKeysForAutomation($automation);
        $filterSubject = $this->getFilterSubject($filter);
        if (!$filterSubject) {
          $errors[$filter->getId()] = __('Field not found', 'mailpoet');
        } elseif (!in_array($filterSubject->getKey(), $subjectKeys, true)) {
          // translators: %s is the name of a subject (data structure) that provides the field
          $errors[$filter->getId()] = sprintf(__('A trigger that provides %s is required', 'mailpoet'), $filterSubject->getName());
        }
      }
    }

    if ($errors) {
      $throwable = ValidationException::create()->withMessage('invalid-automation-filters');
      foreach ($errors as $errorKey => $errorMsg) {
        $throwable->withError((string)$errorKey, (string)$errorMsg);
      }
      throw $throwable;
    }
  }

  public function complete(Automation $automation): void {
  }

  /** @return Subject<Payload> */
  private function getFilterSubject(Filter $filter): ?Subject {
    foreach ($this->registry->getSubjects() as $subject) {
      foreach ($subject->getFields() as $field) {
        if ($field->getKey() === $filter->getFieldKey()) {
          return $subject;
        }
      }
    }
    return null;
  }
}
