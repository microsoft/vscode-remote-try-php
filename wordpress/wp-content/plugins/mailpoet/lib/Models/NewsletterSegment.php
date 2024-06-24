<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Models;

if (!defined('ABSPATH')) exit;


/**
 * @property int $newsletterId
 * @property int $segmentId
 * @property string $updatedAt
 *
 * @deprecated This model is deprecated. Use \MailPoet\Newsletter\Segment\NewsletterSegmentRepository
 * and \MailPoet\Entities\NewsletterSegmentEntity instead. This class can be removed after 2024-05-30.
 */
class NewsletterSegment extends Model {
  public static $_table = MP_NEWSLETTER_SEGMENT_TABLE; // phpcs:ignore PSR2.Classes.PropertyDeclaration

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
      'Calling ' . esc_html($methodName) . ' is deprecated and will be removed. Use \MailPoet\Newsletter\Segment\NewsletterSegmentRepository and \MailPoet\Entities\NewsletterSegmentEntity instead.',
      E_USER_DEPRECATED
    );
  }
}
