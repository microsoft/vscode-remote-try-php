<?php declare(strict_types = 1);

namespace MailPoet\EmailEditor\Engine\Renderer\ContentRenderer\Postprocessors;

if (!defined('ABSPATH')) exit;


use MailPoet\EmailEditor\Engine\ThemeController;

/**
 * In some case the blocks HTML contains CSS variables.
 * For example when spacing is set from a preset the inline styles contain var(--wp--preset--spacing--10), var(--wp--preset--spacing--20) etc.
 * This postprocessor uses variables from theme.json and replaces the CSS variables with their values in final email HTML.
 */
class VariablesPostprocessor implements Postprocessor {
  private ThemeController $themeController;

  public function __construct(
    ThemeController $themeController
  ) {
    $this->themeController = $themeController;
  }

  public function postprocess(string $html): string {
    $variables = $this->themeController->getVariablesValuesMap();
    $replacements = [];

    foreach ($variables as $varName => $varValue) {
      $varPattern = '/' . preg_quote('var(' . $varName . ')', '/') . '/i';
      $replacements[$varPattern] = $varValue;
    }

    // Pattern to match style attributes and their values.
    $callback = function ($matches) use ($replacements) {
      // For each match, replace CSS variables with their values
      $style = $matches[1];
      $style = preg_replace(array_keys($replacements), array_values($replacements), $style);
      return 'style="' . esc_attr($style) . '"';
    };

    // We want to replace the CSS variables only in the style attributes to avoid replacing the actual content.
    $stylePattern = '/style="(.*?)"/i';
    $stylePatternAlt = "/style='(.*?)'/i";
    $html = (string)preg_replace_callback($stylePattern, $callback, $html);
    $html = (string)preg_replace_callback($stylePatternAlt, $callback, $html);

    return $html;
  }
}
