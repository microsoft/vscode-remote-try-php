<?php declare(strict_types = 1);

namespace MailPoet\EmailEditor\Integrations\MailPoet;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\Menu;
use MailPoet\Features\FeaturesController;
use MailPoet\WP\Functions as WPFunctions;

class EmailEditor {
  const MAILPOET_EMAIL_POST_TYPE = 'mailpoet_email';

  private WPFunctions $wp;

  private FeaturesController $featuresController;

  private EmailApiController $emailApiController;

  private Cli $cli;

  public function __construct(
    WPFunctions $wp,
    FeaturesController $featuresController,
    EmailApiController $emailApiController,
    Cli $cli
  ) {
    $this->wp = $wp;
    $this->featuresController = $featuresController;
    $this->emailApiController = $emailApiController;
    $this->cli = $cli;
  }

  public function initialize(): void {
    if (!$this->featuresController->isSupported(FeaturesController::GUTENBERG_EMAIL_EDITOR)) {
      return;
    }
    $this->cli->initialize();
    $this->wp->addFilter('mailpoet_email_editor_post_types', [$this, 'addEmailPostType']);
    $this->wp->addAction('rest_delete_mailpoet_email', [$this->emailApiController, 'trashEmail'], 10, 1);
    $this->wp->addFilter('mailpoet_is_email_editor_page', [$this, 'isEditorPage'], 10, 1);
    $this->extendEmailPostApi();
  }

  public function addEmailPostType(array $postTypes): array {
    $postTypes[] = [
      'name' => self::MAILPOET_EMAIL_POST_TYPE,
      'args' => [
        'labels' => [
          'name' => __('Emails', 'mailpoet'),
          'singular_name' => __('Email', 'mailpoet'),
        ],
        'rewrite' => ['slug' => self::MAILPOET_EMAIL_POST_TYPE],
      ],
    ];
    return $postTypes;
  }

  public function isEditorPage(bool $isEditorPage): bool {
    return $isEditorPage || (isset($_GET['page']) && $_GET['page'] === Menu::EMAIL_EDITOR_V2_PAGE_SLUG);
  }

  public function extendEmailPostApi() {
    $this->wp->registerRestField(self::MAILPOET_EMAIL_POST_TYPE, 'mailpoet_data', [
      'get_callback' => [$this->emailApiController, 'getEmailData'],
      'update_callback' => [$this->emailApiController, 'saveEmailData'],
      'schema' => $this->emailApiController->getEmailDataSchema(),
    ]);
  }
}
