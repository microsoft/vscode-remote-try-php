<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Entities;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\EntityTraits\AutoincrementedIdTrait;
use MailPoet\Doctrine\EntityTraits\CreatedAtTrait;
use MailPoet\Doctrine\EntityTraits\SafeToOneAssociationLoadTrait;
use MailPoet\Doctrine\EntityTraits\UpdatedAtTrait;
use MailPoetVendor\Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="dynamic_segment_filters")
 */
class DynamicSegmentFilterEntity {
  use AutoincrementedIdTrait;
  use CreatedAtTrait;
  use UpdatedAtTrait;
  use SafeToOneAssociationLoadTrait;

  /**
   * @ORM\ManyToOne(targetEntity="MailPoet\Entities\SegmentEntity", inversedBy="filters")
   * @var SegmentEntity|null
   */
  private $segment;

  /**
   * @ORM\Embedded(class="MailPoet\Entities\DynamicSegmentFilterData", columnPrefix=false)
   * @var DynamicSegmentFilterData
   */
  private $filterData;

  public function __construct(
    SegmentEntity $segment,
    DynamicSegmentFilterData $filterData
  ) {
    $this->segment = $segment;
    $this->filterData = $filterData;
  }

  public function __clone() {
    $this->id = null;
    $this->segment = null;
  }

  /**
   * @return SegmentEntity|null
   */
  public function getSegment() {
    $this->safelyLoadToOneAssociation('segment');
    return $this->segment;
  }

  /**
   * @return DynamicSegmentFilterData
   */
  public function getFilterData() {
    return $this->filterData;
  }

  public function setSegment(SegmentEntity $segment) {
    $this->segment = $segment;
  }

  public function setFilterData(DynamicSegmentFilterData $filterData) {
    $this->filterData = $filterData;
  }
}
