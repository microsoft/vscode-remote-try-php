<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Subscribers;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\Renderer;
use MailPoet\Entities\SegmentEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Mailer\MailerFactory;
use MailPoet\Mailer\MetaInfo;
use MailPoet\Settings\SettingsController;
use MailPoet\WP\Functions as WPFunctions;

class NewSubscriberNotificationMailer {
  const SETTINGS_KEY = 'subscriber_email_notification';

  /** @var MailerFactory */
  private $mailerFactory;

  /** @var Renderer */
  private $renderer;

  /** @var SettingsController */
  private $settings;

  /** @var MetaInfo */
  private $mailerMetaInfo;

  public function __construct(
    MailerFactory $mailerFactory,
    Renderer $renderer,
    SettingsController $settings
  ) {
    $this->mailerFactory = $mailerFactory;
    $this->renderer = $renderer;
    $this->settings = $settings;
    $this->mailerMetaInfo = new MetaInfo();
  }

  /**
   * @param SubscriberEntity $subscriber
   * @param SegmentEntity[] $segments
   *
   * @throws \Exception
   */
  public function send(SubscriberEntity $subscriber, array $segments): void {
    $settings = $this->settings->get(NewSubscriberNotificationMailer::SETTINGS_KEY);
    if ($this->isDisabled($settings)) {
      return;
    }
    try {
      $extraParams = [
        'meta' => $this->mailerMetaInfo->getNewSubscriberNotificationMetaInfo(),
      ];
      $this->mailerFactory->getDefaultMailer()->send($this->constructNewsletter($subscriber, $segments), $settings['address'], $extraParams);
    } catch (\Exception $e) {
      if (WP_DEBUG) {
        throw $e;
      }
    }
  }

  public static function isDisabled($settings) {
    if (!is_array($settings)) {
      return true;
    }
    if (!isset($settings['enabled'])) {
      return true;
    }
    if (!isset($settings['address'])) {
      return true;
    }
    if (empty(trim($settings['address']))) {
      return true;
    }
    return !(bool)$settings['enabled'];
  }

  /**
   * @param SubscriberEntity $subscriber
   * @param SegmentEntity[] $segments
   *
   * @return array
   * @throws \Exception
   */
  private function constructNewsletter(SubscriberEntity $subscriber, array $segments) {
    $segmentNames = $this->getSegmentNames($segments);
    $context = [
      'subscriber_email' => $subscriber->getEmail(),
      'segments_names' => $segmentNames,
      'link_settings' => WPFunctions::get()->getSiteUrl(null, '/wp-admin/admin.php?page=mailpoet-settings'),
      'link_premium' => WPFunctions::get()->getSiteUrl(null, '/wp-admin/admin.php?page=mailpoet-upgrade'),
    ];
    return [
      // translators: %s is name of the segment.
      'subject' => sprintf(__('New subscriber to %s', 'mailpoet'), $segmentNames),
      'body' => [
        'html' => $this->renderer->render('emails/newSubscriberNotification.html', $context),
        'text' => $this->renderer->render('emails/newSubscriberNotification.txt', $context),
      ],
    ];
  }

  /**
   * @param SegmentEntity[] $segments
   * @return string
   */
  private function getSegmentNames(array $segments): string {
    $names = [];
    foreach ($segments as $segment) {
      $names[] = $segment->getName();
    }
    return implode(', ', $names);
  }
}
