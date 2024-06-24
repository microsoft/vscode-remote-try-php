<?php declare(strict_types = 1);

namespace MailPoet\EmailEditor\Integrations\Core\Renderer\Blocks;

if (!defined('ABSPATH')) exit;


use MailPoet\EmailEditor\Engine\SettingsController;
use MailPoet\EmailEditor\Integrations\Core\Renderer\Blocks\AbstractBlockRenderer;
use MailPoet\EmailEditor\Integrations\Utils\DomDocumentHelper;
use WP_Style_Engine;

class Columns extends AbstractBlockRenderer {
  protected function renderContent(string $blockContent, array $parsedBlock, SettingsController $settingsController): string {
    $content = '';
    foreach ($parsedBlock['innerBlocks'] ?? [] as $block) {
      $content .= render_block($block);
    }

    return str_replace(
      '{columns_content}',
      $content,
      $this->getBlockWrapper($blockContent, $parsedBlock, $settingsController)
    );
  }

  /**
   * Based on MJML <mj-section>
   */
  private function getBlockWrapper(string $blockContent, array $parsedBlock, SettingsController $settingsController): string {
    $originalWrapperClassname = (new DomDocumentHelper($blockContent))->getAttributeValueByTagName('div', 'class') ?? '';
    $block_attributes = wp_parse_args($parsedBlock['attrs'] ?? [], [
      'align' => null,
      'width' => $settingsController->getLayoutWidthWithoutPadding(),
      'style' => [],
    ]);

    $columnsStyles = $this->getStylesFromBlock([
      'spacing' => [ 'padding' => $block_attributes['style']['spacing']['padding'] ?? [] ],
      'color' => $block_attributes['style']['color'] ?? [],
      'background' => $block_attributes['style']['background'] ?? [],
    ])['declarations'];

    $borderStyles = $this->getStylesFromBlock(['border' => $block_attributes['style']['border'] ?? []])['declarations'];

    if (!empty($borderStyles)) {
      $columnsStyles = array_merge($columnsStyles, ['border-style' => 'solid'], $borderStyles);
    }

    if (empty($columnsStyles['background-size'])) {
      $columnsStyles['background-size'] = 'cover';
    }

    $renderedColumns = '<table class="' . esc_attr('email-block-columns ' . $originalWrapperClassname) . '" style="width:100%;border-collapse:separate;text-align:left;' . esc_attr(WP_Style_Engine::compile_css($columnsStyles, '')) . '" align="center" border="0" cellpadding="0" cellspacing="0" role="presentation">
      <tbody>
        <tr>{columns_content}</tr>
      </tbody>
    </table>';

    // Margins are not supported well in outlook for tables, so wrap in another table.
    $margins = $block_attributes['style']['spacing']['margin'] ?? [];

    if (!empty($margins)) {
      $marginToPaddingStyles = $this->getStylesFromBlock([
        'spacing' => [ 'margin' => $margins ],
      ])['css'];
      $renderedColumns = '<table class="email-block-columns-wrapper" style="width:100%;border-collapse:separate;text-align:left;' . esc_attr($marginToPaddingStyles) . '" align="center" border="0" cellpadding="0" cellspacing="0" role="presentation">
        <tbody>
          <tr>
            <td>' . $renderedColumns . '</td>
          </tr>
        </tbody>
      </table>';
    }

    return $renderedColumns;
  }
}
