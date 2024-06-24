<?php declare(strict_types = 1);

namespace MailPoet\EmailEditor\Engine\Renderer\ContentRenderer;

if (!defined('ABSPATH')) exit;


use MailPoet\EmailEditor\Engine\SettingsController;
use MailPoet\EmailEditor\Engine\ThemeController;
use MailPoetVendor\Pelago\Emogrifier\CssInliner;
use WP_Block_Template;
use WP_Post;

class ContentRenderer {
  private BlocksRegistry $blocksRegistry;
  private ProcessManager $processManager;
  private SettingsController $settingsController;
  private ThemeController $themeController;
  private $post = null;
  private $template = null;

  const CONTENT_STYLES_FILE = 'content.css';

  public function __construct(
    ProcessManager $preprocessManager,
    BlocksRegistry $blocksRegistry,
    SettingsController $settingsController,
    ThemeController $themeController
  ) {
    $this->processManager = $preprocessManager;
    $this->blocksRegistry = $blocksRegistry;
    $this->settingsController = $settingsController;
    $this->themeController = $themeController;
  }

  private function initialize() {
    add_filter('render_block', [$this, 'renderBlock'], 10, 2);
    add_filter('block_parser_class', [$this, 'blockParser']);
    add_filter('mailpoet_blocks_renderer_parsed_blocks', [$this, 'preprocessParsedBlocks']);

    do_action('mailpoet_blocks_renderer_initialized', $this->blocksRegistry);
  }

  public function render(WP_Post $post, WP_Block_Template $template): string {
    $this->post = $post;
    $this->template = $template;
    $this->setTemplateGlobals($post, $template);
    $this->initialize();
    $renderedHtml = get_the_block_template_html();
    $this->reset();

    return $this->processManager->postprocess($this->inlineStyles($renderedHtml, $post, $template));
  }

  public function blockParser() {
    return 'MailPoet\EmailEditor\Engine\Renderer\ContentRenderer\BlocksParser';
  }

  public function preprocessParsedBlocks(array $parsedBlocks): array {
    return $this->processManager->preprocess($parsedBlocks, $this->settingsController->getLayout(), $this->themeController->getStyles($this->post, $this->template));
  }

  public function renderBlock($blockContent, $parsedBlock) {
    if (!$this->blocksRegistry->hasBlockRenderer($parsedBlock['blockName'])) {
      return $blockContent;
    }
    $renderer = $this->blocksRegistry->getBlockRenderer($parsedBlock['blockName']);
    return $renderer ? $renderer->render($blockContent, $parsedBlock, $this->settingsController) : $blockContent;
  }

  private function setTemplateGlobals(WP_Post $post, WP_Block_Template $template) {
    global $_wp_current_template_content, $_wp_current_template_id;
    $_wp_current_template_id = $template->id;
    $_wp_current_template_content = $template->content;
    $GLOBALS['post'] = $post;
  }

  /**
  * As we use default WordPress filters, we need to remove them after email rendering
  * so that we don't interfere with possible post rendering that might happen later.
  */
  private function reset() {
    $this->blocksRegistry->removeAllBlockRenderers();
    remove_filter('render_block', [$this, 'renderBlock']);
    remove_filter('block_parser_class', [$this, 'blockParser']);
    remove_filter('mailpoet_blocks_renderer_parsed_blocks', [$this, 'preprocessParsedBlocks']);
  }

  /**
   * @param string $html
   * @return string
   */
  private function inlineStyles($html, WP_Post $post, $template = null) {
    $styles = (string)file_get_contents(dirname(__FILE__) . '/' . self::CONTENT_STYLES_FILE);

    // Apply default contentWidth to constrained blocks.
    $layout = $this->settingsController->getLayout();
    $styles .= sprintf(
      '
      .is-layout-constrained > *:not(.alignleft):not(.alignright):not(.alignfull) {
        max-width: %1$s;
        margin-left: auto !important;
        margin-right: auto !important;
      }
      .is-layout-constrained > .alignwide {
        max-width: %2$s;
        margin-left: auto !important;
        margin-right: auto !important;
      }
      ',
      $layout['contentSize'],
      $layout['wideSize']
    );

    // Get styles from theme.
    $styles .= $this->themeController->getStylesheetForRendering($post, $template);
    $blockSupportStyles = $this->themeController->getStylesheetFromContext('block-supports', []);
    // Get styles from block-supports stylesheet. This includes rules such as layout (contentWidth) that some blocks use.
    // @see https://github.com/WordPress/WordPress/blob/3c5da9c74344aaf5bf8097f2e2c6a1a781600e03/wp-includes/script-loader.php#L3134
    // @internal :where is not supported by emogrifier, so we need to replace it with *.
    $blockSupportStyles = str_replace(
      ':where(:not(.alignleft):not(.alignright):not(.alignfull))',
      '*:not(.alignleft):not(.alignright):not(.alignfull)',
      $blockSupportStyles
    );
    // Layout CSS assumes the top level block will have a single DIV wrapper with children. Since our blocks use tables,
    // we need to adjust this to look for children in the TD element. This may requires more advanced replacement but
    // this works in the current version of Gutenberg.
    // Example rule we're targetting: .wp-container-core-group-is-layout-1.wp-container-core-group-is-layout-1 > *
    $blockSupportStyles = preg_replace(
      '/group-is-layout-(\d+) >/',
      'group-is-layout-$1 > tbody tr td >',
      $blockSupportStyles
    );

    $styles .= $blockSupportStyles;

    // Debugging for content styles. Remember these get inlined.
    //echo '<pre>';
    //var_dump($styles);
    //echo '</pre>';

    $styles = '<style>' . wp_strip_all_tags((string)apply_filters('mailpoet_email_content_renderer_styles', $styles, $post)) . '</style>';

    return CssInliner::fromHtml($styles . $html)->inlineCss()->render();
  }
}
