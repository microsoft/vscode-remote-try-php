<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter\Renderer\Blocks;

if (!defined('ABSPATH')) exit;


use MailPoet\Newsletter\Renderer\EscapeHelper as EHelper;
use MailPoet\Newsletter\Renderer\StylesHelper;
use MailPoet\Util\pQuery\pQuery;
use MailPoetVendor\CSS;

class Footer {
  public function render($element) {
    $element['text'] = preg_replace('/\n/', '<br />', $element['text']);
    $element['text'] = preg_replace('/(<\/?p.*?>)/i', '', $element['text']);
    $lineHeight = sprintf(
      '%spx',
      StylesHelper::$defaultLineHeight * (int)$element['styles']['text']['fontSize']
    );
    if (!is_string($element['text'])) {
      throw new \RuntimeException('$element[\'text\'] should be a string.');
    }
    $dOMParser = new pQuery();
    $DOM = $dOMParser->parseStr($element['text']);
    if (isset($element['styles']['link'])) {
      $links = $DOM->query('a');
      if ($links->count()) {
        $css = new CSS();
        foreach ($links as $link) {
          $elementLinkStyles = StylesHelper::getStyles($element['styles'], 'link');
          $link->style = $css->mergeInlineStyles($elementLinkStyles, $link->style);
        }
      }
    }
    $backgroundColor = $element['styles']['block']['backgroundColor'];
    $backgroundColor = ($backgroundColor !== 'transparent') ?
      'bgcolor="' . $backgroundColor . '"' :
      false;
    if (!$backgroundColor) unset($element['styles']['block']['backgroundColor']);
    $style = 'line-height: ' . $lineHeight . ';' . StylesHelper::getBlockStyles($element) . StylesHelper::getStyles($element['styles'], 'text');
    $style = EHelper::escapeHtmlStyleAttr($style);
    $template = '
      <tr>
        <td class="mailpoet_header_footer_padded mailpoet_footer" ' . $backgroundColor . ' style="' . $style . '">
          ' . str_replace('&', '&amp;', $DOM->html()) . '
        </td>
      </tr>';
    return $template;
  }
}
