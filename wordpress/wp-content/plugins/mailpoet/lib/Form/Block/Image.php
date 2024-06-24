<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Form\Block;

if (!defined('ABSPATH')) exit;


use MailPoet\Form\FormHtmlSanitizer;
use MailPoet\WP\Functions as WPFunctions;

class Image {
  /** @var WPFunctions */
  private $wp;

  /** @var FormHtmlSanitizer */
  private $htmlSanitizer;

  public function __construct(
    WPFunctions $wp,
    FormHtmlSanitizer $htmlSanitizer
  ) {
    $this->wp = $wp;
    $this->htmlSanitizer = $htmlSanitizer;
  }

  public function render(array $block): string {
    if (empty($block['params']['url'])) {
      return '';
    }
    return $this->wrapImage($block['params'], $this->renderImage($block['params']));
  }

  private function renderImage(array $params): string {
    $attributes = [];
    $styles = [];
    $attributes[] = 'src="' . $this->wp->escAttr($params['url']) . '"';
    $attributes[] = $params['alt'] ? 'alt="' . $this->wp->escAttr($params['alt']) . '"' : 'alt';
    if ($params['title']) {
      $attributes[] = 'title="' . $this->wp->escAttr($params['title']) . '"';
    }
    if ($params['id']) {
      $attributes[] = 'class="wp-image-' . $this->wp->escAttr($params['id']) . '"';
      $attributes[] = 'srcset="' . $this->wp->wpGetAttachmentImageSrcset(intval($params['id']), $params['size_slug']) . '"';
    }
    if ($params['width']) {
      $attributes[] = 'width=' . intval($params['width']);
      $styles[] = 'width: ' . intval($params['width']) . 'px';
    }
    if ($params['height']) {
      $attributes[] = 'height=' . intval($params['height']);
      $styles[] = 'height: ' . intval($params['height']) . 'px';
    }
    if ($styles) {
      $attributes[] = 'style="' . $this->wp->escAttr(implode(';', $styles)) . '"';
    }
    return '<img ' . implode(' ', $attributes) . '>';
  }

  private function wrapImage(array $params, string $img): string {
    // Figure
    $figureClasses = ['size-' . $params['size_slug']];
    if ($params['align']) {
      $figureClasses[] = 'align' . $params['align'];
    }
    // Link
    if ($params['href']) {
      $img = $this->wrapToLink($params, $img);
    }
    $caption = $params['caption'] ? "<figcaption>{$this->htmlSanitizer->sanitize($params['caption'])}</figcaption>" : '';
    $figure = '<figure class="' . $this->wp->escAttr(implode(' ', $figureClasses)) . '">' . $img . $caption . '</figure>';
    // Main wrapper
    $divClasses = ['mailpoet_form_image'];
    if (trim($params['class_name'])) {
      $divClasses[] = trim($params['class_name']);
    }
    return '<div class="' . $this->wp->escAttr(implode(' ', $divClasses)) . '">' . $figure . '</div>';
  }

  private function wrapToLink(array $params, string $img): string {
    $attributes = ['href="' . $this->wp->escAttr($params['href']) . '"'];
    if ($params['link_class']) {
      $attributes[] = 'class="' . $this->wp->escAttr($params['link_class']) . '"';
    }
    if ($params['link_target']) {
      $attributes[] = 'target="' . $this->wp->escAttr($params['link_target']) . '"';
    }
    if ($params['rel']) {
      $attributes[] = 'rel="' . $this->wp->escAttr($params['rel']) . '"';
    }
    return '<a ' . implode(' ', $attributes) . ' >' . $img . '</a>';
  }
}
