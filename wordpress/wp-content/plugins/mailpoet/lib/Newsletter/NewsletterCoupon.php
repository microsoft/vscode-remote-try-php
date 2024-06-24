<?php declare(strict_types = 1);

namespace MailPoet\Newsletter;

if (!defined('ABSPATH')) exit;


use MailPoet\Newsletter\Renderer\Blocks\Coupon;

class NewsletterCoupon {
  public function cleanupBodySensitiveData(array $newsletterBody): array {

    if (!is_array($newsletterBody) || empty($newsletterBody['content'])) {
      return $newsletterBody;
    }
    $cleanBlocks = $this->cleanupCouponBlocks($newsletterBody['content']['blocks']);
    return array_merge(
      $newsletterBody,
      [
        'content' => array_merge(
          $newsletterBody['content'],
          ['blocks' => $cleanBlocks]
        ),
      ]
    );
  }

  private function cleanupCouponBlocks(array &$blocks): array {
    foreach ($blocks as &$block) {
      if (isset($block['blocks']) && !empty($block['blocks'])) {
        $this->cleanupCouponBlocks($block['blocks']);
      }

      if (isset($block['type']) && $block['type'] === Coupon::TYPE) {
        $block['code'] = Coupon::CODE_PLACEHOLDER;

        if(isset($block['couponId']))
          unset($block['couponId']);
      }
    }
    return $blocks;
  }
}
