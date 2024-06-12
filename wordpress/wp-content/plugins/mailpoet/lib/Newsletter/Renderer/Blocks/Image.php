<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter\Renderer\Blocks;

if (!defined('ABSPATH')) exit;


use MailPoet\Newsletter\Renderer\EscapeHelper as EHelper;
use MailPoet\Newsletter\Renderer\StylesHelper;
use MailPoet\WP\Functions as WPFunctions;

class Image {
  public function render($element, $columnBaseWidth) {
    if (empty($element['src'])) {
      return '';
    }
    if (substr($element['src'], 0, 1) == '/' && substr($element['src'], 1, 1) != '/') {
      $element['src'] = WPFunctions::get()->getOption('siteurl') . $element['src'];
    }

    $element['width'] = str_replace('px', '', $element['width']);
    $element['height'] = str_replace('px', '', $element['height']);
    $originalWidth = 0;
    if (is_numeric($element['width']) && is_numeric($element['height'])) {
      $element['width'] = (int)$element['width'];
      $element['height'] = (int)$element['height'];
      $originalWidth = $element['width'];
      $element = $this->adjustImageDimensions($element, $columnBaseWidth);
    }

    // If image was downsized because of column width set width to aways fill full column (e.g. on mobile)
    $style = '';
    if ($element['fullWidth'] === true && $originalWidth > $element['width']) {
      $style = 'style="width:100%"';
    }

    $imageTemplate = '
      <img src="' . EHelper::escapeHtmlLinkAttr($element['src']) . '" width="' . EHelper::escapeHtmlAttr($element['width']) . '" alt="' . EHelper::escapeHtmlAttr($element['alt']) . '"' . $style . '/>
      ';
    if (!empty($element['link'])) {
      $imageTemplate = '<a href="' . EHelper::escapeHtmlLinkAttr($element['link']) . '">' . trim($imageTemplate) . '</a>';
    }
    $align = 'center';
    if (!empty($element['styles']['block']['textAlign']) && in_array($element['styles']['block']['textAlign'], ['left', 'right'])) {
      $align = $element['styles']['block']['textAlign'];
    }

    $template = '
      <tr>
        <td class="mailpoet_image ' . (($element['fullWidth'] === false) ? 'mailpoet_padded_vertical mailpoet_padded_side' : '') . '" align="' . EHelper::escapeHtmlAttr($align) . '" valign="top">
          ' . trim($imageTemplate) . '
        </td>
      </tr>';
    return $template;
  }

  public function adjustImageDimensions($element, $columnBaseWidth) {
    $paddedWidth = StylesHelper::$paddingWidth * 2;
    // scale image to fit column width
    if ($element['width'] > $columnBaseWidth) {
      $ratio = $element['width'] / $columnBaseWidth;
      $element['width'] = $columnBaseWidth;
      $element['height'] = (int)ceil($element['height'] / $ratio);
    }
    // resize image if the image is padded and wider than padded column width
    if (
      $element['fullWidth'] === false &&
      $element['width'] > ($columnBaseWidth - $paddedWidth)
    ) {
      $ratio = $element['width'] / ($columnBaseWidth - $paddedWidth);
      $element['width'] = $columnBaseWidth - $paddedWidth;
      $element['height'] = (int)ceil($element['height'] / $ratio);
    }
    return $element;
  }
}
