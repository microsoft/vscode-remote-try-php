<?php declare(strict_types = 1);

namespace MailPoet\WooCommerce;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\NewsletterEntity;
use MailPoet\Newsletter\NewslettersRepository;
use MailPoet\Newsletter\Renderer\Blocks\Coupon;
use MailPoet\NewsletterProcessingException;
use MailPoet\WP\DateTime;

class CouponPreProcessor {

  /** @var bool */
  private $generated = false;

  /** @var NewslettersRepository */
  private $newslettersRepository;

  /** @var Helper */
  private $wcHelper;

  public function __construct(
    Helper $wcHelper,
    NewslettersRepository $newslettersRepository
  ) {
    $this->wcHelper = $wcHelper;
    $this->newslettersRepository = $newslettersRepository;
  }

  /**
   * @throws NewsletterProcessingException
   */
  public function processCoupons(NewsletterEntity $newsletter, array $blocks, bool $preview = false): array {
    if ($preview) {
      return $blocks;
    }
    
    $generated = $this->ensureCouponForBlocks($blocks, $newsletter);
    $body = $newsletter->getBody();

    if ($generated && $body && $this->shouldPersist($newsletter)) {
      $updatedBody = array_merge(
        $body,
        [
          'content' => array_merge(
            $body['content'],
            ['blocks' => $blocks]
          ),
        ]
      );
      $newsletter->setBody($updatedBody);
      $this->newslettersRepository->flush();
    }
    return $blocks;
  }

  private function ensureCouponForBlocks(array &$blocks, NewsletterEntity $newsletter): bool {

    foreach ($blocks as &$innerBlock) {
      if (isset($innerBlock['blocks']) && !empty($innerBlock['blocks'])) {
        $this->ensureCouponForBlocks($innerBlock['blocks'], $newsletter);
      }
      if (isset($innerBlock['type']) && $innerBlock['type'] === Coupon::TYPE) {
        if (!$this->wcHelper->isWooCommerceActive()) {
          throw NewsletterProcessingException::create()->withMessage(__('WooCommerce is not active', 'mailpoet'));
        }
        if ($this->shouldGenerateCoupon($innerBlock)) {
          try {
            $innerBlock['couponId'] = $this->addOrUpdateCoupon($innerBlock, $newsletter);
            $this->generated = true;
          } catch (\Exception $e) {
            throw NewsletterProcessingException::create()->withMessage($e->getMessage())->withCode($e->getCode());
          }
        }
      }
    }

    return $this->generated;
  }

  /**
   * @param array $couponBlock
   * @param NewsletterEntity $newsletter
   * @return int
   * @throws \WC_Data_Exception|\Exception
   */
  private function addOrUpdateCoupon(array $couponBlock, NewsletterEntity $newsletter) {
    $coupon = $this->wcHelper->createWcCoupon($couponBlock['couponId'] ?? '');
    if ($this->shouldGenerateCoupon($couponBlock)) {
      $code = isset($couponBlock['code']) && $couponBlock['code'] !== Coupon::CODE_PLACEHOLDER ? $couponBlock['code'] : $this->generateRandomCode();
      $coupon->set_code($code);
    }

    $coupon->set_description(
      sprintf(
      // translators: %1$s is newsletter id and %2$s is the subject.
        _x('Auto Generated coupon by MailPoet for email: %1$s: %2$s', 'Coupon block code generation', 'mailpoet'),
        $newsletter->getId(),
        $newsletter->getSubject()
      )
    );

    // general
    $coupon->set_discount_type($couponBlock['discountType']);

    if (isset($couponBlock['amount'])) {
      $coupon->set_amount($couponBlock['amount']);
    }

    if (isset($couponBlock['expiryDay'])) {
      $expiration = (new DateTime())->getCurrentDateTime()
        ->modify("+{$couponBlock['expiryDay']} day")
        ->getTimestamp();
      $coupon->set_date_expires($expiration);
    }

    $coupon->set_free_shipping($couponBlock['freeShipping'] ?? false);

    // usage restriction
    $coupon->set_minimum_amount($couponBlock['minimumAmount'] ?? '');
    $coupon->set_maximum_amount($couponBlock['maximumAmount'] ?? '');
    $coupon->set_individual_use($couponBlock['individualUse'] ?? false);
    $coupon->set_exclude_sale_items($couponBlock['excludeSaleItems'] ?? false);

    $coupon->set_product_ids($this->getItemIds($couponBlock['productIds'] ?? []));
    $coupon->set_excluded_product_ids($this->getItemIds($couponBlock['excludedProductIds'] ?? []));

    $coupon->set_product_categories($this->getItemIds($couponBlock['productCategoryIds'] ?? []));
    $coupon->set_excluded_product_categories($this->getItemIds($couponBlock['excludedProductCategoryIds'] ?? []));

    $coupon->set_email_restrictions(explode(',', $couponBlock['emailRestrictions'] ?? ''));

    // usage limit
    $coupon->set_usage_limit($couponBlock['usageLimit'] ?? 0);
    $coupon->set_usage_limit_per_user($couponBlock['usageLimitPerUser'] ?? 0);
    return $coupon->save();
  }

  private function getItemIds(array $items): array {
    if (empty($items)) {
      return [];
    }

    return array_map(function ($item) {
      return $item['id'];
    }, $items);
  }

  /**
   * Generates Coupon code for XXXX-XXXXXX-XXXX pattern
   */
  private function generateRandomCode(): string {
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $length = strlen($chars);
    return sprintf(
      "%s-%s-%s",
      substr($chars, rand(0, $length - 5), 4),
      substr($chars, rand(0, $length - 8), 7),
      substr($chars, rand(0, $length - 5), 4)
    );
  }

  private function shouldGenerateCoupon(array $block): bool {
    return empty($block['couponId']);
  }

  /**
   * Only emails that can have their body-HTML re-generated should persist the generated couponId
   */
  private function shouldPersist(NewsletterEntity $newsletter): bool {
    return in_array($newsletter->getType(), NewsletterEntity::TYPES_WITH_RESETTABLE_BODY);
  }
}
