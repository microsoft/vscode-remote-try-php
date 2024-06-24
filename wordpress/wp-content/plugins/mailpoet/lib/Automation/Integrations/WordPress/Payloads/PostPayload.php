<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\WordPress\Payloads;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Integration\Payload;
use MailPoet\Automation\Engine\WordPress;

class PostPayload implements Payload {
  /** @var int */
  private $postId;

  /** @var WordPress */
  private $wp;

  public function __construct(
    int $postId,
    WordPress $wp
  ) {
    $this->postId = $postId;
    $this->wp = $wp;
  }

  public function getPostId(): int {
    return $this->postId;
  }

  public function getPost(): ?\WP_Post {
    $post = $this->wp->getPost($this->postId);
    return $post instanceof \WP_Post ? $post : null;
  }
}
