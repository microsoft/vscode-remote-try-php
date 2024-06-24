<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Models;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\SendingQueueEntity;
use MailPoet\Util\Helpers;

/**
 * @property int $countProcessed
 * @property int $countToProcess
 * @property int $countTotal
 * @property string|array $newsletterRenderedBody
 * @property string $newsletterRenderedSubject
 * @property int $taskId
 * @property int $newsletterId
 * @property string|object|array|null $meta
 * @property string|array $subscribers
 * @property string|null $deletedAt
 * @property string $scheduledAt
 * @property string $status
 *
 * @deprecated This model is deprecated. Use \MailPoet\Newsletter\Sending\SendingQueuesRepository
 * and \MailPoet\Entities\SendingQueueEntity instead. This class can be removed after 2024-05-30.
 */
class SendingQueue extends Model {
  public static $_table = MP_SENDING_QUEUES_TABLE; // phpcs:ignore PSR2.Classes.PropertyDeclaration
  const STATUS_COMPLETED = SendingQueueEntity::STATUS_COMPLETED;
  const STATUS_SCHEDULED = SendingQueueEntity::STATUS_SCHEDULED;
  const STATUS_PAUSED = SendingQueueEntity::STATUS_PAUSED;
  const PRIORITY_HIGH = SendingQueueEntity::PRIORITY_HIGH;
  const PRIORITY_MEDIUM = SendingQueueEntity::PRIORITY_MEDIUM;
  const PRIORITY_LOW = SendingQueueEntity::PRIORITY_LOW;

  /**
   * @deprecated
   */
  public function __construct() {
    self::deprecationError(__METHOD__);
    parent::__construct();

    $this->addValidations('newsletter_rendered_body', [
      'validRenderedNewsletterBody' => __('Rendered newsletter body is invalid!', 'mailpoet'),
    ]);
  }

  /**
   * @deprecated
   */
  public function task() {
    self::deprecationError(__METHOD__);
    return $this->hasOne(__NAMESPACE__ . '\ScheduledTask', 'id', 'task_id');
  }

  /**
   * @deprecated
   */
  public function newsletter() {
    self::deprecationError(__METHOD__);
    return $this->has_one(__NAMESPACE__ . '\Newsletter', 'id', 'newsletter_id');
  }

  /**
   * @deprecated
   */
  public function pause() {
    self::deprecationError(__METHOD__);
    if ($this->countProcessed === $this->countTotal) {
      return false;
    } else {
      return $this->task()->findOne()->pause();
    }
  }

  /**
   * @deprecated
   */
  public function resume() {
    self::deprecationError(__METHOD__);
    if ($this->countProcessed === $this->countTotal) {
      return $this->complete();
    } else {
      $this->newsletter()->findOne()->setStatus(Newsletter::STATUS_SENDING);
      return $this->task()->findOne()->resume();
    }
  }

  /**
   * @deprecated
   */
  public function complete() {
    self::deprecationError(__METHOD__);
    return $this->task()->findOne()->complete();
  }

  /**
   * @deprecated
   */
  public function save() {
    self::deprecationError(__METHOD__);
    $this->newsletterRenderedBody = $this->getNewsletterRenderedBody();
    if (!Helpers::isJson($this->newsletterRenderedBody) && !is_null($this->newsletterRenderedBody)) {
      $this->set(
        'newsletter_rendered_body',
        (string)json_encode($this->newsletterRenderedBody)
      );
    }
    if (!is_null($this->meta) && !Helpers::isJson($this->meta)) {
      $this->set(
        'meta',
        (string)json_encode($this->meta)
      );
    }
    parent::save();
    $this->newsletterRenderedBody = $this->getNewsletterRenderedBody();
    return $this;
  }

  /**
   * @deprecated
   */
  public function getNewsletterRenderedBody($type = false) {
    self::deprecationError(__METHOD__);
    $renderedNewsletter = $this->decodeRenderedNewsletterBodyObject($this->newsletterRenderedBody);
    return ($type && !empty($renderedNewsletter[$type])) ?
      $renderedNewsletter[$type] :
      $renderedNewsletter;
  }

  /**
   * @deprecated
   */
  public function getMeta() {
    self::deprecationError(__METHOD__);
    return (Helpers::isJson($this->meta) && is_string($this->meta)) ? json_decode($this->meta, true) : $this->meta;
  }

  /**
   * @deprecated
   */
  public function asArray() {
    self::deprecationError(__METHOD__);
    $model = parent::asArray();
    $model['newsletter_rendered_body'] = $this->getNewsletterRenderedBody();
    $model['meta'] = $this->getMeta();
    return $model;
  }

  /**
   * @deprecated
   */
  private function decodeRenderedNewsletterBodyObject($renderedBody) {
    self::deprecationError(__METHOD__);
    if (is_serialized($renderedBody)) {
      return unserialize($renderedBody);
    }
    if (Helpers::isJson($renderedBody)) {
      return json_decode($renderedBody, true);
    }
    return $renderedBody;
  }

  /**
   * @deprecated
   */
  public static function getTasks() {
    self::deprecationError(__METHOD__);
    return ScheduledTask::tableAlias('tasks')
    ->selectExpr('tasks.*')
    ->join(
      MP_SENDING_QUEUES_TABLE,
      'tasks.id = queues.task_id',
      'queues'
    );
  }

  /**
   * @deprecated
   */
  public static function joinWithTasks() {
    self::deprecationError(__METHOD__);
    return static::tableAlias('queues')
    ->join(
      MP_SCHEDULED_TASKS_TABLE,
      'tasks.id = queues.task_id',
      'tasks'
    );
  }

  /**
   * @deprecated
   */
  public static function joinWithSubscribers() {
    self::deprecationError(__METHOD__);
    return static::joinWithTasks()
    ->join(
      MP_SCHEDULED_TASK_SUBSCRIBERS_TABLE,
      'tasks.id = subscribers.task_id',
      'subscribers'
    );
  }

  /**
   * @deprecated
   */
  public static function findTaskByNewsletterId($newsletterId) {
    self::deprecationError(__METHOD__);
    return static::getTasks()
    ->where('queues.newsletter_id', $newsletterId);
  }

  /**
   * @deprecated This is here for displaying the deprecation warning for properties.
   */
  public function __get($key) {
    self::deprecationError('property "' . $key . '"');
    return parent::__get($key);
  }

  /**
   * @deprecated This is here for displaying the deprecation warning for static calls.
   */
  public static function __callStatic($name, $arguments) {
    self::deprecationError($name);
    return parent::__callStatic($name, $arguments);
  }

  private static function deprecationError($methodName) {
    trigger_error(
      'Calling ' . esc_html($methodName) . ' is deprecated and will be removed. Use \MailPoet\Newsletter\Sending\SendingQueuesRepository and \MailPoet\Entities\SendingQueueEntity instead.',
      E_USER_DEPRECATED
    );
  }
}
