<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config;

if (!defined('ABSPATH')) exit;


use MailPoet\Cron\CronTrigger;
use MailPoet\Cron\Workers\AuthorizedSendingEmailsCheck;
use MailPoet\Cron\Workers\BackfillEngagementData;
use MailPoet\Cron\Workers\Beamer;
use MailPoet\Cron\Workers\InactiveSubscribers;
use MailPoet\Cron\Workers\Mixpanel;
use MailPoet\Cron\Workers\NewsletterTemplateThumbnails;
use MailPoet\Cron\Workers\StatsNotifications\Worker;
use MailPoet\Cron\Workers\SubscriberLinkTokens;
use MailPoet\Cron\Workers\SubscribersLastEngagement;
use MailPoet\Cron\Workers\UnsubscribeTokens;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\NewsletterOptionFieldEntity;
use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Entities\SegmentEntity;
use MailPoet\Entities\StatisticsFormEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Entities\UserFlagEntity;
use MailPoet\Mailer\MailerLog;
use MailPoet\Newsletter\Sending\ScheduledTasksRepository;
use MailPoet\Referrals\ReferralDetector;
use MailPoet\Segments\SegmentsRepository;
use MailPoet\Segments\WP;
use MailPoet\Services\Bridge;
use MailPoet\Settings\Pages;
use MailPoet\Settings\SettingsController;
use MailPoet\Settings\UserFlagsRepository;
use MailPoet\Subscribers\NewSubscriberNotificationMailer;
use MailPoet\Subscribers\Source;
use MailPoet\Subscription\Captcha\CaptchaConstants;
use MailPoet\Subscription\Captcha\CaptchaRenderer;
use MailPoet\Util\Helpers;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Carbon\Carbon;
use MailPoetVendor\Doctrine\ORM\EntityManager;

class Populator {
  public $prefix;
  public $models;
  public $templates;
  /** @var SettingsController */
  private $settings;
  /** @var WPFunctions */
  private $wp;
  /** @var CaptchaRenderer */
  private $captchaRenderer;
  /** @var ReferralDetector  */
  private $referralDetector;
  const TEMPLATES_NAMESPACE = '\MailPoet\Config\PopulatorData\Templates\\';
  /** @var WP */
  private $wpSegment;
  /** @var EntityManager */
  private $entityManager;
  /** @var ScheduledTasksRepository */
  private $scheduledTasksRepository;
  /** @var SegmentsRepository */
  private $segmentsRepository;

  public function __construct(
    SettingsController $settings,
    WPFunctions $wp,
    CaptchaRenderer $captchaRenderer,
    ReferralDetector $referralDetector,
    EntityManager $entityManager,
    WP $wpSegment,
    ScheduledTasksRepository $scheduledTasksRepository,
    SegmentsRepository $segmentsRepository
  ) {
    $this->settings = $settings;
    $this->wp = $wp;
    $this->captchaRenderer = $captchaRenderer;
    $this->wpSegment = $wpSegment;
    $this->referralDetector = $referralDetector;
    $this->prefix = Env::$dbPrefix;
    $this->models = [
      'newsletter_option_fields',
      'newsletter_templates',
    ];
    $this->templates = [
      'WelcomeBlank1Column',
      'WelcomeBlank12Column',
      'GiftWelcome',
      'Minimal',
      'Phone',
      'Sunglasses',
      'RealEstate',
      'AppWelcome',
      'FoodBox',
      'Poet',
      'PostNotificationsBlank1Column',
      'ModularStyleStories',
      'RssSimpleNews',
      'NotSoMedium',
      'WideStoryLayout',
      'IndustryConference',
      'ScienceWeekly',
      'NewspaperTraditional',
      'ClearNews',
      'DogFood',
      'KidsClothing',
      'RockBand',
      'WineCity',
      'Fitness',
      'Motor',
      'Avocado',
      'BookStoreWithCoupon',
      'FlowersWithCoupon',
      'NewsletterBlank1Column',
      'NewsletterBlank12Column',
      'NewsletterBlank121Column',
      'NewsletterBlank13Column',
      'SimpleText',
      'TakeAHike',
      'NewsDay',
      'WorldCup',
      'FestivalEvent',
      'RetroComputingMagazine',
      'Shoes',
      'Music',
      'Hotels',
      'PieceOfCake',
      'BuddhistTemple',
      'Mosque',
      'Synagogue',
      'Faith',
      'College',
      'RenewableEnergy',
      'PrimarySchool',
      'ComputerRepair',
      'YogaStudio',
      'Retro',
      'Charity',
      'CityLocalNews',
      'Coffee',
      'Vlogger',
      'Birds',
      'Engineering',
      'BrandingAgencyNews',
      'WordPressTheme',
      'Drone',
      'FashionBlog',
      'FashionStore',
      'FashionBlogA',
      'Photography',
      'JazzClub',
      'Guitarist',
      'HealthyFoodBlog',
      'Software',
      'LifestyleBlogA',
      'FashionShop',
      'LifestyleBlogB',
      'Painter',
      'FarmersMarket',
      'ConfirmInterestBeforeDeactivation',
      'ConfirmInterestOrUnsubscribe',
    ];
    $this->entityManager = $entityManager;
    $this->scheduledTasksRepository = $scheduledTasksRepository;
    $this->segmentsRepository = $segmentsRepository;
  }

  public function up() {
    $localizer = new Localizer();
    $localizer->forceLoadWebsiteLocaleText();

    array_map([$this, 'populate'], $this->models);

    $this->createDefaultSegment();
    $this->createDefaultSettings();
    $this->createDefaultUsersFlags();
    $this->createMailPoetPage();
    $this->createSourceForSubscribers();
    $this->scheduleInitialInactiveSubscribersCheck();
    $this->scheduleAuthorizedSendingEmailsCheck();
    $this->scheduleBeamer();

    $this->scheduleUnsubscribeTokens();
    $this->scheduleSubscriberLinkTokens();
    $this->detectReferral();
    $this->scheduleSubscriberLastEngagementDetection();
    $this->scheduleNewsletterTemplateThumbnails();
    $this->scheduleBackfillEngagementData();
    $this->scheduleMixpanel();
  }

  private function createMailPoetPage() {
    $page = Pages::getDefaultMailPoetPage();
    if ($page === null) {
      $mailpoetPageId = Pages::createMailPoetPage();
    } else {
      $mailpoetPageId = (int)$page->ID;
    }

    $subscription = $this->settings->get('subscription.pages', []);
    if (empty($subscription)) {
      $this->settings->set('subscription.pages', [
        'unsubscribe' => $mailpoetPageId,
        'manage' => $mailpoetPageId,
        'confirmation' => $mailpoetPageId,
        'captcha' => $mailpoetPageId,
        'confirm_unsubscribe' => $mailpoetPageId,
      ]);
    } else {
      // For existing installations
      $captchaPageSetting = (empty($subscription['captcha']) || $subscription['captcha'] !== $mailpoetPageId)
        ? $mailpoetPageId : $subscription['captcha'];
      $confirmUnsubPageSetting = empty($subscription['confirm_unsubscribe'])
        ? $mailpoetPageId : $subscription['confirm_unsubscribe'];

      $this->settings->set('subscription.pages', array_merge($subscription, [
        'captcha' => $captchaPageSetting,
        'confirm_unsubscribe' => $confirmUnsubPageSetting,
      ]));
    }
  }

  private function createDefaultSettings() {
    $settingsDbVersion = $this->settings->fetch('db_version');
    $currentUser = $this->wp->wpGetCurrentUser();

    // set cron trigger option to default method
    if (!$this->settings->fetch(CronTrigger::SETTING_NAME)) {
      $this->settings->set(CronTrigger::SETTING_NAME, [
        'method' => CronTrigger::DEFAULT_METHOD,
      ]);
    }

    // set default sender info based on current user
    $currentUserName = $currentUser->display_name ?: ''; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    // parse current user name if an email is used
    $senderName = explode('@', $currentUserName);
    $senderName = reset($senderName);
    // If current user is not set, default to admin email
    $senderAddress = $currentUser->user_email ?: $this->wp->getOption('admin_email'); // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    $defaultSender = [
      'name' => $senderName,
      'address' => $senderAddress ?: '',
    ];
    $savedSender = $this->settings->fetch('sender', []);

    /**
     * Set default from name & address
     * In some cases ( like when the plugin is getting activated other than from WP Admin ) user data may not
     * still be set at this stage, so setting the defaults for `sender` is postponed
     */
    if (empty($savedSender) || empty($savedSender['address'])) {
      $this->settings->set('sender', $defaultSender);
    }

    // enable signup confirmation by default
    if (!$this->settings->fetch('signup_confirmation')) {
      $this->settings->set('signup_confirmation', [
        'enabled' => true,
      ]);
    }

    // set installation date
    if (!$this->settings->fetch('installed_at')) {
      $this->settings->set('installed_at', date("Y-m-d H:i:s"));
    }

    // set captcha settings
    $captcha = $this->settings->fetch('captcha');
    $reCaptcha = $this->settings->fetch('re_captcha');
    if (empty($captcha)) {
      $captchaType = CaptchaConstants::TYPE_DISABLED;
      if (!empty($reCaptcha['enabled'])) {
        $captchaType = CaptchaConstants::TYPE_RECAPTCHA;
      } elseif ($this->captchaRenderer->isSupported()) {
        $captchaType = CaptchaConstants::TYPE_BUILTIN;
      }
      $this->settings->set('captcha', [
        'type' => $captchaType,
        'recaptcha_site_token' => !empty($reCaptcha['site_token']) ? $reCaptcha['site_token'] : '',
        'recaptcha_secret_token' => !empty($reCaptcha['secret_token']) ? $reCaptcha['secret_token'] : '',
      ]);
    }

    $subscriberEmailNotification = $this->settings->fetch(NewSubscriberNotificationMailer::SETTINGS_KEY);
    if (empty($subscriberEmailNotification)) {
      $sender = $this->settings->fetch('sender', []);
      $this->settings->set('subscriber_email_notification', [
        'enabled' => true,
        'automated' => true,
        'address' => isset($sender['address']) ? $sender['address'] : null,
      ]);
    }

    $statsNotifications = $this->settings->fetch(Worker::SETTINGS_KEY);
    if (empty($statsNotifications)) {
      $sender = $this->settings->fetch('sender', []);
      $this->settings->set(Worker::SETTINGS_KEY, [
        'enabled' => true,
        'address' => isset($sender['address']) ? $sender['address'] : null,
      ]);
    }

    $woocommerceOptinOnCheckout = $this->settings->fetch('woocommerce.optin_on_checkout');
    $legacyLabelText = _x('Yes, I would like to be added to your mailing list', "default email opt-in message displayed on checkout page for ecommerce websites", 'mailpoet');
    $currentLabelText = _x('I would like to receive exclusive emails with discounts and product information', "default email opt-in message displayed on checkout page for ecommerce websites", 'mailpoet');
    if (empty($woocommerceOptinOnCheckout)) {
      $this->settings->set('woocommerce.optin_on_checkout', [
        'enabled' => empty($settingsDbVersion), // enable on new installs only
        'message' => $currentLabelText,
      ]);
    } elseif (isset($woocommerceOptinOnCheckout['message']) && $woocommerceOptinOnCheckout['message'] === $legacyLabelText) {
      $this->settings->set('woocommerce.optin_on_checkout.message', $currentLabelText);
    }
    // reset mailer log
    MailerLog::resetMailerLog();
  }

  private function createDefaultUsersFlags() {
    $lastAnnouncementSeen = $this->settings->fetch('last_announcement_seen');
    if (!empty($lastAnnouncementSeen)) {
      foreach ($lastAnnouncementSeen as $userId => $value) {
        $this->createOrUpdateUserFlag($userId, 'last_announcement_seen', $value);
      }
      $this->settings->delete('last_announcement_seen');
    }

    $prefix = 'user_seen_editor_tutorial';
    $prefixLength = strlen($prefix);
    foreach ($this->settings->getAll() as $name => $value) {
      if (substr($name, 0, $prefixLength) === $prefix) {
        $userId = substr($name, $prefixLength);
        $this->createOrUpdateUserFlag($userId, 'editor_tutorial_seen', $value);
        $this->settings->delete($name);
      }
    }
  }

  private function createOrUpdateUserFlag($userId, $name, $value) {
    $userFlagsRepository = \MailPoet\DI\ContainerWrapper::getInstance(WP_DEBUG)->get(UserFlagsRepository::class);
    $flag = $userFlagsRepository->findOneBy([
      'userId' => $userId,
      'name' => $name,
    ]);

    if (!$flag) {
      $flag = new UserFlagEntity();
      $flag->setUserId($userId);
      $flag->setName($name);
      $userFlagsRepository->persist($flag);
    }
    $flag->setValue($value);
    $userFlagsRepository->flush();
  }

  private function createDefaultSegment() {
    // WP Users segment
    $this->segmentsRepository->getWPUsersSegment();
    // WooCommerce customers segment
    $this->segmentsRepository->getWooCommerceSegment();

    // Synchronize WP Users
    $this->wpSegment->synchronizeUsers();

    // Default segment
    $defaultSegment = $this->segmentsRepository->findOneBy(
      ['type' => 'default'],
      ['id' => 'ASC']
    );

    if (!$defaultSegment instanceof SegmentEntity) {
      $defaultSegment = new SegmentEntity(
        __('Newsletter mailing list', 'mailpoet'),
        SegmentEntity::TYPE_DEFAULT,
        __('This list is automatically created when you install MailPoet.', 'mailpoet')
      );
      $this->segmentsRepository->persist($defaultSegment);
      $this->segmentsRepository->flush();
    }

    return $defaultSegment;
  }

  protected function newsletterOptionFields() {
    $optionFields = [
      [
        'name' => 'isScheduled',
        'newsletter_type' => 'standard',
      ],
      [
        'name' => 'scheduledAt',
        'newsletter_type' => 'standard',
      ],
      [
        'name' => 'event',
        'newsletter_type' => 'welcome',
      ],
      [
        'name' => 'segment',
        'newsletter_type' => 'welcome',
      ],
      [
        'name' => 'role',
        'newsletter_type' => 'welcome',
      ],
      [
        'name' => 'afterTimeNumber',
        'newsletter_type' => 'welcome',
      ],
      [
        'name' => 'afterTimeType',
        'newsletter_type' => 'welcome',
      ],
      [
        'name' => 'intervalType',
        'newsletter_type' => 'notification',
      ],
      [
        'name' => 'timeOfDay',
        'newsletter_type' => 'notification',
      ],
      [
        'name' => 'weekDay',
        'newsletter_type' => 'notification',
      ],
      [
        'name' => 'monthDay',
        'newsletter_type' => 'notification',
      ],
      [
        'name' => 'nthWeekDay',
        'newsletter_type' => 'notification',
      ],
      [
        'name' => 'schedule',
        'newsletter_type' => 'notification',
      ],
      [
        'name' => 'group',
        'newsletter_type' => NewsletterEntity::TYPE_AUTOMATIC,
      ],
      [
        'name' => 'group',
        'newsletter_type' => NewsletterEntity::TYPE_AUTOMATION,
      ],
      [
        'name' => 'group',
        'newsletter_type' => NewsletterEntity::TYPE_AUTOMATION_TRANSACTIONAL,
      ],
      [
        'name' => 'event',
        'newsletter_type' => NewsletterEntity::TYPE_AUTOMATIC,
      ],
      [
        'name' => 'event',
        'newsletter_type' => NewsletterEntity::TYPE_AUTOMATION,
      ],
      [
        'name' => 'event',
        'newsletter_type' => NewsletterEntity::TYPE_AUTOMATION_TRANSACTIONAL,
      ],
      [
        'name' => 'sendTo',
        'newsletter_type' => NewsletterEntity::TYPE_AUTOMATIC,
      ],
      [
        'name' => 'segment',
        'newsletter_type' => NewsletterEntity::TYPE_AUTOMATIC,
      ],
      [
        'name' => 'afterTimeNumber',
        'newsletter_type' => NewsletterEntity::TYPE_AUTOMATIC,
      ],
      [
        'name' => 'afterTimeType',
        'newsletter_type' => NewsletterEntity::TYPE_AUTOMATIC,
      ],
      [
        'name' => 'meta',
        'newsletter_type' => NewsletterEntity::TYPE_AUTOMATIC,
      ],
      [
        'name' => 'afterTimeNumber',
        'newsletter_type' => NewsletterEntity::TYPE_RE_ENGAGEMENT,
      ],
      [
        'name' => 'afterTimeType',
        'newsletter_type' => NewsletterEntity::TYPE_RE_ENGAGEMENT,
      ],
      [
        'name' => 'automationId',
        'newsletter_type' => NewsletterEntity::TYPE_AUTOMATION,
      ],
      [
        'name' => 'automationStepId',
        'newsletter_type' => NewsletterEntity::TYPE_AUTOMATION,
      ],
      [
        'name' => NewsletterOptionFieldEntity::NAME_FILTER_SEGMENT_ID,
        'newsletter_type' => NewsletterEntity::TYPE_STANDARD,
      ],
      [
        'name' => NewsletterOptionFieldEntity::NAME_FILTER_SEGMENT_ID,
        'newsletter_type' => NewsletterEntity::TYPE_RE_ENGAGEMENT,
      ],
      [
        'name' => NewsletterOptionFieldEntity::NAME_FILTER_SEGMENT_ID,
        'newsletter_type' => NewsletterEntity::TYPE_NOTIFICATION,
      ],
    ];

    return [
      'rows' => $optionFields,
      'identification_columns' => [
        'name',
        'newsletter_type',
      ],
    ];
  }

  protected function newsletterTemplates() {
    $templates = [];
    foreach ($this->templates as $template) {
      $template = self::TEMPLATES_NAMESPACE . $template;
      $template = new $template(Env::$assetsUrl);
      $templates[] = $template->get();
    }
    return [
      'rows' => $templates,
      'identification_columns' => [
        'name',
      ],
      'remove_duplicates' => true,
    ];
  }

  protected function populate($model) {
    $modelMethod = Helpers::underscoreToCamelCase($model);
    $table = $this->prefix . $model;
    $dataDescriptor = $this->$modelMethod();
    $rows = $dataDescriptor['rows'];
    $identificationColumns = array_fill_keys(
      $dataDescriptor['identification_columns'],
      ''
    );
    $removeDuplicates =
      isset($dataDescriptor['remove_duplicates']) && $dataDescriptor['remove_duplicates'];

    foreach ($rows as $row) {
      $existenceComparisonFields = array_intersect_key(
        $row,
        $identificationColumns
      );

      if (!$this->rowExists($table, $existenceComparisonFields)) {
        $this->insertRow($table, $row);
      } else {
        if ($removeDuplicates) {
          $this->removeDuplicates($table, $row, $existenceComparisonFields);
        }
        $this->updateRow($table, $row, $existenceComparisonFields);
      }
    }
  }

  private function rowExists(string $tableName, array $columns): bool {
    global $wpdb;

    $conditions = array_map(function($key, $value) {
      return esc_sql($key) . "='" . esc_sql($value) . "'";
    }, array_keys($columns), $columns);

    $table = esc_sql($tableName);
    // $conditions is escaped
    // phpcs:ignore WordPressDotOrg.sniffs.DirectDB.UnescapedDBParameter
    return $wpdb->get_var(
      "SELECT COUNT(*) FROM $table WHERE " . implode(' AND ', $conditions)
    ) > 0;
  }

  private function insertRow($table, $row) {
    global $wpdb;

    return $wpdb->insert(
      $table,
      $row
    );
  }

  private function updateRow($table, $row, $where) {
    global $wpdb;

    return $wpdb->update(
      $table,
      $row,
      $where
    );
  }

  private function removeDuplicates($table, $row, $where) {
    global $wpdb;

    $conditions = ['1=1'];
    $values = [];
    foreach ($where as $field => $value) {
      $conditions[] = "`t1`.`" . esc_sql($field) . "` = `t2`.`" . esc_sql($field) . "`";
      $conditions[] = "`t1`.`" . esc_sql($field) . "` = %s";
      $values[] = $value;
    }

    $conditions = implode(' AND ', $conditions);

    $table = esc_sql($table);
    return $wpdb->query(
      $wpdb->prepare(
        "DELETE t1 FROM $table t1, $table t2 WHERE t1.id < t2.id AND $conditions",
        $values
      )
    );
  }

  private function createSourceForSubscribers() {
    $statisticsFormTable = $this->entityManager->getClassMetadata(StatisticsFormEntity::class)->getTableName();
    $subscriberTable = $this->entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();

    $this->entityManager->getConnection()->executeStatement(
      ' UPDATE LOW_PRIORITY `' . $subscriberTable . '` subscriber ' .
      ' JOIN `' . $statisticsFormTable . '` stats ON stats.subscriber_id=subscriber.id ' .
      ' SET `source` = "' . Source::FORM . '"' .
      ' WHERE `source` = "' . Source::UNKNOWN . '"'
    );

    $this->entityManager->getConnection()->executeStatement(
      'UPDATE LOW_PRIORITY `' . $subscriberTable . '`' .
      ' SET `source` = "' . Source::WORDPRESS_USER . '"' .
      ' WHERE `source` = "' . Source::UNKNOWN . '"' .
      ' AND `wp_user_id` IS NOT NULL'
    );

    $this->entityManager->getConnection()->executeStatement(
      'UPDATE LOW_PRIORITY `' . $subscriberTable . '`' .
      ' SET `source` = "' . Source::WOOCOMMERCE_USER . '"' .
      ' WHERE `source` = "' . Source::UNKNOWN . '"' .
      ' AND `is_woocommerce_user` = 1'
    );
  }

  private function scheduleInitialInactiveSubscribersCheck() {
    $this->scheduleTask(
      InactiveSubscribers::TASK_TYPE,
      Carbon::createFromTimestamp($this->wp->currentTime('timestamp'))->addHour()
    );
  }

  private function scheduleAuthorizedSendingEmailsCheck() {
    if (!Bridge::isMPSendingServiceEnabled()) {
      return;
    }
    $this->scheduleTask(
      AuthorizedSendingEmailsCheck::TASK_TYPE,
      Carbon::createFromTimestamp($this->wp->currentTime('timestamp'))
    );
  }

  private function scheduleBeamer() {
    if (!$this->settings->get('last_announcement_date')) {
      $this->scheduleTask(
        Beamer::TASK_TYPE,
        Carbon::createFromTimestamp($this->wp->currentTime('timestamp'))
      );
    }
  }

  private function scheduleUnsubscribeTokens() {
    $this->scheduleTask(
      UnsubscribeTokens::TASK_TYPE,
      Carbon::createFromTimestamp($this->wp->currentTime('timestamp'))
    );
  }

  private function scheduleSubscriberLinkTokens() {
    $this->scheduleTask(
      SubscriberLinkTokens::TASK_TYPE,
      Carbon::createFromTimestamp($this->wp->currentTime('timestamp'))
    );
  }

  private function scheduleMixpanel() {
    $this->scheduleTask(Mixpanel::TASK_TYPE, Carbon::createFromTimestamp($this->wp->currentTime('timestamp')));
  }

  private function scheduleTask($type, $datetime, $priority = null) {
    $task = $this->scheduledTasksRepository->findOneBy(
      [
        'type' => $type,
        'status' => [ScheduledTaskEntity::STATUS_SCHEDULED, null],
      ]
    );

    if ($task) {
      return true;
    }

    $task = new ScheduledTaskEntity();
    $task->setType($type);
    $task->setStatus(ScheduledTaskEntity::STATUS_SCHEDULED);
    $task->setScheduledAt($datetime);

    if ($priority !== null) {
      $task->setPriority($priority);
    }

    $this->scheduledTasksRepository->persist($task);
    $this->scheduledTasksRepository->flush();
  }

  private function detectReferral() {
    $this->referralDetector->detect();
  }

  private function scheduleSubscriberLastEngagementDetection() {
    if (version_compare((string)$this->settings->get('db_version', '3.72.1'), '3.72.0', '>')) {
      return;
    }
    $this->scheduleTask(
      SubscribersLastEngagement::TASK_TYPE,
      Carbon::createFromTimestamp($this->wp->currentTime('timestamp'))
    );
  }

  private function scheduleNewsletterTemplateThumbnails() {
    $this->scheduleTask(
      NewsletterTemplateThumbnails::TASK_TYPE,
      Carbon::createFromTimestamp($this->wp->currentTime('timestamp')),
      ScheduledTaskEntity::PRIORITY_LOW
    );
  }

  private function scheduleBackfillEngagementData(): void {
    $existingTask = $this->scheduledTasksRepository->findOneBy(
      [
        'type' => BackfillEngagementData::TASK_TYPE,
      ]
    );
    if ($existingTask) {
      return;
    }
    $this->scheduleTask(
      BackfillEngagementData::TASK_TYPE,
      Carbon::createFromTimestamp($this->wp->currentTime('timestamp'))
    );
  }
}
