<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter\Renderer\Blocks;

if (!defined('ABSPATH')) exit;


use MailPoet\Newsletter\Renderer\EscapeHelper as EHelper;
use MailPoet\Newsletter\Renderer\StylesHelper;

class Divider {
  public function render($element) {
    $backgroundColor = $element['styles']['block']['backgroundColor'];
    $dividerCellStyle = "border-top-width: {$element['styles']['block']['borderWidth']};";
    $dividerCellStyle .= "border-top-style: {$element['styles']['block']['borderStyle']};";
    $dividerCellStyle .= "border-top-color: {$element['styles']['block']['borderColor']};";
    $template = '
      <tr>
        <td class="mailpoet_divider" valign="top" ' .
        (($element['styles']['block']['backgroundColor'] !== 'transparent') ?
          'bgColor="' . EHelper::escapeHtmlAttr($backgroundColor) . '" style="background-color:' . EHelper::escapeHtmlStyleAttr($backgroundColor) . ';' :
          'style="'
        ) .
      sprintf(
        'padding: %s %spx %s %spx;',
        EHelper::escapeHtmlStyleAttr($element['styles']['block']['padding']),
        StylesHelper::$paddingWidth,
        EHelper::escapeHtmlStyleAttr($element['styles']['block']['padding']),
        StylesHelper::$paddingWidth
      ) . '">
          <table width="100%" border="0" cellpadding="0" cellspacing="0"
          style="border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;">
            <tr>
              <td class="mailpoet_divider-cell" style="' . EHelper::escapeHtmlStyleAttr($dividerCellStyle) . '">
             </td>
            </tr>
          </table>
        </td>
      </tr>';
    return $template;
  }
}
