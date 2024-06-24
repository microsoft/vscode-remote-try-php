<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Mailer;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\SubscriberEntity;

class MetaInfo {
  public function getSendingTestMetaInfo() {
    return $this->makeMetaInfo('sending_test', 'unknown', 'administrator');
  }

  public function getPreviewMetaInfo() {
    return $this->makeMetaInfo('preview', 'unknown', 'administrator');
  }

  public function getStatsNotificationMetaInfo() {
    return $this->makeMetaInfo('email_stats_notification', 'unknown', 'administrator');
  }

  public function getWordPressTransactionalMetaInfo(SubscriberEntity $subscriber = null) {
    return $this->makeMetaInfo(
      'transactional',
      $subscriber ? $subscriber->getStatus() : 'unknown',
      $subscriber ? $subscriber->getSource() : 'unknown'
    );
  }

  public function getConfirmationMetaInfo(SubscriberEntity $subscriber) {
    return $this->makeMetaInfo('confirmation', $subscriber->getStatus(), $subscriber->getSource());
  }

  public function getNewSubscriberNotificationMetaInfo() {
    return $this->makeMetaInfo('new_subscriber_notification', 'unknown', 'administrator');
  }

  public function getNewsletterMetaInfo(NewsletterEntity $newsletter, SubscriberEntity $subscriber) {
    $type = $newsletter->getType();
    switch ($newsletter->getType()) {
      case NewsletterEntity::TYPE_AUTOMATIC:
        $group = !is_null($newsletter->getOptionValue('group')) ? $newsletter->getOptionValue('group') : 'unknown';
        $event = !is_null($newsletter->getOptionValue('event')) ? $newsletter->getOptionValue('event') : 'unknown';
        $type = sprintf('automatic_%s_%s', $group, $event);
        break;
      case NewsletterEntity::TYPE_STANDARD:
        $type = 'newsletter';
        break;
      case NewsletterEntity::TYPE_WELCOME:
        $type = 'welcome';
        break;
      case NewsletterEntity::TYPE_NOTIFICATION:
      case NewsletterEntity::TYPE_NOTIFICATION_HISTORY:
        $type = 'post_notification';
        break;
    }
    return $this->makeMetaInfo($type, $subscriber->getStatus(), $subscriber->getSource());
  }

  private function makeMetaInfo($emailType, $subscriberStatus, $subscriberSource) {
    return [
      'email_type' => $emailType,
      'subscriber_status' => $subscriberStatus,
      'subscriber_source' => $subscriberSource ?: 'unknown',
    ];
  }
}
