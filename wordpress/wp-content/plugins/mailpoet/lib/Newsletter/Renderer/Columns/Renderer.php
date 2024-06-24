<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter\Renderer\Columns;

if (!defined('ABSPATH')) exit;


use MailPoet\Newsletter\Renderer\EscapeHelper as EHelper;

class Renderer {
  public function render($contentBlock, $columnsData) {
    if (is_null($contentBlock['blocks']) && isset($contentBlock['type'])) {
      return "<!-- Skipped unsupported block type: {$contentBlock['type']} -->";
    }

    $columnsCount = count($contentBlock['blocks']);

    if ($columnsCount === 1) {
      return $this->renderOneColumn($contentBlock, $columnsData[0]);
    }
    return $this->renderMultipleColumns($contentBlock, $columnsData);
  }

  private function renderOneColumn($contentBlock, $content) {
    $template = $this->getOneColumnTemplate(
      $contentBlock['styles']['block'],
      isset($contentBlock['image']) ? $contentBlock['image'] : null
    );
    return $template['content_start'] . $content . $template['content_end'];
  }

  public function getOneColumnTemplate($styles, $image) {
    $backgroundCss = $this->getBackgroundCss($styles, $image);
    $template['content_start'] = '
      <tr>
        <td class="mailpoet_content" align="center" style="border-collapse:collapse;' . $backgroundCss . '" ' . $this->getBgColorAttribute($styles, $image) . '>
          <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-spacing:0;mso-table-lspace:0;mso-table-rspace:0">
            <tbody>
              <tr>
                <td style="padding-left:0;padding-right:0">
                  <table width="100%" border="0" cellpadding="0" cellspacing="0" class="mailpoet_' . ColumnsHelper::columnClass(1) . '" style="border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0;">
                    <tbody>';
    $template['content_end'] = '
                    </tbody>
                  </table>
                </td>
              </tr>
            </tbody>
          </table>
        </td>
      </tr>';
    return $template;
  }

  private function renderMultipleColumns($contentBlock, $columnsData) {
    $columnsCount = count($contentBlock['blocks']);
    $columnsLayout = isset($contentBlock['columnLayout']) ? $contentBlock['columnLayout'] : null;

    $widths = ColumnsHelper::columnWidth($columnsCount, $columnsLayout);
    $class = ColumnsHelper::columnClass($columnsCount);
    $alignment = ColumnsHelper::columnAlignment($columnsCount);
    $index = 0;
    $result = $this->getMultipleColumnsContainerStart($class, $contentBlock['styles']['block'], isset($contentBlock['image']) ? $contentBlock['image'] : null);
    foreach ($columnsData as $content) {
      $result .= $this->getMultipleColumnsContentStart($widths[$index++], $alignment, $class);
      $result .= $content;
      $result .= $this->getMultipleColumnsContentEnd();
    }
    $result .= $this->getMultipleColumnsContainerEnd();
    return $result;
  }

  private function getMultipleColumnsContainerStart($class, $styles, $image) {
    return '
      <tr>
        <td class="mailpoet_content-' . $class . '" align="left" style="border-collapse:collapse;' . $this->getBackgroundCss($styles, $image) . '" ' . $this->getBgColorAttribute($styles, $image) . '>
          <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-spacing:0;mso-table-lspace:0;mso-table-rspace:0">
            <tbody>
              <tr>
                <td align="center" style="font-size:0;"><!--[if mso]>
                  <table border="0" width="100%" cellpadding="0" cellspacing="0">
                    <tbody>
                      <tr>';
  }

  private function getMultipleColumnsContainerEnd() {
    return '
                  </tr>
                </tbody>
              </table>
            <![endif]--></td>
            </tr>
          </tbody>
        </table>
      </td>
    </tr>';
  }

  private function getMultipleColumnsContentEnd() {
    return '
            </tbody>
          </table>
        </div><!--[if mso]>
      </td>';
  }

  public function getMultipleColumnsContentStart($width, $alignment, $class) {
    return '
      <td width="' . $width . '" valign="top">
        <![endif]--><div style="display:inline-block; max-width:' . $width . 'px; vertical-align:top; width:100%;">
          <table width="' . $width . '" class="mailpoet_' . $class . '" border="0" cellpadding="0" cellspacing="0" align="' . $alignment . '" style="width:100%;max-width:' . $width . 'px;border-spacing:0;mso-table-lspace:0;mso-table-rspace:0;table-layout:fixed;margin-left:auto;margin-right:auto;padding-left:0;padding-right:0;">
            <tbody>';
  }

  private function getBackgroundCss($styles, $image) {
    if ($image !== null && $image['src'] !== null) {
      $backgroundColor = isset($styles['backgroundColor']) && $styles['backgroundColor'] !== 'transparent' ? $styles['backgroundColor'] : '#ffffff';
      $repeat = $image['display'] === 'tile' ? 'repeat' : 'no-repeat';
      $size = $image['display'] === 'scale' ? 'cover' : 'contain';
      $style = sprintf(
        'background: %s url(%s) %s center/%s;background-color: %s;background-image: url(%s);background-repeat: %s;background-position: center;background-size: %s;',
        $backgroundColor,
        $image['src'],
        $repeat,
        $size,
        $backgroundColor,
        $image['src'],
        $repeat,
        $size
      );
      return EHelper::escapeHtmlStyleAttr($style);
    } else {
      if (!isset($styles['backgroundColor'])) return false;
      $backgroundColor = $styles['backgroundColor'];
      return ($backgroundColor !== 'transparent') ?
        EHelper::escapeHtmlStyleAttr(sprintf('background-color:%s!important;', $backgroundColor)) :
        false;
    }
  }

  private function getBgColorAttribute($styles, $image) {
    if (
      ($image === null || $image['src'] === null)
      && isset($styles['backgroundColor'])
      && $styles['backgroundColor'] !== 'transparent'
    ) {
      return 'bgcolor="' . EHelper::escapeHtmlAttr($styles['backgroundColor']) . '"';
    }
    return null;
  }
}
