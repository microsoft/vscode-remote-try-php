<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\WordPress\Subjects;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Subject as SubjectData;
use MailPoet\Automation\Engine\Integration\Payload;
use MailPoet\Automation\Engine\Integration\Subject;
use MailPoet\Automation\Engine\WordPress;
use MailPoet\Automation\Integrations\WordPress\Fields\CommentFieldsFactory;
use MailPoet\Automation\Integrations\WordPress\Payloads\CommentPayload;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

/**
 * @implements Subject<CommentPayload>
 */
class CommentSubject implements Subject {
  const KEY = 'wordpress:comment';

  /** @var WordPress */
  private $wp;

  /** @var CommentFieldsFactory */
  private $commentFieldsFactory;

  public function __construct(
    WordPress $wp,
    CommentFieldsFactory $commentFieldsFactory
  ) {
    $this->wp = $wp;
    $this->commentFieldsFactory = $commentFieldsFactory;
  }

  public function getKey(): string {
    return self::KEY;
  }

  public function getName(): string {
    // translators: automation subject (entity entering automation) title
    return __('Comment', 'mailpoet');
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object([
      'comment_id' => Builder::integer()->required(),
    ]);
  }

  public function getFields(): array {
    return $this->commentFieldsFactory->getFields();
  }

  public function getPayload(SubjectData $subjectData): Payload {
    $commentId = (int)$subjectData->getArgs()['comment_id'];
    return new CommentPayload($commentId, $this->wp);
  }
}
