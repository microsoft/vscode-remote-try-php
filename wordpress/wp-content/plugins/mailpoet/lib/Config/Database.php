<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Idiorm\ORM;
use PDO;

class Database {
  public function init(PDO $pdo) {
    ORM::setDb($pdo);
    $this->setupLogging();
    $this->defineTables();
  }

  public function setupLogging() {
    ORM::configure('logging', WP_DEBUG);
  }

  public function defineTables() {
    if (!defined('MP_SETTINGS_TABLE')) {
      define('MP_SETTINGS_TABLE', Env::$dbPrefix . 'settings');
      define('MP_SEGMENTS_TABLE', Env::$dbPrefix . 'segments');
      define('MP_FORMS_TABLE', Env::$dbPrefix . 'forms');
      define('MP_CUSTOM_FIELDS_TABLE', Env::$dbPrefix . 'custom_fields');
      define('MP_SUBSCRIBERS_TABLE', Env::$dbPrefix . 'subscribers');
      define('MP_SUBSCRIBER_SEGMENT_TABLE', Env::$dbPrefix . 'subscriber_segment');
      define('MP_SUBSCRIBER_CUSTOM_FIELD_TABLE', Env::$dbPrefix . 'subscriber_custom_field');
      define('MP_SUBSCRIBER_IPS_TABLE', Env::$dbPrefix . 'subscriber_ips');
      define('MP_NEWSLETTER_SEGMENT_TABLE', Env::$dbPrefix . 'newsletter_segment');
      define('MP_SCHEDULED_TASKS_TABLE', Env::$dbPrefix . 'scheduled_tasks');
      define('MP_SCHEDULED_TASK_SUBSCRIBERS_TABLE', Env::$dbPrefix . 'scheduled_task_subscribers');
      define('MP_SENDING_QUEUES_TABLE', Env::$dbPrefix . 'sending_queues');
      define('MP_NEWSLETTERS_TABLE', Env::$dbPrefix . 'newsletters');
      define('MP_NEWSLETTER_TEMPLATES_TABLE', Env::$dbPrefix . 'newsletter_templates');
      define('MP_NEWSLETTER_OPTION_FIELDS_TABLE', Env::$dbPrefix . 'newsletter_option_fields');
      define('MP_NEWSLETTER_OPTION_TABLE', Env::$dbPrefix . 'newsletter_option');
      define('MP_NEWSLETTER_LINKS_TABLE', Env::$dbPrefix . 'newsletter_links');
      define('MP_NEWSLETTER_POSTS_TABLE', Env::$dbPrefix . 'newsletter_posts');
      define('MP_STATISTICS_NEWSLETTERS_TABLE', Env::$dbPrefix . 'statistics_newsletters');
      define('MP_STATISTICS_CLICKS_TABLE', Env::$dbPrefix . 'statistics_clicks');
      define('MP_STATISTICS_OPENS_TABLE', Env::$dbPrefix . 'statistics_opens');
      define('MP_STATISTICS_UNSUBSCRIBES_TABLE', Env::$dbPrefix . 'statistics_unsubscribes');
      define('MP_STATISTICS_FORMS_TABLE', Env::$dbPrefix . 'statistics_forms');
      define('MP_STATISTICS_WOOCOMMERCE_PURCHASES_TABLE', Env::$dbPrefix . 'statistics_woocommerce_purchases');
      define('MP_MAPPING_TO_EXTERNAL_ENTITIES_TABLE', Env::$dbPrefix . 'mapping_to_external_entities');
      define('MP_LOG_TABLE', Env::$dbPrefix . 'log');
      define('MP_STATS_NOTIFICATIONS_TABLE', Env::$dbPrefix . 'stats_notifications');
      define('MP_USER_FLAGS_TABLE', Env::$dbPrefix . 'user_flags');
      define('MP_FEATURE_FLAGS_TABLE', Env::$dbPrefix . 'feature_flags');
      define('MP_DYNAMIC_SEGMENTS_FILTERS_TABLE', Env::$dbPrefix . 'dynamic_segment_filters');
    }
  }
}
