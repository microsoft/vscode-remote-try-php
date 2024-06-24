<?php declare(strict_types = 1);

namespace MailPoet\Homepage;

if (!defined('ABSPATH')) exit;


use MailPoet\AutomaticEmails\WooCommerce\Events\AbandonedCart;
use MailPoet\Automation\Engine\Storage\AutomationStorage;
use MailPoet\Automation\Integrations\MailPoet\Actions\SendEmailAction;
use MailPoet\Automation\Integrations\MailPoet\Triggers\SomeoneSubscribesTrigger;
use MailPoet\Automation\Integrations\MailPoet\Triggers\UserRegistrationTrigger;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Form\FormsRepository;
use MailPoet\Newsletter\NewslettersRepository;
use MailPoet\Services\AuthorizedSenderDomainController;
use MailPoet\Services\Bridge;
use MailPoet\Settings\SettingsController;
use MailPoet\Subscribers\SubscribersCountsController;
use MailPoet\Util\License\Features\Subscribers as SubscribersFeature;
use MailPoet\Util\Notices\SenderDomainAuthenticationNotices;
use MailPoet\WooCommerce\Helper as WooCommerceHelper;

class HomepageDataController {
  public const UPSELL_SUBSCRIBERS_COUNT_REQUIRED = 600;

  /** @var SettingsController */
  private $settingsController;

  /** @var FormsRepository */
  private $formsRepository;

  /** @var WooCommerceHelper */
  private $wooCommerceHelper;

  /** @var NewslettersRepository */
  private $newslettersRepository;

  /** @var AutomationStorage */
  private $automationStorage;

  /** @var SubscribersFeature */
  private $subscribersFeature;

  /** @var SubscribersCountsController */
  private $subscribersCountsController;

  /** @var AuthorizedSenderDomainController */
  private $senderDomainController;

  /** @var SenderDomainAuthenticationNotices */
  private $senderDomainAuthenticationNotices;

  public function __construct(
    SettingsController $settingsController,
    FormsRepository $formsRepository,
    NewslettersRepository $newslettersRepository,
    AutomationStorage $automationStorage,
    SubscribersFeature $subscribersFeature,
    SubscribersCountsController $subscribersCountsController,
    AuthorizedSenderDomainController $senderDomainController,
    SenderDomainAuthenticationNotices $senderDomainAuthenticationNotices,
    WooCommerceHelper $wooCommerceHelper
  ) {
    $this->settingsController = $settingsController;
    $this->formsRepository = $formsRepository;
    $this->newslettersRepository = $newslettersRepository;
    $this->automationStorage = $automationStorage;
    $this->wooCommerceHelper = $wooCommerceHelper;
    $this->subscribersFeature = $subscribersFeature;
    $this->subscribersCountsController = $subscribersCountsController;
    $this->senderDomainController = $senderDomainController;
    $this->senderDomainAuthenticationNotices = $senderDomainAuthenticationNotices;
  }

  public function getPageData(): array {
    $subscribersCount = $this->subscribersFeature->getSubscribersCount();
    $formsCount = $this->formsRepository->count();
    $showTaskList = !$this->settingsController->get('homepage.task_list_dismissed', false);
    $showProductDiscovery = !$this->settingsController->get('homepage.product_discovery_dismissed', false);
    $showUpsell = !$this->settingsController->get('homepage.upsell_dismissed', false);
    $fullyVerifiedSenderDomains = $this->senderDomainController->getFullyVerifiedSenderDomains(true);
    $senderDomainsCount = count($fullyVerifiedSenderDomains);
    return [
      'taskListDismissed' => !$showTaskList,
      'productDiscoveryDismissed' => !$showProductDiscovery,
      'upsellDismissed' => !$showUpsell,
      'taskListStatus' => $showTaskList ? $this->getTaskListStatus($subscribersCount, $formsCount, $senderDomainsCount) : null,
      'productDiscoveryStatus' => $showProductDiscovery ? $this->getProductDiscoveryStatus($formsCount) : null,
      'upsellStatus' => $showUpsell ? $this->getUpsellStatus($subscribersCount) : null,
      'wooCustomersCount' => $this->wooCommerceHelper->getCustomersCount(),
      'subscribersCount' => $subscribersCount,
      'formsCount' => $formsCount,
      'subscribersStats' => $this->getSubscribersStats(),
      'isNewUserForSenderDomainAuth' => $this->senderDomainController->isNewUser(),
      'isFreeMailUser' => $this->senderDomainAuthenticationNotices->isFreeMailUser(),
    ];
  }

  /**
   * @return array{senderSet:bool, mssConnected:bool, wooSubscribersImported:bool, subscribersAdded:bool, senderDomainAuthenticated:bool}
   */
  private function getTaskListStatus(int $subscribersCount, int $formsCount, int $senderDomainsCount): array {
    return [
      'senderSet' => $this->settingsController->get('sender.address', false) && $this->settingsController->get('sender.name', false),
      'mssConnected' => Bridge::isMSSKeySpecified(),
      'wooSubscribersImported' => (bool)$this->settingsController->get('woocommerce_import_screen_displayed', false),
      'subscribersAdded' => $formsCount || ($subscribersCount > 10),
      'senderDomainAuthenticated' => $senderDomainsCount > 0,
    ];
  }

  /**
   * @return array{setUpWelcomeCampaign:bool, addSubscriptionForm:bool, sendFirstNewsletter:bool, setUpAbandonedCartEmail:bool, brandWooEmails:bool}
   */
  private function getProductDiscoveryStatus(int $formsCount): array {
    $sentStandard = $this->newslettersRepository->getCountForStatusAndTypes(
      NewsletterEntity::STATUS_SENT,
      [NewsletterEntity::TYPE_STANDARD]
    );
    $scheduledStandard = $this->newslettersRepository->getCountForStatusAndTypes(
      NewsletterEntity::STATUS_SCHEDULED,
      [NewsletterEntity::TYPE_STANDARD]
    );
    $activePostNotificationsAndAutomaticEmails = $this->newslettersRepository->getCountForStatusAndTypes(
      NewsletterEntity::STATUS_ACTIVE,
      [NewsletterEntity::TYPE_NOTIFICATION, NewsletterEntity::TYPE_AUTOMATIC]
    );
    $abandonedCartEmailsCount = $this->newslettersRepository->getCountOfActiveAutomaticEmailsForEvent(AbandonedCart::SLUG);
    $welcomeEmailsCount = $this->newslettersRepository->getCountForStatusAndTypes(NewsletterEntity::STATUS_ACTIVE, [NewsletterEntity::TYPE_WELCOME]);
    $welcomeEmailLikeAutomationsCount = $this->automationStorage->getCountOfActiveByTriggerKeysAndAction(
      [UserRegistrationTrigger::KEY, SomeoneSubscribesTrigger::KEY],
      SendEmailAction::KEY
    );
    return [
      'setUpWelcomeCampaign' => ($welcomeEmailsCount + $welcomeEmailLikeAutomationsCount) > 0,
      'addSubscriptionForm' => $formsCount > 0,
      'sendFirstNewsletter' => ($sentStandard + $scheduledStandard + $activePostNotificationsAndAutomaticEmails) > 0,
      'setUpAbandonedCartEmail' => $abandonedCartEmailsCount > 0,
      'brandWooEmails' => (bool)$this->settingsController->get('woocommerce.use_mailpoet_editor', false),
    ];
  }

  /**
   * @return array{canDisplay:bool}
   */
  private function getUpsellStatus(int $subscribersCount): array {
    $hasValidMssKey = $this->subscribersFeature->hasValidMssKey();

    return [
      'canDisplay' => !$hasValidMssKey && $subscribersCount > self::UPSELL_SUBSCRIBERS_COUNT_REQUIRED,
    ];
  }

  /**
   * This method returns data for subscribers stats statistics section.
   *
   * global:
   *  - subscribed:    int number of subscribers who were added in last 30 days by checking lastSubscribedAt column and ignoring current global status
   *  - unsubscribed:  int number of subscribers who have a record in statistics_unsubscribes table in last 30 days
   *  - changePercent: float ($subscribedSubscribersCount - $subscribedSubscribers30DaysAgo) / $subscribedSubscribers30DaysAgo) * 100
   *
   * lists:
   *  - id:                     int id of the list
   *  - name:                   string name of the list
   *  - subscribed:             int number of subscribers who were added to a list in last 30 days and have both list statuse "subscribed"
   *  - unsubscribed:           int number of subscribers who were removed from a list in last 30 (list status is unsubscribed and updated_at is in last 30 days)
   *  - averageEngagementScore: float engagement score of the list
   *
   * @return array{global:array{subscribed:int, unsubscribed:int, changePercent:float|int}, lists:array<int, array>}
   */
  private function getSubscribersStats(): array {
    $listData = [];
    $counts = $this->subscribersCountsController->getHomepageStatistics();
    $listsDataSubscribed = $counts['listsDataSubscribed'] ?? [];
    foreach ($listsDataSubscribed as $list) {
      $listData[$list['id']] = array_intersect_key($list, array_flip(['name', 'id', 'type', 'averageEngagementScore']));
      $listData[$list['id']]['subscribed'] = $list['count'];
      $listData[$list['id']]['unsubscribed'] = 0;
    }
    $listsDataUnsubscribed = $counts['listsDataUnsubscribed'] ?? [];
    foreach ($listsDataUnsubscribed as $list) {
      if (!isset($listData[$list['id']])) {
        $listData[$list['id']] = array_intersect_key($list, array_flip(['name', 'id', 'type', 'averageEngagementScore']));
        $listData[$list['id']]['subscribed'] = 0;
      }
      $listData[$list['id']]['unsubscribed'] = $list['count'];
    }

    $subscribedCount = intval($counts['subscribedCount'] ?? 0);
    $unsubscribedCount = intval($counts['unsubscribedCount'] ?? 0);
    $subscribedSubscribersCount = intval($counts['subscribedSubscribersCount'] ?? 0);
    $subscribedSubscribers30DaysAgo = $subscribedSubscribersCount - $subscribedCount + $unsubscribedCount;
    if ($subscribedSubscribers30DaysAgo > 0) {
      $globalChangePercent = (($subscribedSubscribersCount - $subscribedSubscribers30DaysAgo) / $subscribedSubscribers30DaysAgo) * 100;
      if (floor($globalChangePercent) !== (float)$globalChangePercent) {
        $globalChangePercent = round($globalChangePercent, 1);
      }
    } else {
      $globalChangePercent = $subscribedSubscribersCount * 100;
    }

    return [
      'global' => [
        'subscribed' => $subscribedCount,
        'unsubscribed' => $unsubscribedCount,
        'changePercent' => $globalChangePercent,
      ],
      'lists' => array_values($listData),
    ];
  }
}
