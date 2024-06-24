<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Form\Block;

if (!defined('ABSPATH')) exit;


use MailPoet\Form\BlockStylesRenderer;
use MailPoet\Form\BlockWrapperRenderer;
use MailPoet\WP\Functions as WPFunctions;

class Text {
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
    $type = 'text';
    $automationId = ' ';
    $id = '';
    if ($block['id'] === 'email') {
      $type = 'email';
      $autocomplete = 'email';
    } else if ($block['id'] === 'first_name') {
      $autocomplete = 'given-name';
    } else if ($block['id'] === 'last_name') {
      $autocomplete = 'family-name';
    } else {
      $autocomplete = 'on';
    }

    if (in_array($block['id'], ['email', 'last_name', 'first_name'], true)) {
      $automationId = 'data-automation-id="form_' . $this->wp->escAttr($block['id']) . '" ';
    }

    if (isset($formSettings['id'])) {
      $id = 'id="form_' . $this->wp->escAttr($block['id']) . '_' . $this->wp->escAttr($formSettings['id']) . '" ';
    }

    $styles = $this->inputStylesRenderer->renderForTextInput($block['styles'] ?? [], $formSettings);

    $name = $this->rendererHelper->getFieldName($block);

    $html = $this->inputStylesRenderer->renderPlaceholderStyles($block, 'input[name="data[' . $name . ']"]');

    $html .= $this->rendererHelper->renderLabel($block, $formSettings);

    $html .= '<input type="' . $type . '" autocomplete="' . $autocomplete . '" class="mailpoet_text" ';

    $html .= $id;

    $html .= 'name="data[' . $name . ']" ';

    $html .= 'title="' . $this->rendererHelper->getFieldLabel($block) . '" ';

    $html .= 'value="' . $this->rendererHelper->getFieldValue($block) . '" ';

    if ($styles) {
      $html .= 'style="' . $this->wp->escAttr($styles) . '" ';
    }

    $html .= $automationId;

    $html .= $this->rendererHelper->renderInputPlaceholder($block);

    $html .= $this->rendererHelper->getInputValidation($block);

    $html .= $this->rendererHelper->getInputModifiers($block);

    $html .= '/>';

    return $this->wrapper->render($block, $html);
  }
}
