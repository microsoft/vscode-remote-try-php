<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Validation\AutomationRules;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Registry;
use MailPoet\Automation\Engine\Validation\AutomationGraph\AutomationNode;
use MailPoet\Automation\Engine\Validation\AutomationGraph\AutomationNodeVisitor;
use MailPoet\Validator\ValidationException;
use MailPoet\Validator\Validator;

class ValidStepArgsRule implements AutomationNodeVisitor {
  /** @var Registry */
  private $registry;

  /** @var Validator */
  private $validator;

  public function __construct(
    Registry $registry,
    Validator $validator
  ) {
    $this->registry = $registry;
    $this->validator = $validator;
  }

  public function initialize(Automation $automation): void {
  }

  public function visitNode(Automation $automation, AutomationNode $node): void {
    $step = $node->getStep();
    $registryStep = $this->registry->getStep($step->getKey());
    if (!$registryStep) {
      return;
    }

    $schema = $registryStep->getArgsSchema();
    $properties = $schema->toArray()['properties'] ?? null;
    if (!$properties) {
      $this->validator->validate($schema, $step->getArgs());
      return;
    }

    $errors = [];
    foreach ($properties as $property => $propertySchema) {
      $schemaToValidate = array_merge(
        $schema->toArray(),
        ['properties' => [$property => $propertySchema]]
      );
      try {
        $this->validator->validateSchemaArray(
          $schemaToValidate,
          $step->getArgs(),
          $property
        );
      } catch (ValidationException $e) {
        $errors[$property] = $e->getWpError()->get_error_code();
      }
    }
    if ($errors) {
      $throwable = ValidationException::create();
      foreach ($errors as $errorKey => $errorMsg) {
        $throwable->withError((string)$errorKey, (string)$errorMsg);
      }
      throw $throwable;
    }
  }

  public function complete(Automation $automation): void {
  }
}
