<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Models;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\SegmentEntity;
use MailPoet\WooCommerce\Helper as WCHelper;
use MailPoet\WP\Functions;

/**
 * @property array $subscribersCount
 * @property array $automatedEmailsSubjects
 * @property string $name
 * @property string $type
 * @property string $description
 * @property string $countConfirmations
 *
 * @deprecated This model is deprecated. Use \MailPoet\Newsletter\Segment\SegmentsRepository and
 * \MailPoet\Entities\SegmentEntity instead. This class can be removed after 2024-05-30.
 */
class Segment extends Model {
  public static $_table = MP_SEGMENTS_TABLE; // phpcs:ignore PSR2.Classes.PropertyDeclaration
  const TYPE_WP_USERS = SegmentEntity::TYPE_WP_USERS;
  const TYPE_WC_USERS = SegmentEntity::TYPE_WC_USERS;
  const TYPE_WC_MEMBERSHIPS = SegmentEntity::TYPE_WC_MEMBERSHIPS;
  const TYPE_DEFAULT = SegmentEntity::TYPE_DEFAULT;

  /**
   * @deprecated
   */
  public function __construct() {
    self::deprecationError(__METHOD__);
    parent::__construct();

    $this->addValidations('name', [
      'required' => __('Please specify a name.', 'mailpoet'),
    ]);
  }

  /**
   * @deprecated
   */
  public function delete() {
    self::deprecationError(__METHOD__);
    // delete all relations to subscribers
    SubscriberSegment::where('segment_id', $this->id)->deleteMany();
    return parent::delete();
  }

  /**
   * @deprecated
   */
  public function newsletters() {
    self::deprecationError(__METHOD__);
    return $this->has_many_through(
      __NAMESPACE__ . '\Newsletter',
      __NAMESPACE__ . '\NewsletterSegment',
      'segment_id',
      'newsletter_id'
    );
  }

  /**
   * @deprecated
   */
  public function subscribers() {
    self::deprecationError(__METHOD__);
    return $this->has_many_through(
      __NAMESPACE__ . '\Subscriber',
      __NAMESPACE__ . '\SubscriberSegment',
      'segment_id',
      'subscriber_id'
    );
  }

  /**
   * @deprecated
   */
  public function duplicate($data = []) {
    self::deprecationError(__METHOD__);
    $duplicate = parent::duplicate($data);

    if ($duplicate !== false) {
      foreach ($this->subscribers()->findResultSet() as $relation) {
        $newRelation = SubscriberSegment::create();
        $newRelation->set('subscriber_id', $relation->id);
        $newRelation->set('segment_id', $duplicate->id);
        $newRelation->save();
      }

      return $duplicate;
    }
    return false;
  }

  /**
   * @deprecated
   */
  public function addSubscriber($subscriberId) {
    self::deprecationError(__METHOD__);
    $relation = SubscriberSegment::create();
    $relation->set('subscriber_id', $subscriberId);
    $relation->set('segment_id', $this->id);
    return $relation->save();
  }

  /**
   * @deprecated
   */
  public function removeSubscriber($subscriberId) {
    self::deprecationError(__METHOD__);
    return SubscriberSegment::where('subscriber_id', $subscriberId)
      ->where('segment_id', $this->id)
      ->delete();
  }

  /**
   * @deprecated
   */
  public static function getWPSegment() {
    self::deprecationError(__METHOD__);
    $wpSegment = self::where('type', self::TYPE_WP_USERS)->findOne();

    if ($wpSegment === false) {
      // create the wp users segment
      $wpSegment = Segment::create();
      $wpSegment->hydrate([
        'name' => __('WordPress Users', 'mailpoet'),
        'description' =>
          __('This list contains all of your WordPress users.', 'mailpoet'),
        'type' => self::TYPE_WP_USERS,
      ]);
      $wpSegment->save();
    }

    return $wpSegment;
  }

  /**
   * @deprecated
   */
  public static function getWooCommerceSegment() {
    self::deprecationError(__METHOD__);
    $wcSegment = self::where('type', self::TYPE_WC_USERS)->findOne();

    if ($wcSegment === false) {
      // create the WooCommerce customers segment
      $wcSegment = Segment::create();
      $wcSegment->hydrate([
        'name' => __('WooCommerce Customers', 'mailpoet'),
        'description' =>
          __('This list contains all of your WooCommerce customers.', 'mailpoet'),
        'type' => self::TYPE_WC_USERS,
      ]);
      $wcSegment->save();
    }

    return $wcSegment;
  }

  /**
   * @deprecated Use the non static implementation in \MailPoet\Segments\WooCommerce::shouldShowWooCommerceSegment instead
   */
  public static function shouldShowWooCommerceSegment() {
    self::deprecationError(__METHOD__);
    $woocommerceHelper = new WCHelper(Functions::get());
    $isWoocommerceActive = $woocommerceHelper->isWooCommerceActive();
    $woocommerceUserExists = Segment::tableAlias('segment')
      ->where('segment.type', Segment::TYPE_WC_USERS)
      ->join(
        MP_SUBSCRIBER_SEGMENT_TABLE,
        'segment_subscribers.segment_id = segment.id',
        'segment_subscribers'
      )
      ->limit(1)
      ->findOne();

    if (!$isWoocommerceActive && !$woocommerceUserExists) {
      return false;
    }
    return true;
  }

  /**
   * @deprecated
   */
  public static function getSegmentTypes() {
    self::deprecationError(__METHOD__);
    $types = [Segment::TYPE_DEFAULT, Segment::TYPE_WP_USERS];
    if (Segment::shouldShowWooCommerceSegment()) {
      $types[] = Segment::TYPE_WC_USERS;
    }
    return $types;
  }

  /**
   * @deprecated
   */
  public static function groupBy($orm, $group = null) {
    self::deprecationError(__METHOD__);
    if ($group === 'trash') {
      $orm->whereNotNull('deleted_at');
    } else {
      $orm->whereNull('deleted_at');
    }
    return $orm;
  }

  /**
   * @deprecated
   */
  public static function getPublic() {
    self::deprecationError(__METHOD__);
    return self::getPublished()->where('type', self::TYPE_DEFAULT)->orderByAsc('name');
  }

  /**
   * @deprecated
   */
  public static function bulkTrash($orm) {
    self::deprecationError(__METHOD__);
    $count = parent::bulkAction($orm, function($ids) {
      Segment::rawExecute(join(' ', [
        'UPDATE `' . Segment::$_table . '`',
        'SET `deleted_at` = NOW()',
        'WHERE `id` IN (' . rtrim(str_repeat('?,', count($ids)), ',') . ')',
        'AND `type` = "' . Segment::TYPE_DEFAULT . '"',
      ]), $ids);
    });

    return ['count' => $count];
  }

  /**
   * @deprecated
   */
  public static function bulkDelete($orm) {
    self::deprecationError(__METHOD__);
    $count = parent::bulkAction($orm, function($ids) {
      // delete segments (only default)
      $segments = Segment::whereIn('id', $ids)
        ->where('type', Segment::TYPE_DEFAULT)
        ->findMany();
      $ids = array_map(function($segment) {
        return $segment->id;
      }, $segments);
      if (!$ids) {
        return;
      }
      SubscriberSegment::whereIn('segment_id', $ids)
        ->deleteMany();
      Segment::whereIn('id', $ids)->deleteMany();
    });

    return ['count' => $count];
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
      'Calling ' . esc_html($methodName) . ' is deprecated and will be removed. Use \MailPoet\Newsletter\Segment\SegmentsRepository and \MailPoet\Entities\SegmentEntity instead.',
      E_USER_DEPRECATED
    );
  }
}
