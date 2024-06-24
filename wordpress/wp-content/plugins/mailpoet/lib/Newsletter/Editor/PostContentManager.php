<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter\Editor;

if (!defined('ABSPATH')) exit;


use MailPoet\Util\pQuery\pQuery;
use MailPoet\WooCommerce\Helper as WooCommerceHelper;
use MailPoet\WP\Functions as WPFunctions;

class PostContentManager {
  const WP_POST_CLASS = 'mailpoet_wp_post';

  public $maxExcerptLength = 60;

  /** @var WooCommerceHelper */
  private $woocommerceHelper;

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    WooCommerceHelper $woocommerceHelper = null
  ) {
    $this->wp = new WPFunctions;
    $this->maxExcerptLength = $this->wp->applyFilters('mailpoet_newsletter_post_excerpt_length', $this->maxExcerptLength);
    $this->woocommerceHelper = $woocommerceHelper ?: new WooCommerceHelper($this->wp);
  }

  public function getContent($post, $displayType) {
    if ($displayType === 'titleOnly') {
      return '';
    }
    if ($this->woocommerceHelper->isWooCommerceActive() && $this->wp->getPostType($post) === 'product') {
      $product = $this->woocommerceHelper->wcGetProduct($post->ID);
      if ($product) {
        return $this->getContentForProduct($product, $displayType);
      }
    }
    if ($displayType === 'excerpt') {
      if ($this->wp->hasExcerpt($post)) {
        return self::stripShortCodes($this->wp->getTheExcerpt($post));
      }
      return $this->generateExcerpt($post->post_content); // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    }
    return self::stripShortCodes($post->post_content); // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
  }

  public function filterContent($content, $displayType, $withPostClass = true) {
    $content = self::convertEmbeddedContent($content);

    // convert h4 h5 h6 to h3
    $content = preg_replace('/<([\/])?h[456](.*?)>/', '<$1h3$2>', $content);

    // convert currency signs
    $content = str_replace(
      ['$', '€', '£', '¥'],
      ['&#36;', '&euro;', '&pound;', '&#165;'],
      $content
    );

    // strip useless tags
    $tagsNotBeingStripped = [
      '<p>', '<em>', '<span>', '<b>', '<strong>', '<i>',
      '<a>', '<ul>', '<ol>', '<li>', '<br>', '<blockquote>',
    ];
    if ($displayType === 'full') {
      $tagsNotBeingStripped = array_merge($tagsNotBeingStripped, ['<figure>', '<img>', '<h1>', '<h2>', '<h3>', '<hr>']);
    }

    if (is_array($content)) {
      $content = implode(' ', $content);
    }

    $content = strip_tags($content, implode('', $tagsNotBeingStripped));
    if ($withPostClass) {
      $dOMParser = new pQuery();
      $DOM = $dOMParser->parseStr(WPFunctions::get()->wpautop($content));
      $paragraphs = $DOM->query('p');
      foreach ($paragraphs as $paragraph) {
        // We replace the class attribute to avoid conflicts in the newsletter editor
        $paragraph->removeAttr('class');
        $paragraph->addClass(self::WP_POST_CLASS);
      }
      $content = $DOM->__toString();
    } else {
      $content = WPFunctions::get()->wpautop($content);
    }
    $content = trim($content);

    return $content;
  }

  private function getContentForProduct($product, $displayType) {
    if ($displayType === 'excerpt') {
      return $product->get_short_description();
    }
    return $product->get_description();
  }

  private function generateExcerpt($content) {
    // remove image captions in gutenberg
    $content = preg_replace(
      "/<figcaption.*?>.*?<\/figcaption>/",
      '',
      $content
    );
    // remove image captions in classic posts
    $content = preg_replace(
      "/\[caption.*?\](.*?)\[\/caption\]/",
      '',
      $content
    );

    $content = self::stripShortCodes($content);

    // if excerpt is empty then try to find the "more" tag
    $excerpts = explode('<!--more-->', $content);
    if (count($excerpts) > 1) {
      // <!--more--> separator was present
      return $excerpts[0];
    } else {
      // Separator not present, try to shorten long posts
      return WPFunctions::get()->wpTrimWords($content, $this->maxExcerptLength, ' &hellip;');
    }
  }

  private function stripShortCodes($content) {
    // remove captions
    $content = preg_replace(
      "/\[caption.*?\](.*<\/a>)(.*?)\[\/caption\]/",
      '$1',
      $content
    );

    // remove other shortcodes
    $content = preg_replace('/\[[^\[\]]*\]/', '', $content);

    return $content;
  }

  private function convertEmbeddedContent($content = '') {
    // remove embedded video and replace with links
    $content = preg_replace(
      '#<iframe.*?src=\"(.+?)\".*><\/iframe>#',
      '<a href="$1">' . __('Click here to view media.', 'mailpoet') . '</a>',
      $content
    );

    // replace youtube links
    $content = preg_replace(
      '#http://www.youtube.com/embed/([a-zA-Z0-9_-]*)#Ui',
      'http://www.youtube.com/watch?v=$1',
      $content
    );

    return $content;
  }
}
