<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\WooCommerce\Fields;

if (!defined('ABSPATH')) exit;


use DateTimeImmutable;
use MailPoet\Automation\Engine\Data\Field;
use MailPoet\Automation\Engine\WordPress;
use MailPoet\Automation\Integrations\WooCommerce\Payloads\CustomerPayload;
use WC_Customer;

class CustomerReviewFieldsFactory {
  /** @var WordPress */
  private $wordPress;

  public function __construct(
    WordPress $wordPress
  ) {
    $this->wordPress = $wordPress;
  }

  /** @return Field[] */
  public function getFields(): array {
    return [
      new Field(
        'woocommerce:customer:review-count',
        Field::TYPE_INTEGER,
        __('Review count', 'mailpoet'),
        function (CustomerPayload $payload, array $params = []) {
          $customer = $payload->getCustomer();
          if (!$customer) {
            return 0;
          }
          $inTheLastSeconds = isset($params['in_the_last']) ? (int)$params['in_the_last'] : null;
          return $this->getUniqueProductReviewCount($customer, $inTheLastSeconds);
        },
        [
          'params' => ['in_the_last'],
        ]
      ),
      new Field(
        'woocommerce:customer:last-review-date',
        Field::TYPE_DATETIME,
        __('Last review date', 'mailpoet'),
        function (CustomerPayload $payload) {
          $customer = $payload->getCustomer();
          return $customer ? $this->getLastReviewDate($customer) : null;
        }
      ),
    ];
  }

  /**
   * Calculate the customer's review count excluding multiple reviews on the same product.
   * Inspired by AutomateWoo implementation.
   */
  private function getUniqueProductReviewCount(WC_Customer $customer, int $inTheLastSeconds = null): int {
    $wpdb = $this->wordPress->getWpdb();
    $inTheLastFilter = isset($inTheLastSeconds) ? "AND c.comment_date_gmt >= DATE_SUB(current_timestamp, INTERVAL %d SECOND)" : '';

    /** @var literal-string $sql */
    $sql = "
      SELECT COUNT(DISTINCT c.comment_post_ID) FROM {$wpdb->comments} c
      JOIN {$wpdb->posts} p ON c.comment_post_ID = p.ID
      WHERE p.post_type = 'product'
      AND c.comment_parent = 0
      AND c.comment_approved = 1
      AND c.comment_type = 'review'
      AND (c.user_ID = %d OR c.comment_author_email = %s)
      $inTheLastFilter
    ";
    return (int)$wpdb->get_var(
      (string)$wpdb->prepare(
        $sql,
        array_merge(
          [$customer->get_id(), $customer->get_email()],
          isset($inTheLastSeconds) ? [intval($inTheLastSeconds)] : []
        )
      )
    );
  }

  private function getLastReviewDate(WC_Customer $customer): ?DateTimeImmutable {
    $wpdb = $this->wordPress->getWpdb();
    /** @var literal-string $sql */
    $sql = "
      SELECT c.comment_date FROM {$wpdb->comments} c
      JOIN {$wpdb->posts} p ON c.comment_post_ID = p.ID
      WHERE p.post_type = 'product'
      AND c.comment_parent = 0
      AND c.comment_approved = 1
      AND c.comment_type = 'review'
      AND (c.user_ID = %d OR c.comment_author_email = %s)
      ORDER BY c.comment_date DESC
      LIMIT 1
    ";

    $date = $wpdb->get_var(
      (string)$wpdb->prepare($sql, [$customer->get_id(), $customer->get_email()])
    );
    return $date ? new DateTimeImmutable($date, $this->wordPress->wpTimezone()) : null;
  }
}
