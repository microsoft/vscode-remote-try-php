<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Models;

if (!defined('ABSPATH')) exit;


use MailPoet\Util\Helpers;

/**
 * @property int $subscriberId
 * @property int $customFieldId
 * @property string $value
 *
 * @deprecated This model is deprecated. Use \MailPoet\Subscribers\SubscriberCustomFieldRepository
 * and \MailPoet\Entities\SubscriberCustomFieldEntity instead. This class can be removed after 2024-05-30.
 */
class SubscriberCustomField extends Model {
  public static $_table = MP_SUBSCRIBER_CUSTOM_FIELD_TABLE; // phpcs:ignore PSR2.Classes.PropertyDeclaration

  /**
   * @deprecated
   */
  public static function createOrUpdate($data = []) {
    self::deprecationError(__METHOD__);
    $customField = CustomField::findOne($data['custom_field_id']);
    if ($customField instanceof CustomField) {
      $customField = $customField->asArray();
    } else {
      return false;
    }

    if ($customField['type'] === 'date') {
      if (is_array($data['value'])) {
        $day = (
          isset($data['value']['day'])
          ? (int)$data['value']['day']
          : 1
        );
        $month = (
          isset($data['value']['month'])
          ? (int)$data['value']['month']
          : 1
        );
        $year = (
          isset($data['value']['year'])
          ? (int)$data['value']['year']
          : 1970
        );
        $data['value'] = mktime(0, 0, 0, $month, $day, $year);
      }
    }

    return parent::_createOrUpdate($data, [
      'custom_field_id' => $data['custom_field_id'],
      'subscriber_id' => $data['subscriber_id'],
    ]);
  }

  /**
   * @deprecated
   */
  public static function createMultiple($values) {
    self::deprecationError(__METHOD__);
    return self::rawExecute(
      'INSERT IGNORE INTO `' . self::$_table . '` ' .
      '(custom_field_id, subscriber_id, value) ' .
      'VALUES ' . rtrim(
        str_repeat(
          '(?, ?, ?)' . ', ',
          count($values)
        ),
        ', '
      ),
      Helpers::flattenArray($values)
    );
  }

  /**
   * @deprecated
   */
  public static function updateMultiple($values) {
    self::deprecationError(__METHOD__);
    $subscriberIds = array_unique(array_column($values, 1));
    $query = sprintf(
      "UPDATE `%s` SET value = (CASE %s ELSE value END) WHERE subscriber_id IN (%s)",
      self::$_table,
      str_repeat('WHEN custom_field_id = ? AND subscriber_id = ? THEN ? ', count($values)),
      implode(',', $subscriberIds)
    );
    self::rawExecute(
      $query,
      Helpers::flattenArray($values)
    );
  }

  /**
   * @deprecated
   */
  public static function deleteSubscriberRelations($subscriber) {
    self::deprecationError(__METHOD__);
    if ($subscriber === false) return false;
    $relations = self::where('subscriber_id', $subscriber->id);
    return $relations->deleteMany();
  }

  /**
   * @deprecated
   */
  public static function deleteManySubscriberRelations(array $subscriberIds) {
    self::deprecationError(__METHOD__);
    if (empty($subscriberIds)) return false;
    $relations = self::whereIn('subscriber_id', $subscriberIds);
    return $relations->deleteMany();
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
      'Calling ' . esc_html($methodName) . ' is deprecated and will be removed. Use \MailPoet\Subscribers\SubscriberCustomFieldRepository and \MailPoet\Entities\SubscriberCustomFieldEntity instead.',
      E_USER_DEPRECATED
    );
  }
}
