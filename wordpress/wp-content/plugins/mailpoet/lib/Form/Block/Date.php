<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Form\Block;

if (!defined('ABSPATH')) exit;


use MailPoet\Form\BlockStylesRenderer;
use MailPoet\Form\BlockWrapperRenderer;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Carbon\CarbonImmutable;

class Date {

  /** @var BlockRendererHelper */
  private $rendererHelper;

  /** @var BlockWrapperRenderer */
  private $wrapper;

  /** @var BlockStylesRenderer */
  private $blockStylesRenderer;

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    BlockRendererHelper $rendererHelper,
    BlockStylesRenderer $blockStylesRenderer,
    BlockWrapperRenderer $wrapper,
    WPFunctions $wp
  ) {
    $this->rendererHelper = $rendererHelper;
    $this->wrapper = $wrapper;
    $this->blockStylesRenderer = $blockStylesRenderer;
    $this->wp = $wp;
  }

  public function render(array $block, array $formSettings, ?int $formId = null): string {
    $html = '';
    $html .= $this->rendererHelper->renderLabel($block, $formSettings);
    $html .= $this->renderDateSelect($formId, $block, $formSettings);
    return $this->wrapper->render($block, $html);
  }

  private function renderDateSelect(?int $formId, array $block = [], $formSettings = []): string {
    $html = '';

    $fieldName = 'data[' . $this->rendererHelper->getFieldName($block) . ']';

    $dateFormats = $this->getDateFormats();

    // automatically select first date format
    $dateFormat = $dateFormats[$block['params']['date_type']][0];

    // set date format if specified
    if (
      isset($block['params']['date_format'])
      && strlen(trim($block['params']['date_format'])) > 0
    ) {
      $dateFormat = $block['params']['date_format'];
    }

    // generate an array of selectors based on date format
    $dateSelectors = explode('/', $dateFormat);

    foreach ($dateSelectors as $dateSelector) {
      if ($dateSelector === 'DD') {
        $html .= '<select class="mailpoet_date_day" ';
        $html .= ' style="' . $this->wp->escAttr($this->blockStylesRenderer->renderForSelect([], $formSettings)) . '"';
        $html .= $this->rendererHelper->getInputValidation($block, [
          'required-message' => __('Please select a day', 'mailpoet'),
        ], $formId);
        $html .= 'name="' . $fieldName . '[day]" placeholder="' . __('Day', 'mailpoet') . '">';
        $html .= $this->getDays($block);
        $html .= '</select>';
      } else if ($dateSelector === 'MM') {
        $html .= '<select class="mailpoet_select mailpoet_date_month" data-automation-id="form_date_month" ';
        $html .= ' style="' . $this->wp->escAttr($this->blockStylesRenderer->renderForSelect([], $formSettings)) . '"';
        $html .= $this->rendererHelper->getInputValidation($block, [
          'required-message' => __('Please select a month', 'mailpoet'),
        ], $formId);
        $html .= 'name="' . $fieldName . '[month]" placeholder="' . __('Month', 'mailpoet') . '">';
        $html .= $this->getMonths($block);
        $html .= '</select>';
      } else if ($dateSelector === 'YYYY') {
        $html .= '<select class="mailpoet_date_year" data-automation-id="form_date_year" ';
        $html .= ' style="' . $this->wp->escAttr($this->blockStylesRenderer->renderForSelect([], $formSettings)) . '"';
        $html .= $this->rendererHelper->getInputValidation($block, [
          'required-message' => __('Please select a year', 'mailpoet'),
        ], $formId);
        $html .= 'name="' . $fieldName . '[year]" placeholder="' . __('Year', 'mailpoet') . '">';
        $html .= $this->getYears($block);
        $html .= '</select>';
      }
    }

    $html .= '<span class="mailpoet_error_' . $this->wp->escAttr($block['id']) . ($formId ? '_' . $formId : '') . '"></span>';

    return $html;
  }

  public function getDateTypes(): array {
    return [
      'year_month_day' => __('Year, month, day', 'mailpoet'),
      'year_month' => __('Year, month', 'mailpoet'),
      'month' => __('Month (January, February,...)', 'mailpoet'),
      'year' => __('Year', 'mailpoet'),
    ];
  }

  public function getDateFormats(): array {
    return [
      'year_month_day' => ['MM/DD/YYYY', 'DD/MM/YYYY', 'YYYY/MM/DD'],
      'year_month' => ['MM/YYYY', 'YYYY/MM'],
      'year' => ['YYYY'],
      'month' => ['MM'],
    ];
  }

  public function getMonthNames(): array {
    return [__('January', 'mailpoet'), __('February', 'mailpoet'), __('March', 'mailpoet'), __('April', 'mailpoet'),
      __('May', 'mailpoet'), __('June', 'mailpoet'), __('July', 'mailpoet'), __('August', 'mailpoet'), __('September', 'mailpoet'),
      __('October', 'mailpoet'), __('November', 'mailpoet'), __('December', 'mailpoet'),
    ];
  }

  private function getMonths(array $block = []): string {
    $defaults = [
      'selected' => null,
    ];

    if (!empty($block['params']['value'])) {
      $date = CarbonImmutable::createFromFormat('Y-m-d H:i:s', $block['params']['value']);
      if ($date instanceof CarbonImmutable) {
        $defaults['selected'] = (int)date('m', $date->getTimestamp());
      }
    } elseif (!empty($block['params']['is_default_today'])) {
      // is default today
      $defaults['selected'] = (int)date('m');
    }
    // merge block with defaults
    $block = array_merge($defaults, $block);

    $monthNames = $this->getMonthNames();

    $html = '';

    // empty value label
    $html .= '<option value="">' . __('Month', 'mailpoet') . '</option>';

    for ($i = 1; $i < 13; $i++) {
      $isSelected = ($i === $block['selected']) ? 'selected="selected"' : '';
      $html .= '<option value="' . $i . '" ' . $isSelected . '>';
      $html .= $monthNames[$i - 1];
      $html .= '</option>';
    }

    return $html;
  }

  private function getYears(array $block = []): string {
    $defaults = [
      'selected' => null,
      'from' => (int)date('Y') - 100,
      'to' => (int)date('Y'),
    ];

    if (!empty($block['params']['value'])) {
      $date = CarbonImmutable::createFromFormat('Y-m-d H:i:s', $block['params']['value']);
      if ($date instanceof CarbonImmutable) {
        $defaults['selected'] = (int)date('Y', $date->getTimestamp());
      }
    } elseif (!empty($block['params']['is_default_today'])) {
      // is default today
      $defaults['selected'] = (int)date('Y');
    }

    // merge block with defaults
    $block = array_merge($defaults, $block);

    $html = '';

    // empty value label
    $html .= '<option value="">' . __('Year', 'mailpoet') . '</option>';

    // return years as an array
    for ($i = (int)$block['to']; $i > (int)($block['from'] - 1); $i--) {
      $isSelected = ($i === $block['selected']) ? 'selected="selected"' : '';
      $html .= '<option value="' . $i . '" ' . $isSelected . '>' . $i . '</option>';
    }

    return $html;
  }

  private function getDays(array $block = []): string {
    $defaults = [
      'selected' => null,
    ];
    if (!empty($block['params']['value'])) {
      $date = CarbonImmutable::createFromFormat('Y-m-d H:i:s', $block['params']['value']);
      if ($date instanceof CarbonImmutable) {
        $defaults['selected'] = (int)date('d', $date->getTimestamp());
      }
    } elseif (!empty($block['params']['is_default_today'])) {
      // is default today
      $defaults['selected'] = (int)date('d');
    }

    // merge block with defaults
    $block = array_merge($defaults, $block);

    $html = '';

    // empty value label
    $html .= '<option value="">' . __('Day', 'mailpoet') . '</option>';

    // return days as an array
    for ($i = 1; $i < 32; $i++) {
      $isSelected = ($i === $block['selected']) ? 'selected="selected"' : '';
      $html .= '<option value="' . $i . '" ' . $isSelected . '>' . $i . '</option>';
    }

    return $html;
  }
}
