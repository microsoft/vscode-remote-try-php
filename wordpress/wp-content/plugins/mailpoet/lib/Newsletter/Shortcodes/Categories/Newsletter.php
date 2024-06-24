<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter\Shortcodes\Categories;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\SendingQueueEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Newsletter\NewslettersRepository;
use MailPoet\WP\Functions as WPFunctions;
use MailPoet\WP\Posts as WPPosts;

class Newsletter implements CategoryInterface {
  /** @var NewslettersRepository */
  private $newslettersRepository;

  private WPFunctions $wp;

  public function __construct(
    NewslettersRepository $newslettersRepository,
    WPFunctions $wp
  ) {
    $this->newslettersRepository = $newslettersRepository;
    $this->wp = $wp;
  }

  public function process(
    array $shortcodeDetails,
    NewsletterEntity $newsletter = null,
    SubscriberEntity $subscriber = null,
    SendingQueueEntity $queue = null,
    string $content = '',
    bool $wpUserPreview = false
  ): ?string {
    switch ($shortcodeDetails['action']) {
      case 'subject':
        return ($newsletter instanceof NewsletterEntity) ? $newsletter->getSubject() : null;

      case 'total':
        return (string)substr_count($content, 'data-post-id');

      case 'post_title':
        preg_match_all('/data-post-id="(\d+)"/ism', $content, $posts);
        $postIds = array_unique($posts[1]);
        $latestPost = (!empty($postIds)) ? $this->getLatestWPPost($postIds) : null;
        if ($latestPost) {
          // When a user with role author publish a post containing "&" in the title, the character is saved as "&amp;" in the database.
          // Removing HTML tags from the title because
          $title = $this->wp->wpStripAllTags($latestPost['post_title']);
          // Decoding special characters such as &amp; to &, etc.
          return htmlspecialchars_decode($title);
        }
        return null;

      case 'number':
        if (!($newsletter instanceof NewsletterEntity)) return null;
        if ($newsletter->getType() !== NewsletterEntity::TYPE_NOTIFICATION_HISTORY) {
          return null;
        }
        $sentNewsletters = $this->newslettersRepository->countBy([
          'parent' => $newsletter->getParent(),
          'status' => NewsletterEntity::STATUS_SENT,
        ]);
        return (string)++$sentNewsletters;

      default:
        return null;
    }
  }

  public function ensureConsistentQueryType(\WP_Query $query) {
    // Queries with taxonomies are autodetected as 'is_archive=true' and 'is_home=false'
    // while queries without them end up being 'is_archive=false' and 'is_home=true'.
    // This is to fix that by always enforcing constistent behavior.
    $query->is_archive = true; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    $query->is_home = false; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
  }

  private function getLatestWPPost($postIds) {
    // set low priority to execute 'ensureConstistentQueryType' before any other filter
    $filterPriority = defined('PHP_INT_MIN') ? constant('PHP_INT_MIN') : ~PHP_INT_MAX;
    $this->wp->addAction('pre_get_posts', [$this, 'ensureConsistentQueryType'], $filterPriority);
    $posts = new \WP_Query(
      [
        'post_type' => WPPosts::getTypes(),
        'post__in' => $postIds,
        'posts_per_page' => 1,
        'ignore_sticky_posts' => true,
        'orderby' => 'post_date',
        'order' => 'DESC',
      ]
    );
    $this->wp->removeAction('pre_get_posts', [$this, 'ensureConsistentQueryType'], $filterPriority);
    return (!empty($posts->posts[0])) && ($posts->posts[0] instanceof \WP_Post) ?
      $posts->posts[0]->to_array() :
      false;
  }
}
