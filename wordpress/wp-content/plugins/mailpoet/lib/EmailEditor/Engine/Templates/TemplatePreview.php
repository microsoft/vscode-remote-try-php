<?php declare(strict_types = 1);

namespace MailPoet\EmailEditor\Engine\Templates;

if (!defined('ABSPATH')) exit;


use MailPoet\EmailEditor\Engine\ThemeController;
use MailPoet\Validator\Builder;
use WP_Theme_JSON;

class TemplatePreview {
  private ThemeController $themeController;
  private Templates $templates;

  public function __construct(
    ThemeController $themeController,
    Templates $templates
  ) {
    $this->themeController = $themeController;
    $this->templates = $templates;
  }

  public function initialize(): void {
    register_rest_field(
      'wp_template',
      'email_theme_css',
      [
        'get_callback' => [$this, 'getEmailThemePreviewCss'],
        'update_callback' => null,
        'schema' => Builder::string()->toArray(),
      ]
    );
  }

  /**
   * Generates CSS for preview of email theme
   * They are applied in the preview BLockPreview in template selection
   */
  public function getEmailThemePreviewCss($template): string {
    $editorTheme = clone $this->themeController->getTheme();
    $templateTheme = $this->templates->getBlockTemplateTheme($template['id'], $template['wp_id']);
    if (is_array($templateTheme)) {
      $editorTheme->merge(new WP_Theme_JSON($templateTheme, 'custom'));
    }
    $additionalCSS = file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'preview.css');
    return $editorTheme->get_stylesheet() . $additionalCSS;
  }
}
