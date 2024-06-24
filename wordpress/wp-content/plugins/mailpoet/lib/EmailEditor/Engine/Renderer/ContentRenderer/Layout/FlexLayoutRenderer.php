<?php declare(strict_types = 1);

namespace MailPoet\EmailEditor\Engine\Renderer\ContentRenderer\Layout;

if (!defined('ABSPATH')) exit;


use MailPoet\EmailEditor\Engine\SettingsController;

/**
 * This class provides functionality to render inner blocks of a block that supports reduced flex layout.
 */
class FlexLayoutRenderer {
  public function renderInnerBlocksInLayout(array $parsedBlock, SettingsController $settingsController): string {
    $themeStyles = $settingsController->getEmailStyles();
    $flexGap = $themeStyles['spacing']['blockGap'] ?? '0px';
    $flexGapNumber = $settingsController->parseNumberFromStringWithPixels($flexGap);

    $marginTop = $parsedBlock['email_attrs']['margin-top'] ?? '0px';
    $justify = $parsedBlock['attrs']['layout']['justifyContent'] ?? 'left';
    $styles = wp_style_engine_get_styles($parsedBlock['attrs']['style'] ?? [])['css'] ?? '';
    $styles .= 'margin-top: ' . $marginTop . ';';
    $styles .= 'text-align: ' . $justify;

    // MS Outlook doesn't support style attribute in divs so we conditionally wrap the buttons in a table and repeat styles
    $outputHtml = sprintf(
      '<!--[if mso | IE]><table align="%2$s" role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%%"><tr><td style="%1$s" ><![endif]-->
      <div style="%1$s"><table class="layout-flex-wrapper" style="display:inline-block"><tbody><tr>',
      esc_attr($styles),
      esc_attr($justify)
    );

    $innerBlocks = $this->computeWidthsForFlexLayout($parsedBlock, $settingsController, $flexGapNumber);

    foreach ($innerBlocks as $key => $block) {
      $styles = [];
      if ($block['email_attrs']['layout_width'] ?? null) {
        $styles['width'] = $block['email_attrs']['layout_width'];
      }
      if ($key > 0) {
        $styles['padding-left'] = $flexGap;
      }
      $outputHtml .= '<td class="layout-flex-item" style="' . esc_attr(\WP_Style_Engine::compile_css($styles, '')) . '">' . render_block($block) . '</td>';
    }
    $outputHtml .= '</tr></table></div>
    <!--[if mso | IE]></td></tr></table><![endif]-->';

    return $outputHtml;
  }

  private function computeWidthsForFlexLayout(array $parsedBlock, SettingsController $settingsController, float $flexGap): array {
    $blocksCount = count($parsedBlock['innerBlocks']);
    $totalUsedWidth = 0; // Total width assuming items without set width would consume proportional width
    $parentWidth = $settingsController->parseNumberFromStringWithPixels($parsedBlock['email_attrs']['width'] ?? SettingsController::EMAIL_WIDTH);
    $innerBlocks = $parsedBlock['innerBlocks'] ?? [];

    foreach ($innerBlocks as $key => $block) {
      $blockWidthPercent = ($block['attrs']['width'] ?? 0) ? intval($block['attrs']['width']) : 0;
      $blockWidth = floor($parentWidth * ($blockWidthPercent / 100));
      // If width is not set, we assume it's 25% of the parent width
      $totalUsedWidth += $blockWidth ?: floor($parentWidth * (25 / 100));

      if (!$blockWidth) {
        $innerBlocks[$key]['email_attrs']['layout_width'] = null; // Will be rendered as auto
        continue;
      }
      $innerBlocks[$key]['email_attrs']['layout_width'] = $this->getWidthWithoutGap($blockWidth, $flexGap, $blockWidthPercent) . 'px';
    }

    // When there is only one block, or percentage is set reasonably we don't need to adjust and just render as set by user
    if ($blocksCount <= 1 || ($totalUsedWidth <= $parentWidth)) {
      return $innerBlocks;
    }

    foreach ($innerBlocks as $key => $block) {
      $proportionalSpaceOverflow = $parentWidth / $totalUsedWidth;
      $blockWidth = $block['email_attrs']['layout_width'] ? $settingsController->parseNumberFromStringWithPixels($block['email_attrs']['layout_width']) : 0;
      $blockProportionalWidth = $blockWidth * $proportionalSpaceOverflow;
      $blockProportionalPercentage = ($blockProportionalWidth / $parentWidth) * 100;
      $innerBlocks[$key]['email_attrs']['layout_width'] = $blockWidth ? $this->getWidthWithoutGap($blockProportionalWidth, $flexGap, $blockProportionalPercentage) . 'px' : null;
    }
    return $innerBlocks;
  }

  /**
   * How much of width we will strip to keep some space for the gap
   * This is computed based on CSS rule used in the editor:
   * For block with width set to X percent
   * width: calc(X% - (var(--wp--style--block-gap) * (100 - X)/100)));
   */
  private function getWidthWithoutGap(float $blockWidth, float $flexGap, float $blockWidthPercent): int {
    $widthGapReduction = $flexGap * ((100 - $blockWidthPercent) / 100);
    return intval(floor($blockWidth - $widthGapReduction));
  }
}
