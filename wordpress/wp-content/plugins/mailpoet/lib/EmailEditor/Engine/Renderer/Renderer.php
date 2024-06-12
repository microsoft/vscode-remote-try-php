<?php declare(strict_types = 1);

namespace MailPoet\EmailEditor\Engine\Renderer;

if (!defined('ABSPATH')) exit;


use MailPoet\EmailEditor\Engine\Renderer\ContentRenderer\ContentRenderer;
use MailPoet\EmailEditor\Engine\Templates\Templates;
use MailPoet\EmailEditor\Engine\ThemeController;
use MailPoetVendor\Html2Text\Html2Text;
use MailPoetVendor\Pelago\Emogrifier\CssInliner;
use WP_Style_Engine;
use WP_Theme_JSON;

class Renderer {
  private ThemeController $themeController;
  private ContentRenderer $contentRenderer;
  private Templates $templates;
  /** @var WP_Theme_JSON|null */
  private static $theme = null;

  const TEMPLATE_FILE = 'template-canvas.php';
  const TEMPLATE_STYLES_FILE = 'template-canvas.css';

  public function __construct(
    ContentRenderer $contentRenderer,
    Templates $templates,
    ThemeController $themeController
  ) {
    $this->contentRenderer = $contentRenderer;
    $this->templates = $templates;
    $this->themeController = $themeController;
  }

  /**
   * During rendering, this stores the theme data for the template being rendered.
   */
  public static function getTheme() {
    return self::$theme;
  }

  public function render(\WP_Post $post, string $subject, string $preHeader, string $language, $metaRobots = ''): array {
    $templateId = 'mailpoet/mailpoet//' . (get_page_template_slug($post) ?: 'email-general');
    $template = $this->templates->getBlockTemplate($templateId);
    $theme = $this->templates->getBlockTemplateTheme($templateId, $template->wp_id); // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

    // Set the theme for the template. This is merged with base theme.json and core json before rendering.
    self::$theme = new WP_Theme_JSON($theme, 'default');

    $emailStyles = $this->themeController->getStyles($post, $template, true);
    $templateHtml = $this->contentRenderer->render($post, $template);

    ob_start();
    include self::TEMPLATE_FILE;
    $renderedTemplate = (string)ob_get_clean();

    $templateStyles =
    WP_Style_Engine::compile_css(
      [
            'background-color' => $emailStyles['color']['background'] ?? 'inherit',
            'color' => $emailStyles['color']['text'] ?? 'inherit',
            'padding-top' => $emailStyles['spacing']['padding']['top'] ?? '0px',
            'padding-bottom' => $emailStyles['spacing']['padding']['bottom'] ?? '0px',
            'font-family' => $emailStyles['typography']['fontFamily'] ?? 'inherit',
            'line-height' => $emailStyles['typography']['lineHeight'] ?? '1.5',
            'font-size' => $emailStyles['typography']['fontSize'] ?? 'inherit',
          ],
      'body, .email_layout_wrapper'
    );
    $templateStyles .= file_get_contents(dirname(__FILE__) . '/' . self::TEMPLATE_STYLES_FILE);
    $templateStyles = '<style>' . wp_strip_all_tags((string)apply_filters('mailpoet_email_renderer_styles', $templateStyles, $post)) . '</style>';
    $renderedTemplate = $this->inlineCSSStyles($templateStyles . $renderedTemplate);

    // This is a workaround to support link :hover in some clients. Ideally we would remove the ability to set :hover
    // however this is not possible using the color panel from Gutenberg.
    if (isset($emailStyles['elements']['link'][':hover']['color']['text'])) {
      $renderedTemplate = str_replace('<!-- Forced Styles -->', '<style>a:hover { color: ' . esc_attr($emailStyles['elements']['link'][':hover']['color']['text']) . ' !important; }</style>', $renderedTemplate);
    }

    return [
      'html' => $renderedTemplate,
      'text' => $this->renderTextVersion($renderedTemplate),
    ];
  }

  /**
   * @param string $template
   * @return string
   */
  private function inlineCSSStyles($template) {
    return CssInliner::fromHtml($template)->inlineCss()->render();
  }

  /**
   * @param string $template
   * @return string
   */
  private function renderTextVersion($template) {
    $template = (mb_detect_encoding($template, 'UTF-8', true)) ? $template : mb_convert_encoding($template, 'UTF-8', mb_list_encodings());
    return @Html2Text::convert($template);
  }
}
