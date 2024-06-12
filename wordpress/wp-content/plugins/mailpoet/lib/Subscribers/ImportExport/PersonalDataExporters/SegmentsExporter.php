<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Subscribers\ImportExport\PersonalDataExporters;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\SubscriberEntity;
use MailPoet\Entities\SubscriberSegmentEntity;
use MailPoet\Subscribers\SubscribersRepository;
use MailPoet\WP\DateTime;

class SegmentsExporter {

  /*** @var SubscribersRepository */
  private $subscribersRepository;

  public function __construct(
    SubscribersRepository $subscribersRepository
  ) {
    $this->subscribersRepository = $subscribersRepository;
  }

  /**
   * @param string $email
   * @return array(data: mixed[], done: boolean)
   */
  public function export(string $email): array {
    return [
      'data' => $this->exportSubscriber($this->subscribersRepository->findOneBy(['email' => trim($email)])),
      'done' => true,
    ];
  }

  /**
   * @param SubscriberEntity|null $subscriber
   * @return mixed[]
   */
  private function exportSubscriber(?SubscriberEntity $subscriber): array {
    if (!$subscriber) return [];

    $result = [];
    $segments = $subscriber->getSubscriberSegments();

    foreach ($segments as $segment) {
      $result[] = $this->exportSegment($segment);
    }

    return $result;
  }

  /**
   * @param SubscriberSegmentEntity $segment
   * @return mixed[]
   */
  private function exportSegment(SubscriberSegmentEntity $segment): array {
    $segmentData = [];
    $segmentData[] = [
      'name' => __('List name', 'mailpoet'),
      'value' => $segment->getSegment() ? $segment->getSegment()->getName() : '',
    ];
    $segmentData[] = [
      'name' => __('Subscription status', 'mailpoet'),
      'value' => $segment->getStatus(),
    ];
    $segmentData[] = [
      'name' => __('Timestamp of the subscription (or last change of the subscription status)', 'mailpoet'),
      'value' => $segment->getUpdatedAt()->format(DateTime::DEFAULT_DATE_TIME_FORMAT),
    ];
    return [
      'group_id' => 'mailpoet-lists',
      'group_label' => __('MailPoet Mailing Lists', 'mailpoet'),
      'item_id' => 'list-' . ($segment->getSegment() ? $segment->getSegment()->getId() : ''),
      'data' => $segmentData,
    ];
  }
}
