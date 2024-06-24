<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Form\Block;

if (!defined('ABSPATH')) exit;


use MailPoet\WP\Functions as WPFunctions;

class Paragraph {
  /** @var WPFunctions */
  private $wp;

  public function __construct(
    WPFunctions $wp
  ) {
    $this->wp = $wp;
  }

  public function render(array $block): string {
    $content = ($block['params']['content'] ?? '');
    return $this->wrapContent($content, $block);
  }

  private function wrapContent(string $content, array $block): string {
    $attributes = $this->renderAttributes($block);
    $openTag = $this->getOpenTag($attributes);
    return $openTag
      . $content
      . "</p>";
  }

  private function getOpenTag(array $attributes): string {
    if (empty($attributes)) {
      return "<p>";
    }
    return "<p " . join(' ', $attributes) . ">";
  }

  private function renderAttributes(array $block): array {
    $result = [];
    $result[] = $this->renderClass($block);
    $result[] = $this->renderStyle($block);
    $result = array_filter($result, function ($attribute) {
      return $attribute !== null;
    });
    return $result;
  }

  private function renderClass(array $block) {
    $classes = ['mailpoet_form_paragraph'];
    if (isset($block['params']['class_name'])) {
      $classes[] = $block['params']['class_name'];
    }
    if (isset($block['params']['drop_cap']) && $block['params']['drop_cap'] === '1') {
      $classes[] = 'has-drop-cap';
    }
    if (!empty($block['params']['background_color']) || !empty($block['params']['gradient'])) {
      $classes[] = 'mailpoet-has-background-color';
    }
    if (!empty($block['params']['font_size'])) {
      $classes[] = 'mailpoet-has-font-size';
    }
    if (empty($classes)) {
      return null;
    }
    return 'class="'
    . $this->wp->escAttr(join(' ', $classes))
    . '"';
  }

  private function renderStyle(array $block) {
    $styles = [];
    if (!empty($block['params']['background_color'])) {
      $styles[] = 'background-color: ' . $block['params']['background_color'];
    }
    if (!empty($block['params']['gradient'])) {
      $styles[] = "background: {$block['params']['gradient']};";
    }
    if (!empty($block['params']['align'])) {
      $styles[] = 'text-align: ' . $block['params']['align'];
    }
    if (!empty($block['params']['text_color'])) {
      $styles[] = 'color: ' . $block['params']['text_color'];
    }
    if (!empty($block['params']['font_size'])) {
      $styles[] = 'font-size: ' . $block['params']['font_size'] . (is_numeric($block['params']['font_size']) ? 'px' : '');
    }
    if (!empty($block['params']['line_height'])) {
      $styles[] = 'line-height: ' . $block['params']['line_height'];
    }
    if (!empty($block['params']['padding']) && is_array($block['params']['padding'])) {
      $top = $block['params']['padding']['top'] ?? 0;
      $right = $block['params']['padding']['right'] ?? 0;
      $bottom = $block['params']['padding']['bottom'] ?? 0;
      $left = $block['params']['padding']['left'] ?? 0;
      $styles[] = "padding:{$top} {$right} {$bottom} {$left};";
    }
    if (empty($styles)) {
      return null;
    }
    return 'style="'
      . $this->wp->escAttr(join('; ', $styles))
      . '"';
  }
}
