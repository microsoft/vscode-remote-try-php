<?php declare(strict_types = 1);

namespace MailPoet\EmailEditor\Integrations\Core\Renderer\Blocks;

if (!defined('ABSPATH')) exit;


use MailPoet\EmailEditor\Engine\SettingsController;
use MailPoet\EmailEditor\Integrations\Utils\DomDocumentHelper;

class Image extends AbstractBlockRenderer {
  protected function renderContent($blockContent, array $parsedBlock, SettingsController $settingsController): string {
    $parsedHtml = $this->parseBlockContent($blockContent);

    if (!$parsedHtml) {
      return '';
    }

    $imageUrl = $parsedHtml['imageUrl'];
    $image = $parsedHtml['image'];
    $caption = $parsedHtml['caption'];

    $parsedBlock = $this->addImageSizeWhenMissing($parsedBlock, $imageUrl, $settingsController);
    $image = $this->addImageDimensions($image, $parsedBlock, $settingsController);
    $image = $this->applyImageBorderStyle($image, $parsedBlock, $settingsController);
    $image = $this->applyRoundedStyle($image, $parsedBlock);

    return str_replace(
      ['{image_content}', '{caption_content}'],
      [$image, $caption],
      $this->getBlockWrapper($parsedBlock, $settingsController)
    );
  }

  private function applyRoundedStyle(string $blockContent, array $parsedBlock): string {
    // Because the isn't an attribute for definition of rounded style, we have to check the class name
    if (isset($parsedBlock['attrs']['className']) && strpos($parsedBlock['attrs']['className'], 'is-style-rounded') !== false) {
      // If the image should be in a circle, we need to set the border-radius to 9999px to make it the same as is in the editor
      // This style cannot be applied on the wrapper, and we need to set it directly on the image
      $blockContent = $this->removeStyleAttributeFromElement($blockContent, ['tag_name' => 'img'], 'border-radius');
      $blockContent = $this->addStyleToElement($blockContent, ['tag_name' => 'img'], 'border-radius: 9999px;');
    }

    return $blockContent;
  }

  /**
   * When the width is not set, it's important to get it for the image to be displayed correctly
   */
  private function addImageSizeWhenMissing(array $parsedBlock, string $imageUrl, SettingsController $settingsController): array {
    if (!isset($parsedBlock['attrs']['width'])) {
      $maxWidth = $settingsController->parseNumberFromStringWithPixels($parsedBlock['email_attrs']['width'] ?? SettingsController::EMAIL_WIDTH);
      $imageSize = wp_getimagesize($imageUrl);
      $imageSize = $imageSize ? $imageSize[0] : $maxWidth;
      // Because width is primarily used for the max-width property, we need to add the left and right border width to it
      $borderWidth = $parsedBlock['attrs']['style']['border']['width'] ?? '0px';
      $borderLeftWidth = $parsedBlock['attrs']['style']['border']['left']['width'] ?? $borderWidth;
      $borderRightWidth = $parsedBlock['attrs']['style']['border']['right']['width'] ?? $borderWidth;
      $width = min($imageSize, $maxWidth);
      $width += $settingsController->parseNumberFromStringWithPixels($borderLeftWidth ?? '0px');
      $width += $settingsController->parseNumberFromStringWithPixels($borderRightWidth ?? '0px');
      $parsedBlock['attrs']['width'] = "{$width}px";
    }
    return $parsedBlock;
  }

  private function applyImageBorderStyle(string $blockContent, array $parsedBlock, SettingsController $settingsController): string {
    // Getting individual border properties
    $borderStyles = wp_style_engine_get_styles(['border' => $parsedBlock['attrs']['style']['border'] ?? []]);
    $borderStyles = $borderStyles['declarations'] ?? [];
    if (!empty($borderStyles)) {
      $borderStyles['border-style'] = 'solid';
      $borderStyles['box-sizing'] = 'border-box';
    }

    return $this->addStyleToElement($blockContent, ['tag_name' => 'img'], \WP_Style_Engine::compile_css($borderStyles, ''));
  }

  /**
   * Settings width and height attributes for images is important for MS Outlook.
   */
  private function addImageDimensions($blockContent, array $parsedBlock, SettingsController $settingsController): string {
    $html = new \WP_HTML_Tag_Processor($blockContent);
    if ($html->next_tag(['tag_name' => 'img'])) {
      // Getting height from styles and if it's set, we set the height attribute
      $styles = $html->get_attribute('style') ?? '';
      $styles = $settingsController->parseStylesToArray($styles);
      $height = $styles['height'] ?? null;
      if ($height && $height !== 'auto' && is_numeric($settingsController->parseNumberFromStringWithPixels($height))) {
        $height = $settingsController->parseNumberFromStringWithPixels($height);
        $html->set_attribute('height', esc_attr($height));
      }

      if (isset($parsedBlock['attrs']['width'])) {
        $width = $settingsController->parseNumberFromStringWithPixels($parsedBlock['attrs']['width']);
        $html->set_attribute('width', esc_attr($width));
      }
      $blockContent = $html->get_updated_html();
    }

    return $blockContent;
  }

  /**
   * This method configure the font size of the caption because it's set to 0 for the parent element to avoid unexpected white spaces
   * We try to use font-size passed down from the parent element $parsedBlock['email_attrs']['font-size'], but if it's not set, we use the default font-size from the email theme.
   */
  private function getCaptionStyles(SettingsController $settingsController, array $parsedBlock): string {
    $themeData = $settingsController->getTheme()->get_data();

    $styles = [
      'text-align' => 'center',
    ];

    $styles['font-size'] = $parsedBlock['email_attrs']['font-size'] ?? $themeData['styles']['typography']['fontSize'];
    return \WP_Style_Engine::compile_css($styles, '');
  }

  /**
   * Based on MJML <mj-image> but because MJML doesn't support captions, our solution is a bit different
   */
  private function getBlockWrapper(array $parsedBlock, SettingsController $settingsController): string {
    $styles = [
      'border-collapse' => 'collapse',
      'border-spacing' => '0px',
      'font-size' => '0px',
      'vertical-align' => 'top',
      'width' => '100%',
    ];

    // When the image is not aligned, the wrapper is set to 100% width due to caption that can be longer than the image
    $wrapperWidth = isset($parsedBlock['attrs']['align']) ? ($parsedBlock['attrs']['width'] ?? '100%') : '100%';
    $wrapperStyles = $styles;
    $wrapperStyles['width'] = $wrapperWidth;

    $captionStyles = $this->getCaptionStyles($settingsController, $parsedBlock);

    $styles['width'] = '100%';
    $align = $parsedBlock['attrs']['align'] ?? 'left';

    return '
      <table
        role="presentation"
        border="0"
        cellpadding="0"
        cellspacing="0"
        style="' . esc_attr(\WP_Style_Engine::compile_css($styles, '')) . '"
        width="100%"
      >
        <tr>
          <td align="' . esc_attr($align) . '">
            <table
              role="presentation"
              border="0"
              cellpadding="0"
              cellspacing="0"
              style="' . esc_attr(\WP_Style_Engine::compile_css($wrapperStyles, '')) . '"
              width="' . esc_attr($wrapperWidth) . '"
            >
              <tr>
                <td>{image_content}</td>
              </tr>
              <tr>
                <td style="' . esc_attr($captionStyles) . '">{caption_content}</td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    ';
  }

  /**
   * @param array{tag_name: string, class_name?: string} $tag
   * @param string $style
   */
  private function addStyleToElement($blockContent, array $tag, string $style): string {
    $html = new \WP_HTML_Tag_Processor($blockContent);
    if ($html->next_tag($tag)) {
      $elementStyle = $html->get_attribute('style') ?? '';
      $elementStyle = !empty($elementStyle) ? (rtrim($elementStyle, ';') . ';') : ''; // Adding semicolon if it's missing
      $elementStyle .= $style;
      $html->set_attribute('style', esc_attr($elementStyle));
      $blockContent = $html->get_updated_html();
    }

    return $blockContent;
  }

  /**
   * @param array{tag_name: string, class_name?: string} $tag
   */
  private function removeStyleAttributeFromElement($blockContent, array $tag, string $styleName): string {
    $html = new \WP_HTML_Tag_Processor($blockContent);
    if ($html->next_tag($tag)) {
      $elementStyle = $html->get_attribute('style') ?? '';
      $elementStyle = preg_replace('/' . $styleName . ':(.?[0-9]+px)+;?/', '', $elementStyle);
      $html->set_attribute('style', esc_attr($elementStyle));
      $blockContent = $html->get_updated_html();
    }

    return $blockContent;
  }

  /**
   * @param string $blockContent
   * @return array{imageUrl: string, image: string, caption: string}|null
   */
  private function parseBlockContent(string $blockContent): ?array {
    // If block's image is not set, we don't need to parse the content
    if (empty($blockContent)) {
      return null;
    }

    $domHelper = new DomDocumentHelper($blockContent);

    $figureTag = $domHelper->findElement('figure');
    if (!$figureTag) {
      return null;
    }

    $imgTag = $domHelper->findElement('img');
    if (!$imgTag) {
      return null;
    }

    $imageSrc = $domHelper->getAttributeValue($imgTag, 'src');
    $imageHtml = $domHelper->getOuterHtml($imgTag);

    $figcaption = $domHelper->findElement('figcaption');
    $figcaptionHtml = $figcaption ? $domHelper->getOuterHtml($figcaption) : '';
    $figcaptionHtml = str_replace(['<figcaption', '</figcaption>'], ['<span', '</span>'], $figcaptionHtml);


    return [
      'imageUrl' => $imageSrc ?: '',
      'image' => $imageHtml,
      'caption' => $figcaptionHtml ?: '',
    ];
  }
}
