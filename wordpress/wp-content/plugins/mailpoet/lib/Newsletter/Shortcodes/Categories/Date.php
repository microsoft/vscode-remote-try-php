<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter\Shortcodes\Categories;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\SendingQueueEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\WP\Functions as WPFunctions;

class Date implements CategoryInterface {
  public function process(
    array $shortcodeDetails,
    NewsletterEntity $newsletter = null,
    SubscriberEntity $subscriber = null,
    SendingQueueEntity $queue = null,
    string $content = '',
    bool $wpUserPreview = false
  ): ?string {
    $actionMapping = [
      'd' => 'd',
      'dordinal' => 'jS',
      'dtext' => 'l',
      'm' => 'm',
      'mtext' => 'F',
      'y' => 'Y',
    ];
    $wp = new WPFunctions();
    $date = $wp->currentTime('timestamp');
    if (
      ($newsletter instanceof NewsletterEntity)
      && ($newsletter->getSentAt() instanceof \DateTimeInterface)
      && ($newsletter->getStatus() === NewsletterEntity::STATUS_SENT)
    ) {
      $date = $newsletter->getSentAt()->getTimestamp();
    }
    if (!empty($actionMapping[$shortcodeDetails['action']])) {
      return WPFunctions::get()->dateI18n($actionMapping[$shortcodeDetails['action']], $date);
    }
    return ($shortcodeDetails['action'] === 'custom' && $shortcodeDetails['action_argument'] === 'format') ?
      WPFunctions::get()->dateI18n($shortcodeDetails['action_argument_value'], $date) :
      null;
  }
}
