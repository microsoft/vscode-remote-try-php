<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\WordPress\Subjects;

if (!defined('ABSPATH')) exit;


use DateTimeImmutable;
use MailPoet\Automation\Engine\Data\Field;
use MailPoet\Automation\Engine\Data\Subject as SubjectData;
use MailPoet\Automation\Engine\Integration\Payload;
use MailPoet\Automation\Engine\Integration\Subject;
use MailPoet\Automation\Engine\WordPress;
use MailPoet\Automation\Integrations\WordPress\Payloads\UserPayload;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;
use WP_User;

/**
 * @implements Subject<UserPayload>
 */
class UserSubject implements Subject {
  const KEY = 'wordpress:user';

  /** @var WordPress */
  private $wordPress;

  public function __construct(
    WordPress $wordPress
  ) {
    $this->wordPress = $wordPress;
  }

  public function getName(): string {
    // translators: automation subject (entity entering automation) title
    return __('WordPress user', 'mailpoet');
  }

  public function getKey(): string {
    return self::KEY;
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object([
      'user_id' => Builder::integer()->required(),
    ]);
  }

  public function getPayload(SubjectData $subjectData): Payload {
    $id = $subjectData->getArgs()['user_id'];
    $user = new WP_User($id);
    return new UserPayload($user);
  }

  public function getFields(): array {
    global $wp_roles;
    $roles = [];
    foreach ($wp_roles->role_names as $id => $name) { // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
      $roles[] = ['id' => $id, 'name' => $name];
    }

    return [
      new Field(
        'wordpress:user:email',
        Field::TYPE_STRING,
        __('Email', 'mailpoet'),
        function (UserPayload $payload) {
          return $payload->getEmail();
        }
      ),
      new Field(
        'wordpress:user:is-guest',
        Field::TYPE_BOOLEAN,
        __('Is guest', 'mailpoet'),
        function (UserPayload $payload) {
          return !$payload->exists();
        }
      ),
      new Field(
        'wordpress:user:registered-date',
        Field::TYPE_DATETIME,
        __('Registered date', 'mailpoet'),
        function (UserPayload $payload) {
          $date = $payload->getUser()->user_registered;
          return $date ? new DateTimeImmutable($date, $this->wordPress->wpTimezone()) : null;
        }
      ),
      new Field(
        'wordpress:user:roles',
        Field::TYPE_ENUM_ARRAY,
        __('Roles', 'mailpoet'),
        function (UserPayload $payload) {
          return $payload->getRoles();
        },
        [
          'options' => $roles,
        ]
      ),
    ];
  }
}
