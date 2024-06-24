<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter\Editor;

if (!defined('ABSPATH')) exit;


use MailPoet\Util\DOM as DOMUtil;
use MailPoet\Util\pQuery\pQuery;
use MailPoetVendor\pQuery\DomNode;

class StructureTransformer {
  public function transform($content, $imageFullWidth) {
    $root = pQuery::parseStr($content);

    $this->hoistImagesToRoot($root);
    $structure = $this->transformTagsToBlocks($root, $imageFullWidth);
    $structure = $this->mergeNeighboringBlocks($structure);
    return $structure;
  }

  /**
   * Hoists images to root level, preserves order by splitting neighboring
   * elements and inserts tags as children of top ancestor
   */
  protected function hoistImagesToRoot(DomNode $root) {
    foreach ($root->query('img') as $item) {
      $topAncestor = DOMUtil::findTopAncestor($item);
      $offset = $topAncestor->index();

      if ($item->hasParent('a') || $item->hasParent('figure')) {
        $item = $item->parent;
      }

      DOMUtil::splitOn($item->getRoot(), $item);
    }
  }

  /**
   * Transforms HTML tags into their respective JSON objects,
   * turns other root children into text blocks
   */
  private function transformTagsToBlocks(DomNode $root, $imageFullWidth) {
    $children = $this->filterOutFiguresWithoutImages($root->children);
    return array_map(function($item) use ($imageFullWidth) {
      if ($this->isImageElement($item)) {
        $image = $item->tag === 'img' ? $item : $item->query('img')[0];
        $width = $image->getAttribute('width');
        $height = $image->getAttribute('height');
        return [
          'type' => 'image',
          'link' => $item->getAttribute('href') ?: '',
          'src' => $image->getAttribute('src'),
          'alt' => $image->getAttribute('alt'),
          'fullWidth' => $imageFullWidth,
          'width' => $width === null ? 'auto' : $width,
          'height' => $height === null ? 'auto' : $height,
          'styles' => [
            'block' => [
              'textAlign' => $this->getImageAlignment($image),
            ],
          ],
        ];
      } else {
        return [
          'type' => 'text',
          'text' => $item->toString(),
        ];
      }

    }, $children);
  }

  private function filterOutFiguresWithoutImages(array $items) {
    $items = array_filter($items, function (DomNode $item) {
      if ($item->tag === 'figure' && $item->query('img')->count() === 0) {
        return false;
      }
      return true;
    });
    return array_values($items);
  }

  private function isImageElement(DomNode $item) {
    return $item->tag === 'img' || (in_array($item->tag, ['a', 'figure'], true) && $item->query('img')->count() > 0);
  }

  private function getImageAlignment(DomNode $image) {
    $alignItem = $image->hasParent('figure') ? $image->parent : $image;
    if ($alignItem->hasClass('aligncenter')) {
      $align = 'center';
    } elseif ($alignItem->hasClass('alignleft')) {
      $align = 'left';
    } elseif ($alignItem->hasClass('alignright')) {
      $align = 'right';
    } else {
      $align = 'left';
    }
    return $align;
  }

  /**
   * Merges neighboring blocks when possible.
   * E.g. 2 adjacent text blocks may be combined into one.
   */
  private function mergeNeighboringBlocks(array $structure) {
    $updatedStructure = [];
    $textAccumulator = '';
    foreach ($structure as $item) {
      if ($item['type'] === 'text') {
        $textAccumulator .= $item['text'];
      }
      if ($item['type'] !== 'text') {
        if (!empty($textAccumulator)) {
          $updatedStructure[] = [
            'type' => 'text',
            'text' => trim($textAccumulator),
          ];
          $textAccumulator = '';
        }
        $updatedStructure[] = $item;
      }
    }

    if (!empty($textAccumulator)) {
      $updatedStructure[] = [
        'type' => 'text',
        'text' => trim($textAccumulator),
      ];
    }

    return $updatedStructure;
  }
}
