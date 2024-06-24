<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\WordPress\SubjectTransformers;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Subject;
use MailPoet\Automation\Engine\Integration\SubjectTransformer;
use MailPoet\Automation\Engine\WordPress;
use MailPoet\Automation\Integrations\WordPress\Subjects\CommentSubject;
use MailPoet\Automation\Integrations\WordPress\Subjects\PostSubject;

class CommentSubjectToPostSubjectTransformer implements SubjectTransformer {

  /** @var WordPress */
  private $wp;

  public function __construct(
    WordPress $wp
  ) {
    $this->wp = $wp;
  }

  public function accepts(): string {
    return CommentSubject::KEY;
  }

  public function returns(): string {
    return PostSubject::KEY;
  }

  public function transform(Subject $data): ?Subject {

    if ($this->accepts() !== $data->getKey()) {
      throw new \InvalidArgumentException('Invalid subject type');
    }

    $commentId = (int)$data->getArgs()['comment_id'];
    $comment = $this->wp->getComment($commentId);
    if (!$comment instanceof \WP_Comment) {
      return null;
    }
    //phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
    $post = $this->wp->getPost((int)$comment->comment_post_ID);
    if (!$post instanceof \WP_Post) {
      return null;
    }
    return new Subject(PostSubject::KEY, ['post_id' => (int)$post->ID]);
  }
}
