<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\MailPoet\Subjects;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Subject as SubjectData;
use MailPoet\Automation\Engine\Integration\Payload;
use MailPoet\Automation\Engine\Integration\Subject;
use MailPoet\Automation\Integrations\MailPoet\Fields\NewsletterLinkFieldsFactory;
use MailPoet\Automation\Integrations\MailPoet\Payloads\NewsletterLinkPayload;
use MailPoet\Cron\Workers\StatsNotifications\NewsletterLinkRepository;
use MailPoet\NotFoundException;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

/**
 * @implements Subject<NewsletterLinkPayload>
 */
class NewsletterLinkSubject implements Subject {


  const KEY = 'mailpoet:email-link';

  /** @var NewsletterLinkFieldsFactory */
  private $emailLinkFieldsFactory;

  /** @var NewsletterLinkRepository */
  private $newsletterLinkRepository;

  public function __construct(
    NewsletterLinkFieldsFactory $emailLinkFieldsFactory,
    NewsletterLinkRepository $newsletterLinkRepository
  ) {
    $this->emailLinkFieldsFactory = $emailLinkFieldsFactory;
    $this->newsletterLinkRepository = $newsletterLinkRepository;
  }

  public function getKey(): string {
    return self::KEY;
  }

  public function getName(): string {
    // translators: automation subject (entity entering automation) title
    return __('Email link', 'mailpoet');
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object([
      'link_id' => Builder::integer()->minimum(1)->required(),
    ]);
  }

  public function getFields(): array {
    return $this->emailLinkFieldsFactory->getFields();
  }

  public function getPayload(SubjectData $subjectData): Payload {
    $linkId = $subjectData->getArgs()['link_id'];
    $linkEntity = $this->newsletterLinkRepository->findOneById($linkId);
    if (!$linkEntity) {
      throw NotFoundException::create()->withMessage(sprintf("Email link with ID '%d' not found", $linkId));
    }

    return new NewsletterLinkPayload($linkEntity);
  }
}
