<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Form\Block;

if (!defined('ABSPATH')) exit;


use MailPoet\Form\BlockStylesRenderer;
use MailPoet\Form\BlockWrapperRenderer;
use MailPoet\WP\Functions as WPFunctions;

class Submit {

  /** @var BlockRendererHelper */
  private $rendererHelper;

  /** @var BlockWrapperRenderer */
  private $wrapper;

  /** @var BlockStylesRenderer */
  private $stylesRenderer;

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    BlockRendererHelper $rendererHelper,
    BlockWrapperRenderer $wrapper,
    BlockStylesRenderer $stylesRenderer,
    WPFunctions $wp
  ) {
    $this->rendererHelper = $rendererHelper;
    $this->wrapper = $wrapper;
    $this->stylesRenderer = $stylesRenderer;
    $this->wp = $wp;
  }

  public function render(array $block, array $formSettings): string {
    $html = '';

    $html .= '<input type="submit" class="mailpoet_submit" ';

    $html .= 'value="' . $this->rendererHelper->getFieldLabel($block) . '" ';

    $html .= 'data-automation-id="subscribe-submit-button" ';

    if (isset($block['styles']['font_family'])) {
      $html .= "data-font-family='{$this->wp->escAttr($block['styles']['font_family'])}' " ;
    }

    $styles = $this->stylesRenderer->renderForButton($block['styles'] ?? [], $formSettings);

    if ($styles) {
      $html .= 'style="' . $this->wp->escAttr($styles) . '" ';
    }

    $html .= '/>';

    $html .= '<span class="mailpoet_form_loading"><span class="mailpoet_bounce1"></span><span class="mailpoet_bounce2"></span><span class="mailpoet_bounce3"></span></span>';

    return $this->wrapper->render($block, $html);
  }
}
