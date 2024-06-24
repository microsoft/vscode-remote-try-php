<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter\Renderer\Blocks;

if (!defined('ABSPATH')) exit;


use MailPoet\AutomaticEmails\WooCommerce\Events\AbandonedCart;
use MailPoet\AutomaticEmails\WooCommerce\WooCommerce as WooCommerceEmail;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\NewsletterOptionEntity;
use MailPoet\Entities\SendingQueueEntity;

class AbandonedCartContent {
  /** @var AutomatedLatestContentBlock  */
  private $ALCBlock;

  public function __construct(
    AutomatedLatestContentBlock $ALCBlock
  ) {
    $this->ALCBlock = $ALCBlock;
  }

  public function render(
    NewsletterEntity $newsletter,
    array $args,
    bool $preview = false,
    SendingQueueEntity $sendingQueue = null
  ): array {
    if (
      !in_array(
        $newsletter->getType(),
        [
        NewsletterEntity::TYPE_AUTOMATIC,
        NewsletterEntity::TYPE_AUTOMATION_TRANSACTIONAL,
        NewsletterEntity::TYPE_AUTOMATION,
        ],
        true
      )
    ) {
      // Do not display the block if not an automatic email
      return [];
    }
    $groupOption = $newsletter->getOptions()->filter(function (NewsletterOptionEntity $newsletterOption = null) {
      if (!$newsletterOption) return false;
      $optionField = $newsletterOption->getOptionField();
      return $optionField && $optionField->getName() === 'group';
    })->first();
    $eventOption = $newsletter->getOptions()->filter(function (NewsletterOptionEntity $newsletterOption = null) {
      if (!$newsletterOption) return false;
      $optionField = $newsletterOption->getOptionField();
      return $optionField && $optionField->getName() === 'event';
    })->first();
    if (
      !$groupOption
      || $groupOption->getValue() !== WooCommerceEmail::SLUG
      || !$eventOption
      || $eventOption->getValue() !== AbandonedCart::SLUG
    ) {
      // Do not display the block if not an AbandonedCart email
      return [];
    }
    if ($preview) {
      // Display latest products for preview (no 'posts' argument specified)
      return $this->ALCBlock->render($newsletter, $args);
    }
    if (!$sendingQueue) {
      // Do not display the block if we're not sending an email
      return [];
    }
    $meta = $sendingQueue->getMeta();
    if (empty($meta[AbandonedCart::TASK_META_NAME])) {
      // Do not display the block if a cart is empty
      return [];
    }
    $args['amount'] = 50;
    $args['posts'] = $meta[AbandonedCart::TASK_META_NAME];
    return $this->ALCBlock->render($newsletter, $args);
  }
}
