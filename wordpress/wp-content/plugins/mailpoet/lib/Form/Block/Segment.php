<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Form\Block;

if (!defined('ABSPATH')) exit;


use MailPoet\Form\BlockWrapperRenderer;
use MailPoet\Segments\SegmentsRepository;
use MailPoet\WP\Functions as WPFunctions;

class Segment {

  /** @var BlockRendererHelper */
  private $rendererHelper;

  /** @var WPFunctions */
  private $wp;

  /** @var BlockWrapperRenderer */
  private $wrapper;

  /** @var SegmentsRepository */
  private $segmentsRepository;

  public function __construct(
    BlockRendererHelper $rendererHelper,
    BlockWrapperRenderer $wrapper,
    WPFunctions $wp,
    SegmentsRepository $segmentsRepository
  ) {
    $this->rendererHelper = $rendererHelper;
    $this->wrapper = $wrapper;
    $this->wp = $wp;
    $this->segmentsRepository = $segmentsRepository;
  }

  public function render(array $block, array $formSettings, ?int $formId = null): string {
    $html = '';

    $fieldName = 'data[' . $this->rendererHelper->getFieldName($block) . ']';
    $fieldValidation = $this->rendererHelper->getInputValidation($block, [], $formId);

    // Add fieldset around the checkboxes
    $html .= '<fieldset>';
    $html .= $this->rendererHelper->renderLegend($block, $formSettings);

    $options = (!empty($block['params']['values'])
      ? $block['params']['values']
      : []
    );

    $options = array_map(function ($option) {
      $option['id'] = intval($option['id']);
      return $option;
    }, $options);
    $segmentsNamesMap = $this->getSegmentsNames($options);

    foreach ($options as $option) {
      if (!isset($option['id']) || !isset($segmentsNamesMap[$option['id']])) continue;

      $isChecked = (isset($option['is_checked']) && $option['is_checked']) ? 'checked="checked"' : '';

      $html .= '<label class="mailpoet_checkbox_label" '
        . $this->rendererHelper->renderFontStyle($formSettings)
        . '>';
      $html .= '<input type="checkbox" class="mailpoet_checkbox" ';
      $html .= 'name="' . $fieldName . '[]" ';
      $html .= 'value="' . $option['id'] . '" ' . $isChecked . ' ';
      $html .= $fieldValidation;
      $html .= ' /> ' . $this->wp->escAttr($segmentsNamesMap[$option['id']]);
      $html .= '</label>';
    }

    $html .= '<span class="mailpoet_error_' . $block['id'] . ($formId ? '_' . $formId : '') . '"></span>';

    // End fieldset around checkboxes
    $html .= '</fieldset>';

    return $this->wrapper->render($block, $html);
  }

  private function getSegmentsNames($values): array {
    $ids = array_column($values, 'id');
    $segments = $this->segmentsRepository->findBy(['id' => $ids]);
    $namesMap = [];
    foreach ($segments as $segment) {
      $namesMap[$segment->getId()] = $segment->getName();
    }
    return $namesMap;
  }
}
