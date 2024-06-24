<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter;

if (!defined('ABSPATH')) exit;


use MailPoet\Cron\Workers\SendingQueue\Tasks\Newsletter as NewsletterQueueTask;
use MailPoet\EmailEditor\Integrations\MailPoet\EmailEditor;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\NewsletterOptionEntity;
use MailPoet\Entities\NewsletterOptionFieldEntity;
use MailPoet\Entities\NewsletterSegmentEntity;
use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Entities\SegmentEntity;
use MailPoet\Entities\WpPostEntity;
use MailPoet\InvalidStateException;
use MailPoet\Newsletter\Options\NewsletterOptionFieldsRepository;
use MailPoet\Newsletter\Options\NewsletterOptionsRepository;
use MailPoet\Newsletter\Scheduler\PostNotificationScheduler;
use MailPoet\Newsletter\Scheduler\Scheduler;
use MailPoet\Newsletter\Segment\NewsletterSegmentRepository;
use MailPoet\Newsletter\Sending\ScheduledTasksRepository;
use MailPoet\NewsletterTemplates\NewsletterTemplatesRepository;
use MailPoet\NotFoundException;
use MailPoet\Services\AuthorizedEmailsController;
use MailPoet\Settings\SettingsController;
use MailPoet\UnexpectedValueException;
use MailPoet\Util\Security;
use MailPoet\WP\Emoji;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Carbon\Carbon;
use MailPoetVendor\Doctrine\ORM\EntityManager;

class NewsletterSaveController {
  /** @var AuthorizedEmailsController */
  private $authorizedEmailsController;

  /** @var Emoji */
  private $emoji;

  /** @var EntityManager */
  private $entityManager;

  /** @var NewslettersRepository */
  private $newslettersRepository;

  /** @var NewsletterOptionsRepository */
  private $newsletterOptionsRepository;

  /** @var NewsletterOptionFieldsRepository */
  private $newsletterOptionFieldsRepository;

  /** @var NewsletterSegmentRepository */
  private $newsletterSegmentRepository;

  /** @var NewsletterTemplatesRepository */
  private $newsletterTemplatesRepository;

  /** @var PostNotificationScheduler */
  private $postNotificationScheduler;

  /** @var ScheduledTasksRepository */
  private $scheduledTasksRepository;

  /** @var SettingsController */
  private $settings;

  /** @var Security */
  private $security;

  /** @var WPFunctions */
  private $wp;

  /** @var ApiDataSanitizer */
  private $dataSanitizer;

  /** @var Scheduler */
  private $scheduler;

  /*** @var NewsletterCoupon */
  private $newsletterCoupon;

  public function __construct(
    AuthorizedEmailsController $authorizedEmailsController,
    Emoji $emoji,
    EntityManager $entityManager,
    NewslettersRepository $newslettersRepository,
    NewsletterOptionsRepository $newsletterOptionsRepository,
    NewsletterOptionFieldsRepository $newsletterOptionFieldsRepository,
    NewsletterSegmentRepository $newsletterSegmentRepository,
    NewsletterTemplatesRepository $newsletterTemplatesRepository,
    PostNotificationScheduler $postNotificationScheduler,
    ScheduledTasksRepository $scheduledTasksRepository,
    SettingsController $settings,
    Security $security,
    WPFunctions $wp,
    ApiDataSanitizer $dataSanitizer,
    Scheduler $scheduler,
    NewsletterCoupon $newsletterCoupon
  ) {
    $this->authorizedEmailsController = $authorizedEmailsController;
    $this->emoji = $emoji;
    $this->entityManager = $entityManager;
    $this->newslettersRepository = $newslettersRepository;
    $this->newsletterOptionsRepository = $newsletterOptionsRepository;
    $this->newsletterOptionFieldsRepository = $newsletterOptionFieldsRepository;
    $this->newsletterSegmentRepository = $newsletterSegmentRepository;
    $this->newsletterTemplatesRepository = $newsletterTemplatesRepository;
    $this->postNotificationScheduler = $postNotificationScheduler;
    $this->scheduledTasksRepository = $scheduledTasksRepository;
    $this->settings = $settings;
    $this->security = $security;
    $this->wp = $wp;
    $this->dataSanitizer = $dataSanitizer;
    $this->scheduler = $scheduler;
    $this->newsletterCoupon = $newsletterCoupon;
  }

  public function save(array $data = []): NewsletterEntity {
    if (!empty($data['template_id'])) {
      $template = $this->newsletterTemplatesRepository->findOneById($data['template_id']);
      if ($template) {
        $data['body'] = json_encode($template->getBody());
      }
    }

    if (!empty($data['body'])) {
      $body = $this->emoji->encodeForUTF8Column(MP_NEWSLETTERS_TABLE, 'body', $data['body']);
      $body = $this->dataSanitizer->sanitizeBody(json_decode($body, true));
      $data['body'] = json_encode($body);
    }

    $newsletter = isset($data['id']) ? $this->getNewsletter($data) : $this->createNewsletter($data);
    $data = $this->sanitizeAutomationEmailData($data, $newsletter);
    $oldSenderAddress = $newsletter->getSenderAddress();

    $this->updateNewsletter($newsletter, $data);
    $this->newslettersRepository->flush();
    if (!empty($data['segments'])) {
      $this->updateSegments($newsletter, $data['segments']);
    }
    if (!empty($data['options'])) {
      $this->updateOptions($newsletter, $data['options']);
    }

    // save default sender if needed
    if (!$this->settings->get('sender') && !empty($data['sender_address']) && !empty($data['sender_name'])) {
      $this->settings->set('sender', [
        'address' => $data['sender_address'],
        'name' => $data['sender_name'],
      ]);
    }

    $this->rescheduleIfNeeded($newsletter);
    $this->updateQueue($newsletter, $data['options'] ?? []);
    $this->authorizedEmailsController->onNewsletterSenderAddressUpdate($newsletter, $oldSenderAddress);
    if (isset($data['new_editor']) && $data['new_editor']) {
      $this->ensureWpPost($newsletter);
    }
    return $newsletter;
  }

  private function sanitizeAutomationEmailData(array $data, NewsletterEntity $newsletter): array {
    if ($newsletter->getType() !== NewsletterEntity::TYPE_AUTOMATION) {
      return $data;
    }
    $data['segments'] = [];
    return $data;
  }

  public function duplicate(NewsletterEntity $newsletter): NewsletterEntity {
    $duplicate = clone $newsletter;

    // reset timestamps
    $createdAt = Carbon::createFromTimestamp($this->wp->currentTime('timestamp'));
    $duplicate->setCreatedAt($createdAt);
    $duplicate->setUpdatedAt($createdAt);
    $duplicate->setDeletedAt(null);

    // translators: %s is the subject of the mail which has been copied.
    $duplicate->setSubject(sprintf(__('Copy of %s', 'mailpoet'), $newsletter->getSubject()));
    // generate new unsubscribe token
    $duplicate->setUnsubscribeToken($this->security->generateUnsubscribeTokenByEntity($duplicate));
    // reset status
    $duplicate->setStatus(NewsletterEntity::STATUS_DRAFT);
    // reset hash
    $duplicate->setHash(Security::generateHash());
    // reset sent at date
    $duplicate->setSentAt(null);

    $body = $duplicate->getBody();
    if ($body) {
      $duplicate->setBody($this->newsletterCoupon->cleanupBodySensitiveData($body));
    }
    $this->newslettersRepository->persist($duplicate);
    $this->newslettersRepository->flush();

    // duplicate wp post data
    $post = $this->wp->getPost($newsletter->getWpPostId());
    if ($post instanceof \WP_Post) {
      $newPostId = $this->wp->wpInsertPost([
        'post_status' => NewsletterEntity::STATUS_DRAFT,
        'post_author' => $this->wp->getCurrentUserId(),
        'post_content' => $post->post_content, // @phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        'post_type' => $post->post_type, // @phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        // translators: %s is the campaign name of the mail which has been copied.
        'post_title' => sprintf(__('Copy of %s', 'mailpoet'), $post->post_title), // @phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
      ]);
      // Post meta duplication
      $originalPostMeta = $this->wp->getPostMeta($post->ID);
      foreach ($originalPostMeta as $key => $values) {
        foreach ($values as $value) {
          // Unserialize the value if it was serialized to avoid invalid data format
          if (is_string($value) && is_serialized($value)) {
            $value = unserialize($value);
          }
          update_post_meta($newPostId, $key, $value);
        }
      }

      $duplicate->setWpPost($this->entityManager->getReference(WpPostEntity::class, $newPostId));
    }

    // create relationships between duplicate and segments
    foreach ($newsletter->getNewsletterSegments() as $newsletterSegment) {
      $segment = $newsletterSegment->getSegment();
      if (!$segment) {
          continue;
      }
      $duplicateSegment = new NewsletterSegmentEntity($duplicate, $segment);
      $duplicate->getNewsletterSegments()->add($duplicateSegment);
      $this->newsletterSegmentRepository->persist($duplicateSegment);
    }

    // duplicate options
    $ignoredOptions = [
      NewsletterOptionFieldEntity::NAME_IS_SCHEDULED,
      NewsletterOptionFieldEntity::NAME_SCHEDULED_AT,
    ];
    foreach ($newsletter->getOptions() as $newsletterOption) {
      $optionField = $newsletterOption->getOptionField();
      if (!$optionField) {
        continue;
      }
      if (in_array($optionField->getName(), $ignoredOptions, true)) {
        continue;
      }
      $duplicateOption = new NewsletterOptionEntity($duplicate, $optionField);
      $duplicateOption->setValue($newsletterOption->getValue());
      $duplicate->getOptions()->add($duplicateOption);
      $this->newsletterOptionsRepository->persist($duplicateOption);
    }
    $this->newslettersRepository->flush();

    return $duplicate;
  }

  private function getNewsletter(array $data): NewsletterEntity {
    if (!isset($data['id'])) {
      throw new UnexpectedValueException();
    }

    $newsletter = $this->newslettersRepository->findOneById((int)$data['id']);
    if (!$newsletter) {
      throw new NotFoundException();
    }
    return $newsletter;
  }

  private function createNewsletter(array $data): NewsletterEntity {
    $newsletter = new NewsletterEntity();
    $newsletter->setUnsubscribeToken($this->security->generateUnsubscribeTokenByEntity($newsletter));
    $newsletter->setHash(Security::generateHash());
    // set default sender based on settings
    if (empty($data['sender'])) {
      $sender = $this->settings->get('sender', []);
      $data['sender_name'] = $sender['name'] ?? '';
      $data['sender_address'] = $sender['address'] ?? '';
    }

    // set default reply_to based on settings
    if (empty($data['reply_to'])) {
      $replyTo = $this->settings->get('reply_to', []);
      $data['reply_to_name'] = $replyTo['name'] ?? '';
      $data['reply_to_address'] = $replyTo['address'] ?? '';
    }

    $this->updateNewsletter($newsletter, $data);
    $this->newslettersRepository->persist($newsletter);
    return $newsletter;
  }

  private function updateNewsletter(NewsletterEntity $newsletter, array $data) {
    if (array_key_exists('type', $data)) {
      $newsletter->setType($data['type']);
    }

    if (array_key_exists('subject', $data)) {
      $newsletter->setSubject($data['subject']);
    }

    if (array_key_exists('preheader', $data)) {
      $newsletter->setPreheader($data['preheader']);
    }

    if (array_key_exists('body', $data)) {
      $newsletter->setBody(json_decode($data['body'], true));
    }

    if (array_key_exists('ga_campaign', $data)) {
      $newsletter->setGaCampaign($data['ga_campaign']);
    }

    if (array_key_exists('sender_name', $data)) {
      $newsletter->setSenderName($data['sender_name'] ?? '');
    }

    if (array_key_exists('sender_address', $data)) {
      $newsletter->setSenderAddress($data['sender_address'] ?? '');
    }

    if (array_key_exists('reply_to_name', $data)) {
      $newsletter->setReplyToName($data['reply_to_name'] ?? '');
    }

    if (array_key_exists('reply_to_address', $data)) {
      $newsletter->setReplyToAddress($data['reply_to_address'] ?? '');
    }

    if ($newsletter->getStatus() === NewsletterEntity::STATUS_CORRUPT) {
      $newsletter->setStatus(NewsletterEntity::STATUS_SENDING);
    }
  }

  private function updateSegments(NewsletterEntity $newsletter, array $segments) {
    $newsletterSegments = [];
    foreach ($segments as $segmentData) {
      if (!is_array($segmentData) || !isset($segmentData['id'])) {
        continue;
      }

      $segment = $this->entityManager->getReference(SegmentEntity::class, (int)$segmentData['id']);
      if (!$segment) {
        continue;
      }

      $newsletterSegment = $this->newsletterSegmentRepository->findOneBy([
        'newsletter' => $newsletter,
        'segment' => $segment,
      ]);

      if (!$newsletterSegment) {
        $newsletterSegment = new NewsletterSegmentEntity($newsletter, $segment);
        $this->entityManager->persist($newsletterSegment);
      }

      if (!$newsletter->getNewsletterSegments()->contains($newsletterSegment)) {
        $newsletter->getNewsletterSegments()->add($newsletterSegment);
      }
      $newsletterSegments[] = $newsletterSegment;
    }

    // on Doctrine < 2.6, when using orphan removal, we need to remove items manually instead of replacing the
    // whole collection (see https://github.com/doctrine/orm/commit/1587aac4ff6b0753ddd5f8b8d4558b6b40096057)
    foreach ($newsletter->getNewsletterSegments() as $newsletterSegment) {
      if (!in_array($newsletterSegment, $newsletterSegments, true)) {
        $newsletter->getNewsletterSegments()->removeElement($newsletterSegment); // triggers orphan removal
      }
    }

    $this->entityManager->flush();
  }

  private function updateOptions(NewsletterEntity $newsletter, array $options) {
    $optionFields = $this->newsletterOptionFieldsRepository->findBy(['newsletterType' => $newsletter->getType()]);
    foreach ($optionFields as $optionField) {
      if (!isset($options[$optionField->getName()])) {
        continue;
      }

      $option = $this->newsletterOptionsRepository->findOneBy([
        'newsletter' => $newsletter,
        'optionField' => $optionField,
      ]);

      if (!$option) {
        $option = new NewsletterOptionEntity($newsletter, $optionField);
        $this->newsletterOptionsRepository->persist($option);
      }
      $option->setValue($options[$optionField->getName()]);

      if (!$newsletter->getOptions()->contains($option)) {
        $newsletter->getOptions()->add($option);
      }
    }

    $this->entityManager->flush();
  }

  private function rescheduleIfNeeded(NewsletterEntity $newsletter) {
    if ($newsletter->getType() !== NewsletterEntity::TYPE_NOTIFICATION) {
      return;
    }

    // generate the new schedule from options and get the new "next run" date
    $schedule = $this->postNotificationScheduler->processPostNotificationSchedule($newsletter);
    $nextRunDateString = $this->scheduler->getNextRunDate($schedule);
    $nextRunDate = $nextRunDateString ? Carbon::createFromFormat('Y-m-d H:i:s', $nextRunDateString) : null;
    if ($nextRunDate === false) {
      throw InvalidStateException::create()->withMessage('Invalid next run date generated');
    }

    // find previously scheduled jobs and reschedule them
    $scheduledTasks = $this->scheduledTasksRepository->findByNewsletterAndStatus($newsletter, ScheduledTaskEntity::STATUS_SCHEDULED);
    foreach ($scheduledTasks as $scheduledTask) {
      $scheduledTask->setScheduledAt($nextRunDate);
    }
    $this->entityManager->flush();

    // 'processPostNotificationSchedule' modifies newsletter options by old model - let's reload them
    foreach ($newsletter->getOptions() as $newsletterOption) {
      $this->entityManager->refresh($newsletterOption);
    }
  }

  private function updateQueue(NewsletterEntity $newsletter, array $options) {
    if ($newsletter->getType() !== NewsletterEntity::TYPE_STANDARD) {
      return;
    }

    $queue = $newsletter->getLatestQueue();
    if (!$queue) {
      return;
    }

    // if newsletter was previously scheduled and is now unscheduled, set its status to DRAFT and delete associated queue record
    if ($newsletter->getStatus() === NewsletterEntity::STATUS_SCHEDULED && isset($options['isScheduled']) && empty($options['isScheduled'])) {
      $this->entityManager->remove($queue);
      $newsletter->setStatus(NewsletterEntity::STATUS_DRAFT);
    } else {
      $queue->setNewsletterRenderedSubject(null);
      $queue->setNewsletterRenderedBody(null);
      $this->entityManager->persist($queue);

      $newsletterQueueTask = new NewsletterQueueTask();
      $task = $queue->getTask();

      if (!$task instanceof ScheduledTaskEntity) {
        throw new InvalidStateException();
      }

      $newsletterQueueTask->preProcessNewsletter($newsletter, $task);
    }
    $this->entityManager->flush();
  }

  private function ensureWpPost(NewsletterEntity $newsletter): void {
    if ($newsletter->getWpPostId()) {
      return;
    }

    $newPostId = $this->wp->wpInsertPost([
      'post_content' => '',
      'post_type' => EmailEditor::MAILPOET_EMAIL_POST_TYPE,
      'post_status' => 'draft',
      'post_author' => $this->wp->getCurrentUserId(),
      'post_title' => __('New Email', 'mailpoet'),
    ]);
    $newsletter->setWpPost($this->entityManager->getReference(WpPostEntity::class, $newPostId));
    $this->entityManager->flush();
  }
}
