<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Form\Util;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\FormEntity;
use MailPoetVendor\Sabberworm\CSS\Parser as CSSParser;

class Styles {
  public function prefixStyles($stylesheet, $prefix = '') {
    if (!$stylesheet) return;
    $styles = new CSSParser($stylesheet);
    $styles = $styles->parse();
    $formattedStyles = [];
    foreach ($styles->getAllDeclarationBlocks() as $styleDeclaration) {
      $selectors = array_map(function($selector) use ($prefix) {
        $stringSelector = is_string($selector) ? $selector : $selector->__toString();
        return sprintf('%s %s', $prefix, $stringSelector);
      }, $styleDeclaration->getSelectors());
      $selectors = implode(', ', $selectors);
      $rules = array_map(function($rule) {
        return $rule->__toString();
      }, $styleDeclaration->getRules());
      $rules = sprintf('{ %s }', implode(' ', $rules));
      $formattedStyles[] = sprintf('%s %s', $selectors, $rules);
    }
    return implode(PHP_EOL, $formattedStyles);
  }

  public function renderFormSettingsStyles(FormEntity $form, string $selector, string $displayType): string {
    if (!is_array($form->getSettings())) return '';
    $formSettings = $form->getSettings();
    // Wrapper styles
    $styles = [];

    if (isset($formSettings['border_size']) && isset($formSettings['border_color'])) {
      $styles[] = 'border: ' . $formSettings['border_size'] . 'px solid ' . $formSettings['border_color'];
    }

    if (isset($formSettings['border_radius'])) {
      $styles[] = 'border-radius: ' . $formSettings['border_radius'] . 'px';
    }

    $backgrounds = [];
    $mobileBackgrounds = [];
    if (isset($formSettings['background_image_url']) && $formSettings['background_image_url']) {
      $backgroundPosition = 'center';
      $backgroundRepeat = 'no-repeat';
      $backgroundSize = 'cover';
      if (isset($formSettings['background_image_display']) && $formSettings['background_image_display'] === 'fit') {
        $backgroundPosition = 'center top';
        $backgroundSize = 'contain';
      }
      if (isset($formSettings['background_image_display']) && $formSettings['background_image_display'] === 'tile') {
        $backgroundRepeat = 'repeat';
        $backgroundSize = 'auto';
      }
      $backgrounds[] = "url(" . trim($formSettings['background_image_url']) . ") $backgroundPosition / $backgroundSize $backgroundRepeat";
    }

    if (!empty($formSettings['gradient'])) {
      $backgrounds[] = trim($formSettings['gradient']);
      $mobileBackgrounds[] = trim($formSettings['gradient']);
    }

    if (!empty($formSettings['backgroundColor'])) {
      $backgrounds[] = trim($formSettings['backgroundColor']);
      $mobileBackgrounds[] = trim($formSettings['backgroundColor']);
    }

    if ($backgrounds) {
      $styles[] = 'background: ' . join(', ', $backgrounds);
    }

    if (isset($formSettings['fontColor'])) {
      $styles[] = 'color: ' . trim($formSettings['fontColor']);
    }

    if (isset($formSettings['alignment'])) {
      $styles[] = 'text-align: ' . $formSettings['alignment'];
    }
    $formWrapperStyles = $selector . '{' . join(';', $styles) . ';}';

    // Media styles for mobile
    $media = $this->getMobileStyles($selector, $displayType, $mobileBackgrounds);

    // Form element styles
    $formStyles = [];
    if (isset($formSettings['form_padding'])) {
      if (
        in_array(
          $displayType,
          [FormEntity::DISPLAY_TYPE_POPUP, FormEntity::DISPLAY_TYPE_FIXED_BAR, FormEntity::DISPLAY_TYPE_SLIDE_IN]
        )
      ) {
        $padding = $formSettings['form_padding'];
        $media .= " @media (min-width: 500px) {{$selector} {padding: {$padding}px;}} ";
      } else {
        $formStyles[] = 'padding: ' . $formSettings['form_padding'] . 'px';
      }
    }
    $formElementStyles = '';
    if ($formStyles) {
      $formElementStyles = $selector . ' form.mailpoet_form {' . join(';', $formStyles) . ';}';
    }

    // Width styles
    $widthStyles = $this->renderWidthStyles($formSettings, $selector, $displayType);

    $typeSpecificStyles = $this->getFormTypeSpecificStyles($selector, $displayType);

    $messagesStyles = $this->renderFormMessageStyles($form, $selector);

    $additionalStyles = $selector . ' .mailpoet_paragraph.last {margin-bottom: 0} ';
    $media .= " @media (min-width: 500px) {{$selector} .last .mailpoet_paragraph:last-child {margin-bottom: 0}} ";
    $media .= " @media (max-width: 500px) {{$selector} .mailpoet_form_column:last-child .mailpoet_paragraph:last-child {margin-bottom: 0}} ";

    return $formWrapperStyles
      . $formElementStyles
      . $widthStyles
      . $messagesStyles
      . $typeSpecificStyles
      . $additionalStyles
      . $media;
  }

  private function renderWidthStyles(array $formSettings, string $selector, string $displayType): string {
    $styles = [];

    if (isset($formSettings['form_placement'][$displayType]['styles']['width'])) {
      $width = $this->getWidthValue($formSettings['form_placement'][$displayType]['styles']['width']);
    }

    if ($displayType === FormEntity::DISPLAY_TYPE_POPUP) {
      if (isset($width)) {
        $styles[] = "width: $width";
        $styles[] = "max-width: 100vw";
      } else { // BC compatibilty
        $styles[] = 'width: 560px';
        $styles[] = 'max-width: 560px';
      }
    } elseif ($displayType === FormEntity::DISPLAY_TYPE_SLIDE_IN) {
      if (isset($width)) {
        $styles[] = "width: $width";
        $styles[] = "max-width: 100vw";
      } else { // BC compatibilty
        $styles[] = 'max-width: 600px';
        $styles[] = 'min-width: 350px';
      }
    } elseif ($displayType === FormEntity::DISPLAY_TYPE_FIXED_BAR) {
      if (isset($width)) {
        $styles[] = "width: $width";
        $styles[] = "max-width: 100%";
      } else { // BC compatibilty
        $styles[] = 'max-width: 960px';
      }
    } elseif ($displayType === FormEntity::DISPLAY_TYPE_BELOW_POST) {
      if (isset($width)) {
        $styles[] = "width: $width";
      }
    } elseif ($displayType === FormEntity::DISPLAY_TYPE_OTHERS) {
      if (isset($width)) {
        $styles[] = "width: $width";
      }
    }

    $widthSelector = $selector;
    $widthSelector .= $displayType === FormEntity::DISPLAY_TYPE_FIXED_BAR ? ' form.mailpoet_form' : '';

    if (!$styles) {
      return '';
    }
    return $widthSelector . '{' . join(';', $styles) . ';}';
  }

  private function getWidthValue(array $width) {
    return $width['value'] . ($width['unit'] === 'percent' ? '%' : 'px');
  }

  public function renderFormMessageStyles(FormEntity $form, string $selector): string {
    $formSettings = $form->getSettings();
    if (!is_array($formSettings)) {
      return '';
    }
    return $this->renderMessagesStyles($formSettings, $selector);
  }

  private function renderMessagesStyles(array $formSettings, string $selector): string {
    $styles = "$selector .mailpoet_message {margin: 0; padding: 0 20px;}";
    if (isset($formSettings['success_validation_color']) && $formSettings['success_validation_color']) {
      $success = $formSettings['success_validation_color'];
      $styles .= "
        $selector .mailpoet_validate_success {color: $success}
        $selector input.parsley-success {color: $success}
        $selector select.parsley-success {color: $success}
        $selector textarea.parsley-success {color: $success}
      ";
    }
    if (isset($formSettings['error_validation_color']) && $formSettings['error_validation_color']) {
      $error = $formSettings['error_validation_color'];
      $styles .= "
        $selector .mailpoet_validate_error {color: $error}
        $selector input.parsley-error {color: $error}
        $selector select.parsley-error {color: $error}
        $selector textarea.textarea.parsley-error {color: $error}
        $selector .parsley-errors-list {color: $error}
        $selector .parsley-required {color: $error}
        $selector .parsley-custom-error-message {color: $error}
      ";
    }
    return $styles;
  }

  private function getFormTypeSpecificStyles(string $selector, string $displayType): string {
    $styles = [];
    if ($displayType === FormEntity::DISPLAY_TYPE_SLIDE_IN) {
      $styles[] = $selector . '.mailpoet_form_slide_in { border-bottom-left-radius: 0; border-bottom-right-radius: 0; }';
      $styles[] = $selector . '.mailpoet_form_position_right { border-top-right-radius: 0; }';
      $styles[] = $selector . '.mailpoet_form_position_left { border-top-left-radius: 0; }';
    }
    return join('', $styles);
  }

  private function getMobileStyles(string $selector, string $displayType, array $mobileBackgrounds): string {
    $wrapperStyles = [];
    if ($mobileBackgrounds) {
      $wrapperStyles[] = 'background: ' . join(', ', $mobileBackgrounds) . ';';
    } else {
      $wrapperStyles[] = 'background-image: none;';
    }
    if (
      in_array(
        $displayType,
        [FormEntity::DISPLAY_TYPE_POPUP, FormEntity::DISPLAY_TYPE_FIXED_BAR, FormEntity::DISPLAY_TYPE_SLIDE_IN]
      )
    ) {
      $wrapperStyles = array_merge($wrapperStyles, [
        'animation: none;',
        'border: none;',
        'border-radius: 0;',
        'bottom: 0;',
        'left: 0;',
        'max-height: 40%;',
        'padding: 20px;',
        'right: 0;',
        'top: auto;',
        'transform: none;',
        'width: 100%;',
        'min-width: 100%;',
      ]);
    }
    return "@media (max-width: 500px) {{$selector} {" . join('', $wrapperStyles) . "}}";
  }
}
