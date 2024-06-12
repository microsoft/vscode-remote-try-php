<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Models;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\ScheduledTaskSubscriberEntity;

/**
 * @property int $taskId
 * @property int $subscriberId
 * @property int $processed
 * @property int $failed
 * @property string $error
 *
 * @deprecated This model is deprecated. Use \MailPoet\Newsletter\Sending\ScheduledTaskSubscribersRepository
 * and \MailPoet\Entities\ScheduledTaskSubscriberEntity instead. This class can be removed after 2024-05-30.
 */
class ScheduledTaskSubscriber extends Model {
  const STATUS_UNPROCESSED = ScheduledTaskSubscriberEntity::STATUS_UNPROCESSED;
  const STATUS_PROCESSED = ScheduledTaskSubscriberEntity::STATUS_PROCESSED;

  const FAIL_STATUS_OK = ScheduledTaskSubscriberEntity::FAIL_STATUS_OK;
  const FAIL_STATUS_FAILED = ScheduledTaskSubscriberEntity::FAIL_STATUS_FAILED;

  const SENDING_STATUS_SENT = ScheduledTaskSubscriberEntity::SENDING_STATUS_SENT;
  const SENDING_STATUS_FAILED = ScheduledTaskSubscriberEntity::SENDING_STATUS_FAILED;
  const SENDING_STATUS_UNPROCESSED = ScheduledTaskSubscriberEntity::SENDING_STATUS_UNPROCESSED;

  public static $_table = MP_SCHEDULED_TASK_SUBSCRIBERS_TABLE; // phpcs:ignore PSR2.Classes.PropertyDeclaration
  public static $_id_column = ['task_id', 'subscriber_id']; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps,PSR2.Classes.PropertyDeclaration

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
  public static function createOrUpdate($data = []) {
    self::deprecationError(__METHOD__);
    if (!is_array($data) || empty($data['task_id']) || empty($data['subscriber_id'])) {
      return;
    }
    $data['processed'] = !empty($data['processed']) ? self::STATUS_PROCESSED : self::STATUS_UNPROCESSED;
    $data['failed'] = !empty($data['failed']) ? self::FAIL_STATUS_FAILED : self::FAIL_STATUS_OK;
    return parent::_createOrUpdate($data, [
      'subscriber_id' => $data['subscriber_id'],
      'task_id' => $data['task_id'],
    ]);
  }

  /**
   * @deprecated This method can be removed after 2024-01-04.
   */
  public static function setSubscribers($taskId, array $subscriberIds) {
    self::deprecationError(__METHOD__);
    static::clearSubscribers($taskId);
    return static::addSubscribers($taskId, $subscriberIds);
  }

  /**
   * For large batches use MailPoet\Segments\SubscribersFinder::addSubscribersToTaskFromSegments()
   *
   * @deprecated
   */
  public static function addSubscribers($taskId, array $subscriberIds) {
    self::deprecationError(__METHOD__);
    foreach ($subscriberIds as $subscriberId) {
      self::createOrUpdate([
        'task_id' => $taskId,
        'subscriber_id' => $subscriberId,
      ]);
    }
  }

  /**
   * @deprecated
   */
  public static function clearSubscribers($taskId) {
    self::deprecationError(__METHOD__);
    return self::where('task_id', $taskId)->deleteMany();
  }

  /**
   * @deprecated
   */
  public static function getUnprocessedCount($taskId) {
    self::deprecationError(__METHOD__);
    return self::getCount($taskId, self::STATUS_UNPROCESSED);
  }

  /**
   * @deprecated
   */
  public static function getProcessedCount($taskId) {
    self::deprecationError(__METHOD__);
    return self::getCount($taskId, self::STATUS_PROCESSED);
  }

  /**
   * @deprecated
   */
  private static function getCount($taskId, $processed = null) {
    self::deprecationError(__METHOD__);
    $orm = self::where('task_id', $taskId);
    if (!is_null($processed)) {
      $orm->where('processed', $processed);
    }
    return $orm->count();
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
      'Calling ' . esc_html($methodName) . ' is deprecated and will be removed. Use \MailPoet\Newsletter\Sending\ScheduledTaskSubscribersRepository and \MailPoet\Entities\ScheduledTaskSubscriberEntity instead.',
      E_USER_DEPRECATED
    );
  }
}
