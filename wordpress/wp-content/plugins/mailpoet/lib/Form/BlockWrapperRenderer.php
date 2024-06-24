<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Form;

if (!defined('ABSPATH')) exit;


use MailPoet\WP\Functions as WPFunctions;

class BlockWrapperRenderer {
  /** @var WPFunctions */
  private $wp;

  public function __construct(
    WPFunctions $wp
  ) {
    $this->wp = $wp;
  }

  public function render(array $block, string $blockContent): string {
    $classes = isset($block['params']['class_name']) ? " " . $block['params']['class_name'] : '';
    return '<div class="mailpoet_paragraph' . $this->wp->escAttr($classes) . '">' . $blockContent . '</div>';
  }
}
