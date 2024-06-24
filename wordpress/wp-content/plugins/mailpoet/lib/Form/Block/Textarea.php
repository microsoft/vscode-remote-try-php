<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Form\Block;

if (!defined('ABSPATH')) exit;


use MailPoet\Form\BlockStylesRenderer;
use MailPoet\Form\BlockWrapperRenderer;
use MailPoet\WP\Functions as WPFunctions;

class Textarea {
  /** @var BlockRendererHelper */
  private $rendererHelper;

  /** @var BlockStylesRenderer */
  private $inputStylesRenderer;

   /** @var BlockWrapperRenderer */
  private $wrapper;

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    BlockRendererHelper $rendererHelper,
    BlockStylesRenderer $inputStylesRenderer,
    BlockWrapperRenderer $wrapper,
    WPFunctions $wp
  ) {
    $this->rendererHelper = $rendererHelper;
    $this->inputStylesRenderer = $inputStylesRenderer;
    $this->wrapper = $wrapper;
    $this->wp = $wp;
  }

  public function render(array $block, array $formSettings): string {
    $html = '';
    $name = $this->rendererHelper->getFieldName($block);
    $styles = $this->inputStylesRenderer->renderForTextInput($block['styles'] ?? [], $formSettings);

    $html .= $this->rendererHelper->renderLabel($block, $formSettings);

    $lines = (isset($block['params']['lines']) ? (int)$block['params']['lines'] : 1);
    $html .= $this->inputStylesRenderer->renderPlaceholderStyles($block, 'textarea[name="data[' . $name . ']"]');

    $html .= '<textarea class="mailpoet_textarea" data-automation-id="form_custom_text_area" rows="' . $lines . '" ';

    $html .= 'name="data[' . $name . ']"';

    $html .= $this->rendererHelper->renderInputPlaceholder($block);

    $html .= $this->rendererHelper->getInputValidation($block);

    $html .= $this->rendererHelper->getInputModifiers($block);

    if ($styles) {
      $html .= 'style="' . $this->wp->escAttr($styles) . '" ';
    }

    $html .= '>' . $this->rendererHelper->escapeShortCodes($this->rendererHelper->getFieldValue($block)) . '</textarea>';

    return $this->wrapper->render($block, $html);
  }
}
