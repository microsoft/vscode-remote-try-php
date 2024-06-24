<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Control;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Subject as SubjectData;
use MailPoet\Automation\Engine\Data\SubjectEntry;
use MailPoet\Automation\Engine\Exceptions;
use MailPoet\Automation\Engine\Integration\Payload;
use MailPoet\Automation\Engine\Integration\Subject;
use MailPoet\Automation\Engine\Registry;

class SubjectLoader {
  /** @var Registry */
  private $registry;

  public function __construct(
    Registry $registry
  ) {
    $this->registry = $registry;
  }

  /**
   * @param SubjectData[] $subjectData
   * @return SubjectEntry<Subject<Payload>>[]
   */
  public function getSubjectsEntries(array $subjectData): array {
    $subjectEntries = [];
    foreach ($subjectData as $data) {
      $subjectEntries[] = $this->getSubjectEntry($data);
    }
    return $subjectEntries;
  }

  /**
   * @param SubjectData $subjectData
   * @return SubjectEntry<Subject<Payload>>
   */
  public function getSubjectEntry(SubjectData $subjectData): SubjectEntry {
    $key = $subjectData->getKey();
    $subject = $this->registry->getSubject($key);
    if (!$subject) {
      throw Exceptions::subjectNotFound($key);
    }
    return new SubjectEntry($subject, $subjectData);
  }
}
