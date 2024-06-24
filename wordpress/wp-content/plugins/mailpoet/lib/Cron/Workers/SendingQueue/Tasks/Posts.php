<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Cron\Workers\SendingQueue\Tasks;

if (!defined('ABSPATH')) exit;


use MailPoet\DI\ContainerWrapper;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\NewsletterPostEntity;
use MailPoet\Logging\LoggerFactory;
use MailPoet\Newsletter\NewsletterPostsRepository;

class Posts {
  /** @var LoggerFactory */
  private $loggerFactory;

  /** @var NewsletterPostsRepository */
  private $newsletterPostRepository;

  public function __construct() {
    $this->loggerFactory = LoggerFactory::getInstance();
    $this->newsletterPostRepository = ContainerWrapper::getInstance()->get(NewsletterPostsRepository::class);
  }

  public function extractAndSave($renderedNewsletter, NewsletterEntity $newsletter): bool {
    if ($newsletter->getType() !== NewsletterEntity::TYPE_NOTIFICATION_HISTORY) {
      return false;
    }
    $this->loggerFactory->getLogger(LoggerFactory::TOPIC_POST_NOTIFICATIONS)->info(
      'extract and save posts - before',
      ['newsletter_id' => $newsletter->getId()]
    );
    preg_match_all(
      '/data-post-id="(\d+)"/ism',
      $renderedNewsletter['html'],
      $matchedPostsIds
    );
    $matchedPostsIds = $matchedPostsIds[1];
    if (!count($matchedPostsIds)) {
      return false;
    }
    $parent = $newsletter->getParent(); // parent post notification
    if (!$parent instanceof NewsletterEntity) {
      $this->loggerFactory->getLogger(LoggerFactory::TOPIC_POST_NOTIFICATIONS)->info(
        'parent post has not been found',
        ['newsletter_id' => $newsletter->getId()]
      );
      return false;
    }
    foreach ($matchedPostsIds as $postId) {
      $newsletterPost = new NewsletterPostEntity($parent, $postId);
      $this->newsletterPostRepository->persist($newsletterPost);
    }
    $this->newsletterPostRepository->flush();
    $this->loggerFactory->getLogger(LoggerFactory::TOPIC_POST_NOTIFICATIONS)->info(
      'extract and save posts - after',
      ['newsletter_id' => $newsletter->getId(), 'matched_posts_ids' => $matchedPostsIds]
    );
    return true;
  }

  public function getAlcPostsCount($renderedNewsletter, NewsletterEntity $newsletter) {
    $templatePostsCount = substr_count($newsletter->getContent(), 'data-post-id');
    $newsletterPostsCount = substr_count($renderedNewsletter['html'], 'data-post-id');
    return $newsletterPostsCount - $templatePostsCount;
  }
}
