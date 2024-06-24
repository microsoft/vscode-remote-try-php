<?php declare(strict_types = 1);

namespace MailPoet\Migrations\App;

if (!defined('ABSPATH')) exit;


use MailPoet\Cron\CronTrigger;
use MailPoet\Cron\Workers\StatsNotifications\Worker;
use MailPoet\Entities\FormEntity;
use MailPoet\Form\FormsRepository;
use MailPoet\Migrator\AppMigration;
use MailPoet\Settings\SettingsChangeHandler;
use MailPoet\Settings\SettingsController;
use MailPoet\Settings\TrackingConfig;
use MailPoet\Util\Notices\ChangedTrackingNotice;
use MailPoet\WP\Functions as WPFunctions;

/**
 * Extracted from original Migration_20221028_105818.php when separating Db and App migrations.
 */
class Migration_20221028_105818_App extends AppMigration {
  /** @var SettingsController */
  private $settings;

  /** @var SettingsChangeHandler */
  private $settingsChangeHandler;

  /** @var FormsRepository */
  private $formsRepository;

  /** @var WPFunctions */
  private $wp;

  public function run(): void {
    $this->settings = $this->container->get(SettingsController::class);
    $this->settingsChangeHandler = $this->container->get(SettingsChangeHandler::class);
    $this->formsRepository = $this->container->get(FormsRepository::class);
    $this->wp = $this->container->get(WPFunctions::class);

    $this->updateDefaultInactiveSubscriberTimeRange();
    $this->setDefaultValueForLoadingThirdPartyLibrariesForExistingInstalls();
    $this->disableMailPoetCronTrigger();

    // POPULATOR
    $this->enableStatsNotificationsForAutomatedEmails();
    $this->addPlacementStatusToForms();
    $this->migrateFormPlacement();
    $this->updateToUnifiedTrackingSettings();
  }

  private function updateDefaultInactiveSubscriberTimeRange(): bool {
    // Skip if the installed version is newer than the release that preceded this migration, or if it's a fresh install
    $currentlyInstalledVersion = (string)$this->settings->get('db_version', '3.78.1');
    if (version_compare($currentlyInstalledVersion, '3.78.0', '>')) {
      return false;
    }

    $currentValue = (int)$this->settings->get('deactivate_subscriber_after_inactive_days');
    if ($currentValue === 180) {
      $this->settings->set('deactivate_subscriber_after_inactive_days', 365);
      $this->settingsChangeHandler->onInactiveSubscribersIntervalChange();
    }

    return true;
  }

  private function setDefaultValueForLoadingThirdPartyLibrariesForExistingInstalls(): bool {
    // skip the migration if the DB version is higher than 3.91.1 or is not set (a new installation)
    if (version_compare($this->settings->get('db_version', '3.91.2'), '3.91.1', '>')) {
      return false;
    }

    $thirdPartyScriptsEnabled = $this->settings->get('3rd_party_libs');
    if (is_null($thirdPartyScriptsEnabled)) {
      // keep loading 3rd party libraries for existing users so the functionality is not broken
      $this->settings->set('3rd_party_libs.enabled', '1');
    }

    return true;
  }

  private function enableStatsNotificationsForAutomatedEmails() {
    if (version_compare((string)$this->settings->get('db_version', '3.31.2'), '3.31.1', '>')) {
      return;
    }
    $settings = $this->settings->get(Worker::SETTINGS_KEY);
    $settings['automated'] = true;
    $this->settings->set(Worker::SETTINGS_KEY, $settings);
  }

  private function addPlacementStatusToForms() {
    if (version_compare((string)$this->settings->get('db_version', '3.49.0'), '3.48.1', '>')) {
      return;
    }
    $forms = $this->formsRepository->findAll();
    foreach ($forms as $form) {
      $settings = $form->getSettings();
      if (
        (isset($settings['place_form_bellow_all_posts']) && $settings['place_form_bellow_all_posts'] === '1')
        || (isset($settings['place_form_bellow_all_pages']) && $settings['place_form_bellow_all_pages'] === '1')
      ) {
        $settings['form_placement_bellow_posts_enabled'] = '1';
      } else {
        $settings['form_placement_bellow_posts_enabled'] = '';
      }
      if (
        (isset($settings['place_popup_form_on_all_posts']) && $settings['place_popup_form_on_all_posts'] === '1')
        || (isset($settings['place_popup_form_on_all_pages']) && $settings['place_popup_form_on_all_pages'] === '1')
      ) {
        $settings['form_placement_popup_enabled'] = '1';
      } else {
        $settings['form_placement_popup_enabled'] = '';
      }
      if (
        (isset($settings['place_fixed_bar_form_on_all_posts']) && $settings['place_fixed_bar_form_on_all_posts'] === '1')
        || (isset($settings['place_fixed_bar_form_on_all_pages']) && $settings['place_fixed_bar_form_on_all_pages'] === '1')
      ) {
        $settings['form_placement_fixed_bar_enabled'] = '1';
      } else {
        $settings['form_placement_fixed_bar_enabled'] = '';
      }
      if (
        (isset($settings['place_slide_in_form_on_all_posts']) && $settings['place_slide_in_form_on_all_posts'] === '1')
        || (isset($settings['place_slide_in_form_on_all_pages']) && $settings['place_slide_in_form_on_all_pages'] === '1')
      ) {
        $settings['form_placement_slide_in_enabled'] = '1';
      } else {
        $settings['form_placement_slide_in_enabled'] = '';
      }
      $form->setSettings($settings);
    }
    $this->formsRepository->flush();
  }

  private function migrateFormPlacement() {
    if (version_compare((string)$this->settings->get('db_version', '3.50.0'), '3.49.1', '>')) {
      return;
    }
    $forms = $this->formsRepository->findAll();
    foreach ($forms as $form) {
      $settings = $form->getSettings();
      if (!is_array($settings)) continue;
      $settings['form_placement'] = [
        FormEntity::DISPLAY_TYPE_POPUP => [
          'enabled' => $settings['form_placement_popup_enabled'],
          'delay' => $settings['popup_form_delay'] ?? 0,
          'styles' => $settings['popup_styles'] ?? [],
          'posts' => [
            'all' => $settings['place_popup_form_on_all_posts'] ?? '',
          ],
          'pages' => [
            'all' => $settings['place_popup_form_on_all_pages'] ?? '',
          ],
        ],
        FormEntity::DISPLAY_TYPE_FIXED_BAR => [
          'enabled' => $settings['form_placement_fixed_bar_enabled'],
          'delay' => $settings['fixed_bar_form_delay'] ?? 0,
          'styles' => $settings['fixed_bar_styles'] ?? [],
          'position' => $settings['fixed_bar_form_position'] ?? 'top',
          'posts' => [
            'all' => $settings['place_fixed_bar_form_on_all_posts'] ?? '',
          ],
          'pages' => [
            'all' => $settings['place_fixed_bar_form_on_all_pages'] ?? '',
          ],
        ],
        FormEntity::DISPLAY_TYPE_BELOW_POST => [
          'enabled' => $settings['form_placement_bellow_posts_enabled'],
          'styles' => $settings['below_post_styles'] ?? [],
          'posts' => [
            'all' => $settings['place_form_bellow_all_posts'] ?? '',
          ],
          'pages' => [
            'all' => $settings['place_form_bellow_all_pages'] ?? '',
          ],
        ],
        FormEntity::DISPLAY_TYPE_SLIDE_IN => [
          'enabled' => $settings['form_placement_slide_in_enabled'],
          'delay' => $settings['slide_in_form_delay'] ?? 0,
          'position' => $settings['slide_in_form_position'] ?? 'right',
          'styles' => $settings['slide_in_styles'] ?? [],
          'posts' => [
            'all' => $settings['place_slide_in_form_on_all_posts'] ?? '',
          ],
          'pages' => [
            'all' => $settings['place_slide_in_form_on_all_pages'] ?? '',
          ],
        ],
        FormEntity::DISPLAY_TYPE_OTHERS => [
          'styles' => $settings['other_styles'] ?? [],
        ],
      ];
      if (isset($settings['form_placement_slide_in_enabled'])) unset($settings['form_placement_slide_in_enabled']);
      if (isset($settings['form_placement_fixed_bar_enabled'])) unset($settings['form_placement_fixed_bar_enabled']);
      if (isset($settings['form_placement_popup_enabled'])) unset($settings['form_placement_popup_enabled']);
      if (isset($settings['form_placement_bellow_posts_enabled'])) unset($settings['form_placement_bellow_posts_enabled']);
      if (isset($settings['place_form_bellow_all_pages'])) unset($settings['place_form_bellow_all_pages']);
      if (isset($settings['place_form_bellow_all_posts'])) unset($settings['place_form_bellow_all_posts']);
      if (isset($settings['place_popup_form_on_all_pages'])) unset($settings['place_popup_form_on_all_pages']);
      if (isset($settings['place_popup_form_on_all_posts'])) unset($settings['place_popup_form_on_all_posts']);
      if (isset($settings['popup_form_delay'])) unset($settings['popup_form_delay']);
      if (isset($settings['place_fixed_bar_form_on_all_pages'])) unset($settings['place_fixed_bar_form_on_all_pages']);
      if (isset($settings['place_fixed_bar_form_on_all_posts'])) unset($settings['place_fixed_bar_form_on_all_posts']);
      if (isset($settings['fixed_bar_form_delay'])) unset($settings['fixed_bar_form_delay']);
      if (isset($settings['fixed_bar_form_position'])) unset($settings['fixed_bar_form_position']);
      if (isset($settings['place_slide_in_form_on_all_pages'])) unset($settings['place_slide_in_form_on_all_pages']);
      if (isset($settings['place_slide_in_form_on_all_posts'])) unset($settings['place_slide_in_form_on_all_posts']);
      if (isset($settings['slide_in_form_delay'])) unset($settings['slide_in_form_delay']);
      if (isset($settings['slide_in_form_position'])) unset($settings['slide_in_form_position']);
      if (isset($settings['other_styles'])) unset($settings['other_styles']);
      if (isset($settings['slide_in_styles'])) unset($settings['slide_in_styles']);
      if (isset($settings['below_post_styles'])) unset($settings['below_post_styles']);
      if (isset($settings['fixed_bar_styles'])) unset($settings['fixed_bar_styles']);
      if (isset($settings['popup_styles'])) unset($settings['popup_styles']);
      $form->setSettings($settings);
    }
    $this->formsRepository->flush();
  }

  private function updateToUnifiedTrackingSettings() {
    if (version_compare((string)$this->settings->get('db_version', '3.74.3'), '3.74.2', '>')) {
      return;
    }
    $emailTracking = $this->settings->get('tracking.enabled', true);
    $wooTrackingCookie = $this->settings->get('woocommerce.accept_cookie_revenue_tracking.enabled');
    if ($wooTrackingCookie === null) { // No setting for WooCommerce Cookie Tracking - WooCommerce was not active
      $trackingLevel = $emailTracking ? TrackingConfig::LEVEL_FULL : TrackingConfig::LEVEL_BASIC;
    } elseif ($wooTrackingCookie) { // WooCommerce Cookie Tracking enabled
      $trackingLevel = TrackingConfig::LEVEL_FULL;
      // Cookie was enabled but tracking disabled and we are switching to full.
      // So we activate an admin notice to let the user know that we activated tracking
      if (!$emailTracking) {
        $this->wp->setTransient(ChangedTrackingNotice::OPTION_NAME, true);
      }
    } else { // WooCommerce Tracking Cookie Disabled
      $trackingLevel = $emailTracking ? TrackingConfig::LEVEL_PARTIAL : TrackingConfig::LEVEL_BASIC;
    }
    $this->settings->set('tracking.level', $trackingLevel);
  }

  private function disableMailPoetCronTrigger() {
    $method = $this->settings->get(CronTrigger::SETTING_NAME . '.method');
    if ($method !== 'MailPoet') {
      return;
    }
    $this->settings->set(CronTrigger::SETTING_NAME . '.method', CronTrigger::METHOD_WORDPRESS);
  }
}
