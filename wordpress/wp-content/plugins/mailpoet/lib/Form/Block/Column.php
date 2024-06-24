<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Form\Block;

if (!defined('ABSPATH')) exit;


use MailPoet\WP\Functions as WPFunctions;

class Column {
  /** @var WPFunctions */
  private $wp;

  public function __construct(
    WPFunctions $wp
  ) {
    $this->wp = $wp;
  }

  public function render(array $block, string $content): string {
    return "<div {$this->getClass($block['params'])}{$this->getStyles($block['params'])}>$content</div>";
  }

  private function getStyles(array $params): string {
    $styles = [];
    if (
      !empty($params['width']) &&
      (strlen($params['width']) > 0 && ctype_digit(substr($params['width'], 0, 1)))
    ) {
      $widthValue = $params['width'] . (is_numeric($params['width']) ? '%' : '');
      $styles[] = "flex-basis:{$widthValue}";
    }
    if (!empty($params['padding']) && is_array($params['padding'])) {
      $top = $params['padding']['top'] ?? 0;
      $right = $params['padding']['right'] ?? 0;
      $bottom = $params['padding']['bottom'] ?? 0;
      $left = $params['padding']['left'] ?? 0;
      $styles[] = "padding:{$top} {$right} {$bottom} {$left};";
    }
    if (!empty($params['text_color'])) {
      $styles[] = "color:{$params['text_color']};";
    }
    if (!empty($params['background_color'])) {
      $styles[] = "background-color:{$params['background_color']};";
    }
    if (!empty($params['gradient'])) {
      $styles[] = "background:{$params['gradient']};";
    }
    if (!count($styles)) {
      return '';
    }
    return ' style="' . $this->wp->escAttr(implode(';', $styles)) . ';"';
  }

  private function getClass(array $params): string {
    $classes = ['mailpoet_form_column'];
    if (!empty($params['vertical_alignment'])) {
      $classes[] = "mailpoet_vertically_align_{$params['vertical_alignment']}";
    }
    if (!empty($params['class_name'])) {
      $classes[] = $params['class_name'];
    }
    $classes = implode(' ', $classes);
    return "class=\"{$this->wp->escAttr($classes)}\"";
  }
}
