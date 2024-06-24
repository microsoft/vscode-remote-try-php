<?php declare(strict_types = 1);

namespace MailPoet\API\JSON\v1;

if (!defined('ABSPATH')) exit;


use MailPoet\API\JSON\Endpoint as APIEndpoint;
use MailPoet\API\JSON\SuccessResponse;
use MailPoet\Config\AccessControl;
use MailPoet\WooCommerce\Helper;
use MailPoet\WP\Functions as WPFunctions;

class Coupons extends APIEndpoint {
  public const DEFAULT_PAGE_SIZE = 100;

  /** @var Helper  */
  public $helper;

  /*** @var WPFunctions */
  private $wp;

  public $permissions = [
    'global' => AccessControl::PERMISSION_MANAGE_EMAILS,
  ];

  public function __construct(
    WPFunctions $wp,
    Helper $helper
  ) {
    $this->wp = $wp;
    $this->helper = $helper;
  }

  public function getCoupons(array $data = []): SuccessResponse {
    $pageSize = $data['page_size'] ?? self::DEFAULT_PAGE_SIZE;
    $pageNumber = $data['page_number'] ?? 1;
    $discountType = $data['discount_type'] ?? null;
    $search = $data['search'] ?? null;
    $includeCouponIds = $data['include_coupon_ids'] ?? [];
    return $this->successResponse(
      $this->formatCoupons($this->helper->getCouponList(
        (int)$pageSize,
        (int)$pageNumber,
        $discountType,
        $search,
        $includeCouponIds
      ))
    );
  }

  /**
   * @param array $couponPosts
   * @return array
   */
  private function formatCoupons(array $couponPosts): array {
    return array_map(function (\WP_Post $post): array {
      $discountType = $this->wp->getPostMeta($post->ID, 'discount_type', true);
      return [
        'id' => $post->ID,
        'text' => $post->post_title, // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        'excerpt' => $post->post_excerpt, // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        'discountType' => $discountType,
      ];
    }, $couponPosts);
  }
}
