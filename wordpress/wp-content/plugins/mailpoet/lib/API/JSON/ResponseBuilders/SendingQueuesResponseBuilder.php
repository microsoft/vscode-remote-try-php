<?php declare(strict_types = 1);

namespace MailPoet\API\JSON\ResponseBuilders;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Entities\SendingQueueEntity;

class SendingQueuesResponseBuilder {
  public function build(SendingQueueEntity $sendingQueue): array {
    if (!$sendingQueue->getTask() instanceof ScheduledTaskEntity) {
      throw new \RuntimeException('Invalid state. SendingQueue has no ScheduledTask associated.');
    }

    return [
      'id' => $sendingQueue->getId(),
      'type' => $sendingQueue->getTask()->getType(),
      'status' => $sendingQueue->getTask()->getStatus(),
      'priority' => $sendingQueue->getTask()->getPriority(),
      'scheduled_at' => $this->getFormattedDateOrNull($sendingQueue->getTask()->getScheduledAt()),
      'processed_at' => $this->getFormattedDateOrNull($sendingQueue->getTask()->getProcessedAt()),
      'created_at' => $this->getFormattedDateOrNull($sendingQueue->getTask()->getCreatedAt()),
      'updated_at' => $this->getFormattedDateOrNull($sendingQueue->getTask()->getUpdatedAt()),
      'deleted_at' => $this->getFormattedDateOrNull($sendingQueue->getTask()->getDeletedAt()),
      'in_progress' => $sendingQueue->getTask()->getInProgress(),
      'reschedule_count' => $sendingQueue->getTask()->getRescheduleCount(),
      'meta' => $sendingQueue->getMeta(),
      'task_id' => $sendingQueue->getTask()->getId(),
      'newsletter_id' => ($sendingQueue->getNewsletter() instanceof NewsletterEntity) ? $sendingQueue->getNewsletter()->getId() : null,
      'newsletter_rendered_body' => $sendingQueue->getNewsletterRenderedBody(),
      'newsletter_rendered_subject' => $sendingQueue->getNewsletterRenderedSubject(),
      'count_total' => $sendingQueue->getCountTotal(),
      'count_processed' => $sendingQueue->getCountProcessed(),
      'count_to_process' => $sendingQueue->getCountToProcess(),
    ];
  }

  private function getFormattedDateOrNull(?\DateTimeInterface $date): ?string {
    return $date ? $date->format('Y-m-d H:i:s') : null;
  }
}
