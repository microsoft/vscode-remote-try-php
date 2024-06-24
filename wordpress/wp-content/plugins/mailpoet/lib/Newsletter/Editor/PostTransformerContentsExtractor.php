<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter\Editor;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\Env;
use MailPoet\WooCommerce\Helper as WooCommerceHelper;
use MailPoet\WP\Functions as WPFunctions;

class PostTransformerContentsExtractor {

  private $args;

  /** @var WPFunctions */
  private $wp;

  /** @var WooCommerceHelper */
  private $woocommerceHelper;

  public function __construct(
    $args
  ) {
    $this->args = $args;
    $this->wp = new WPFunctions();
    $this->woocommerceHelper = new WooCommerceHelper($this->wp);
  }

  public function getContent($post, $withPostClass, $displayType) {
    $contentManager = new PostContentManager();
    $metaManager = new MetaInformationManager();

    $content = $contentManager->getContent($post, $this->args['displayType']);
    $content = $metaManager->appendMetaInformation($content, $post, $this->args);
    $content = $contentManager->filterContent($content, $displayType, $withPostClass);

    $structureTransformer = new StructureTransformer();
    $content = $structureTransformer->transform($content, $this->args['imageFullWidth'] === true);

    if ($this->isProduct($post)) {
      $content = $this->addProductDataToContent($content, $post);
    }

    $readMoreBtn = $this->getReadMoreButton($post);
    $blocksCount = count($content);
    if (!$readMoreBtn) {
      // Don't attach a button
    } else if ($readMoreBtn['type'] === 'text' && $blocksCount > 0 && $content[$blocksCount - 1]['type'] === 'text') {
      $content[$blocksCount - 1]['text'] .= $readMoreBtn['text'];
    } else {
      $content[] = $readMoreBtn;
    }
    return $content;
  }

  private function getImageInfo($id) {
    /*
     * In some cases wp_get_attachment_image_src ignore the second parameter
     * and use global variable $content_width value instead.
     * By overriding it ourselves when ensure a constant behaviour regardless
     * of the user setup.
     *
     * https://mailpoet.atlassian.net/browse/MAILPOET-1365
     */
    global $content_width; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps, default is NULL

    $contentWidthCopy = $content_width; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    $content_width = Env::NEWSLETTER_CONTENT_WIDTH; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    $imageInfo = $this->wp->wpGetAttachmentImageSrc($id, 'mailpoet_newsletter_max');
    $content_width = $contentWidthCopy; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

    return $imageInfo;
  }

  public function getFeaturedImage($post) {
    $postId = $post->ID;
    $postTitle = $this->sanitizeTitle($post->post_title); // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    $imageFullWidth = (bool)filter_var($this->args['imageFullWidth'], FILTER_VALIDATE_BOOLEAN);

    if (!has_post_thumbnail($postId)) {
      return false;
    }

    $thumbnailId = $this->wp->getPostThumbnailId($postId);
    $imageInfo = $this->getImageInfo($thumbnailId);

    // get alt text
    $altText = trim(strip_tags(get_post_meta(
      $thumbnailId,
      '_wp_attachment_image_alt',
      true
    )));
    if (strlen($altText) === 0) {
      // if the alt text is empty then use the post title
      $altText = trim(strip_tags($postTitle));
    }

    return [
      'type' => 'image',
      'link' => $this->wp->getPermalink($postId),
      'src' => $imageInfo[0],
      'alt' => $altText,
      'fullWidth' => $imageFullWidth,
      'width' => $imageInfo[1],
      'height' => $imageInfo[2],
      'styles' => [
        'block' => [
          'textAlign' => 'center',
        ],
      ],
    ];
  }

  private function getReadMoreButton($post) {
    if ($this->args['readMoreType'] === 'none') {
      return false;
    }

    if ($this->args['readMoreType'] === 'button') {
      $button = $this->args['readMoreButton'];
      $button['url'] = $this->wp->getPermalink($post->ID);
      return $button;
    }

    $readMoreText = sprintf(
      '<p><a href="%s">%s</a></p>',
      $this->wp->getPermalink($post->ID),
      $this->args['readMoreText']
    );

    return [
      'type' => 'text',
      'text' => $readMoreText,
    ];
  }

  public function getTitle($post) {
    $title = $post->post_title; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

    if (filter_var($this->args['titleIsLink'], FILTER_VALIDATE_BOOLEAN)) {
      $title = '<a href="' . $this->wp->getPermalink($post->ID) . '">' . $title . '</a>';
    }

    if (in_array($this->args['titleFormat'], ['h1', 'h2', 'h3'])) {
      $tag = $this->args['titleFormat'];
    } elseif ($this->args['titleFormat'] === 'ul') {
      $tag = 'li';
    } else {
      $tag = 'h1';
    }

    $alignment = (in_array($this->args['titleAlignment'], ['left', 'right', 'center'])) ? $this->args['titleAlignment'] : 'left';

    $title = '<' . $tag . ' data-post-id="' . $post->ID . '" style="text-align: ' . $alignment . ';">' . $title . '</' . $tag . '>';

    // The allowed HTML is based on all the possible ways we might construct a $title above
    $commonAttributes = [
      'data-post-id' => [],
      'style' => [],
    ];

    $allowedTitleHtml = [
      'a' => [
        'href' => [],
      ],
      'li' => $commonAttributes,
      'h1' => $commonAttributes,
      'h2' => $commonAttributes,
      'h3' => $commonAttributes,
    ];

    return [
      'type' => 'text',
      'text' => wp_kses($title, $allowedTitleHtml),
    ];
  }

  private function getPrice($post) {
    $price = null;
    $product = null;
    if ($this->woocommerceHelper->isWooCommerceActive()) {
      $product = $this->woocommerceHelper->wcGetProduct($post->ID);
    }
    if ($product) {
      $price = '<h2>' . strip_tags($product->get_price_html(), '<span><del>') . '</h2>';
    }
    return $price;
  }

  private function addProductDataToContent($content, $post) {
    if (!isset($this->args['pricePosition']) || $this->args['pricePosition'] === 'hidden') {
      return $content;
    }
    $price = $this->getPrice($post);
    $blocksCount = count($content);
    if ($blocksCount > 0 && $content[$blocksCount - 1]['type'] === 'text') {
      if ($this->args['pricePosition'] === 'below') {
        $content[$blocksCount - 1]['text'] = $content[$blocksCount - 1]['text'] . $price;
      } else {
        $content[$blocksCount - 1]['text'] = $price . $content[$blocksCount - 1]['text'];
      }
    } else {
      $content[] = [
        'type' => 'text',
        'text' => $price,
      ];
    }
    return $content;
  }

  public function isProduct($post) {
    return $post->post_type === 'product'; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
  }

  /**
   * Replaces double quote character with a unicode
   * alternative to avoid problems when inlining CSS.
   * [MAILPOET-1937]
   *
   * @param  string $title
   * @return string
   */
  private function sanitizeTitle($title) {
    return str_replace('"', '＂', $title);
  }
}
