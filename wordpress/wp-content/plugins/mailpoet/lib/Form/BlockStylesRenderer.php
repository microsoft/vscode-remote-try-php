<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Form;

if (!defined('ABSPATH')) exit;


use MailPoet\WP\Functions as WPFunctions;

class BlockStylesRenderer {
  /** @var WPFunctions */
  private $wp;

  public function __construct(
    WPFunctions $wp
  ) {
    $this->wp = $wp;
  }

  public function renderForTextInput(array $styles, array $formSettings = []): string {
    $rules = [];
    if (isset($styles['full_width']) && intval($styles['full_width'])) {
      $rules[] = 'width:100%;';
      $rules[] = 'box-sizing:border-box;'; // to avoid a larger width increased by padding
    }
    if (isset($styles['background_color']) && empty($styles['gradient'])) {
      $rules[] = "background-color:{$styles['background_color']};";
    }
    if (isset($styles['border_size']) || isset($styles['border_radius']) || isset($styles['border_color'])) {
      $rules[] = "border-style:solid;";
    }
    if (isset($styles['border_radius'])) {
      $rules[] = "border-radius:" . intval($styles['border_radius']) . "px !important;";
    }
    if (isset($styles['border_size'])) {
      $rules[] = "border-width:" . intval($styles['border_size']) . "px;";
    }
    if (isset($styles['border_color'])) {
      $rules[] = "border-color:{$styles['border_color']};";
    }
    if (isset($styles['padding'])) {
      $rules[] = "padding:{$styles['padding']}px;";
    } elseif (isset($formSettings['input_padding'])) {
      $rules[] = "padding:{$formSettings['input_padding']}px;";
    }
    if (isset($formSettings['alignment'])) {
      $rules[] = $this->convertAlignmentToMargin($formSettings['alignment']);
    }
    if (isset($styles['font_family'])) {
      $rules[] = "font-family:'{$styles['font_family']}';" ;
    } elseif (isset($formSettings['font_family'])) {
      $rules[] = "font-family:'{$formSettings['font_family']}';" ;
    }
    if (isset($styles['font_size'])) {
      $rules[] = "font-size:" . $styles['font_size'] . (is_numeric($styles['font_size']) ? "px;" : ";");
    }
    if (isset($formSettings['fontSize']) && !isset($styles['font_size'])) {
      $rules[] = "font-size:" . $formSettings['fontSize'] . (is_numeric($formSettings['fontSize']) ? "px;" : ";");
    }
    if (isset($formSettings['fontSize']) || isset($styles['font_size'])) {
      $rules[] = "line-height:1.5;";
      $rules[] = "height:auto;";
    }
    if (isset($styles['font_color'])) {
      $rules[] = "color:{$styles['font_color']};";
    }
    return implode('', $rules);
  }

  public function renderForButton(array $styles, array $formSettings = []): string {
    $rules = [];
    if (!isset($styles['border_color'])) {
      $rules[] = "border-color:transparent;";
    }
    if (!empty($styles['gradient'])) {
      $rules[] = "background: {$styles['gradient']};";
    }
    if (isset($styles['bold']) && $styles['bold'] === '1') {
      $rules[] = "font-weight:bold;";
    }
    return $this->renderForTextInput($styles, $formSettings) . implode('', $rules);
  }

  public function renderForSelect(array $styles, array $formSettings = []): string {
    $rules = [];
    if (isset($formSettings['input_padding'])) {
      $rules[] = "padding:{$formSettings['input_padding']}px;";
    }
    if (isset($formSettings['alignment'])) {
      $rules[] = $this->convertAlignmentToMargin($formSettings['alignment']);
    }
    return implode('', $rules);
  }

  private function convertAlignmentToMargin(string $alignment): string {
    if ($alignment === 'right') {
      return 'margin: 0 0 0 auto;';
    }
    if ($alignment === 'center') {
      return 'margin: 0 auto;';
    }
    return 'margin: 0 auto 0 0;';
  }

  public function renderPlaceholderStyles(array $block, string $selector): string {
    if (
      isset($block['params']['label_within'])
      && $block['params']['label_within']
      && isset($block['styles']['font_color'])
    ) {
      return '<style>'
        . $selector . '::placeholder{'
        . 'color:' . $this->wp->escAttr($block['styles']['font_color']) . ';'
        . 'opacity: 1;'
        . '}'
        . '</style>';
    }
    return '';
  }
}
