<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Form\Block;

if (!defined('ABSPATH')) exit;


class Html {
  /** @var BlockRendererHelper */
  private $rendererHelper;

  public function __construct(
    BlockRendererHelper $rendererHelper
  ) {
    $this->rendererHelper = $rendererHelper;
  }

  public function render(array $block, array $formSettings): string {
    $html = '';
    $text = '';

    if (isset($block['params']['text']) && $block['params']['text']) {
      $text = html_entity_decode($block['params']['text'], ENT_QUOTES);
    }

    if (isset($block['params']['nl2br']) && $block['params']['nl2br']) {
      $text = nl2br($text);
    }

    $classes = isset($block['params']['class_name']) ? " " . $block['params']['class_name'] : '';
    $html .= '<div class="mailpoet_paragraph' . $classes . '" ' . $this->rendererHelper->renderFontStyle($formSettings) . '>';
    $html .= $text;
    $html .= '</div>';

    return $html;
  }
}
