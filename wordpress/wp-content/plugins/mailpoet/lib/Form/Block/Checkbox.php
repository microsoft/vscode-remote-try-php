<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Form\Block;

if (!defined('ABSPATH')) exit;


use MailPoet\Form\BlockWrapperRenderer;
use MailPoet\Form\FormHtmlSanitizer;
use MailPoet\WP\Functions as WPFunctions;

class Checkbox {

  /** @var BlockRendererHelper */
  private $rendererHelper;

  /** @var BlockWrapperRenderer */
  private $wrapper;

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    BlockRendererHelper $rendererHelper,
    BlockWrapperRenderer $wrapper,
    WPFunctions $wp
  ) {
    $this->rendererHelper = $rendererHelper;
    $this->wrapper = $wrapper;
    $this->wp = $wp;
  }

  public function render(array $block, array $formSettings, ?int $formId = null): string {
    $html = '';

    $fieldName = 'data[' . $this->rendererHelper->getFieldName($block) . ']';
    $fieldValidation = $this->rendererHelper->getInputValidation($block, [], $formId);

    $html .= '<fieldset>';
    $html .= $this->rendererHelper->renderLegend($block, $formSettings);

    $options = (!empty($block['params']['values'])
      ? $block['params']['values']
      : []
    );

    $selectedValue = $this->rendererHelper->getFieldValue($block);
    $isFieldRequired = $this->rendererHelper->getFieldIsRequired($block);

    foreach ($options as $option) {
      $hiddenValue = $isFieldRequired ? '1' : '0'; // Mandatory Fields can not be Empty
      $html .= '<input type="hidden" value="' . $hiddenValue . '"  name="' . $fieldName . '" />';

      $html .= '<label class="mailpoet_checkbox_label" '
        . $this->rendererHelper->renderFontStyle($formSettings) . '>';

      $html .= '<input type="checkbox" class="mailpoet_checkbox" ';

      $html .= 'name="' . $fieldName . '" ';

      $html .= 'value="1" ';

      $html .= (
        (
          $selectedValue === ''
          && isset($option['is_checked'])
          && $option['is_checked']
        ) || ($selectedValue)
      ) ? 'checked="checked"' : '';

      $html .= $fieldValidation;

      $html .= ' /> ' . $this->wp->wpKses($option['value'], FormHtmlSanitizer::ALLOWED_HTML);

      $html .= '</label>';
    }

    $html .= '</fieldset>';

    $html .= '<span class="mailpoet_error_' . $this->wp->escAttr($block['id']) . ($formId ? '_' . $formId : '') . '"></span>';

    return $this->wrapper->render($block, $html);
  }
}
