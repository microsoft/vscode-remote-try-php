<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\MailPoet\SubjectTransformers;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Subject;
use MailPoet\Automation\Engine\Integration\SubjectTransformer;
use MailPoet\Automation\Integrations\MailPoet\Subjects\SegmentSubject;
use MailPoet\Automation\Integrations\WooCommerce\Subjects\OrderSubject;
use MailPoet\Segments\SegmentsRepository;

class OrderSubjectToSegmentSubjectTransformer implements SubjectTransformer {

  /** @var SegmentsRepository */
  private $segmentRepository;

  public function __construct(
    SegmentsRepository $segmentRepository
  ) {
    $this->segmentRepository = $segmentRepository;
  }

  public function accepts(): string {
    return OrderSubject::KEY;
  }

  public function returns(): string {
    return SegmentSubject::KEY;
  }

  public function transform(Subject $data): Subject {

    if ($this->accepts() !== $data->getKey()) {
      throw new \InvalidArgumentException('Invalid subject type');
    }

    $wooCommerceSegment = $this->segmentRepository->getWooCommerceSegment();
    return new Subject(SegmentSubject::KEY, ['segment_id' => $wooCommerceSegment->getId()]);
  }
}
