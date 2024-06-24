<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config;

if (!defined('ABSPATH')) exit;


use MailPoet\AdminPages\Pages\Automation;
use MailPoet\AdminPages\Pages\AutomationAnalytics;
use MailPoet\AdminPages\Pages\AutomationEditor;
use MailPoet\AdminPages\Pages\AutomationTemplates;
use MailPoet\AdminPages\Pages\DynamicSegments;
use MailPoet\AdminPages\Pages\EmailEditor as EmailEditorPage;
use MailPoet\AdminPages\Pages\ExperimentalFeatures;
use MailPoet\AdminPages\Pages\FormEditor;
use MailPoet\AdminPages\Pages\Forms;
use MailPoet\AdminPages\Pages\Help;
use MailPoet\AdminPages\Pages\Homepage;
use MailPoet\AdminPages\Pages\Landingpage;
use MailPoet\AdminPages\Pages\Logs;
use MailPoet\AdminPages\Pages\NewsletterEditor;
use MailPoet\AdminPages\Pages\Newsletters;
use MailPoet\AdminPages\Pages\Settings;
use MailPoet\AdminPages\Pages\StaticSegments;
use MailPoet\AdminPages\Pages\Subscribers;
use MailPoet\AdminPages\Pages\SubscribersExport;
use MailPoet\AdminPages\Pages\SubscribersImport;
use MailPoet\AdminPages\Pages\Upgrade;
use MailPoet\AdminPages\Pages\WelcomeWizard;
use MailPoet\AdminPages\Pages\WooCommerceSetup;
use MailPoet\DI\ContainerWrapper;
use MailPoet\Form\Util\CustomFonts;
use MailPoet\Util\License\Features\CapabilitiesManager;
use MailPoet\WP\Functions as WPFunctions;

class Menu {
  const MAIN_PAGE_SLUG = self::HOMEPAGE_PAGE_SLUG;
  const NO_PARENT_PAGE_SLUG = 'mailpoet-no-parent';

  const EMAILS_PAGE_SLUG = 'mailpoet-newsletters';
  const FORMS_PAGE_SLUG = 'mailpoet-forms';
  const EMAIL_EDITOR_PAGE_SLUG = 'mailpoet-newsletter-editor';
  const EMAIL_EDITOR_V2_PAGE_SLUG = 'mailpoet-email-editor';
  const FORM_EDITOR_PAGE_SLUG = 'mailpoet-form-editor';
  const HOMEPAGE_PAGE_SLUG = 'mailpoet-homepage';
  const FORM_TEMPLATES_PAGE_SLUG = 'mailpoet-form-editor-template-selection';
  const SUBSCRIBERS_PAGE_SLUG = 'mailpoet-subscribers';
  const IMPORT_PAGE_SLUG = 'mailpoet-import';
  const EXPORT_PAGE_SLUG = 'mailpoet-export';
  const LISTS_PAGE_SLUG = 'mailpoet-lists';
  const SEGMENTS_PAGE_SLUG = 'mailpoet-segments';
  const SETTINGS_PAGE_SLUG = 'mailpoet-settings';
  const HELP_PAGE_SLUG = 'mailpoet-help';
  const UPGRADE_PAGE_SLUG = 'mailpoet-upgrade';
  const WELCOME_WIZARD_PAGE_SLUG = 'mailpoet-welcome-wizard';
  const WOOCOMMERCE_SETUP_PAGE_SLUG = 'mailpoet-woocommerce-setup';
  const EXPERIMENTS_PAGE_SLUG = 'mailpoet-experimental';
  const LOGS_PAGE_SLUG = 'mailpoet-logs';
  const AUTOMATIONS_PAGE_SLUG = 'mailpoet-automation';
  const AUTOMATION_EDITOR_PAGE_SLUG = 'mailpoet-automation-editor';
  const AUTOMATION_ANALYTICS_PAGE_SLUG = 'mailpoet-automation-analytics';
  const AUTOMATION_TEMPLATES_PAGE_SLUG = 'mailpoet-automation-templates';

  const LANDINGPAGE_PAGE_SLUG = 'mailpoet-landingpage';

  const ICON_BASE64_SVG = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxNTIuMDIgMTU2LjQiPjxwYXRoIGZpbGw9IiNhN2FhYWQiIGQ9Ik0zNy43MSw4OS4xYzMuNSwwLDUuOS0uOCw3LjItMi4zYTgsOCwwLDAsMCwyLTUuNFYzNS43bDE3LDQ1LjFhMTIuNjgsMTIuNjgsMCwwLDAsMy43LDUuNGMxLjYsMS4zLDQsMiw3LjIsMmExMi41NCwxMi41NCwwLDAsMCw1LjktMS40LDguNDEsOC40MSwwLDAsMCwzLjktNWwxOC4xLTUwVjgxYTguNTMsOC41MywwLDAsMCwyLjEsNi4xYzEuNCwxLjQsMy43LDIuMiw2LjksMi4yLDMuNSwwLDUuOS0uOCw3LjItMi4zYTgsOCwwLDAsMCwyLTUuNFY4LjdhNy40OCw3LjQ4LDAsMCwwLTMuMy02LjZjLTIuMS0xLjQtNS0yLjEtOC42LTIuMWExOS4zLDE5LjMsMCwwLDAtOS40LDIsMTEuNjMsMTEuNjMsMCwwLDAtNS4xLDYuOEw3NC45MSw2Ny4xLDU0LjQxLDguNGExMi40LDEyLjQsMCwwLDAtNC41LTYuMmMtMi4xLTEuNS01LTIuMi04LjgtMi4yYTE2LjUxLDE2LjUxLDAsMCwwLTguOSwyLjFjLTIuMywxLjUtMy41LDMuOS0zLjUsNy4yVjgwLjhjMCwyLjguNyw0LjgsMiw2LjJDMzIuMjEsODguNCwzNC40MSw4OS4xLDM3LjcxLDg5LjFaIi8+PHBhdGggZmlsbD0iI2E3YWFhZCIgZD0iTTE0OSwxMTYuNmwtMi40LTEuOWE3LjQsNy40LDAsMCwwLTkuNC4zLDE5LjY1LDE5LjY1LDAsMCwxLTEyLjUsNC42aC0yMS40QTM3LjA4LDM3LjA4LDAsMCwwLDc3LDEzMC41bC0xLjEsMS4yLTEuMS0xLjFhMzcuMjUsMzcuMjUsMCwwLDAtMjYuMy0xMC45SDI3YTE5LjU5LDE5LjU5LDAsMCwxLTEyLjQtNC42LDcuMjgsNy4yOCwwLDAsMC05LjQtLjNsLTIuNCwxLjlBNy40Myw3LjQzLDAsMCwwLDAsMTIyLjJhNy4xNCw3LjE0LDAsMCwwLDIuNCw1LjdBMzcuMjgsMzcuMjgsMCwwLDAsMjcsMTM3LjRoMjEuNmExOS41OSwxOS41OSwwLDAsMSwxOC45LDE0LjR2LjJjLjEuNywxLjIsNC40LDguNSw0LjRzOC40LTMuNyw4LjUtNC40di0uMmExOS41OSwxOS41OSwwLDAsMSwxOC45LTE0LjRIMTI1YTM3LjI4LDM3LjI4LDAsMCwwLDI0LjYtOS41LDcuNDIsNy40MiwwLDAsMCwyLjQtNS43QTcuODYsNy44NiwwLDAsMCwxNDksMTE2LjZaIi8+PC9zdmc+';

  public $mpApiKeyValid;
  public $premiumKeyValid;

  /** @var AccessControl */
  private $accessControl;

  /** @var WPFunctions */
  private $wp;

  /** @var ServicesChecker */
  private $servicesChecker;

  /** @var ContainerWrapper */
  private $container;

  /** @var Router */
  private $router;

  /** @var CustomFonts  */
  private $customFonts;

  private CapabilitiesManager $capabilitiesManager;

  public function __construct(
    AccessControl $accessControl,
    WPFunctions $wp,
    ServicesChecker $servicesChecker,
    ContainerWrapper $container,
    Router $router,
    CustomFonts $customFonts,
    CapabilitiesManager $capabilitiesManager
  ) {
    $this->accessControl = $accessControl;
    $this->wp = $wp;
    $this->servicesChecker = $servicesChecker;
    $this->container = $container;
    $this->router = $router;
    $this->customFonts = $customFonts;
    $this->capabilitiesManager = $capabilitiesManager;
  }

  public function init() {
    $this->checkPremiumKey();

    $this->wp->addAction(
      'admin_menu',
      [
        $this,
        'setup',
      ]
    );

    $this->wp->addFilter('parent_file', [$this, 'highlightNestedMailPoetSubmenus']);
  }

  public function setup() {
    global $parent_file;
    $parent_file = self::EMAILS_PAGE_SLUG;
    if (!$this->accessControl->validatePermission(AccessControl::PERMISSION_ACCESS_PLUGIN_ADMIN)) return;

    $this->router->checkRedirects();

    $this->registerMailPoetMenu();

    if (!self::isOnMailPoetAdminPage()) {
      return;
    }
    $this->wp->doAction('mailpoet_conflict_resolver_styles');
    $this->wp->doAction('mailpoet_conflict_resolver_scripts');

    if (
      !isset($_REQUEST['page'])
      || sanitize_text_field(wp_unslash($_REQUEST['page'])) !== 'mailpoet-newsletter-editor'
    ) {
      return;
    }
    // Disable WP emojis to not interfere with the newsletter editor emoji handling
    $this->disableWPEmojis();
    if (!$this->customFonts->displayCustomFonts()) {
      return;
    }
    $this->wp->addAction('admin_head', function () {
      echo '<link href="https://fonts.googleapis.com/css?family='
        . 'Arvo:400,400i,700,700i'
        . '|Lato:400,400i,700,700i'
        . '|Lora:400,400i,700,700i'
        . '|Merriweather:400,400i,700,700i'
        . '|Merriweather+Sans:400,400i,700,700i'
        . '|Noticia+Text:400,400i,700,700i'
        . '|Open+Sans:400,400i,700,700i'
        . '|Playfair+Display:400,400i,700,700i'
        . '|Roboto:400,400i,700,700i'
        . '|Source+Sans+Pro:400,400i,700,700i'
        . '|Oswald:400,400i,700,700i'
        . '|Raleway:400,400i,700,700i'
        . '|Permanent+Marker:400,400i,700,700i'
        . '|Pacifico:400,400i,700,700i'
        . '" rel="stylesheet">';
    });
  }

  private function registerMailPoetMenu() {

    // Main page
    $this->wp->addMenuPage(
      'MailPoet',
      'MailPoet',
      AccessControl::PERMISSION_ACCESS_PLUGIN_ADMIN,
      self::MAIN_PAGE_SLUG,
      '',
      self::ICON_BASE64_SVG,
      30
    );

    // Welcome wizard page
    $this->wp->addSubmenuPage(
      self::NO_PARENT_PAGE_SLUG,
      $this->setPageTitle(__('Welcome Wizard', 'mailpoet')),
      esc_html__('Welcome Wizard', 'mailpoet'),
      AccessControl::PERMISSION_ACCESS_PLUGIN_ADMIN,
      self::WELCOME_WIZARD_PAGE_SLUG,
      [
        $this,
        'welcomeWizard',
      ]
    );

    // Landingpage
    $this->wp->addSubmenuPage(
      self::NO_PARENT_PAGE_SLUG,
      $this->setPageTitle(__('MailPoet', 'mailpoet')),
      esc_html__('MailPoet', 'mailpoet'),
      AccessControl::PERMISSION_ACCESS_PLUGIN_ADMIN,
      self::LANDINGPAGE_PAGE_SLUG,
      [
        $this,
        'landingPage',
      ]
    );

    $this->registerMailPoetSubMenuEntries();
  }

  private function registerMailPoetSubMenuEntries() {
    // Homepage
    $this->wp->addSubmenuPage(
      self::MAIN_PAGE_SLUG,
      $this->setPageTitle(__('Home', 'mailpoet')),
      esc_html__('Home', 'mailpoet'),
      AccessControl::PERMISSION_ACCESS_PLUGIN_ADMIN,
      self::HOMEPAGE_PAGE_SLUG,
      [
        $this,
        'homepage',
      ]
    );

    // Emails page
    $newslettersPage = $this->wp->addSubmenuPage(
      self::MAIN_PAGE_SLUG,
      $this->setPageTitle(__('Emails', 'mailpoet')),
      esc_html__('Emails', 'mailpoet'),
      AccessControl::PERMISSION_MANAGE_EMAILS,
      self::EMAILS_PAGE_SLUG,
      [
        $this,
        'newsletters',
      ]
    );

    // add limit per page to screen options
    $this->wp->addAction('load-' . $newslettersPage, function() {
      $this->wp->addScreenOption('per_page', [
        'label' => _x(
          'Number of newsletters per page',
          'newsletters per page (screen options)',
          'mailpoet'
        ),
        'option' => 'mailpoet_newsletters_per_page',
      ]);
    });

    // newsletter editor
    $this->wp->addSubmenuPage(
      self::EMAILS_PAGE_SLUG,
      $this->setPageTitle(__('Newsletter', 'mailpoet')),
      esc_html__('Newsletter Editor', 'mailpoet'),
      AccessControl::PERMISSION_MANAGE_EMAILS,
      self::EMAIL_EDITOR_PAGE_SLUG,
      [
        $this,
        'newletterEditor',
      ]
    );

    // newsletter editor
    $emailEditorPage = $this->wp->addSubmenuPage(
      self::EMAILS_PAGE_SLUG,
      $this->setPageTitle(__('Email', 'mailpoet')),
      esc_html__('Email Editor', 'mailpoet'),
      AccessControl::PERMISSION_MANAGE_EMAILS,
      self::EMAIL_EDITOR_V2_PAGE_SLUG,
      [
        $this,
        'emailEditor',
      ]
    );

    // Add body class for email editor page
    // We need to mark the page as a block editor page so that some of the block editor styles are applied properly
    $this->wp->addAction('load-' . $emailEditorPage, function() {
      $this->wp->addFilter('admin_body_class', function ($classes) {
        return ltrim($classes . ' block-editor-page');
      });
    });

    $this->registerAutomationMenu();

    // Forms page
    $formsPage = $this->wp->addSubmenuPage(
      self::MAIN_PAGE_SLUG,
      $this->setPageTitle(__('Forms', 'mailpoet')),
      esc_html__('Forms', 'mailpoet'),
      AccessControl::PERMISSION_MANAGE_FORMS,
      self::FORMS_PAGE_SLUG,
      [
        $this,
        'forms',
      ]
    );

    // add limit per page to screen options
    $this->wp->addAction('load-' . $formsPage, function() {
      $this->wp->addScreenOption('per_page', [
        'label' => _x(
          'Number of forms per page',
          'forms per page (screen options)',
          'mailpoet'
        ),
        'option' => 'mailpoet_forms_per_page',
      ]);
    });

    // form editor
    $formEditorPage = $this->wp->addSubmenuPage(
      self::FORMS_PAGE_SLUG,
      $this->setPageTitle(__('Form Editor', 'mailpoet')),
      esc_html__('Form Editor', 'mailpoet'),
      AccessControl::PERMISSION_MANAGE_FORMS,
      self::FORM_EDITOR_PAGE_SLUG,
      [
        $this,
        'formEditor',
      ]
    );

    // add body class for form editor page
    $this->wp->addAction('load-' . $formEditorPage, function() {
      $this->wp->addFilter('admin_body_class', function ($classes) {
        return ltrim($classes . ' block-editor-page');
      });
    });

    // form editor templates
    $formTemplateSelectionEditorPage = $this->wp->addSubmenuPage(
      self::FORMS_PAGE_SLUG,
      $this->setPageTitle(__('Select Form Template', 'mailpoet')),
      esc_html__('Select Form Template', 'mailpoet'),
      AccessControl::PERMISSION_MANAGE_FORMS,
      self::FORM_TEMPLATES_PAGE_SLUG,
      [
        $this,
        'formEditorTemplateSelection',
      ]
    );

    // Subscribers page
    $subscribersPage = $this->wp->addSubmenuPage(
      self::MAIN_PAGE_SLUG,
      $this->setPageTitle(__('Subscribers', 'mailpoet')),
      esc_html__('Subscribers', 'mailpoet'),
      AccessControl::PERMISSION_MANAGE_SUBSCRIBERS,
      self::SUBSCRIBERS_PAGE_SLUG,
      [
        $this,
        'subscribers',
      ]
    );

    // add limit per page to screen options
    $this->wp->addAction('load-' . $subscribersPage, function() {
      $this->wp->addScreenOption('per_page', [
        'label' => _x(
          'Number of subscribers per page',
          'subscribers per page (screen options)',
          'mailpoet'
        ),
        'option' => 'mailpoet_subscribers_per_page',
      ]);
    });

    // import
    $this->wp->addSubmenuPage(
      self::SUBSCRIBERS_PAGE_SLUG,
      $this->setPageTitle(__('Import', 'mailpoet')),
      esc_html__('Import', 'mailpoet'),
      AccessControl::PERMISSION_MANAGE_SUBSCRIBERS,
      self::IMPORT_PAGE_SLUG,
      [
        $this,
        'import',
      ]
    );

    // export
    $this->wp->addSubmenuPage(
      self::SUBSCRIBERS_PAGE_SLUG,
      $this->setPageTitle(__('Export', 'mailpoet')),
      esc_html__('Export', 'mailpoet'),
      AccessControl::PERMISSION_MANAGE_SUBSCRIBERS,
      self::EXPORT_PAGE_SLUG,
      [
        $this,
        'export',
      ]
    );

    // Lists page
    $listsPage = $this->wp->addSubmenuPage(
      self::MAIN_PAGE_SLUG,
      $this->setPageTitle(__('Lists', 'mailpoet')),
      esc_html__('Lists', 'mailpoet'),
      AccessControl::PERMISSION_MANAGE_SEGMENTS,
      self::LISTS_PAGE_SLUG,
      [
        $this,
        'lists',
      ]
    );

    // add limit per page to screen options
    $this->wp->addAction('load-' . $listsPage, function() {
      $this->wp->addScreenOption('per_page', [
        'label' => _x(
          'Number of lists per page',
          'lists per page (screen options)',
          'mailpoet'
        ),
        'option' => 'mailpoet_lists_per_page',
      ]);
    });

    // Segments page
    $segmentsPage = $this->wp->addSubmenuPage(
      self::MAIN_PAGE_SLUG,
      $this->setPageTitle(__('Segments', 'mailpoet')),
      esc_html__('Segments', 'mailpoet'),
      AccessControl::PERMISSION_MANAGE_SEGMENTS,
      self::SEGMENTS_PAGE_SLUG,
      [
        $this,
        'segments',
      ]
    );

    // add limit per page to screen options
    $this->wp->addAction('load-' . $segmentsPage, function() {
      $this->wp->addScreenOption('per_page', [
        'label' => _x(
          'Number of segments per page',
          'segments per page (screen options)',
          'mailpoet'
        ),
        'option' => 'mailpoet_segments_per_page',
      ]);
    });

    // Settings page
    $this->wp->addSubmenuPage(
      self::MAIN_PAGE_SLUG,
      $this->setPageTitle(__('Settings', 'mailpoet')),
      esc_html__('Settings', 'mailpoet'),
      AccessControl::PERMISSION_MANAGE_SETTINGS,
      self::SETTINGS_PAGE_SLUG,
      [
        $this,
        'settings',
      ]
    );

    // Help page
    $this->wp->addSubmenuPage(
      self::MAIN_PAGE_SLUG,
      $this->setPageTitle(__('Help', 'mailpoet')),
      esc_html__('Help', 'mailpoet'),
      AccessControl::PERMISSION_MANAGE_HELP,
      self::HELP_PAGE_SLUG,
      [
        $this,
        'help',
      ]
    );

    // Upgrade page
    if ($this->capabilitiesManager->showUpgradePage()) {
      $this->wp->addSubmenuPage(
        self::MAIN_PAGE_SLUG,
        $this->setPageTitle(__('Upgrade', 'mailpoet')),
        esc_html__('Upgrade', 'mailpoet'),
        AccessControl::PERMISSION_ACCESS_PLUGIN_ADMIN,
        self::UPGRADE_PAGE_SLUG,
        [
          $this,
          'upgrade',
        ]
      );
    }

    // WooCommerce Setup
    $this->wp->addSubmenuPage(
      self::NO_PARENT_PAGE_SLUG,
      $this->setPageTitle(__('WooCommerce Setup', 'mailpoet')),
      esc_html__('WooCommerce Setup', 'mailpoet'),
      AccessControl::PERMISSION_ACCESS_PLUGIN_ADMIN,
      self::WOOCOMMERCE_SETUP_PAGE_SLUG,
      [
        $this,
        'wooCommerceSetup',
      ]
    );

    // Experimental page
    $this->wp->addSubmenuPage(
      self::SETTINGS_PAGE_SLUG,
      $this->setPageTitle(__('Experimental Features', 'mailpoet')),
      '',
      AccessControl::PERMISSION_MANAGE_FEATURES,
      self::EXPERIMENTS_PAGE_SLUG,
      [$this, 'experimentalFeatures']
    );

    // display logs page
    $this->wp->addSubmenuPage(
      self::SETTINGS_PAGE_SLUG,
      $this->setPageTitle(__('Logs', 'mailpoet')),
      '',
      AccessControl::PERMISSION_ACCESS_PLUGIN_ADMIN,
      self::LOGS_PAGE_SLUG,
      [$this, 'logs']
    );
  }

  private function registerAutomationMenu() {
    $automationPage = $this->wp->addSubmenuPage(
      self::MAIN_PAGE_SLUG,
      $this->setPageTitle(__('Automations', 'mailpoet')),
      esc_html__('Automations', 'mailpoet'),
      AccessControl::PERMISSION_MANAGE_EMAILS,
      self::AUTOMATIONS_PAGE_SLUG,
      [$this, 'automation']
    );

    // Automation editor
    $automationEditorPage = $this->wp->addSubmenuPage(
      self::AUTOMATIONS_PAGE_SLUG,
      $this->setPageTitle(__('Automation Editor', 'mailpoet')),
      esc_html__('Automation Editor', 'mailpoet'),
      AccessControl::PERMISSION_MANAGE_AUTOMATIONS,
      self::AUTOMATION_EDITOR_PAGE_SLUG,
      [$this, 'automationEditor']
    );

    // Automation analytics
    $this->wp->addSubmenuPage(
      self::AUTOMATIONS_PAGE_SLUG,
      $this->setPageTitle(__('Automation Analytics', 'mailpoet')),
      esc_html__('Automation Analytics', 'mailpoet'),
      AccessControl::PERMISSION_MANAGE_AUTOMATIONS,
      self::AUTOMATION_ANALYTICS_PAGE_SLUG,
      [$this, 'automationAnalytics']
    );

    // Automation templates
    $this->wp->addSubmenuPage(
      self::AUTOMATIONS_PAGE_SLUG,
      $this->setPageTitle(__('Automation Templates', 'mailpoet')),
      esc_html__('Automation Templates', 'mailpoet'),
      AccessControl::PERMISSION_MANAGE_AUTOMATIONS,
      self::AUTOMATION_TEMPLATES_PAGE_SLUG,
      [$this, 'automationTemplates']
    );

    // add body class for automation editor page
    $this->wp->addAction('load-' . $automationPage, function() {
      $this->wp->addFilter('admin_body_class', function ($classes) {
        return ltrim($classes . ' mailpoet-automation-is-onboarding');
      });
    });
    $this->wp->addAction('load-' . $automationEditorPage, function() {
      $this->wp->addFilter('admin_body_class', function ($classes) {
        return ltrim($classes . ' site-editor-php');
      });
    });
  }

  public function disableWPEmojis() {
    $this->wp->removeAction('admin_print_scripts', 'print_emoji_detection_script');
    $this->wp->removeAction('admin_print_styles', 'print_emoji_styles');
  }

  public function welcomeWizard() {
    $this->container->get(WelcomeWizard::class)->render();
  }

  public function landingPage() {
    $this->container->get(Landingpage::class)->render();
  }

  public function wooCommerceSetup() {
    $this->container->get(WooCommerceSetup::class)->render();
  }

  public function upgrade() {
    $this->container->get(Upgrade::class)->render();
  }

  public function settings() {
    $this->container->get(Settings::class)->render();
  }

  public function help() {
    $this->container->get(Help::class)->render();
  }

  public function homepage() {
    $this->container->get(Homepage::class)->render();
  }

  public function automation() {
    $this->container->get(Automation::class)->render();
  }

  public function automationTemplates() {
    $this->container->get(AutomationTemplates::class)->render();
  }

  public function automationEditor() {
    $this->container->get(AutomationEditor::class)->render();
  }

  public function automationAnalytics() {
    $this->container->get(AutomationAnalytics::class)->render();
  }

  public function experimentalFeatures() {
    $this->container->get(ExperimentalFeatures::class)->render();
  }

  public function logs() {
    $this->container->get(Logs::class)->render();
  }

  public function subscribers() {
    $this->container->get(Subscribers::class)->render();
  }

  public function lists() {
    $this->container->get(StaticSegments::class)->render();
  }

  public function segments() {
    $this->container->get(DynamicSegments::class)->render();
  }

  public function forms() {
    $this->container->get(Forms::class)->render();
  }

  public function newsletters() {
    $this->container->get(Newsletters::class)->render();
  }

  public function newletterEditor() {
    $this->container->get(NewsletterEditor::class)->render();
  }

  public function emailEditor() {
    $this->container->get(EmailEditorPage::class)->render();
  }

  public function import() {
    $this->container->get(SubscribersImport::class)->render();
  }

  public function export() {
    $this->container->get(SubscribersExport::class)->render();
  }

  public function formEditor() {
    $this->container->get(FormEditor::class)->render();
  }

  public function formEditorTemplateSelection() {
    $this->container->get(FormEditor::class)->renderTemplateSelection();
  }

  public function setPageTitle($title) {
    return sprintf(
      '%s - %s',
      __('MailPoet', 'mailpoet'),
      $title
    );
  }

  public function highlightNestedMailPoetSubmenus($parentFile) {
    global $plugin_page, $submenu, $submenu_file;

    $page = $this->getPageFromContext();
    if ($page) {
      $plugin_page = $page;
      return $parentFile;
    }

    if ($parentFile === self::MAIN_PAGE_SLUG || !self::isOnMailPoetAdminPage()) {
      return $parentFile;
    }

    // find slug of the current submenu item
    $parentSlug = null;
    foreach ($submenu as $groupSlug => $group) {
      foreach ($group as $item) {
        if (($item[2] ?? null) === $plugin_page) {
          $parentSlug = $groupSlug;
          break 2;
        }
      }
    }

    if ($parentSlug && $parentSlug !== self::NO_PARENT_PAGE_SLUG) {
      // highlight parent submenu item
      $plugin_page = $parentSlug;
    } else {
      // no parent, hide MailPoet submenu for setup, wizards, error pages, etc.
      unset($submenu[self::MAIN_PAGE_SLUG]);
      $plugin_page = self::MAIN_PAGE_SLUG;
    }
    return $parentFile;
  }

  public static function isOnMailPoetAutomationPage(): bool {
    $screenId = isset($_REQUEST['page']) ? sanitize_text_field(wp_unslash($_REQUEST['page'])) : '';
    $automationPages = [
        'mailpoet-automation',
        'mailpoet-automation-templates',
        'mailpoet-automation-editor',
      ];
    return in_array(
      $screenId,
      $automationPages,
      true
    );
  }

  public static function isOnMailPoetAdminPage(array $exclude = null, $screenId = null) {
    if (is_null($screenId)) {
      if (empty($_REQUEST['page'])) {
        return false;
      }
      $screenId = sanitize_text_field(wp_unslash($_REQUEST['page']));
    }
    if (!empty($exclude)) {
      foreach ($exclude as $slug) {
        if (stripos($screenId, $slug) !== false) {
          return false;
        }
      }
    }
    return (stripos($screenId, 'mailpoet-') !== false);
  }

  /**
   * This error page is used when the initialization is failed
   * to display admin notices only
   */
  public static function addErrorPage(AccessControl $accessControl) {
    if (!self::isOnMailPoetAdminPage() || !isset($_REQUEST['page'])) {
      return false;
    }

    $page = sanitize_text_field(wp_unslash($_REQUEST['page']));
    // Check if page already exists
    if (
      get_plugin_page_hook($page, '')
      || WPFunctions::get()->getPluginPageHook($page, self::MAIN_PAGE_SLUG)
    ) {
      return false;
    }
    WPFunctions::get()->addSubmenuPage(
      self::NO_PARENT_PAGE_SLUG,
      'MailPoet',
      'MailPoet',
      AccessControl::PERMISSION_ACCESS_PLUGIN_ADMIN,
      $page,
      [
        __CLASS__,
        'errorPageCallback',
      ]
    );
  }

  public static function errorPageCallback() {
    // Used for displaying admin notices only
  }

  public function checkPremiumKey(ServicesChecker $checker = null) {
    $showNotices = isset($_SERVER['SCRIPT_NAME'])
      && stripos(sanitize_text_field(wp_unslash($_SERVER['SCRIPT_NAME'])), 'plugins.php') !== false;
    $checker = $checker ?: $this->servicesChecker;
    $this->premiumKeyValid = $checker->isPremiumKeyValid($showNotices);
  }

  public function getPageFromContext(): ?string {
    $context = isset($_GET['context']) ? sanitize_text_field(wp_unslash($_GET['context'])) : null;
    if ($context === 'automation') {
      return self::AUTOMATIONS_PAGE_SLUG;
    }
    return null;
  }
}
