<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\WordPress\Payloads;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Integration\Payload;
use MailPoet\Automation\Engine\WordPress;

class CommentPayload implements Payload {
  /** @var int */
  private $commentId;

  /** @var WordPress */
  protected $wp;

  public function __construct(
    int $commentId,
    WordPress $wp
  ) {
    $this->commentId = $commentId;
    $this->wp = $wp;
  }

  public function getCommentId(): int {
    return $this->commentId;
  }

  public function getComment(): ?\WP_Comment {
    $comment = $this->wp->getComment($this->commentId);
    return $comment instanceof \WP_Comment ? $comment : null;
  }
}
