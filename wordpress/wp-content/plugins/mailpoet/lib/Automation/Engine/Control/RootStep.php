<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Control;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\StepValidationArgs;
use MailPoet\Automation\Engine\Integration\Step;
use MailPoet\Validator\Schema\ObjectSchema;

class RootStep implements Step {
  public function getKey(): string {
    return 'core:root';
  }

  public function getName(): string {
    // translators: not shown to user, no need to translate
    return __('Root step', 'mailpoet');
  }

  public function getArgsSchema(): ObjectSchema {
    return new ObjectSchema();
  }

  public function getSubjectKeys(): array {
    return [];
  }

  public function validate(StepValidationArgs $args): void {
  }
}
