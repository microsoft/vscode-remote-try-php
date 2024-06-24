<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Validation\AutomationRules;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Exceptions;
use MailPoet\Automation\Engine\Exceptions\UnexpectedValueException;
use MailPoet\Automation\Engine\Validation\AutomationGraph\AutomationNode;
use MailPoet\Automation\Engine\Validation\AutomationGraph\AutomationNodeVisitor;
use MailPoet\Validator\ValidationException;
use Throwable;

class ValidStepRule implements AutomationNodeVisitor {
  /** @var AutomationNodeVisitor[] */
  private $rules;

  /** @var array<string, array{step_id: string, fields: array<string,string>}> */
  private $errors = [];

  /** @param AutomationNodeVisitor[] $rules */
  public function __construct(
    array $rules
  ) {
    $this->rules = $rules;
  }

  public function initialize(Automation $automation): void {
    if (!$automation->needsFullValidation()) {
      return;
    }

    foreach ($this->rules as $rule) {
      $rule->initialize($automation);
    }
  }

  public function visitNode(Automation $automation, AutomationNode $node): void {
    if (!$automation->needsFullValidation()) {
      return;
    }

    foreach ($this->rules as $rule) {
      $stepId = $node->getStep()->getId();
      try {
        $rule->visitNode($automation, $node);
      } catch (UnexpectedValueException $e) {
        if (!isset($this->errors[$stepId])) {
          $this->errors[$stepId] = ['step_id' => $stepId, 'message' => $e->getMessage(), 'fields' => [], 'filters' => []];
        }
        $this->errors[$stepId]['fields'] = array_merge(
          $this->mapErrorCodesToErrorMessages($e->getErrors()),
          $this->errors[$stepId]['fields']
        );
      } catch (ValidationException $e) {
        if (!isset($this->errors[$stepId])) {
          $this->errors[$stepId] = ['step_id' => $stepId, 'message' => $e->getMessage(), 'fields' => [], 'filters' => []];
        }

        $key = $rule instanceof ValidStepFiltersRule ? 'filters' : 'fields';
        /** @phpstan-ignore-next-line - PHPStan detects inconsistency in merged array */
        $this->errors[$stepId][$key] = array_merge(
          $this->mapErrorCodesToErrorMessages($e->getErrors()),
          $this->errors[$stepId][$key]
        );
      } catch (Throwable $e) {
        if (!isset($this->errors[$stepId])) {
          $this->errors[$stepId] = ['step_id' => $stepId, 'message' => __('Unknown error.', 'mailpoet'), 'fields' => [], 'filters' => []];
        }
      }
    }
  }

  private function mapErrorCodesToErrorMessages(array $errorCodes): array {

    return array_map(
      function(string $errorCode): string {
        switch ($errorCode) {
          case "rest_property_required":
            return __('This is a required field.', 'mailpoet');
          case "rest_additional_properties_forbidden":
          case "rest_too_few_properties":
          case "rest_too_many_properties":
            return "";
          case "rest_invalid_type":
          case "rest_invalid_multiple":
          case "rest_not_in_enum":
            return __('This field is not well formed.', 'mailpoet');
          case "rest_too_few_items":
            return __('Please add more items.', 'mailpoet');
          case "rest_too_many_items":
            return __('Please remove some items.', 'mailpoet');
          case "rest_duplicate_items":
            return __('Please remove duplicate items.', 'mailpoet');
          case "rest_out_of_bounds":
            return __('This value is out of bounds.', 'mailpoet');
          case "rest_too_short":
            return __('This value is not long enough.', 'mailpoet');
          case "rest_too_long":
            return __('This value is too long.', 'mailpoet');
          case "rest_invalid_pattern":
            return __('This value is not well formed.', 'mailpoet');
          case "rest_no_matching_schema":
            return __('This value does not match the expected format.', 'mailpoet');
          case "rest_one_of_multiple_matches":
            return __('This value is not matching the correct times.', 'mailpoet');
          case "rest_invalid_hex_color":
            return __('This value is not a hex formatted color.', 'mailpoet');
          case "rest_invalid_date":
            return __('This value is not a date.', 'mailpoet');
          case "rest_invalid_email":
            return __('This value is not an email address.', 'mailpoet');
          case "rest_invalid_ip":
            return __('This value is not an IP address.', 'mailpoet');
          case "rest_invalid_uuid":
            return __('This value is not an UUID.', 'mailpoet');
          default:
            return $errorCode;
        }
      },
      $errorCodes
    );
  }

  public function complete(Automation $automation): void {
    if (!$automation->needsFullValidation()) {
      return;
    }

    foreach ($this->rules as $rule) {
      $rule->complete($automation);
    }

    if ($this->errors) {
      throw Exceptions::automationNotValid(__('Some steps are not valid', 'mailpoet'), $this->errors);
    }
  }
}
