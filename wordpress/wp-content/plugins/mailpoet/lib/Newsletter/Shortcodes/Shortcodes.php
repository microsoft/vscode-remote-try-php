<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter\Shortcodes;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\SendingQueueEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Newsletter\Shortcodes\Categories\CategoryInterface;
use MailPoet\Newsletter\Shortcodes\Categories\Date;
use MailPoet\Newsletter\Shortcodes\Categories\Link;
use MailPoet\Newsletter\Shortcodes\Categories\Newsletter;
use MailPoet\Newsletter\Shortcodes\Categories\Site;
use MailPoet\Newsletter\Shortcodes\Categories\Subscriber;
use MailPoet\WP\Functions as WPFunctions;

class Shortcodes {
  /** @var NewsletterEntity|null */
  private $newsletter;

  /** @var SubscriberEntity|null */
  private $subscriber;

  /** @var SendingQueueEntity|null */
  private $queue;

  /** @var bool */
  private $wpUserPreview = false;

  /** @var Date */
  private $dateCategory;

  /** @var Link */
  private $linkCategory;

  /** @var Newsletter */
  private $newsletterCategory;

  /** @var Subscriber */
  private $subscriberCategory;

  /** @var Site */
  private $siteCategory;

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    Date $dateCategory,
    Link $linkCategory,
    Newsletter $newsletterCategory,
    Subscriber $subscriberCategory,
    Site $siteCategory,
    WPFunctions $wp
  ) {
    $this->dateCategory = $dateCategory;
    $this->linkCategory = $linkCategory;
    $this->newsletterCategory = $newsletterCategory;
    $this->subscriberCategory = $subscriberCategory;
    $this->siteCategory = $siteCategory;
    $this->wp = $wp;
  }

  public function setNewsletter(NewsletterEntity $newsletter = null): void {
    $this->newsletter = $newsletter;
  }

  public function setSubscriber(SubscriberEntity $subscriber = null): void {
    $this->subscriber = $subscriber;
  }

  public function setQueue(SendingQueueEntity $queue = null): void {
    $this->queue = $queue;
  }

  public function setWpUserPreview(bool $wpUserPreview): void {
    $this->wpUserPreview = $wpUserPreview;
  }

  public function extract($content, $categories = false) {
    $categories = (is_array($categories)) ? implode('|', $categories) : false;
    // match: [category:shortcode] or [category|category|...:shortcode]
    // dot not match: [category://shortcode] - avoids matching http/ftp links
    $regex = sprintf(
      '/\[%s:(?!\/\/).*?\]/i',
      ($categories) ? '(?:' . $categories . ')' : '(?:\w+)'
    );
    preg_match_all($regex, (string)$content, $shortcodes);
    $shortcodes = $shortcodes[0];
    return (count($shortcodes)) ?
      array_values(array_unique($shortcodes)) :
      false;
  }

  /**
   * Parse a MailPoet-style shortcode.
   * The syntax is [category:action | argument:argument_value], it can have a single argument.
   */
  public function match($shortcode) {
    preg_match(
      '/\[(?P<category>\w+)?:(?P<action>\w+)(?:.*?\|.*?(?P<argument>\w+):(?P<argument_value>.*?))?\]/',
      $shortcode,
      $match
    );
    // If argument exists, copy it to the arguments array
    if (!empty($match['argument'])) {
      $match['arguments'] = [$match['argument'] => isset($match['argument_value']) ? $match['argument_value'] : ''];
    }
    return $match;
  }

  /**
   * Parse a WordPress-style shortcode.
   * The syntax is [category:action arg1="value1" arg2="value2"], it can have multiple arguments.
   */
  public function matchWPShortcode($shortcode) {
    $atts = $this->wp->shortcodeParseAtts(trim($shortcode, '[]/'));
    if (empty($atts[0])) {
      return [];
    }
    $shortcodeName = $atts[0];
    list($category, $action) = explode(':', $shortcodeName);
    $shortcodeDetails = [];
    $shortcodeDetails['category'] = $category;
    $shortcodeDetails['action'] = $action;
    $shortcodeDetails['arguments'] = [];
    foreach ($atts as $attrName => $attrValue) {
      if (is_numeric($attrName)) {
        continue; // Skip unnamed attributes
      }
      $shortcodeDetails['arguments'][$attrName] = $attrValue;
      // Make a shortcut to the first argument
      if (!isset($shortcodeDetails['argument'])) {
        $shortcodeDetails['argument'] = $attrName;
        $shortcodeDetails['argument_value'] = $attrValue;
      }
    }
    return $shortcodeDetails;
  }

  public function process($shortcodes, $content = '') {
    $processedShortcodes = [];
    foreach ($shortcodes as $shortcode) {
      $shortcodeDetails = $this->match($shortcode);
      if (empty($shortcodeDetails)) {
        // Wrong MailPoet shortcode syntax, try to parse as a native WP shortcode
        $shortcodeDetails = $this->matchWPShortcode($shortcode);
      }
      $shortcodeDetails['shortcode'] = $shortcode;
      $shortcodeDetails['category'] = !empty($shortcodeDetails['category']) ?
        $shortcodeDetails['category'] :
        '';
      $shortcodeDetails['action'] = !empty($shortcodeDetails['action']) ?
        $shortcodeDetails['action'] :
        '';
      $shortcodeDetails['action_argument'] = !empty($shortcodeDetails['argument']) ?
        $shortcodeDetails['argument'] :
        '';
      $shortcodeDetails['action_argument_value'] = !empty($shortcodeDetails['argument_value']) ?
        $shortcodeDetails['argument_value'] :
        '';
      $shortcodeDetails['arguments'] = !empty($shortcodeDetails['arguments']) ?
        $shortcodeDetails['arguments'] : [];

      $category = strtolower($shortcodeDetails['category']);
      $categoryClass = $this->getCategoryObject($category);
      if ($categoryClass instanceof CategoryInterface) {
        $processedShortcodes[] = $categoryClass->process(
          $shortcodeDetails,
          $this->newsletter,
          $this->subscriber,
          $this->queue,
          $content,
          $this->wpUserPreview
        );
      } else {
        $customShortcode = $this->wp->applyFilters(
          'mailpoet_newsletter_shortcode',
          $shortcode,
          $this->newsletter,
          $this->subscriber,
          $this->queue,
          $content,
          $shortcodeDetails['arguments'],
          $this->wpUserPreview
        );
        $processedShortcodes[] = ($customShortcode === $shortcode) ?
          false :
          $customShortcode;
      }

    }
    return $processedShortcodes;
  }

  public function replace($content, $contentSource = null, $categories = null) {
    $shortcodes = $this->extract($content, $categories);
    if (!$shortcodes) {
      return $content;
    }
    // if content contains only shortcodes (e.g., [newsletter:post_title]) but their processing
    // depends on some other content (e.g., "post_id" inside a rendered newsletter),
    // then we should use that content source when processing shortcodes
    $processedShortcodes = $this->process(
      $shortcodes,
      ($contentSource) ? $contentSource : $content
    );
    return str_replace($shortcodes, $processedShortcodes, $content);
  }

  private function getCategoryObject($category): ?CategoryInterface {
    if ($category === 'link') {
      return $this->linkCategory;
    } elseif ($category === 'date') {
      return $this->dateCategory;
    } elseif ($category === 'newsletter') {
      return $this->newsletterCategory;
    } elseif ($category === 'subscriber') {
      return $this->subscriberCategory;
    } elseif ($category === 'site') {
      return $this->siteCategory;
    }
    return null;
  }
}
