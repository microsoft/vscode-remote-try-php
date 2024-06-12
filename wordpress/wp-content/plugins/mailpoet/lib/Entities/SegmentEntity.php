<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Entities;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\EntityTraits\AutoincrementedIdTrait;
use MailPoet\Doctrine\EntityTraits\CreatedAtTrait;
use MailPoet\Doctrine\EntityTraits\DeletedAtTrait;
use MailPoet\Doctrine\EntityTraits\UpdatedAtTrait;
use MailPoetVendor\Doctrine\Common\Collections\ArrayCollection;
use MailPoetVendor\Doctrine\ORM\Mapping as ORM;
use MailPoetVendor\Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="segments")
 */
class SegmentEntity {
  use AutoincrementedIdTrait;
  use CreatedAtTrait;
  use UpdatedAtTrait;
  use DeletedAtTrait;

  const TYPE_WP_USERS = 'wp_users';
  const TYPE_WC_USERS = 'woocommerce_users';
  const TYPE_WC_MEMBERSHIPS = 'woocommerce_memberships';
  const TYPE_DEFAULT = 'default';
  const TYPE_DYNAMIC = 'dynamic';
  const TYPE_WITHOUT_LIST = 'without-list';

  const SEGMENT_ENABLED = 'active';
  const SEGMENT_DISABLED = 'disabled';

  /**
   * @ORM\Column(type="string")
   * @Assert\NotBlank()
   * @var string
   */
  private $name;

  /**
   * @ORM\Column(type="string")
   * @var string
   */
  private $type;

  /**
   * @ORM\Column(type="string")
   * @var string
   */
  private $description;

  /**
   * @ORM\OneToMany(targetEntity="MailPoet\Entities\DynamicSegmentFilterEntity", mappedBy="segment")
   * @var ArrayCollection<int, DynamicSegmentFilterEntity>
   */
  private $dynamicFilters;

  /**
   * @ORM\Column(type="float", nullable=true)
   * @var float|null
   */
  private $averageEngagementScore;

  /**
   * @ORM\Column(type="datetimetz", nullable=true)
   * @var \DateTimeInterface|null
   */
  private $averageEngagementScoreUpdatedAt;

  /**
   * @ORM\Column(type="boolean")
   * @var bool
   */
  private $displayInManageSubscriptionPage = false;

  public function __construct(
    string $name,
    string $type,
    string $description
  ) {
    $this->name = $name;
    $this->type = $type;
    $this->description = $description;
    $this->dynamicFilters = new ArrayCollection();
  }

  public function __clone() {
    // reset ID
    $this->id = null;
    $this->dynamicFilters = new ArrayCollection();
  }

  /**
   * @return string
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @param string $name
   */
  public function setName($name) {
    $this->name = $name;
  }

  /**
   * @return string
   */
  public function getType() {
    return $this->type;
  }

  /**
   * @param string $type
   */
  public function setType($type) {
    $this->type = $type;
  }

  /**
   * @return string
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * @param string $description
   */
  public function setDescription($description) {
    $this->description = $description;
  }

  /**
   * @return ArrayCollection<int, DynamicSegmentFilterEntity>
   */
  public function getDynamicFilters() {
    return $this->dynamicFilters;
  }

  public function addDynamicFilter(DynamicSegmentFilterEntity $dynamicSegmentFilterEntity) {
    $this->dynamicFilters->add($dynamicSegmentFilterEntity);
  }

  public function isStatic(): bool {
    return in_array($this->getType(), [self::TYPE_DEFAULT, self::TYPE_WP_USERS, self::TYPE_WC_USERS, self::TYPE_WC_MEMBERSHIPS], true);
  }

  public function getAverageEngagementScore(): ?float {
    return $this->averageEngagementScore;
  }

  public function setAverageEngagementScore(?float $averageEngagementScore): void {
    $this->averageEngagementScore = $averageEngagementScore;
  }

  public function getAverageEngagementScoreUpdatedAt(): ?\DateTimeInterface {
    return $this->averageEngagementScoreUpdatedAt;
  }

  public function setAverageEngagementScoreUpdatedAt(?\DateTimeInterface $averageEngagementScoreUpdatedAt): void {
    $this->averageEngagementScoreUpdatedAt = $averageEngagementScoreUpdatedAt;
  }

  public function getDisplayInManageSubscriptionPage(): bool {
    return $this->displayInManageSubscriptionPage;
  }

  public function setDisplayInManageSubscriptionPage(bool $state): void {
    $this->displayInManageSubscriptionPage = $state;
  }

  /**
   * Returns connect operand from the first filter, when doesn't exist, then returns a default value.
   * @return string
   */
  public function getFiltersConnectOperator(): string {
    $firstFilter = $this->getDynamicFilters()->first();
    $filterData = $firstFilter ? $firstFilter->getFilterData() : null;
    if (!$firstFilter || !$filterData) {
      return DynamicSegmentFilterData::CONNECT_TYPE_AND;
    }
    return $filterData->getParam('connect') ?: DynamicSegmentFilterData::CONNECT_TYPE_AND;
  }
}
