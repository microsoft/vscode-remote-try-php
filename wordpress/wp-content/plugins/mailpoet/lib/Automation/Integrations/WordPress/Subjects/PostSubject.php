<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\WordPress\Subjects;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Subject as SubjectData;
use MailPoet\Automation\Engine\Integration\Payload;
use MailPoet\Automation\Engine\Integration\Subject;
use MailPoet\Automation\Engine\WordPress;
use MailPoet\Automation\Integrations\WordPress\Fields\PostFieldsFactory;
use MailPoet\Automation\Integrations\WordPress\Payloads\PostPayload;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

/**
 * @implements Subject<PostPayload>
 */
class PostSubject implements Subject {
  const KEY = 'wordpress:post';

  /** @var WordPress */
  private $wordPress;

  /** @var PostFieldsFactory */
  private $postFieldsFactory;

  public function __construct(
    WordPress $wordPress,
    PostFieldsFactory $postFieldsFactory
  ) {
    $this->wordPress = $wordPress;
    $this->postFieldsFactory = $postFieldsFactory;
  }

  public function getName(): string {
    // translators: automation subject (entity entering automation) title
    return __('WordPress post', 'mailpoet');
  }

  public function getKey(): string {
    return self::KEY;
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object([
      'post_id' => Builder::integer()->required(),
    ]);
  }

  public function getPayload(SubjectData $subjectData): Payload {
    $id = $subjectData->getArgs()['post_id'];
    return new PostPayload((int)$id, $this->wordPress);
  }

  public function getFields(): array {
    return $this->postFieldsFactory->getFields();
  }
}
