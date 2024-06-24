<?php declare(strict_types = 1);

namespace MailPoet\EmailEditor\Engine\Renderer\ContentRenderer\Preprocessors;

if (!defined('ABSPATH')) exit;


use MailPoet\EmailEditor\Engine\SettingsController;

class TypographyPreprocessor implements Preprocessor {
  /**
   * List of styles that should be copied from parent to children.
   * @var string[]
   */
  private const TYPOGRAPHY_STYLES = [
    'color',
    'font-size',
    'text-decoration',
  ];

  /** @var SettingsController */
  private $settingsController;

  public function __construct(
    SettingsController $settingsController
  ) {
    $this->settingsController = $settingsController;
  }

  public function preprocess(array $parsedBlocks, array $layout, array $styles): array {
    foreach ($parsedBlocks as $key => $block) {
      $block = $this->preprocessParent($block);
      // Set defaults from theme - this needs to be done on top level blocks only
      $block = $this->setDefaultsFromTheme($block);

      $block['innerBlocks'] = $this->copyTypographyFromParent($block['innerBlocks'], $block);
      $parsedBlocks[$key] = $block;
    }
    return $parsedBlocks;
  }

  private function copyTypographyFromParent(array $children, array $parent): array {
    foreach ($children as $key => $child) {
      $child = $this->preprocessParent($child);
      $child['email_attrs'] = array_merge($this->filterStyles($parent['email_attrs']), $child['email_attrs']);
      $child['innerBlocks'] = $this->copyTypographyFromParent($child['innerBlocks'] ?? [], $child);
      $children[$key] = $child;
    }

    return $children;
  }

  private function preprocessParent(array $block): array {
    // Build styles that should be copied to children
    $emailAttrs = [];
    if (isset($block['attrs']['style']['color']['text'])) {
      $emailAttrs['color'] = $block['attrs']['style']['color']['text'];
    }
    // In case the fontSize is set via a slug (small, medium, large, etc.) we translate it to a number
    // The font size slug is set in $block['attrs']['fontSize'] and value in $block['attrs']['style']['typography']['fontSize']
    if (isset($block['attrs']['fontSize'])) {
      $block['attrs']['style']['typography']['fontSize'] = $this->settingsController->translateSlugToFontSize($block['attrs']['fontSize']);
    }
    // Pass font size to email_attrs
    if (isset($block['attrs']['style']['typography']['fontSize'])) {
      $emailAttrs['font-size'] = $block['attrs']['style']['typography']['fontSize'];
    }
    if (isset($block['attrs']['style']['typography']['textDecoration'])) {
      $emailAttrs['text-decoration'] = $block['attrs']['style']['typography']['textDecoration'];
    }
    $block['email_attrs'] = array_merge($emailAttrs, $block['email_attrs'] ?? []);
    return $block;
  }

  private function filterStyles(array $styles): array {
    return array_intersect_key($styles, array_flip(self::TYPOGRAPHY_STYLES));
  }

  private function setDefaultsFromTheme(array $block): array {
    $themeData = $this->settingsController->getTheme()->get_data();
    if (!($block['email_attrs']['color'] ?? '')) {
      $block['email_attrs']['color'] = $themeData['styles']['color']['text'] ?? null;
    }
    if (!($block['email_attrs']['font-size'] ?? '')) {
      $block['email_attrs']['font-size'] = $themeData['styles']['typography']['fontSize'];
    }
    return $block;
  }
}
