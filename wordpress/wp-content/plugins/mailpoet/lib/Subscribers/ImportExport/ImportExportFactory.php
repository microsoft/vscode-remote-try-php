<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Subscribers\ImportExport;

if (!defined('ABSPATH')) exit;


use MailPoet\CustomFields\CustomFieldsRepository;
use MailPoet\DI\ContainerWrapper;
use MailPoet\Entities\SegmentEntity;
use MailPoet\Entities\TagEntity;
use MailPoet\Segments\SegmentsSimpleListRepository;
use MailPoet\Tags\TagRepository;
use MailPoet\Util\Helpers;

class ImportExportFactory {
  const IMPORT_ACTION = 'import';
  const EXPORT_ACTION = 'export';

  /** @var string|null  */
  public $action;

  /** @var SegmentsSimpleListRepository */
  private $segmentsListRepository;

  /** @var CustomFieldsRepository */
  private $customFieldsRepository;

  /** @var TagRepository */
  private $tagRepository;

  public function __construct(
    $action = null
  ) {
    $this->action = $action;
    $this->segmentsListRepository = ContainerWrapper::getInstance()->get(SegmentsSimpleListRepository::class);
    $this->customFieldsRepository = ContainerWrapper::getInstance()->get(CustomFieldsRepository::class);
    $this->tagRepository = ContainerWrapper::getInstance()->get(TagRepository::class);
  }

  public function getSegments() {
    if ($this->action === self::IMPORT_ACTION) {
      $segments = $this->segmentsListRepository->getListWithSubscribedSubscribersCounts([SegmentEntity::TYPE_DEFAULT]);
    } else {
      $segments = $this->segmentsListRepository->getListWithAssociatedSubscribersCounts();
      $segments = $this->segmentsListRepository->addVirtualSubscribersWithoutListSegment($segments);
      $segments = array_values(array_filter($segments, function($segment) {
        return $segment['subscribers'] > 0;
      }));
    }

    return array_map(function($segment) {
      return [
        'id' => $segment['id'],
        'name' => esc_attr($segment['name']),
        'count' => $segment['subscribers'],
      ];
    }, $segments);
  }

  public function getSubscriberFields() {
    $fields = [
      'email' => __('Email', 'mailpoet'),
      'first_name' => __('First name', 'mailpoet'),
      'last_name' => __('Last name', 'mailpoet'),
      'subscribed_ip' => __('Subscription IP', 'mailpoet'),
      'created_at' => __('Subscription time', 'mailpoet'),
      'confirmed_at' => __('Confirmation time', 'mailpoet'),
      'confirmed_ip' => __('Confirmation IP', 'mailpoet'),
    ];
    if ($this->action === 'export') {
      $fields = array_merge(
        $fields,
        [
          'list_status' => _x('List status', 'Subscription status', 'mailpoet'),
          'global_status' => _x('Global status', 'Subscription status', 'mailpoet'),
        ]
      );
    }
    return $fields;
  }

  public function formatSubscriberFields($subscriberFields) {
    return array_map(function($fieldId, $fieldName) {
      return [
        'id' => $fieldId,
        'name' => $fieldName,
        'text' => $fieldName, // Required for select2 default functionality
        'type' => ($fieldId === 'confirmed_at' || $fieldId === 'created_at') ? 'date' : null,
        'custom' => false,
      ];
    }, array_keys($subscriberFields), $subscriberFields);
  }

  public function getSubscriberCustomFields() {
    return $this->customFieldsRepository->findAllAsArray();
  }

  public function formatSubscriberCustomFields($subscriberCustomFields) {
    return array_map(function($field) {
      return [
        'id' => $field['id'],
        'name' => $field['name'],
        'text' => $field['name'], // Required for select2 default functionality
        'type' => $field['type'],
        'params' => unserialize($field['params']),
        'custom' => true,
      ];
    }, $subscriberCustomFields);
  }

  public function formatFieldsForSelect2(
    $subscriberFields,
    $subscriberCustomFields
  ) {
    $actions = ($this->action === 'import') ?
      [
        [
          'id' => 'ignore',
          'name' => __('Ignore field...', 'mailpoet'),
          'text' => __('Ignore field...', 'mailpoet'), // Required for select2 default functionality
        ],
        [
          'id' => 'create',
          'name' => __('Create new field...', 'mailpoet'),
          'text' => __('Create new field...', 'mailpoet'), // Required for select2 default functionality
        ],
      ] :
      [
        [
          'id' => 'select',
          'name' => __('Select all...', 'mailpoet'),
          'text' => __('Select all...', 'mailpoet'), // Required for select2 default functionality
        ],
        [
          'id' => 'deselect',
          'name' => __('Deselect all...', 'mailpoet'),
          'text' => __('Deselect all...', 'mailpoet'), // Required for select2 default functionality
        ],
      ];
    $select2Fields = [
      [
        'name' => __('Actions', 'mailpoet'),
        'text' => __('Actions', 'mailpoet'), // Required for select2 default functionality
        'children' => $actions,
      ],
      [
        'name' => __('System fields', 'mailpoet'),
        'text' => __('System fields', 'mailpoet'), // Required for select2 default functionality
        'children' => $this->formatSubscriberFields($subscriberFields),
      ],
    ];
    if ($subscriberCustomFields) {
      array_push($select2Fields, [
        'name' => __('User fields', 'mailpoet'),
        'text' => __('User fields', 'mailpoet'), // Required for select2 default functionality
        'children' => $this->formatSubscriberCustomFields(
          $subscriberCustomFields
        ),
      ]);
    }
    return $select2Fields;
  }

  public function bootstrap() {
    $subscriberFields = $this->getSubscriberFields();
    $subscriberCustomFields = $this->getSubscriberCustomFields();
    $data['segments'] = json_encode($this->getSegments());
    $data['subscriberFieldsSelect2'] = json_encode(
      $this->formatFieldsForSelect2(
        $subscriberFields,
        $subscriberCustomFields
      )
    );
    if ($this->action === 'import') {
      $data['subscriberFields'] = json_encode(
        array_merge(
          $this->formatSubscriberFields($subscriberFields),
          $this->formatSubscriberCustomFields($subscriberCustomFields)
        )
      );
      $data['maxPostSizeBytes'] = Helpers::getMaxPostSize('bytes');
      $data['maxPostSize'] = Helpers::getMaxPostSize();
      $data['tags'] = array_map(function (TagEntity $tag): array {
        return [
          'id' => $tag->getId(),
          'name' => $tag->getName(),
        ];
      }, $this->tagRepository->findAll());
    }
    $data['zipExtensionLoaded'] = extension_loaded('zip');
    return $data;
  }
}
