<?php declare(strict_types = 1);

namespace MailPoet\EmailEditor\Integrations\Core\Renderer\Blocks;

if (!defined('ABSPATH')) exit;


use MailPoet\EmailEditor\Engine\SettingsController;
use MailPoet\EmailEditor\Integrations\Utils\DomDocumentHelper;

/**
 * Renders a button block.
 * @see https://www.activecampaign.com/blog/email-buttons
 * @see https://documentation.mjml.io/#mj-button
 */
class Button extends AbstractBlockRenderer {
  private function getWrapperStyles(array $blockStyles) {
    $properties = ['border', 'color', 'typography', 'spacing'];
    $styles = $this->getStylesFromBlock(array_intersect_key($blockStyles, array_flip($properties)));
    return (object)[
      'css' => $this->compileCss($styles['declarations'], ['word-break' => 'break-word', 'display' => 'block']),
      'classname' => $styles['classnames'],
    ];
  }

  private function getLinkStyles(array $blockStyles) {
    $styles = $this->getStylesFromBlock([
      'color' => [
        'text' => $blockStyles['color']['text'] ?? '',
      ],
      'typography' => $blockStyles['typography'] ?? [],
    ]);
    return (object)[
      'css' => $this->compileCss($styles['declarations'], ['display' => 'block']),
      'classname' => $styles['classnames'],
    ];
  }

  public function render(string $blockContent, array $parsedBlock, SettingsController $settingsController): string {
    return $this->renderContent($blockContent, $parsedBlock, $settingsController);
  }

  protected function renderContent($blockContent, array $parsedBlock, SettingsController $settingsController): string {
    if (empty($parsedBlock['innerHTML'])) {
      return '';
    }

    $domHelper = new DomDocumentHelper($parsedBlock['innerHTML']);
    $blockClassname = $domHelper->getAttributeValueByTagName('div', 'class') ?? '';
    $buttonLink = $domHelper->findElement('a');

    if (!$buttonLink) {
      return '';
    }

    $buttonText = $domHelper->getElementInnerHTML($buttonLink) ?: '';
    $buttonUrl = $buttonLink->getAttribute('href') ?: '#';

    $blockAttributes = wp_parse_args($parsedBlock['attrs'] ?? [], [
      'width' => '',
      'style' => [],
      'textAlign' => 'center',
      'backgroundColor' => '',
      'textColor' => '',
    ]);

    $blockStyles = array_replace_recursive(
      [
        'color' => array_filter([
          'background' => $blockAttributes['backgroundColor'] ? $settingsController->translateSlugToColor($blockAttributes['backgroundColor']) : null,
          'text' => $blockAttributes['textColor'] ? $settingsController->translateSlugToColor($blockAttributes['textColor']) : null,
        ]),
      ],
      $blockAttributes['style'] ?? []
    );

    if (!empty($blockStyles['border']) && empty($blockStyles['border']['style'])) {
      $blockStyles['border']['style'] = 'solid';
    }

    $wrapperStyles = $this->getWrapperStyles($blockStyles);
    $linkStyles = $this->getLinkStyles($blockStyles);

    return sprintf(
      '<table border="0" cellspacing="0" cellpadding="0" role="presentation" style="width:%1$s;">
        <tr>
          <td align="%2$s" valign="middle" role="presentation" class="%3$s" style="%4$s">
            <a class="button-link %5$s" style="%6$s" href="%7$s" target="_blank">%8$s</a>
          </td>
        </tr>
      </table>',
      esc_attr($blockAttributes['width'] ? '100%' : 'auto'),
      esc_attr($blockAttributes['textAlign']),
      esc_attr($wrapperStyles->classname . ' ' . $blockClassname),
      esc_attr($wrapperStyles->css),
      esc_attr($linkStyles->classname),
      esc_attr($linkStyles->css),
      esc_url($buttonUrl),
      $buttonText,
    );
  }
}
