<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Entities;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\EntityTraits\AutoincrementedIdTrait;
use MailPoet\Doctrine\EntityTraits\CreatedAtTrait;
use MailPoet\Doctrine\EntityTraits\DeletedAtTrait;
use MailPoet\Doctrine\EntityTraits\SafeToOneAssociationLoadTrait;
use MailPoet\Doctrine\EntityTraits\UpdatedAtTrait;
use MailPoet\Util\Helpers;
use MailPoetVendor\Doctrine\ORM\Mapping as ORM;
use MailPoetVendor\Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="sending_queues")
 */
class SendingQueueEntity {
  const STATUS_COMPLETED = 'completed';
  const STATUS_SCHEDULED = 'scheduled';
  const STATUS_PAUSED = 'paused';
  const PRIORITY_HIGH = 1;
  const PRIORITY_MEDIUM = 5;
  const PRIORITY_LOW = 10;

  use AutoincrementedIdTrait;
  use CreatedAtTrait;
  use UpdatedAtTrait;
  use DeletedAtTrait;
  use SafeToOneAssociationLoadTrait;

  /**
   * @ORM\Column(type="json_or_serialized")
   * @Assert\Type("array")
   * @Assert\Collection(
   *   fields = {
   *     "html" = @Assert\NotBlank(),
   *     "text" = @Assert\NotBlank(),
   *   }
   * )
   * @var array|null
   */
  private $newsletterRenderedBody;

  /**
   * @ORM\Column(type="string", nullable=true)
   * @var string|null
   */
  private $newsletterRenderedSubject;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  private $countTotal = 0;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  private $countProcessed = 0;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  private $countToProcess = 0;

  /**
   * @ORM\Column(type="json", nullable=true)
   * @var array|null
   */
  private $meta;

  /**
   * @ORM\OneToOne(targetEntity="MailPoet\Entities\ScheduledTaskEntity", fetch="EAGER")
   * @var ScheduledTaskEntity|null
   */
  private $task;

  /**
   * @ORM\ManyToOne(targetEntity="MailPoet\Entities\NewsletterEntity", inversedBy="queues")
   * @var NewsletterEntity|null
   */
  private $newsletter;

  /**
   * @deprecated This is here only for backward compatibility with custom shortcodes https://kb.mailpoet.com/article/160-create-a-custom-shortcode
   * This can be removed after 2021-08-01
   */
  public function __get($key) {
    $getterName = 'get' . Helpers::underscoreToCamelCase($key, $capitaliseFirstChar = true);
    $callable = [$this, $getterName];
    if (is_callable($callable)) {
      return call_user_func($callable);
    }
  }

  /**
   * @return array|null
   */
  public function getNewsletterRenderedBody() {
    return $this->newsletterRenderedBody;
  }

  /**
   * @param array|null $newsletterRenderedBody
   */
  public function setNewsletterRenderedBody($newsletterRenderedBody) {
    $this->newsletterRenderedBody = $newsletterRenderedBody;
  }

  /**
   * @return string|null
   */
  public function getNewsletterRenderedSubject() {
    return $this->newsletterRenderedSubject;
  }

  /**
   * @param string|null $newsletterRenderedSubject
   */
  public function setNewsletterRenderedSubject($newsletterRenderedSubject) {
    $this->newsletterRenderedSubject = $newsletterRenderedSubject;
  }

  /**
   * @return int
   */
  public function getCountTotal() {
    return $this->countTotal;
  }

  /**
   * @param int $countTotal
   */
  public function setCountTotal($countTotal) {
    $this->countTotal = $countTotal;
  }

  /**
   * @return int
   */
  public function getCountProcessed() {
    return $this->countProcessed;
  }

  /**
   * @param int $countProcessed
   */
  public function setCountProcessed($countProcessed) {
    $this->countProcessed = $countProcessed;
  }

  /**
   * @return int
   */
  public function getCountToProcess() {
    return $this->countToProcess;
  }

  /**
   * @param int $countToProcess
   */
  public function setCountToProcess($countToProcess) {
    $this->countToProcess = $countToProcess;
  }

  /**
   * @return array|null
   */
  public function getMeta() {
    return $this->meta;
  }

  /**
   * @param array|null $meta
   */
  public function setMeta($meta) {
    $this->meta = $meta;
  }

  /**
   * @return ScheduledTaskEntity|null
   */
  public function getTask() {
    $this->safelyLoadToOneAssociation('task');
    return $this->task;
  }

  public function setTask(ScheduledTaskEntity $task) {
    $this->task = $task;
  }

  /**
   * @return NewsletterEntity|null
   */
  public function getNewsletter() {
    $this->safelyLoadToOneAssociation('newsletter');
    return $this->newsletter;
  }

  public function setNewsletter(NewsletterEntity $newsletter) {
    $this->newsletter = $newsletter;
  }
}
