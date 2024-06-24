<?php declare(strict_types = 1);

namespace MailPoet\EmailEditor\Engine\Renderer\ContentRenderer\Preprocessors;

if (!defined('ABSPATH')) exit;


/**
 * This class sets the width of the blocks based on the layout width or column count.
 * The final width in pixels is stored in the email_attrs array because we would like to avoid changing the original attributes.
 */
class BlocksWidthPreprocessor implements Preprocessor {
  public function preprocess(array $parsedBlocks, array $layout, array $styles): array {
    foreach ($parsedBlocks as $key => $block) {
      // Layout width is recalculated for each block because full-width blocks don't exclude padding
      $layoutWidth = $this->parseNumberFromStringWithPixels($layout['contentSize']);
      $alignment = $block['attrs']['align'] ?? null;
      // Subtract padding from the block width if it's not full-width
      if ($alignment !== 'full') {
        $layoutWidth -= $this->parseNumberFromStringWithPixels($styles['spacing']['padding']['left'] ?? '0px');
        $layoutWidth -= $this->parseNumberFromStringWithPixels($styles['spacing']['padding']['right'] ?? '0px');
      }

      $widthInput = $block['attrs']['width'] ?? '100%';
      // Currently we support only % and px units in case only the number is provided we assume it's %
      // because editor saves percent values as a number.
      $widthInput = is_numeric($widthInput) ? "$widthInput%" : $widthInput;
      $width = $this->convertWidthToPixels($widthInput, $layoutWidth);

      if ($block['blockName'] === 'core/columns') {
        // Calculate width of the columns based on the layout width and padding
        $columnsWidth = $layoutWidth;
        $columnsWidth -= $this->parseNumberFromStringWithPixels($block['attrs']['style']['spacing']['padding']['left'] ?? '0px');
        $columnsWidth -= $this->parseNumberFromStringWithPixels($block['attrs']['style']['spacing']['padding']['right'] ?? '0px');
        $borderWidth = $block['attrs']['style']['border']['width'] ?? '0px';
        $columnsWidth -= $this->parseNumberFromStringWithPixels($block['attrs']['style']['border']['left']['width'] ?? $borderWidth);
        $columnsWidth -= $this->parseNumberFromStringWithPixels($block['attrs']['style']['border']['right']['width'] ?? $borderWidth);
        $block['innerBlocks'] = $this->addMissingColumnWidths($block['innerBlocks'], $columnsWidth);
      }

      // Copy layout styles and update width and padding
      $modifiedLayout = $layout;
      $modifiedLayout['contentSize'] = "{$width}px";
      $modifiedStyles = $styles;
      $modifiedStyles['spacing']['padding']['left'] = $block['attrs']['style']['spacing']['padding']['left'] ?? '0px';
      $modifiedStyles['spacing']['padding']['right'] = $block['attrs']['style']['spacing']['padding']['right'] ?? '0px';

      $block['email_attrs']['width'] = "{$width}px";
      $block['innerBlocks'] = $this->preprocess($block['innerBlocks'], $modifiedLayout, $modifiedStyles);
      $parsedBlocks[$key] = $block;
    }
    return $parsedBlocks;
  }

  // TODO: We could add support for other units like em, rem, etc.
  private function convertWidthToPixels(string $currentWidth, float $layoutWidth): float {
    $width = $layoutWidth;
    if (strpos($currentWidth, '%') !== false) {
      $width = (float)str_replace('%', '', $currentWidth);
      $width = round($width / 100 * $layoutWidth);
    } elseif (strpos($currentWidth, 'px') !== false) {
      $width = $this->parseNumberFromStringWithPixels($currentWidth);
    }

    return $width;
  }

  private function parseNumberFromStringWithPixels(string $string): float {
    return (float)str_replace('px', '', $string);
  }

  private function addMissingColumnWidths(array $columns, float $columnsWidth): array {
    $columnsCountWithDefinedWidth = 0;
    $definedColumnWidth = 0;
    $columnsCount = count($columns);
    foreach ($columns as $column) {
      if (isset($column['attrs']['width']) && !empty($column['attrs']['width'])) {
        $columnsCountWithDefinedWidth++;
        $definedColumnWidth += $this->convertWidthToPixels($column['attrs']['width'], $columnsWidth);
      } else {
        // When width is not set we need to add padding to the defined column width for better ratio accuracy
        $definedColumnWidth += $this->parseNumberFromStringWithPixels($column['attrs']['style']['spacing']['padding']['left'] ?? '0px');
        $definedColumnWidth += $this->parseNumberFromStringWithPixels($column['attrs']['style']['spacing']['padding']['right'] ?? '0px');
        $borderWidth = $column['attrs']['style']['border']['width'] ?? '0px';
        $definedColumnWidth += $this->parseNumberFromStringWithPixels($column['attrs']['style']['border']['left']['width'] ?? $borderWidth);
        $definedColumnWidth += $this->parseNumberFromStringWithPixels($column['attrs']['style']['border']['right']['width'] ?? $borderWidth);
      }
    }

    if ($columnsCount - $columnsCountWithDefinedWidth > 0) {
      $defaultColumnsWidth = round(($columnsWidth - $definedColumnWidth) / ($columnsCount - $columnsCountWithDefinedWidth), 2);
      foreach ($columns as $key => $column) {
        if (!isset($column['attrs']['width']) || empty($column['attrs']['width'])) {
          // Add padding to the specific column width because it's not included in the default width
          $columnWidth = $defaultColumnsWidth;
          $columnWidth += $this->parseNumberFromStringWithPixels($column['attrs']['style']['spacing']['padding']['left'] ?? '0px');
          $columnWidth += $this->parseNumberFromStringWithPixels($column['attrs']['style']['spacing']['padding']['right'] ?? '0px');
          $borderWidth = $column['attrs']['style']['border']['width'] ?? '0px';
          $columnWidth += $this->parseNumberFromStringWithPixels($column['attrs']['style']['border']['left']['width'] ?? $borderWidth);
          $columnWidth += $this->parseNumberFromStringWithPixels($column['attrs']['style']['border']['right']['width'] ?? $borderWidth);
          $columns[$key]['attrs']['width'] = "{$columnWidth}px";
        }
      }
    }
    return $columns;
  }
}
