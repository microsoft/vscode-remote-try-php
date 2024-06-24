<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\MailPoet\Fields;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Field;
use MailPoet\Automation\Integrations\MailPoet\Payloads\SubscriberPayload;
use MailPoet\Entities\SegmentEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Segments\SegmentsFinder;
use MailPoet\Segments\SegmentsRepository;
use MailPoet\Tags\TagRepository;

class SubscriberFieldsFactory {
  /** @var SegmentsFinder */
  private $segmentsFinder;

  /** @var SegmentsRepository */
  private $segmentsRepository;

  /** @var SubscriberAutomationFieldsFactory */
  private $automationFieldsFactory;

  /** @var SubscriberCustomFieldsFactory */
  private $customFieldsFactory;

  /** @var TagRepository */
  private $tagRepository;

  /** @var SubscriberStatisticFieldsFactory */
  private $statisticFieldsFactory;

  public function __construct(
    SegmentsFinder $segmentsFinder,
    SegmentsRepository $segmentsRepository,
    SubscriberAutomationFieldsFactory $automationFieldsFactory,
    SubscriberCustomFieldsFactory $customFieldsFactory,
    SubscriberStatisticFieldsFactory $statisticFieldsFactory,
    TagRepository $tagRepository
  ) {
    $this->segmentsFinder = $segmentsFinder;
    $this->segmentsRepository = $segmentsRepository;
    $this->automationFieldsFactory = $automationFieldsFactory;
    $this->customFieldsFactory = $customFieldsFactory;
    $this->statisticFieldsFactory = $statisticFieldsFactory;
    $this->tagRepository = $tagRepository;
  }

  /** @return Field[] */
  public function getFields(): array {
    return array_merge(
      $this->customFieldsFactory->getFields(),
      [
        new Field(
          'mailpoet:subscriber:email',
          Field::TYPE_STRING,
          __('Email address', 'mailpoet'),
          function (SubscriberPayload $payload) {
            return $payload->getEmail();
          }
        ),
        new Field(
          'mailpoet:subscriber:engagement-score',
          Field::TYPE_NUMBER,
          __('Engagement score', 'mailpoet'),
          function (SubscriberPayload $payload) {
            return $payload->getSubscriber()->getEngagementScore();
          }
        ),
        new Field(
          'mailpoet:subscriber:first-name',
          Field::TYPE_STRING,
          __('First name', 'mailpoet'),
          function (SubscriberPayload $payload) {
            return $payload->getSubscriber()->getFirstName();
          }
        ),
        new Field(
          'mailpoet:subscriber:last-name',
          Field::TYPE_STRING,
          __('Last name', 'mailpoet'),
          function (SubscriberPayload $payload) {
            return $payload->getSubscriber()->getLastName();
          }
        ),
        new Field(
          'mailpoet:subscriber:is-globally-subscribed',
          Field::TYPE_BOOLEAN,
          __('Is globally subscribed', 'mailpoet'),
          function (SubscriberPayload $payload) {
            return $payload->getStatus() === SubscriberEntity::STATUS_SUBSCRIBED;
          }
        ),
        new Field(
          'mailpoet:subscriber:last-engagement-at',
          Field::TYPE_DATETIME,
          __('Last engaged', 'mailpoet'),
          function (SubscriberPayload $payload) {
            return $payload->getSubscriber()->getLastEngagementAt();
          }
        ),
        new Field(
          'mailpoet:subscriber:status',
          Field::TYPE_ENUM,
          __('Status', 'mailpoet'),
          function (SubscriberPayload $payload) {
            return $payload->getStatus();
          },
          [
            'options' => [
              [
                'id' => SubscriberEntity::STATUS_SUBSCRIBED,
                'name' => __('Subscribed', 'mailpoet'),
              ],
              [
                'id' => SubscriberEntity::STATUS_UNCONFIRMED,
                'name' => __('Unconfirmed', 'mailpoet'),
              ],
              [
                'id' => SubscriberEntity::STATUS_UNSUBSCRIBED,
                'name' => __('Unsubscribed', 'mailpoet'),
              ],
              [
                'id' => SubscriberEntity::STATUS_INACTIVE,
                'name' => __('Inactive', 'mailpoet'),
              ],
              [
                'id' => SubscriberEntity::STATUS_BOUNCED,
                'name' => __('Bounced', 'mailpoet'),
              ],
            ],
          ]
        ),
        new Field(
          'mailpoet:subscriber:subscription-source',
          Field::TYPE_ENUM,
          __('Subscription source', 'mailpoet'),
          function (SubscriberPayload $payload) {
            return $payload->getSubscriber()->getSource();
          },
          [
            'options' => [
              [
                'id' => 'api',
                'name' => __('API', 'mailpoet'),
              ],
              [
                'id' => 'form',
                'name' => __('Form', 'mailpoet'),
              ],
              [
                'id' => 'unknown',
                'name' => __('Unknown', 'mailpoet'),
              ],
              [
                'id' => 'imported',
                'name' => __('Imported', 'mailpoet'),
              ],
              [
                'id' => 'administrator',
                'name' => __('Administrator', 'mailpoet'),
              ],
              [
                'id' => 'wordpress_user',
                'name' => __('WordPress user', 'mailpoet'),
              ],
              [
                'id' => 'woocommerce_user',
                'name' => __('WooCommerce user', 'mailpoet'),
              ],
              [
                'id' => 'woocommerce_checkout',
                'name' => __('WooCommerce checkout', 'mailpoet'),
              ],
            ],
          ]
        ),
        new Field(
          'mailpoet:subscriber:last-subscribed-at',
          Field::TYPE_DATETIME,
          __('Subscribed date', 'mailpoet'),
          function (SubscriberPayload $payload) {
            return $payload->getSubscriber()->getLastSubscribedAt();
          }
        ),
        new Field(
          'mailpoet:subscriber:lists',
          Field::TYPE_ENUM_ARRAY,
          __('Subscribed lists', 'mailpoet'),
          function (SubscriberPayload $payload) {
            $value = [];
            foreach ($payload->getSubscriber()->getSegments() as $list) {
              if ($list->getType() !== SegmentEntity::TYPE_DYNAMIC) {
                $value[] = $list->getId();
              }
            }
            return $value;
          },
          [
            'options' => array_map(function ($segment) {
              return [
                'id' => $segment->getId(),
                'name' => $segment->getName(),
              ];
            }, $this->segmentsRepository->findByTypeNotIn([SegmentEntity::TYPE_DYNAMIC])),
          ]
        ),
        new Field(
          'mailpoet:subscriber:tags',
          Field::TYPE_ENUM_ARRAY,
          __('Tags', 'mailpoet'),
          function (SubscriberPayload $payload) {
            $value = [];
            foreach ($payload->getSubscriber()->getSubscriberTags() as $subscriberTag) {
              $tag = $subscriberTag->getTag();
              if ($tag) {
                $value[] = $tag->getId();
              }
            }
            return $value;
          },
          [
            'options' => array_map(function ($tag) {
              return [
                'id' => $tag->getId(),
                'name' => $tag->getName(),
              ];
            }, $this->tagRepository->findAll()),
          ]
        ),
        new Field(
          'mailpoet:subscriber:segments',
          Field::TYPE_ENUM_ARRAY,
          __('Segments', 'mailpoet'),
          function (SubscriberPayload $payload) {
            $segments = $this->segmentsFinder->findDynamicSegments($payload->getSubscriber());
            $value = [];
            foreach ($segments as $segment) {
              $value[] = $segment->getId();
            }
            return $value;
          },
          [
            'options' => array_map(function ($segment) {
              return [
                'id' => $segment->getId(),
                'name' => $segment->getName(),
              ];
            }, $this->segmentsRepository->findBy(['type' => SegmentEntity::TYPE_DYNAMIC])),
          ]
        ),
      ],
      $this->statisticFieldsFactory->getFields(),
      $this->automationFieldsFactory->getFields()
    );
  }
}
