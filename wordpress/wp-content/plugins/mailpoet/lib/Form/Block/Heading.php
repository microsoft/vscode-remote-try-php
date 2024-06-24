<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Form\Block;

if (!defined('ABSPATH')) exit;


use MailPoet\WP\Functions as WPFunctions;

class Heading {
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
    $tag = $this->renderTag($block);
    $attributes = $this->renderAttributes($block);
    $openTag = $this->getOpenTag($tag, $attributes);
    return $openTag
      . $content
      . "</$tag>";
  }

  private function renderTag(array $block): string {
    $tag = 'h2';
    if (isset($block['params']['level'])) {
      $tag = 'h' . $block['params']['level'];
    }
    return $tag;
  }

  private function renderAttributes(array $block): array {
    $result = [];
    $classes = $this->renderClass($block);
    if ($classes) {
      $result[] = $classes;
    }
    if (!empty($block['params']['anchor'])) {
      $result[] = $this->renderAnchor($block);
    }
    $styles = $this->renderStyle($block);
    if ($styles) {
      $result[] = $styles;
    }
    return $result;
  }

  private function getOpenTag(string $tag, array $attributes): string {
    if (empty($attributes)) {
      return "<$tag>";
    }
    return "<$tag " . join(' ', $attributes) . ">";
  }

  private function renderClass(array $block): string {
    $classes = ['mailpoet-heading'];
    if (isset($block['params']['class_name'])) {
      $classes[] = $block['params']['class_name'];
    }

    if (!empty($block['params']['background_color']) || !empty($block['params']['gradient'])) {
      $classes[] = 'mailpoet-has-background-color';
    }

    if (!empty($block['params']['font_size'])) {
      $classes[] = 'mailpoet-has-font-size';
    }

    return 'class="'
      . $this->wp->escAttr(join(' ', $classes))
      . '"';
  }

  private function renderAnchor(array $block): string {
    return 'id="'
      . $block['params']['anchor']
      . '"';
  }

  private function renderStyle(array $block): string {
    $styles = [];
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
    if (!empty($block['params']['background_color'])) {
      $styles[] = 'background-color: ' . $block['params']['background_color'];
    }
    if (!empty($block['params']['gradient'])) {
      $styles[] = "background: {$block['params']['gradient']};";
    }
    if (!empty($block['params']['padding']) && is_array($block['params']['padding'])) {
      $top = $block['params']['padding']['top'] ?? 0;
      $right = $block['params']['padding']['right'] ?? 0;
      $bottom = $block['params']['padding']['bottom'] ?? 0;
      $left = $block['params']['padding']['left'] ?? 0;
      $styles[] = "padding:{$top} {$right} {$bottom} {$left};";
    }
    if (empty($styles)) {
      return '';
    }
    return 'style="'
      . $this->wp->escAttr(join('; ', $styles))
      . '"';
  }
}
