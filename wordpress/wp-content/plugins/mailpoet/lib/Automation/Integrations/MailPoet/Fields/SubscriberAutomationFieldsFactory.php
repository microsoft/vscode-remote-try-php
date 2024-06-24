<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\MailPoet\Fields;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Data\AutomationRun;
use MailPoet\Automation\Engine\Data\Field;
use MailPoet\Automation\Engine\Data\Subject;
use MailPoet\Automation\Engine\Storage\AutomationStorage;
use MailPoet\Automation\Integrations\MailPoet\Payloads\SubscriberPayload;
use MailPoet\Automation\Integrations\MailPoet\Subjects\SubscriberSubject;

class SubscriberAutomationFieldsFactory {
  /** @var AutomationStorage */
  private $automationStorage;

  public function __construct(
    AutomationStorage $automationStorage
  ) {
    $this->automationStorage = $automationStorage;
  }

  /** @return Field[] */
  public function getFields(): array {
    $automations = $this->automationStorage->getAutomations(
      array_diff(Automation::STATUS_ALL, [Automation::STATUS_TRASH])
    );
    $args = [
      'options' => array_map(function (Automation $automation) {
        return [
          'id' => $automation->getId(),
          'name' => $automation->getName() . " (#{$automation->getId()})",
        ];
      }, $automations),
      'params' => ['in_the_last'],
    ];

    return [
      new Field(
        'mailpoet:subscriber:automations-entered',
        Field::TYPE_ENUM_ARRAY,
        __('Automations — entered', 'mailpoet'),
        function (SubscriberPayload $payload, array $params = []) {
          return $this->getAutomationIds($payload, null, $params);
        },
        $args
      ),
      new Field(
        'mailpoet:subscriber:automations-processing',
        Field::TYPE_ENUM_ARRAY,
        __('Automations — processing', 'mailpoet'),
        function (SubscriberPayload $payload, array $params = []) {
          return $this->getAutomationIds($payload, [AutomationRun::STATUS_RUNNING], $params);
        },
        $args
      ),
      new Field(
        'mailpoet:subscriber:automations-exited',
        Field::TYPE_ENUM_ARRAY,
        __('Automations — exited', 'mailpoet'),
        function (SubscriberPayload $payload, array $params = []) {
          return $this->getAutomationIds($payload, [AutomationRun::STATUS_COMPLETE], $params);
        },
        $args
      ),
    ];
  }

  private function getAutomationIds(SubscriberPayload $payload, array $status = null, array $params = []): array {
    $inTheLastSeconds = isset($params['in_the_last']) ? (int)$params['in_the_last'] : null;
    $subject = new Subject(SubscriberSubject::KEY, ['subscriber_id' => $payload->getId()]);
    return $this->automationStorage->getAutomationIdsBySubject($subject, $status, $inTheLastSeconds);
  }
}
