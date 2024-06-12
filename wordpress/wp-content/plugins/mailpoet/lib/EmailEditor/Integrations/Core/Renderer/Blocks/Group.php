<?php declare(strict_types = 1);

namespace MailPoet\EmailEditor\Integrations\Core\Renderer\Blocks;

if (!defined('ABSPATH')) exit;


use MailPoet\EmailEditor\Engine\SettingsController;
use MailPoet\EmailEditor\Integrations\Core\Renderer\Blocks\AbstractBlockRenderer;
use MailPoet\EmailEditor\Integrations\Utils\DomDocumentHelper;
use WP_Style_Engine;

class Group extends AbstractBlockRenderer {
  protected function renderContent(string $blockContent, array $parsedBlock, SettingsController $settingsController): string {
    $content = '';
    $innerBlocks = $parsedBlock['innerBlocks'] ?? [];

    foreach ($innerBlocks as $block) {
      $content .= render_block($block);
    }

    return str_replace(
      '{group_content}',
      $content,
      $this->getBlockWrapper($blockContent, $parsedBlock, $settingsController)
    );
  }

  private function getBlockWrapper(string $blockContent, array $parsedBlock, SettingsController $settingsController): string {
    $originalClassname = (new DomDocumentHelper($blockContent))->getAttributeValueByTagName('div', 'class') ?? '';
    $blockAttributes = wp_parse_args($parsedBlock['attrs'] ?? [], [
      'style' => [],
      'backgroundColor' => '',
      'textColor' => '',
      'borderColor' => '',
      'layout' => [],
    ]);

    // Layout, background, borders need to be on the outer table element.
    $tableStyles = $this->getStylesFromBlock([
      'color' => array_filter([
        'background' => $blockAttributes['backgroundColor'] ? $settingsController->translateSlugToColor($blockAttributes['backgroundColor']) : null,
        'text' => $blockAttributes['textColor'] ? $settingsController->translateSlugToColor($blockAttributes['textColor']) : null,
        'border' => $blockAttributes['borderColor'] ? $settingsController->translateSlugToColor($blockAttributes['borderColor']) : null,
      ]),
      'background' => $blockAttributes['style']['background'] ?? [],
      'border' => $blockAttributes['style']['border'] ?? [],
      'spacing' => [ 'padding' => $blockAttributes['style']['spacing']['margin'] ?? [] ],
    ])['declarations'];

    // Padding properties need to be added to the table cell.
    $cellStyles = $this->getStylesFromBlock([
      'spacing' => [ 'padding' => $blockAttributes['style']['spacing']['padding'] ?? [] ],
    ])['declarations'];

    $tableStyles['background-size'] = empty($tableStyles['background-size']) ? 'cover' : $tableStyles['background-size'];
    $justifyContent = $blockAttributes['layout']['justifyContent'] ?? 'center';
    $width = $parsedBlock['email_attrs']['width'] ?? '100%';

    return sprintf(
      '<table class="email-block-group %3$s" style="%1$s" width="100%%" align="center" border="0" cellpadding="0" cellspacing="0" role="presentation">
        <tbody>
          <tr>
            <td class="email-block-group-content" style="%2$s" align="%4$s" width="%5$s">
              {group_content}
            </td>
          </tr>
        </tbody>
      </table>',
      esc_attr(WP_Style_Engine::compile_css($tableStyles, '')),
      esc_attr(WP_Style_Engine::compile_css($cellStyles, '')),
      esc_attr($originalClassname),
      esc_attr($justifyContent),
      esc_attr($width),
    );
  }
}
