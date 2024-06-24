<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Entities;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\EntityTraits\AutoincrementedIdTrait;
use MailPoet\Doctrine\EntityTraits\CreatedAtTrait;
use MailPoet\Doctrine\EntityTraits\UpdatedAtTrait;
use MailPoetVendor\Doctrine\ORM\Mapping as ORM;
use MailPoetVendor\Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="newsletter_option_fields")
 */
class NewsletterOptionFieldEntity {
  // names
  public const NAME_AFTER_TIME_NUMBER = 'afterTimeNumber';
  public const NAME_AFTER_TIME_TYPE = 'afterTimeType';
  public const NAME_EVENT = 'event';
  public const NAME_GROUP = 'group';
  public const NAME_INTERVAL_TYPE = 'intervalType';
  public const NAME_IS_SCHEDULED = 'isScheduled';
  public const NAME_META = 'meta';
  public const NAME_MONTH_DAY = 'monthDay';
  public const NAME_NTH_WEEK_DAY = 'nthWeekDay';
  public const NAME_ROLE = 'role';
  public const NAME_SCHEDULE = 'schedule';
  public const NAME_SCHEDULED_AT = 'scheduledAt';
  public const NAME_SEGMENT = 'segment';
  public const NAME_SEND_TO = 'sendTo';
  public const NAME_TIME_OF_DAY = 'timeOfDay';
  public const NAME_WEEK_DAY = 'weekDay';
  public const NAME_AUTOMATION_ID = 'automationId';
  public const NAME_AUTOMATION_STEP_ID = 'automationStepId';
  public const NAME_FILTER_SEGMENT_ID = 'filterSegmentId';

  use AutoincrementedIdTrait;
  use CreatedAtTrait;
  use UpdatedAtTrait;

  /**
   * @ORM\Column(type="string")
   * @Assert\NotBlank()
   * @var string
   */
  private $name;

  /**
   * @ORM\Column(type="string")
   * @Assert\NotBlank()
   * @var string
   */
  private $newsletterType;

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
  public function getNewsletterType() {
    return $this->newsletterType;
  }

  /**
   * @param string $newsletterType
   */
  public function setNewsletterType($newsletterType) {
    $this->newsletterType = $newsletterType;
  }
}
