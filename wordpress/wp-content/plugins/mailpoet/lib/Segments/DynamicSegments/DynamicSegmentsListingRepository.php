<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Segments\DynamicSegments;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\SegmentEntity;
use MailPoet\Segments\SegmentListingRepository;
use MailPoetVendor\Doctrine\ORM\QueryBuilder;

class DynamicSegmentsListingRepository extends SegmentListingRepository {
  protected function applyParameters(QueryBuilder $queryBuilder, array $parameters): void {
    $queryBuilder
      ->andWhere('s.type = :type')
      ->setParameter('type', SegmentEntity::TYPE_DYNAMIC);
  }
}
