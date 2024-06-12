<?php declare(strict_types = 1);

namespace MailPoet\Migrations\App;

if (!defined('ABSPATH')) exit;


use MailPoet\Homepage\HomepageDataController;
use MailPoet\Migrator\AppMigration;
use MailPoet\Settings\SettingsController;
use MailPoet\WooCommerce\Helper;

class Migration_20230131_121621 extends AppMigration {
  /**
   * This migration detect whether we should display Task List and Product Discovery sections
   * on the homepage for the old users.
   */
  public function run(): void {
    // Hide task list for users who installed the plugin more than 2 weeks ago
    $settings = $this->container->get(SettingsController::class);
    $installedAt = strtotime($settings->get('installed_at', date('Y-m-d H:i:s')));
    $twoWeeksAgo = strtotime('-2 weeks');
    if ($installedAt < $twoWeeksAgo) {
      $settings->set('homepage.task_list_dismissed', true);
    }

    // Hide product discovery for users who completed all tasks
    $homepageDataController = $this->container->get(HomepageDataController::class);
    $wooCommerceHelper = $this->container->get(Helper::class);
    $homepageData = $homepageDataController->getPageData();
    $productDiscoveryStatus = $homepageData['productDiscoveryStatus'];
    if ($wooCommerceHelper->isWooCommerceActive()) {
      $productDiscoveryIsComplete = $productDiscoveryStatus['addSubscriptionForm'] &&
        $productDiscoveryStatus['setUpWelcomeCampaign'] &&
        $productDiscoveryStatus['setUpAbandonedCartEmail'] &&
        $productDiscoveryStatus['brandWooEmails'];
    } else {
      $productDiscoveryIsComplete = $productDiscoveryStatus['addSubscriptionForm'] &&
        $productDiscoveryStatus['setUpWelcomeCampaign'] &&
        $productDiscoveryStatus['sendFirstNewsletter'];
    }
    $settings->set('homepage.product_discovery_dismissed', $productDiscoveryIsComplete);
  }
}
