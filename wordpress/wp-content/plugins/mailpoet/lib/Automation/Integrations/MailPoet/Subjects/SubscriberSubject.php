<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\MailPoet\Subjects;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Field;
use MailPoet\Automation\Engine\Data\Subject as SubjectData;
use MailPoet\Automation\Engine\Integration\Payload;
use MailPoet\Automation\Engine\Integration\Subject;
use MailPoet\Automation\Integrations\MailPoet\Fields\SubscriberFieldsFactory;
use MailPoet\Automation\Integrations\MailPoet\Payloads\SubscriberPayload;
use MailPoet\NotFoundException;
use MailPoet\Subscribers\SubscribersRepository;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

/**
 * @implements Subject<SubscriberPayload>
 */
class SubscriberSubject implements Subject {
  const KEY = 'mailpoet:subscriber';

  /** @var SubscriberFieldsFactory */
  private $subscriberFieldsFactory;

  /** @var SubscribersRepository */
  private $subscribersRepository;

  public function __construct(
    SubscriberFieldsFactory $subscriberFieldsFactory,
    SubscribersRepository $subscribersRepository
  ) {
    $this->subscriberFieldsFactory = $subscriberFieldsFactory;
    $this->subscribersRepository = $subscribersRepository;
  }

  public function getKey(): string {
    return self::KEY;
  }

  public function getName(): string {
    // translators: automation subject (entity entering automation) title
    return __('MailPoet subscriber', 'mailpoet');
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object([
      'subscriber_id' => Builder::integer()->required(),
    ]);
  }

  public function getPayload(SubjectData $subjectData): Payload {
    $id = $subjectData->getArgs()['subscriber_id'];
    $subscriber = $this->subscribersRepository->findOneById($id);
    if (!$subscriber) {
      // translators: %d is the ID.
      throw NotFoundException::create()->withMessage(sprintf(__("Subscriber with ID '%d' not found.", 'mailpoet'), $id));
    }
    return new SubscriberPayload($subscriber);
  }

  /** @return Field[] */
  public function getFields(): array {
    return $this->subscriberFieldsFactory->getFields();
  }
}
