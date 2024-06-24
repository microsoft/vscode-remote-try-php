<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Form\Block;

if (!defined('ABSPATH')) exit;


use MailPoet\WP\Functions as WPFunctions;

class Columns {
  /** @var WPFunctions */
  private $wp;

  public function __construct(
    WPFunctions $wp
  ) {
    $this->wp = $wp;
  }

  public function render(array $block, string $content): string {
    return "<div class='mailpoet_form_columns_container'><div {$this->getClass($block['params'] ?? [])}{$this->getStyles($block['params'] ?? [])}>$content</div></div>";
  }

  private function getStyles(array $params): string {
    $styles = [];
    if (!empty($params['text_color'])) {
      $styles[] = "color:{$params['text_color']};";
    }
    if (!empty($params['background_color'])) {
      $styles[] = "background-color:{$params['background_color']};";
    }
    if (!empty($params['gradient'])) {
      $styles[] = "background:{$params['gradient']};";
    }
    if (!empty($params['padding']) && is_array($params['padding'])) {
      $top = $params['padding']['top'] ?? 0;
      $right = $params['padding']['right'] ?? 0;
      $bottom = $params['padding']['bottom'] ?? 0;
      $left = $params['padding']['left'] ?? 0;
      $styles[] = "padding:{$top} {$right} {$bottom} {$left};";
    }
    if (count($styles)) {
      return ' style="' . $this->wp->escAttr(implode('', $styles)) . '"';
    }
    return '';
  }

  private function getClass(array $params): string {
    $classes = ['mailpoet_form_columns mailpoet_paragraph'];
    if (!empty($params['vertical_alignment'])) {
      $classes[] = "mailpoet_vertically_align_{$params['vertical_alignment']}";
    }
    if (!empty($params['background_color']) || !empty($params['gradient'])) {
      $classes[] = "mailpoet_column_with_background";
    }
    if (!empty($params['text_color'])) {
      $classes[] = "has-{$params['text_color']}-color";
    }
    // BC !isset for older forms that were saved without the flag
    if (!isset($params['is_stacked_on_mobile']) || $params['is_stacked_on_mobile'] === '1') {
      $classes[] = "mailpoet_stack_on_mobile";
    }
    if (!empty($params['class_name'])) {
      $classes[] = $params['class_name'];
    }
    $classes = implode(' ', $classes);
    return "class=\"{$this->wp->escAttr($classes)}\"";
  }
}
