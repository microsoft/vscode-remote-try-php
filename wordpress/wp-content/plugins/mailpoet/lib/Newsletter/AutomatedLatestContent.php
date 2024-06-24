<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter;

if (!defined('ABSPATH')) exit;


use MailPoet\Logging\LoggerFactory;
use MailPoet\Newsletter\Editor\Transformer;
use MailPoet\WP\Functions as WPFunctions;

class AutomatedLatestContent {

  /** @var LoggerFactory */
  private $loggerFactory;

  /** @var int|false */
  private $newsletterId;

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    LoggerFactory $loggerFactory,
    WPFunctions $wp
  ) {
    $this->loggerFactory = $loggerFactory;
    $this->wp = $wp;
  }

  public function filterOutSentPosts(string $where): string {
    $sentPostsQuery = 'SELECT ' . MP_NEWSLETTER_POSTS_TABLE . '.post_id FROM '
      . MP_NEWSLETTER_POSTS_TABLE . ' WHERE '
      . MP_NEWSLETTER_POSTS_TABLE . ".newsletter_id='" . $this->newsletterId . "'";

    $wherePostUnsent = 'ID NOT IN (' . $sentPostsQuery . ')';

    if (!empty($where)) $wherePostUnsent = ' AND ' . $wherePostUnsent;

    return $where . $wherePostUnsent;
  }

  public function ensureConsistentQueryType(\WP_Query $query) {
    // Queries with taxonomies are autodetected as 'is_archive=true' and 'is_home=false'
    // while queries without them end up being 'is_archive=false' and 'is_home=true'.
    // This is to fix that by always enforcing constistent behavior.
    $query->is_archive = true; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    $query->is_home = false; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
  }

  public function getPosts(BlockPostQuery $query) {
    $this->newsletterId = $query->newsletterId;
    // Get posts as logged out user, so private posts hidden by other plugins (e.g. UAM) are also excluded
    $currentUserId = $this->wp->getCurrentUserId();
    // phpcs:ignore Generic.PHP.ForbiddenFunctions.Discouraged
    wp_set_current_user(0);

    $this->loggerFactory->getLogger(LoggerFactory::TOPIC_POST_NOTIFICATIONS)->info(
      'loading automated latest content',
      [
        'args' => $query->args,
        'posts_to_exclude' => $query->postsToExclude,
        'newsletter_id' => $query->newsletterId,
        'newer_than_timestamp' => $query->newerThanTimestamp,
      ]
    );

    // set low priority to execute 'ensureConstistentQueryType' before any other filter
    $filterPriority = defined('PHP_INT_MIN') ? constant('PHP_INT_MIN') : ~PHP_INT_MAX;
    $this->wp->addAction('pre_get_posts', [$this, 'ensureConsistentQueryType'], $filterPriority);
    $this->_attachSentPostsFilter($query->newsletterId);
    $parameters = $query->getQueryParams();
    $this->loggerFactory->getLogger(LoggerFactory::TOPIC_POST_NOTIFICATIONS)->info(
      'getting automated latest content',
      ['parameters' => $parameters]
    );
    $posts = $this->wp->getPosts($parameters);
    $this->logPosts($posts);

    $this->wp->removeAction('pre_get_posts', [$this, 'ensureConsistentQueryType'], $filterPriority);
    $this->_detachSentPostsFilter($query->newsletterId);
    // phpcs:ignore Generic.PHP.ForbiddenFunctions.Discouraged
    wp_set_current_user($currentUserId);
    return $posts;
  }

  public function transformPosts($args, $posts) {
    $transformer = new Transformer($args);
    return $transformer->transform($posts);
  }

  private function _attachSentPostsFilter($newsletterId) {
    if ($newsletterId > 0) {
      $this->wp->addAction('posts_where', [$this, 'filterOutSentPosts']);
    }
  }

  private function _detachSentPostsFilter($newsletterId) {
    if ($newsletterId > 0) {
      $this->wp->removeAction('posts_where', [$this, 'filterOutSentPosts']);
    }
  }

  private function logPosts(array $posts) {
    $postsToLog = [];
    foreach ($posts as $post) {
      $postsToLog[] = [
        'id' => $post->ID,
        'post_date' => $post->post_date, // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
      ];
    }
    $this->loggerFactory->getLogger(LoggerFactory::TOPIC_POST_NOTIFICATIONS)->info(
      'automated latest content loaded posts',
      ['posts' => $postsToLog]
    );
  }
}
