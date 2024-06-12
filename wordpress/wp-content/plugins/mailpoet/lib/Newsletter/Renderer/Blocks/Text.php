<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter\Renderer\Blocks;

if (!defined('ABSPATH')) exit;


use MailPoet\Newsletter\Editor\PostContentManager;
use MailPoet\Newsletter\Renderer\EscapeHelper as EHelper;
use MailPoet\Newsletter\Renderer\StylesHelper;
use MailPoet\Util\pQuery\pQuery;

class Text {
  public function render($element) {
    $html = $element['text'];
    // replace &nbsp; with spaces
    $html = str_replace('&nbsp;', ' ', $html);
    $html = str_replace('\xc2\xa0', ' ', $html);
    $html = $this->convertBlockquotesToTables($html);
    $html = $this->convertParagraphsToTables($html);
    $html = $this->styleLists($html);
    $html = $this->styleHeadings($html);
    $html = $this->removeLastLineBreak($html);
    $template = '
      <tr>
        <td class="mailpoet_text mailpoet_padded_vertical mailpoet_padded_side" valign="top" style="word-break:break-word;word-wrap:break-word;">
          ' . $html . '
        </td>
      </tr>';
    return $template;
  }

  public function convertBlockquotesToTables($html) {
    $dOMParser = new pQuery();
    $DOM = $dOMParser->parseStr($html);
    $blockquotes = $DOM->query('blockquote');
    foreach ($blockquotes as $blockquote) {
      $contents = [];
      $paragraphs = $blockquote->query('p, h1, h2, h3, h4', 0);
      foreach ($paragraphs as $index => $paragraph) {
        if (preg_match('/h\d/', $paragraph->getTag())) {
          $contents[] = $paragraph->getOuterText();
        } else {
          $contents[] = $paragraph->toString(true, true, 1);
        }
          if ($index + 1 < $paragraphs->count()) $contents[] = '<br />';
          $paragraph->remove();
      }
      if (empty($contents)) continue;
      $blockquote->setTag('table');
      $blockquote->addClass('mailpoet_blockquote');
      $blockquote->width = '100%';
      $blockquote->spacing = 0;
      $blockquote->border = 0;
      $blockquote->cellpadding = 0;
      $blockquote->html('
        <tbody>
          <tr>
            <td width="2" bgcolor="#565656"></td>
            <td width="10"></td>
            <td valign="top">
              <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-spacing:0;mso-table-lspace:0;mso-table-rspace:0">
                <tr>
                  <td class="mailpoet_blockquote">
                  ' . implode('', $contents) . '
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </tbody>');
      $blockquote = $this->insertLineBreak($blockquote);
    }
    return $DOM->__toString();
  }

  public function convertParagraphsToTables($html) {
    $dOMParser = new pQuery();
    $DOM = $dOMParser->parseStr($html);
    $paragraphs = $DOM->query('p');
    if (!$paragraphs->count()) return $html;
    foreach ($paragraphs as $paragraph) {
      // process empty paragraphs
      if (!trim($paragraph->html())) {
        $nextElement = ($paragraph->getNextSibling()) ?
          trim($paragraph->getNextSibling()->text()) :
          false;
        $previousElement = ($paragraph->getPreviousSibling()) ?
          trim($paragraph->getPreviousSibling()->text()) :
          false;
        $previousElementTag = ($previousElement) ?
          $paragraph->getPreviousSibling()->tag :
          false;
        // if previous or next paragraphs are empty OR previous paragraph
        // is a heading, insert a break line
        if (
          !$nextElement ||
          !$previousElement ||
          (preg_match('/h\d+/', $previousElementTag))
        ) {
          $paragraph = $this->insertLineBreak($paragraph);
        }
        $paragraph->remove();
        continue;
      }
      $style = (string)$paragraph->style;
      if (!preg_match('/text-align/i', $style)) {
        $style = 'text-align: left;' . $style;
      }
      $contents = $paragraph->toString(true, true, 1);
      $paragraph->setTag('table');
      $paragraph->style = 'border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;';
      $paragraph->width = '100%';
      $paragraph->cellpadding = 0;
      $nextElement = $paragraph->getNextSibling();
      // unless this is the last element in column, add double line breaks
      $lineBreaks = ($nextElement && !trim($nextElement->text())) ?
        '<br /><br />' :
        '';
      // if this element is followed by a list, add single line break
      $lineBreaks = ($nextElement && preg_match('/<li/i', $nextElement->getOuterText())) ?
        '<br />' :
        $lineBreaks;
      if ($paragraph->hasClass(PostContentManager::WP_POST_CLASS)) {
        $paragraph->removeClass(PostContentManager::WP_POST_CLASS);
        // if this element is followed by a paragraph or heading, add double line breaks
        $lineBreaks = ($nextElement && preg_match('/<(p|h[1-6]{1})/i', $nextElement->getOuterText())) ?
          '<br /><br />' :
          $lineBreaks;
      }
      $paragraph->html('
        <tr>
          <td class="mailpoet_paragraph" style="word-break:break-word;word-wrap:break-word;' . EHelper::escapeHtmlStyleAttr($style) . '">
            ' . $contents . $lineBreaks . '
          </td>
        </tr>');
    }
    return $DOM->__toString();
  }

  public function styleLists($html) {
    $dOMParser = new pQuery();
    $DOM = $dOMParser->parseStr($html);
    $lists = $DOM->query('ol, ul, li');
    if (!$lists->count()) return $html;
    foreach ($lists as $list) {
      if ($list->tag === 'li') {
        $list->setInnertext($list->toString(true, true, 1));
        $list->class = 'mailpoet_paragraph';
      } else {
        $list->class = 'mailpoet_paragraph';
        $list->style = StylesHelper::joinStyles($list->style, 'padding-top:0;padding-bottom:0;margin-top:10px;');
      }
      $list->style = StylesHelper::applyTextAlignment($list->style);
      $list->style = StylesHelper::joinStyles($list->style, 'margin-bottom:10px;');
      $list->style = EHelper::escapeHtmlStyleAttr($list->style);
    }
    return $DOM->__toString();
  }

  public function styleHeadings($html) {
    $dOMParser = new pQuery();
    $DOM = $dOMParser->parseStr($html);
    $headings = $DOM->query('h1, h2, h3, h4');
    if (!$headings->count()) return $html;
    foreach ($headings as $heading) {
      $heading->style = StylesHelper::applyTextAlignment($heading->style);
      $heading->style = StylesHelper::joinStyles($heading->style, 'padding:0;font-style:normal;font-weight:normal;');
      $heading->style = EHelper::escapeHtmlStyleAttr($heading->style);
    }
    return $DOM->__toString();
  }

  public function removeLastLineBreak($html) {
    return preg_replace('/(^)?(<br[^>]*?\/?>)+$/i', '', $html);
  }

  public function insertLineBreak($element) {
    $element->parent->insertChild(
      [
        'tag_name' => 'br',
        'self_close' => true,
        'attributes' => [],
      ],
      $element->index() + 1
    );
    return $element;
  }
}
