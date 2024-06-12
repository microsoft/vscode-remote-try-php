<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\MailPoet\SubjectTransformers;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Subject;
use MailPoet\Automation\Engine\Integration\SubjectTransformer;
use MailPoet\Automation\Engine\WordPress;
use MailPoet\Automation\Integrations\MailPoet\Subjects\SubscriberSubject;
use MailPoet\Automation\Integrations\WordPress\Subjects\CommentSubject;
use MailPoet\Subscribers\SubscribersRepository;

class CommentSubjectToSubscriberSubjectTransformer implements SubjectTransformer {

  /** @var WordPress */
  private $wp;

  /** @var SubscribersRepository */
  private $subscribersRepository;

  public function __construct(
    WordPress $wp,
    SubscribersRepository $subscribersRepository
  ) {
    $this->wp = $wp;
    $this->subscribersRepository = $subscribersRepository;
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
    $email = $comment->comment_author_email;
    if (!$this->wp->isEmail($email)) {
      return null;
    }

    $subscriber = $this->subscribersRepository->findOneBy(['email' => $email]);
    if (!$subscriber) {
      return null;
    }

    return new Subject(
      SubscriberSubject::KEY,
      [
        'subscriber_id' => $subscriber->getId(),
      ]
    );
  }

  public function returns(): string {
    return SubscriberSubject::KEY;
  }

  public function accepts(): string {
    return CommentSubject::KEY;
  }
}
