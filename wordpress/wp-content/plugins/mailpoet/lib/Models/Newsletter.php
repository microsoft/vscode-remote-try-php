<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Models;

if (!defined('ABSPATH')) exit;


use MailPoet\DI\ContainerWrapper;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Newsletter\NewsletterDeleteController;
use MailPoet\Newsletter\NewslettersRepository;
use MailPoet\Newsletter\Options\NewsletterOptionFieldsRepository;
use MailPoet\Settings\SettingsController;
use MailPoet\Util\Helpers;
use MailPoet\Util\Security;

/**
 * @property int $id
 * @property int $parentId
 * @property string $type
 * @property object|array|bool $queue
 * @property string $hash
 * @property string $senderName
 * @property string $senderAddress
 * @property string $replyToName
 * @property string $replyToAddress
 * @property string $status
 * @property string|object $meta
 * @property array $options
 * @property bool|array $statistics
 * @property string $sentAt
 * @property string $deletedAt
 * @property int $totalSent
 * @property int $totalScheduled
 * @property array $segments
 * @property string $subject
 * @property string $preheader
 * @property string|array|null $body
 * @property string|null $schedule
 * @property bool|null $isScheduled
 * @property string|null $scheduledAt
 * @property string $gaCampaign
 * @property string $event
 * @property string $unsubscribeToken
 *
 * @deprecated This model is deprecated. Use \MailPoet\Newsletter\NewslettersRepository and
 * \MailPoet\Entities\NewsletterEntity instead. This class can be removed after 2024-05-30.
 */
class Newsletter extends Model {
  public static $_table = MP_NEWSLETTERS_TABLE; // phpcs:ignore PSR2.Classes.PropertyDeclaration
  const TYPE_AUTOMATIC = NewsletterEntity::TYPE_AUTOMATIC;
  const TYPE_AUTOMATION = NewsletterEntity::TYPE_AUTOMATION;
  const TYPE_AUTOMATION_TRANSACTIONAL = NewsletterEntity::TYPE_AUTOMATION_TRANSACTIONAL;
  const TYPE_STANDARD = NewsletterEntity::TYPE_STANDARD;
  const TYPE_WELCOME = NewsletterEntity::TYPE_WELCOME;
  const TYPE_NOTIFICATION = NewsletterEntity::TYPE_NOTIFICATION;
  const TYPE_AUTOMATION_NOTIFICATION = NewsletterEntity::TYPE_AUTOMATION_NOTIFICATION;
  const TYPE_NOTIFICATION_HISTORY = NewsletterEntity::TYPE_NOTIFICATION_HISTORY;
  const TYPE_WC_TRANSACTIONAL_EMAIL = NewsletterEntity::TYPE_WC_TRANSACTIONAL_EMAIL;
  const TYPE_RE_ENGAGEMENT = NewsletterEntity::TYPE_RE_ENGAGEMENT;
  // standard newsletters
  const STATUS_DRAFT = NewsletterEntity::STATUS_DRAFT;
  const STATUS_SCHEDULED = NewsletterEntity::STATUS_SCHEDULED;
  const STATUS_SENDING = NewsletterEntity::STATUS_SENDING;
  const STATUS_SENT = NewsletterEntity::STATUS_SENT;
  // automatic newsletters status
  const STATUS_ACTIVE = NewsletterEntity::STATUS_ACTIVE;

  /**
   * @deprecated
   */
  public function __construct() {
    self::deprecationError(__METHOD__);
    parent::__construct();
    $this->addValidations('type', [
      'required' => __('Please specify a type.', 'mailpoet'),
    ]);
  }

  /**
   * @deprecated
   */
  public function queue() {
    self::deprecationError(__METHOD__);
    return $this->hasOne(__NAMESPACE__ . '\SendingQueue', 'newsletter_id', 'id');
  }

  /**
   * @deprecated
   */
  public function children() {
    self::deprecationError(__METHOD__);
    return $this->hasMany(
      __NAMESPACE__ . '\Newsletter',
      'parent_id',
      'id'
    );
  }

  /**
   * @deprecated
   */
  public function parent() {
    self::deprecationError(__METHOD__);
    return $this->hasOne(
      __NAMESPACE__ . '\Newsletter',
      'id',
      'parent_id'
    );
  }

  /**
   * @deprecated
   */
  public function segments() {
    self::deprecationError(__METHOD__);
    return $this->hasManyThrough(
      __NAMESPACE__ . '\Segment',
      __NAMESPACE__ . '\NewsletterSegment',
      'newsletter_id',
      'segment_id'
    );
  }

  /**
   * @deprecated
   */
  public function segmentRelations() {
    self::deprecationError(__METHOD__);
    return $this->hasMany(
      __NAMESPACE__ . '\NewsletterSegment',
      'newsletter_id',
      'id'
    );
  }

  /**
   * @deprecated
   */
  public function save() {
    self::deprecationError(__METHOD__);
    if (is_string($this->deletedAt) && strlen(trim($this->deletedAt)) === 0) {
      $this->set_expr('deleted_at', 'NULL');
    }

    if (isset($this->body) && ($this->body !== false)) {
      $this->body = $this->getBodyString();
      $this->set(
        'body',
        $this->body
      );
    }

    $this->set(
      'hash',
      ($this->hash)
      ? $this->hash
      : Security::generateHash()
    );
    return parent::save();
  }

  /**
   * @deprecated
   */
  public function trash() {
    $this->save();
    trigger_error('Calling Newsletter::trash() is deprecated and will be removed. Use \MailPoet\Newsletter\NewslettersRepository instead.', E_USER_DEPRECATED);
    ContainerWrapper::getInstance()->get(NewslettersRepository::class)->bulkTrash([$this->id]);
    return $this;
  }

  /**
   * @deprecated
   */
  public function restore() {
    $this->save();
    trigger_error('Calling Newsletter::restore() is deprecated and will be removed. Use \MailPoet\Newsletter\NewslettersRepository instead.', E_USER_DEPRECATED);
    ContainerWrapper::getInstance()->get(NewslettersRepository::class)->bulkRestore([$this->id]);
    return $this;
  }

  /**
   * @deprecated
   */
  public function delete() {
    trigger_error('Calling Newsletter::delete() is deprecated and will be removed. Use \MailPoet\Newsletter\NewslettersRepository instead.', E_USER_DEPRECATED);
    ContainerWrapper::getInstance()->get(NewsletterDeleteController::class)->bulkDelete([$this->id]);
    return null;
  }

  /**
   * @deprecated
   */
  public function setStatus($status = null) {
    self::deprecationError(__METHOD__);
    if ($status === self::STATUS_ACTIVE) {
      if (!$this->body || empty(json_decode($this->getBodyString()))) {
        $this->setError(
          Helpers::replaceLinkTags(
            __('This is an empty email without any content and it cannot be sent. Please update [link]the email[/link].', 'mailpoet'),
            'admin.php?page=mailpoet-newsletter-editor&id=' . $this->id
          )
        );
        return $this;
      }
    }
    if (
      in_array($status, [
        self::STATUS_DRAFT,
        self::STATUS_SCHEDULED,
        self::STATUS_SENDING,
        self::STATUS_SENT,
        self::STATUS_ACTIVE,
      ])
    ) {
      $this->set('status', $status);
      $this->save();
    }

    $typesWithActivation = [self::TYPE_NOTIFICATION, self::TYPE_WELCOME, self::TYPE_AUTOMATIC];

    if (($status === self::STATUS_DRAFT) && in_array($this->type, $typesWithActivation)) {
      ScheduledTask::pauseAllByNewsletter($this);
    }
    if (($status === self::STATUS_ACTIVE) && in_array($this->type, $typesWithActivation)) {
      ScheduledTask::setScheduledAllByNewsletter($this);
    }
    return $this;
  }

  /**
   * @deprecated
   */
  public function asArray() {
    self::deprecationError(__METHOD__);
    $model = parent::asArray();

    if (isset($model['body'])) {
      $model['body'] = json_decode($model['body'], true);
    }
    return $model;
  }

  /**
   * @deprecated
   */
  public function withSegments($inclDeleted = false) {
    self::deprecationError(__METHOD__);
    $this->segments = $this->segments()->findArray();
    if ($inclDeleted) {
      $this->withDeletedSegments();
    }
    return $this;
  }

  /**
   * @deprecated
   */
  public function withDeletedSegments() {
    self::deprecationError(__METHOD__);
    if (!empty($this->segments)) {
      $segmentIds = array_column($this->segments, 'id');
      $links = $this->segmentRelations()
        ->whereNotIn('segment_id', $segmentIds)->findArray();
      $deletedSegments = [];

      foreach ($links as $link) {
        $deletedSegments[] = [
          'id' => $link['segment_id'],
          'name' => __('Deleted list', 'mailpoet'),
        ];
      }
      $this->segments = array_merge($this->segments, $deletedSegments);
    }

    return $this;
  }

  /**
   * @deprecated This method is deprecated. \MailPoet\Entities\NewsletterEntity::getLatestQueue() instead. This method can be removed after 2024-05-30.
   */
  public function getQueue($columns = '*') {
    self::deprecationError(__METHOD__);
  }

  /**
   * @deprecated
   */
  public function getBodyString(): string {
    self::deprecationError(__METHOD__);
    if (is_array($this->body)) {
      return (string)json_encode($this->body);
    }
    if ($this->body === null) {
      return '';
    }
    return $this->body;
  }

  /**
   * @deprecated This method is deprecated. It method can be removed after 2024-05-30.
   */
  public function withSendingQueue() {
    self::deprecationError(__METHOD__);
    $queue = $this->getQueue();
    if ($queue === false) {
      $this->queue = false;
    } else {
      $this->queue = $queue->asArray();
    }
    return $this;
  }

  /**
   * @deprecated
   */
  public static function filterWithOptions($orm, $type) {
    self::deprecationError(__METHOD__);
    $orm = $orm->select(MP_NEWSLETTERS_TABLE . '.*');
    $optionFieldsRepository = ContainerWrapper::getInstance()->get(NewsletterOptionFieldsRepository::class);
    $optionFieldsEntities = $optionFieldsRepository->findAll();
    foreach ($optionFieldsEntities as $optionField) {
      if ($optionField->getNewsletterType() !== $type) {
        continue;
      }
      $orm = $orm->select_expr(
        'IFNULL(GROUP_CONCAT(CASE WHEN ' .
        MP_NEWSLETTER_OPTION_FIELDS_TABLE . '.id=' . $optionField->getId() . ' THEN ' .
        MP_NEWSLETTER_OPTION_TABLE . '.value END), NULL) as "' . $optionField->getName() . '"'
      );
    }
    $orm = $orm
      ->left_outer_join(
        MP_NEWSLETTER_OPTION_TABLE,
        [
          MP_NEWSLETTERS_TABLE . '.id',
          '=',
          MP_NEWSLETTER_OPTION_TABLE . '.newsletter_id',
        ]
      )
      ->left_outer_join(
        MP_NEWSLETTER_OPTION_FIELDS_TABLE,
        [
          MP_NEWSLETTER_OPTION_FIELDS_TABLE . '.id',
          '=',
          MP_NEWSLETTER_OPTION_TABLE . '.option_field_id',
        ]
      )
      ->group_by(MP_NEWSLETTERS_TABLE . '.id');
    return $orm;
  }

  /**
   * @deprecated
   */
  public static function filterStatus($orm, $status = false) {
    self::deprecationError(__METHOD__);
    if (
      in_array($status, [
      self::STATUS_DRAFT,
      self::STATUS_SCHEDULED,
      self::STATUS_SENDING,
      self::STATUS_SENT,
      self::STATUS_ACTIVE,
      ])
    ) {
      $orm->where('status', $status);
    }
    return $orm;
  }

  /**
   * @deprecated
   */
  public static function createOrUpdate($data = []) {
    self::deprecationError(__METHOD__);
    $data['unsubscribe_token'] = Security::generateUnsubscribeToken(self::class);
    return parent::_createOrUpdate($data, false, function($data) {
      $settings = SettingsController::getInstance();
      // set default sender based on settings
      if (empty($data['sender'])) {
        $sender = $settings->get('sender', []);
        $data['sender_name'] = (
          !empty($sender['name'])
          ? $sender['name']
          : ''
        );
        $data['sender_address'] = (
          !empty($sender['address'])
          ? $sender['address']
          : ''
        );
      }

      // set default reply_to based on settings
      if (empty($data['reply_to'])) {
        $replyTo = $settings->get('reply_to', []);
        $data['reply_to_name'] = (
          !empty($replyTo['name'])
          ? $replyTo['name']
          : ''
        );
        $data['reply_to_address'] = (
          !empty($replyTo['address'])
          ? $replyTo['address']
          : ''
        );
      }

      return $data;
    });
  }

  /**
   * @deprecated
   */
  public static function getByHash($hash) {
    self::deprecationError(__METHOD__);
    return parent::where('hash', $hash)
      ->findOne();
  }

  /**
   * @deprecated
   */
  public function getMeta() {
    self::deprecationError(__METHOD__);
    if (!$this->meta) return;

    return (Helpers::isJson($this->meta) && is_string($this->meta)) ? json_decode($this->meta, true) : $this->meta;
  }

  /**
   * @deprecated
   */
  public static function findOneWithOptions($id) {
    self::deprecationError(__METHOD__);
    $newsletter = self::findOne($id);
    if (!$newsletter instanceof self) {
      return false;
    }
    return self::filter('filterWithOptions', $newsletter->type)->findOne($id);
  }

  /**
   * @deprecated This is here for displaying the deprecation warning for properties.
   */
  public function __get($key) {
    self::deprecationError('property "' . $key . '"');
    return parent::__get($key);
  }

  /**
   * @deprecated This is here for displaying the deprecation warning for static calls.
   */
  public static function __callStatic($name, $arguments) {
    self::deprecationError($name);
    return parent::__callStatic($name, $arguments);
  }

  private static function deprecationError($methodName) {
    trigger_error(
      'Calling ' . esc_html($methodName) . ' is deprecated and will be removed. Use \MailPoet\Newsletter\NewslettersRepository and \MailPoet\Entities\NewsletterEntity instead.',
      E_USER_DEPRECATED
    );
  }
}
