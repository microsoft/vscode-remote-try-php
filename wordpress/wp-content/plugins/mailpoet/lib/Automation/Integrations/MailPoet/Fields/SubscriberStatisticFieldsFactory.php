<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\MailPoet\Fields;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Field;
use MailPoet\Automation\Integrations\MailPoet\Payloads\SubscriberPayload;
use MailPoet\Subscribers\Statistics\SubscriberStatisticsRepository;
use MailPoetVendor\Carbon\Carbon;

class SubscriberStatisticFieldsFactory {
  /** @var SubscriberStatisticsRepository */
  private $subscriberStatisticsRepository;

  public function __construct(
    SubscriberStatisticsRepository $subscriberStatisticsRepository
  ) {
    $this->subscriberStatisticsRepository = $subscriberStatisticsRepository;
  }

  /** @return Field[] */
  public function getFields(): array {
    return [
      new Field(
        'mailpoet:subscriber:email-sent-count',
        Field::TYPE_INTEGER,
        __('Email — sent count', 'mailpoet'),
        function (SubscriberPayload $payload, array $params = []) {
          $startTime = $this->getStartTime($params);
          return $this->subscriberStatisticsRepository->getTotalSentCount($payload->getSubscriber(), $startTime);
        },
        [
          'params' => ['in_the_last'],
        ]
      ),
      new Field(
        'mailpoet:subscriber:email-opened-count',
        Field::TYPE_INTEGER,
        __('Email — opened count', 'mailpoet'),
        function (SubscriberPayload $payload, array $params = []) {
          $startTime = $this->getStartTime($params);
          return $this->subscriberStatisticsRepository->getStatisticsOpenCount($payload->getSubscriber(), $startTime);
        },
        [
          'params' => ['in_the_last'],
        ]
      ),
      new Field(
        'mailpoet:subscriber:email-machine-opened-count',
        Field::TYPE_INTEGER,
        __('Email — machine opened count', 'mailpoet'),
        function (SubscriberPayload $payload, array $params = []) {
          $startTime = $this->getStartTime($params);
          return $this->subscriberStatisticsRepository->getStatisticsMachineOpenCount($payload->getSubscriber(), $startTime);
        },
        [
          'params' => ['in_the_last'],
        ]
      ),
      new Field(
        'mailpoet:subscriber:email-clicked-count',
        Field::TYPE_INTEGER,
        __('Email — clicked count', 'mailpoet'),
        function (SubscriberPayload $payload, array $params = []) {
          $startTime = $this->getStartTime($params);
          return $this->subscriberStatisticsRepository->getStatisticsClickCount($payload->getSubscriber(), $startTime);
        },
        [
          'params' => ['in_the_last'],
        ]
      ),
    ];
  }

  private function getStartTime(array $params): ?Carbon {
    $inTheLastSeconds = $params['in_the_last'] ?? null;
    return $inTheLastSeconds ? Carbon::now()->subSeconds((int)$inTheLastSeconds) : null;
  }
}
