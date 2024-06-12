<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\MailPoet\Payloads;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Integration\Payload;
use MailPoet\Entities\SegmentEntity;
use MailPoet\InvalidStateException;

class SegmentPayload implements Payload {
  /** @var SegmentEntity */
  private $segment;

  public function __construct(
    SegmentEntity $segment
  ) {
    $this->segment = $segment;
  }

  public function getId(): int {
    $id = $this->segment->getId();
    if (!$id) {
      throw new InvalidStateException();
    }
    return $id;
  }

  public function getName(): string {
    return $this->segment->getName();
  }

  public function getType(): string {
    return $this->segment->getType();
  }
}
