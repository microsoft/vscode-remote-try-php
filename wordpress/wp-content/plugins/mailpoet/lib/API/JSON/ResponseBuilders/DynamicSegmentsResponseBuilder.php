<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\API\JSON\ResponseBuilders;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\DynamicSegmentFilterData;
use MailPoet\Entities\SegmentEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Segments\SegmentDependencyValidator;
use MailPoet\Segments\SegmentSubscribersRepository;
use MailPoet\Subscribers\SubscribersCountsController;
use MailPoet\WP\Functions;

class DynamicSegmentsResponseBuilder {
  const DATE_FORMAT = 'Y-m-d H:i:s';

  /** @var SegmentsResponseBuilder */
  private $segmentsResponseBuilder;

  /** @var Functions */
  private $wp;

  /** @var SegmentSubscribersRepository */
  private $segmentSubscriberRepository;

  /** @var SegmentDependencyValidator */
  private $segmentDependencyValidator;

  /** @var SubscribersCountsController */
  private $subscribersCountsController;

  public function __construct(
    Functions $wp,
    SegmentSubscribersRepository $segmentSubscriberRepository,
    SegmentsResponseBuilder $segmentsResponseBuilder,
    SegmentDependencyValidator $segmentDependencyValidator,
    SubscribersCountsController $subscribersCountsController
  ) {
    $this->segmentsResponseBuilder = $segmentsResponseBuilder;
    $this->segmentSubscriberRepository = $segmentSubscriberRepository;
    $this->wp = $wp;
    $this->segmentDependencyValidator = $segmentDependencyValidator;
    $this->subscribersCountsController = $subscribersCountsController;
  }

  public function build(SegmentEntity $segmentEntity) {
    $data = $this->segmentsResponseBuilder->build($segmentEntity);
    $data = $this->addMissingPluginProperties($segmentEntity, $data);
    $dynamicFilters = $segmentEntity->getDynamicFilters();
    $filters = [];
    foreach ($dynamicFilters as $dynamicFilter) {
      $filter = $dynamicFilter->getFilterData()->getData();
      $filter['id'] = $dynamicFilter->getId();
      $filter['segmentType'] = $dynamicFilter->getFilterData()->getFilterType(); // We need to add filterType with key segmentType due to BC
      $filter['action'] = $dynamicFilter->getFilterData()->getAction();
      if (isset($filter['country_code']) && !is_array($filter['country_code'])) {
        // Convert to multiple values filter
        $filter['country_code'] = [$filter['country_code']];
      }
      if (isset($filter['wordpressRole']) && !is_array($filter['wordpressRole'])) {
        // new filters are always array, they support multiple values, the old didn't convert old filters to new format
        $filter['wordpressRole'] = [$filter['wordpressRole']];
      }
      if (($filter['segmentType'] === DynamicSegmentFilterData::TYPE_EMAIL) && isset($filter['newsletter_id'])) {
        $filter['newsletter_id'] = intval($filter['newsletter_id']);
      }
      $filters[] = $filter;
    }
    $data['filters'] = $filters;
    return $data;
  }

  public function buildForListing(array $segments): array {
    $data = [];
    foreach ($segments as $segment) {
      $data[] = $this->buildListingItem($segment);
    }
    return $data;
  }

  private function addMissingPluginProperties(SegmentEntity $segment, array $data): array {
    $missingPlugins = $this->segmentDependencyValidator->getMissingPluginsBySegment($segment);
    if ($missingPlugins) {
      $missingPlugin = reset($missingPlugins);
      $data['is_plugin_missing'] = true;

      $missingPluginMessage = $this->segmentDependencyValidator->getCustomErrorMessage($missingPlugin);

      if ($missingPluginMessage) {
        $data['missing_plugin_message'] = $missingPluginMessage;
      } else {
        $data['missing_plugin_message']['message'] =
          sprintf(
            // translators: %s is the name of the missing plugin.
            __('Activate the %s plugin to see the number of subscribers and enable the editing of this segment.', 'mailpoet'),
            $missingPlugin
          );
      }
    } else {
      $data['is_plugin_missing'] = false;
      $data['missing_plugin_message'] = null;
    }
    return $data;
  }

  private function buildListingItem(SegmentEntity $segment): array {
    $data = $this->segmentsResponseBuilder->build($segment);
    $data = $this->addMissingPluginProperties($segment, $data);
    $data['subscribers_url'] = $this->wp->adminUrl(
      'admin.php?page=mailpoet-subscribers#/filter[segment=' . $segment->getId() . ']'
    );

    $segmentStatisticsCount = $this->subscribersCountsController->getSegmentStatisticsCount($segment);
    $data['count_all'] = $segmentStatisticsCount['all'];
    $data['count_subscribed'] = $segmentStatisticsCount[SubscriberEntity::STATUS_SUBSCRIBED];
    return $data;
  }
}
