<?php declare(strict_types = 1);

namespace MailPoet\EmailEditor\Integrations\Core;

if (!defined('ABSPATH')) exit;


use MailPoet\EmailEditor\Engine\Renderer\ContentRenderer\BlocksRegistry;
use MailPoet\EmailEditor\Engine\Renderer\ContentRenderer\Layout\FlexLayoutRenderer;

class Initializer {
  public function initialize(): void {
    add_action('mailpoet_blocks_renderer_initialized', [$this, 'registerCoreBlocksRenderers'], 10, 1);
    add_filter('mailpoet_email_editor_theme_json', [$this, 'adjustThemeJson'], 10, 1);
    add_filter('safe_style_css', [$this, 'allowStyles']);
  }

  /**
   * Register core blocks email renderers when the blocks renderer is initialized.
   */
  public function registerCoreBlocksRenderers(BlocksRegistry $blocksRegistry): void {
    $blocksRegistry->addBlockRenderer('core/paragraph', new Renderer\Blocks\Text());
    $blocksRegistry->addBlockRenderer('core/heading', new Renderer\Blocks\Text());
    $blocksRegistry->addBlockRenderer('core/column', new Renderer\Blocks\Column());
    $blocksRegistry->addBlockRenderer('core/columns', new Renderer\Blocks\Columns());
    $blocksRegistry->addBlockRenderer('core/list', new Renderer\Blocks\ListBlock());
    $blocksRegistry->addBlockRenderer('core/image', new Renderer\Blocks\Image());
    $blocksRegistry->addBlockRenderer('core/buttons', new Renderer\Blocks\Buttons(new FlexLayoutRenderer()));
    $blocksRegistry->addBlockRenderer('core/button', new Renderer\Blocks\Button());
    $blocksRegistry->addBlockRenderer('core/group', new Renderer\Blocks\Group());
  }

  /**
   * Adjusts the editor's theme to add blocks specific settings for core blocks.
   */
  public function adjustThemeJson(\WP_Theme_JSON $editorThemeJson): \WP_Theme_JSON {
    $themeJson = (string)file_get_contents(dirname(__FILE__) . '/theme.json');
    $themeJson = json_decode($themeJson, true);
    /** @var array $themeJson */
    $editorThemeJson->merge(new \WP_Theme_JSON($themeJson, 'default'));
    return $editorThemeJson;
  }

  /**
   * Allow styles for the email editor.
   */
  public function allowStyles(array $allowedStyles): array {
    $allowedStyles[] = 'display';
    $allowedStyles[] = 'mso-padding-alt';
    $allowedStyles[] = 'mso-font-width';
    $allowedStyles[] = 'mso-text-raise';
    return $allowedStyles;
  }
}
