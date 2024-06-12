<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\MailPoet\SubjectTransformers;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Subject;
use MailPoet\Automation\Engine\Integration\SubjectTransformer;
use MailPoet\Automation\Integrations\MailPoet\Subjects\SubscriberSubject;
use MailPoet\Automation\Integrations\WordPress\Subjects\UserSubject;
use MailPoet\Subscribers\SubscribersRepository;

class SubscriberSubjectToWordPressUserSubjectTransformer implements SubjectTransformer {
  /** @var SubscribersRepository */
  private $subscribersRepository;

  public function __construct(
    SubscribersRepository $subscribersRepository
  ) {
    $this->subscribersRepository = $subscribersRepository;
  }

  public function accepts(): string {
    return SubscriberSubject::KEY;
  }

  public function returns(): string {
    return UserSubject::KEY;
  }

  public function transform(Subject $data): Subject {
    if ($this->accepts() !== $data->getKey()) {
      throw new \InvalidArgumentException('Invalid subject type');
    }

    $subscriber = $this->subscribersRepository->findOneById((int)$data->getArgs()['subscriber_id']);
    if (!$subscriber) {
      throw new \InvalidArgumentException('Subscriber not found');
    }
    return new Subject(UserSubject::KEY, ['user_id' => $subscriber->getWpUserId()]);
  }
}
