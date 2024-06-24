<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Subscribers\ImportExport\PersonalDataExporters;

if (!defined('ABSPATH')) exit;


use MailPoet\CustomFields\CustomFieldsRepository;
use MailPoet\Entities\CustomFieldEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Subscribers\Source;
use MailPoet\Subscribers\SubscribersRepository;
use MailPoet\WP\DateTime;

class SubscriberExporter {
  /*** @var SubscribersRepository */
  private $subscribersRepository;

  /*** @var CustomFieldsRepository */
  private $customFieldsRepository;

  /*** @var array<int, string> */
  private $customFields = [];

  public function __construct(
    SubscribersRepository $subscribersRepository,
    CustomFieldsRepository $customFieldsRepository
  ) {
    $this->subscribersRepository = $subscribersRepository;
    $this->customFieldsRepository = $customFieldsRepository;
  }

  /**
   * @param string $email
   * @return array(data: mixed[], done: boolean)
   */
  public function export(string $email): array {
    return [
      'data' => $this->exportSubscriber($this->subscribersRepository->findOneBy(['email' => trim($email)])),
      'done' => true,
    ];
  }

  /**
   * @param SubscriberEntity|null $subscriber
   * @return array|mixed[][]
   */
  private function exportSubscriber(?SubscriberEntity $subscriber): array {
    if (!$subscriber) return [];
    return [[
      'group_id' => 'mailpoet-subscriber',
      'group_label' => __('MailPoet Subscriber Data', 'mailpoet'),
      'item_id' => 'subscriber-' . $subscriber->getId(),
      'data' => $this->getSubscriberExportData($subscriber),
    ]];
  }

  /**
   * @param SubscriberEntity $subscriber
   * @return mixed[][]
   */
  private function getSubscriberExportData(SubscriberEntity $subscriber): array {
    $customFields = $this->getCustomFields();
    $result = [
      [
        'name' => __('First Name', 'mailpoet'),
        'value' => $subscriber->getFirstName(),
      ],
      [
        'name' => __('Last Name', 'mailpoet'),
        'value' => $subscriber->getLastName(),
      ],
      [
        'name' => __('Email', 'mailpoet'),
        'value' => $subscriber->getEmail(),
      ],
      [
        'name' => __('Status', 'mailpoet'),
        'value' => $subscriber->getStatus(),
      ],
    ];
    if ($subscriber->getSubscribedIp()) {
      $result[] = [
        'name' => __('Subscribed IP', 'mailpoet'),
        'value' => $subscriber->getSubscribedIp(),
      ];
    }
    if ($subscriber->getConfirmedIp()) {
      $result[] = [
        'name' => __('Confirmed IP', 'mailpoet'),
        'value' => $subscriber->getConfirmedIp(),
      ];
    }
    $result[] = [
      'name' => __('Created at', 'mailpoet'),
      'value' => $subscriber->getCreatedAt()
        ? $subscriber->getCreatedAt()->format(DateTime::DEFAULT_DATE_TIME_FORMAT)
        : '',
    ];

    foreach ($subscriber->getSubscriberCustomFields() as $subscriberCustomField) {
      $customField = $subscriberCustomField->getCustomField();
      if (!$customField instanceof CustomFieldEntity) {
        continue;
      }
      $customFieldId = $customField->getId();
      if (isset($this->getCustomFields()[$customFieldId])) {
        $result[] = [
          'name' => $customFields[$customFieldId],
          'value' => $subscriberCustomField->getValue(),
        ];
      }
    }

    $result[] = [
      'name' => __("Subscriber's subscription source", 'mailpoet'),
      'value' => $this->formatSource($subscriber->getSource()),
    ];

    return $result;
  }

  /**
   * @return array<int, string>
   */
  private function getCustomFields(): array {
    if (!empty($this->customFields)) {
      return $this->customFields;
    }

    $fields = $this->customFieldsRepository->findAll();
    foreach ($fields as $field) {
      $this->customFields[$field->getId()] = $field->getName();
    }
    return $this->customFields;
  }

  private function formatSource(string $source): string {
    switch ($source) {
      case Source::WORDPRESS_USER:
        return __('Subscriber information synchronized via WP user sync', 'mailpoet');
      case Source::FORM:
        return __('Subscription via a MailPoet subscription form', 'mailpoet');
      case Source::API:
        return __('Added by a 3rd party via MailPoet API', 'mailpoet');
      case Source::ADMINISTRATOR:
        return __('Created by the administrator', 'mailpoet');
      case Source::IMPORTED:
        return __('Imported by the administrator', 'mailpoet');
      default:
        return __('Unknown', 'mailpoet');
    }
  }
}
