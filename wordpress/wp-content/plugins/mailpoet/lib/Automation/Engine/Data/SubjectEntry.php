<?php declare(strict_types = 1);

namespace MailPoet\Automation\Engine\Data;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Subject as SubjectData;
use MailPoet\Automation\Engine\Exceptions;
use MailPoet\Automation\Engine\Integration\Payload;
use MailPoet\Automation\Engine\Integration\Subject;
use Throwable;

/**
 * @template-covariant S of Subject<Payload>
 */
class SubjectEntry {
  /** @var S */
  private $subject;

  /** @var SubjectData */
  private $subjectData;

  /** @var Payload|null */
  private $payloadCache;

  /** @param S $subject */
  public function __construct(
    Subject $subject,
    SubjectData $subjectData
  ) {
    $this->subject = $subject;
    $this->subjectData = $subjectData;
  }

  /** @return S */
  public function getSubject(): Subject {
    return $this->subject;
  }

  public function getSubjectData(): SubjectData {
    return $this->subjectData;
  }

  /** @return Payload */
  public function getPayload() {
    if ($this->payloadCache === null) {
      try {
        $this->payloadCache = $this->subject->getPayload($this->subjectData);
      } catch (Throwable $e) {
        throw Exceptions::subjectLoadFailed($this->subject->getKey(), $this->subjectData->getArgs());
      }
    }
    return $this->payloadCache;
  }
}
