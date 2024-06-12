<?php declare(strict_types = 1);

namespace MailPoet\Newsletter\Shortcodes\Categories;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\SendingQueueEntity;
use MailPoet\Entities\SubscriberCustomFieldEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Subscribers\SubscriberCustomFieldRepository;
use MailPoet\Subscribers\SubscribersRepository;
use MailPoet\WP\Functions as WPFunctions;

class Subscriber implements CategoryInterface {

  /** @var SubscribersRepository */
  private $subscribersRepository;

  /** @var SubscriberCustomFieldRepository */
  private $subscriberCustomFieldRepository;

  public function __construct(
    SubscribersRepository $subscribersRepository,
    SubscriberCustomFieldRepository $subscriberCustomFieldRepository
  ) {
    $this->subscribersRepository = $subscribersRepository;
    $this->subscriberCustomFieldRepository = $subscriberCustomFieldRepository;
  }

  public function process(
    array $shortcodeDetails,
    NewsletterEntity $newsletter = null,
    SubscriberEntity $subscriber = null,
    SendingQueueEntity $queue = null,
    string $content = '',
    bool $wpUserPreview = false
  ): ?string {
    if (!($subscriber instanceof SubscriberEntity)) {
      return $shortcodeDetails['shortcode'];
    }
    $defaultValue = ($shortcodeDetails['action_argument'] === 'default') ?
      $shortcodeDetails['action_argument_value'] :
      '';
    switch ($shortcodeDetails['action']) {
      case 'firstname':
        return (!empty($subscriber->getFirstName())) ? htmlspecialchars($subscriber->getFirstName()) : $defaultValue;
      case 'lastname':
        return !empty($subscriber->getLastName()) ? htmlspecialchars($subscriber->getLastName()) : $defaultValue;
      case 'email':
        return $subscriber->getEmail();
      case 'displayname':
        if ($subscriber->getWpUserId()) {
          $wpUser = WPFunctions::get()->getUserdata($subscriber->getWpUserId());
          return $wpUser->user_login; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        }
        return $defaultValue;
      case 'count':
        return (string)$this->getSubscribersCountWithSubscribedStatus();
      default:
        if (
          preg_match('/cf_(\d+)/', $shortcodeDetails['action'], $customField) &&
          !empty($subscriber->getId())
        ) {
          $customField = $this->subscriberCustomFieldRepository->findOneBy([
            'subscriber' => $subscriber,
            'customField' => $customField[1],
          ]);
          return ($customField instanceof SubscriberCustomFieldEntity && !empty($customField->getValue()))
            ? htmlspecialchars($customField->getValue())
            : $defaultValue;
        }
        return null;
    }
  }

  private function getSubscribersCountWithSubscribedStatus(): int {
    return $this->subscribersRepository->countBy(['status' => SubscriberEntity::STATUS_SUBSCRIBED, 'deletedAt' => null]);
  }
}
