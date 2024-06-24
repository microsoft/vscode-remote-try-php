<?php declare(strict_types = 1);

namespace MailPoet\API\JSON\ResponseBuilders;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\SegmentEntity;
use MailPoet\Subscribers\SubscribersCountsController;
use MailPoet\WP\Functions;

class SegmentsResponseBuilder {
  const DATE_FORMAT = 'Y-m-d H:i:s';

  /** @var Functions */
  private $wp;

  /** @var SubscribersCountsController */
  private $subscribersCountsController;

  public function __construct(
    Functions $wp,
    SubscribersCountsController $subscribersCountsController
  ) {
    $this->wp = $wp;
    $this->subscribersCountsController = $subscribersCountsController;
  }

  public function build(SegmentEntity $segment): array {
    return [
      'id' => (string)$segment->getId(), // (string) for BC
      'name' => $segment->getName(),
      'type' => $segment->getType(),
      'description' => $segment->getDescription(),
      'created_at' => ($createdAt = $segment->getCreatedAt()) ? $createdAt->format(self::DATE_FORMAT) : null,
      'updated_at' => $segment->getUpdatedAt()->format(self::DATE_FORMAT),
      'deleted_at' => ($deletedAt = $segment->getDeletedAt()) ? $deletedAt->format(self::DATE_FORMAT) : null,
      'average_engagement_score' => $segment->getAverageEngagementScore(),
      'filters_connect' => $segment->getFiltersConnectOperator(),
      'showInManageSubscriptionPage' => (int)$segment->getDisplayInManageSubscriptionPage(),
    ];
  }

  public function buildForListing(array $segments): array {
    $data = [];
    foreach ($segments as $segment) {
      $data[] = $this->buildListingItem($segment);
    }
    return $data;
  }

  private function buildListingItem(SegmentEntity $segment): array {
    $data = $this->build($segment);

    $data['subscribers_count'] = $this->subscribersCountsController->getSegmentStatisticsCount($segment);
    $data['subscribers_url'] = $this->wp->adminUrl(
      'admin.php?page=mailpoet-subscribers#/filter[segment=' . $segment->getId() . ']'
    );
    return $data;
  }
}
