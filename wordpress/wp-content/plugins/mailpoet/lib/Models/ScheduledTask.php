<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Models;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Util\Helpers;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Idiorm\ORM;

/**
 * @property int $id
 * @property string $processedAt
 * @property string|null $status
 * @property string|null $type
 * @property int $priority
 * @property string|null $scheduledAt
 * @property bool|null $inProgress
 * @property int $rescheduleCount
 * @property string|array|null $meta
 *
 * @deprecated This model is deprecated. Use \MailPoet\Newsletter\Sending\ScheduledTasksRepository
 * and \MailPoet\Entities\ScheduledTaskEntity instead. This class can be removed after 2024-05-30.
 */
class ScheduledTask extends Model {
  public static $_table = MP_SCHEDULED_TASKS_TABLE; // phpcs:ignore PSR2.Classes.PropertyDeclaration
  const STATUS_COMPLETED = ScheduledTaskEntity::STATUS_COMPLETED;
  const STATUS_SCHEDULED = ScheduledTaskEntity::STATUS_SCHEDULED;
  const STATUS_PAUSED = ScheduledTaskEntity::STATUS_PAUSED;
  const STATUS_INVALID = ScheduledTaskEntity::STATUS_INVALID;
  const VIRTUAL_STATUS_RUNNING = ScheduledTaskEntity::VIRTUAL_STATUS_RUNNING; // For historical reasons this is stored as null in DB
  const PRIORITY_HIGH = ScheduledTaskEntity::PRIORITY_HIGH;
  const PRIORITY_MEDIUM = ScheduledTaskEntity::PRIORITY_MEDIUM;
  const PRIORITY_LOW = ScheduledTaskEntity::PRIORITY_LOW;

  const BASIC_RESCHEDULE_TIMEOUT = ScheduledTaskEntity::BASIC_RESCHEDULE_TIMEOUT;
  const MAX_RESCHEDULE_TIMEOUT = ScheduledTaskEntity::MAX_RESCHEDULE_TIMEOUT;

  private $wp;

  /**
   * @deprecated
   */
  public function __construct() {
    self::deprecationError(__METHOD__);
    parent::__construct();
    $this->wp = WPFunctions::get();
  }

  /**
   * @deprecated
   */
  public function subscribers() {
    self::deprecationError(__METHOD__);
    return $this->hasManyThrough(
      __NAMESPACE__ . '\Subscriber',
      __NAMESPACE__ . '\ScheduledTaskSubscriber',
      'task_id',
      'subscriber_id'
    );
  }

  /**
   * @deprecated
   */
  public function pause() {
    self::deprecationError(__METHOD__);
    $this->set('status', self::STATUS_PAUSED);
    $this->save();
    return ($this->getErrors() === false && $this->id() > 0);
  }

  /**
   * @deprecated
   */
  public static function pauseAllByNewsletter(Newsletter $newsletter) {
    self::deprecationError(__METHOD__);
    ScheduledTask::rawExecute(
      'UPDATE `' . ScheduledTask::$_table . '` t ' .
      'JOIN `' . SendingQueue::$_table . '` q ON t.`id` = q.`task_id` ' .
      'SET t.`status` = "' . self::STATUS_PAUSED . '" ' .
      'WHERE ' .
      'q.`newsletter_id` = ' . $newsletter->id() .
      ' AND t.`status` = "' . self::STATUS_SCHEDULED . '" '
    );
  }

  /**
   * @deprecated
   */
  public function resume() {
    self::deprecationError(__METHOD__);
    $this->setExpr('status', 'NULL');
    $this->save();
    return ($this->getErrors() === false && $this->id() > 0);
  }

  /**
   * @deprecated
   */
  public static function setScheduledAllByNewsletter(Newsletter $newsletter) {
    self::deprecationError(__METHOD__);
    ScheduledTask::rawExecute(
      'UPDATE `' . ScheduledTask::$_table . '` t ' .
      'JOIN `' . SendingQueue::$_table . '` q ON t.`id` = q.`task_id` ' .
      'SET t.`status` = "' . self::STATUS_SCHEDULED . '" ' .
      'WHERE ' .
      'q.`newsletter_id` = ' . $newsletter->id() .
      ' AND t.`status` = "' . self::STATUS_PAUSED . '" ' .
      ' AND t.`scheduled_at` > CURDATE() - INTERVAL 30 DAY'
    );
  }

  /**
   * @deprecated
   */
  public function complete() {
    self::deprecationError(__METHOD__);
    $this->processedAt = $this->wp->currentTime('mysql');
    $this->set('status', self::STATUS_COMPLETED);
    $this->save();
    return ($this->getErrors() === false && $this->id() > 0);
  }

  /**
   * @deprecated
   */
  public function save() {
    self::deprecationError(__METHOD__);
    // set the default priority to medium
    if (!$this->priority) {
      $this->priority = self::PRIORITY_MEDIUM;
    }
    if (!is_null($this->meta) && !Helpers::isJson($this->meta)) {
      $this->set(
        'meta',
        (string)json_encode($this->meta)
      );
    }
    parent::save();
    return $this;
  }

  /**
   * @deprecated
   */
  public function asArray() {
    self::deprecationError(__METHOD__);
    $model = parent::asArray();
    $model['meta'] = $this->getMeta();
    return $model;
  }

  /**
   * @deprecated
   */
  public function getMeta() {
    self::deprecationError(__METHOD__);
    $meta = (Helpers::isJson($this->meta) && is_string($this->meta)) ? json_decode($this->meta, true) : $this->meta;
    return !empty($meta) ? (array)$meta : [];
  }

  /**
   * @deprecated
   */
  public function delete() {
    self::deprecationError(__METHOD__);
    try {
      ORM::get_db()->beginTransaction();
      ScheduledTaskSubscriber::where('task_id', $this->id)->deleteMany();
      parent::delete();
      ORM::get_db()->commit();
    } catch (\Exception $error) {
      ORM::get_db()->rollBack();
      throw $error;
    }
    return null;
  }

  /**
   * @return ScheduledTask|null
   * @deprecated
   */
  public static function findOneScheduledByNewsletterIdAndSubscriberId($newsletterId, $subscriberId) {
    self::deprecationError(__METHOD__);
    return ScheduledTask::tableAlias('tasks')
      ->select('tasks.*')
      ->innerJoin(SendingQueue::$_table, 'queues.task_id = tasks.id', 'queues')
      ->innerJoin(ScheduledTaskSubscriber::$_table, 'task_subscribers.task_id = tasks.id', 'task_subscribers')
      ->where('queues.newsletter_id', $newsletterId)
      ->where('tasks.status', ScheduledTask::STATUS_SCHEDULED)
      ->where('task_subscribers.subscriber_id', $subscriberId)
      ->whereNull('queues.deleted_at')
      ->whereNull('tasks.deleted_at')
      ->findOne() ?: null;
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
      'Calling ' . esc_html($methodName) . ' is deprecated and will be removed. Use \MailPoet\Newsletter\Sending\ScheduledTasksRepository and \MailPoet\Entities\ScheduledTaskEntity instead.',
      E_USER_DEPRECATED
    );
  }
}
