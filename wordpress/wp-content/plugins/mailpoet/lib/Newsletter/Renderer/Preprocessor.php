<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter\Renderer;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\SendingQueueEntity;
use MailPoet\Newsletter\Renderer\Blocks\AbandonedCartContent;
use MailPoet\Newsletter\Renderer\Blocks\AutomatedLatestContentBlock;
use MailPoet\WooCommerce\CouponPreProcessor;
use MailPoet\WooCommerce\TransactionalEmails\ContentPreprocessor;

class Preprocessor {
  const WC_HEADING_BEFORE = '
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-spacing:0;mso-table-lspace:0;mso-table-rspace:0">
            <tr>
              <td class="mailpoet_text" valign="top" style="padding-top:20px;padding-bottom:20px;word-break:break-word;word-wrap:break-word;">';
  const WC_HEADING_AFTER = '
        </td>
      </tr>
    </table>';

  /** @var AbandonedCartContent */
  private $abandonedCartContent;

  /** @var AutomatedLatestContentBlock */
  private $automatedLatestContent;

  /** @var ContentPreprocessor */
  private $wooCommerceContentPreprocessor;

  /*** @var CouponPreProcessor */
  private $couponPreProcessor;

  public function __construct(
    AbandonedCartContent $abandonedCartContent,
    AutomatedLatestContentBlock $automatedLatestContent,
    ContentPreprocessor $wooCommerceContentPreprocessor,
    CouponPreProcessor $couponPreProcessor
  ) {
    $this->abandonedCartContent = $abandonedCartContent;
    $this->automatedLatestContent = $automatedLatestContent;
    $this->wooCommerceContentPreprocessor = $wooCommerceContentPreprocessor;
    $this->couponPreProcessor = $couponPreProcessor;
  }

  /**
   * @param array $content
   * @param NewsletterEntity $newsletter
   * @return array
   */
  public function process(NewsletterEntity $newsletter, $content, bool $preview = false, SendingQueueEntity $sendingQueue = null) {
    if (!array_key_exists('blocks', $content)) {
      return $content;
    }
    $blocks = [];
    $contentBlocks = $content['blocks'];
    $contentBlocks = $this->couponPreProcessor->processCoupons($newsletter, $contentBlocks, $preview);
    foreach ($contentBlocks as $block) {
      $processedBlock = $this->processBlock($newsletter, $block, $preview, $sendingQueue);
      if (!empty($processedBlock)) {
        $blocks = array_merge($blocks, $processedBlock);
      }
    }
    $content['blocks'] = $blocks;
    return $content;
  }

  public function processBlock(NewsletterEntity $newsletter, array $block, bool $preview = false, SendingQueueEntity $sendingQueue = null): array {
    switch ($block['type']) {
      case 'abandonedCartContent':
        return $this->abandonedCartContent->render($newsletter, $block, $preview, $sendingQueue);
      case 'automatedLatestContentLayout':
        return $this->automatedLatestContent->render($newsletter, $block);
      case 'woocommerceHeading':
        return $this->wooCommerceContentPreprocessor->preprocessHeader();
      case 'woocommerceContent':
        return $this->wooCommerceContentPreprocessor->preprocessContent();
    }
    return [$block];
  }
}
