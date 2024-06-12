<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter\Renderer\Blocks;

if (!defined('ABSPATH')) exit;


use MailPoet\Newsletter\Renderer\EscapeHelper as EHelper;

class Social {
  public function render($element) {
    $iconsBlock = '';
    if (is_array($element['icons'])) {
      foreach ($element['icons'] as $index => $icon) {
        if (empty($icon['image'])) {
          continue;
        }

        $style = 'width:' . $icon['width'] . ';height:' . $icon['width'] . ';-ms-interpolation-mode:bicubic;border:0;display:inline;outline:none;';
        $iconsBlock .= '<a href="' . EHelper::escapeHtmlLinkAttr($icon['link']) . '" style="text-decoration:none!important;"
        ><img
          src="' . EHelper::escapeHtmlLinkAttr($icon['image']) . '"
          width="' . (int)$icon['width'] . '"
          height="' . (int)$icon['height'] . '"
          style="' . EHelper::escapeHtmlStyleAttr($style) . '"
          alt="' . EHelper::escapeHtmlAttr($icon['iconType']) . '"
        ></a>&nbsp;';
      }
    }
    $alignment = isset($element['styles']['block']['textAlign']) ? $element['styles']['block']['textAlign'] : 'center';
    if (!empty($iconsBlock)) {
      $template = '
      <tr>
        <td class="mailpoet_padded_side mailpoet_padded_vertical" valign="top" align="' . EHelper::escapeHtmlAttr($alignment) . '">
          ' . $iconsBlock . '
        </td>
      </tr>';
      return $template;
    }
  }
}
