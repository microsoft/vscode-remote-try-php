<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Integration;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Field;
use MailPoet\Automation\Engine\Data\Subject as SubjectData;
use MailPoet\Validator\Schema\ObjectSchema;

/**
 * @template-covariant T of Payload
 */
interface Subject {
  public function getKey(): string;

  public function getName(): string;

  public function getArgsSchema(): ObjectSchema;

  /** @return Field[] */
  public function getFields(): array;

  /** @return T */
  public function getPayload(SubjectData $subjectData): Payload;
}
