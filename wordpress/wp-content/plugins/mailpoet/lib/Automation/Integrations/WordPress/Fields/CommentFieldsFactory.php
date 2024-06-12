<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\WordPress\Fields;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Field;
use MailPoet\Automation\Engine\WordPress;
use MailPoet\Automation\Integrations\WordPress\Payloads\CommentPayload;

class CommentFieldsFactory {

  /** @var WordPress */
  private $wp;

  public function __construct(
    WordPress $wp
  ) {
    $this->wp = $wp;
  }

  /**
   * @return Field[]
   */
  public function getFields(): array {
    return [
      new Field(
        'wordpress:comment:id',
        Field::TYPE_INTEGER,
        __('Comment ID', 'mailpoet'),
        function (CommentPayload $payload) {
          return $payload->getCommentId();
        }
      ),
      new Field(
        'wordpress:comment:author-name',
        Field::TYPE_STRING,
        __('Comment author name', 'mailpoet'),
        function (CommentPayload $payload) {
          $comment = $payload->getComment();
          //phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
          return $comment ? $comment->comment_author : null;
        }
      ),
      new Field(
        'wordpress:comment:author-email',
        Field::TYPE_STRING,
        __('Comment author email', 'mailpoet'),
        function (CommentPayload $payload) {
          $comment = $payload->getComment();
          //phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
          return $comment ? $comment->comment_author_email : null;
        }
      ),
      new Field(
        'wordpress:comment:author-url',
        Field::TYPE_STRING,
        __('Comment author URL', 'mailpoet'),
        function (CommentPayload $payload) {
          $comment = $payload->getComment();
          //phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
          return $comment ? $comment->comment_author_url : null;
        }
      ),
      new Field(
        'wordpress:comment:author-ip',
        Field::TYPE_STRING,
        __('Comment author IP', 'mailpoet'),
        function (CommentPayload $payload) {
          $comment = $payload->getComment();
          //phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
          return $comment ? $comment->comment_author_IP : null;
        }
      ),
      new Field(
        'wordpress:comment:date',
        Field::TYPE_DATETIME,
        __('Comment date', 'mailpoet'),
        function (CommentPayload $payload) {
          $comment = $payload->getComment();
          //phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
          return $comment ? $comment->comment_date_gmt : null;
        }
      ),
      new Field(
        'wordpress:comment:content',
        Field::TYPE_STRING,
        __('Comment content', 'mailpoet'),
        function (CommentPayload $payload) {
          $comment = $payload->getComment();
          //phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
          return $comment ? $comment->comment_content : null;
        }
      ),
      new Field(
        'wordpress:comment:karma',
        Field::TYPE_INTEGER,
        __('Comment karma', 'mailpoet'),
        function (CommentPayload $payload) {
          $comment = $payload->getComment();
          //phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
          return $comment ? (int)$comment->comment_karma : null;
        }
      ),
      new Field(
        'wordpress:comment:status',
        Field::TYPE_ENUM,
        __('Comment status', 'mailpoet'),
        function (CommentPayload $payload) {
          $status = $this->wp->wpGetCommentStatus($payload->getCommentId());
          if (!is_string($status)) {
            return null;
          }

          /**
           * wp_get_comment_status returns 'unapproved' and 'approved' where get_comment_statuses returns 'hold' and 'approve'
           * We need to normalize the status for matches.
           */
          if ($status === 'approved') {
            $status = 'approve';
          }
          if ($status === 'unapproved') {
            $status = 'hold';
          }
          return $status;
        },
        [
          'options' => $this->getCommentStatuses(),
        ]
      ),
      new Field(
        'wordpress:comment:comment-agent',
        Field::TYPE_STRING,
        __('Comment user agent', 'mailpoet'),
        function (CommentPayload $payload) {
          $comment = $payload->getComment();
          //phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
          return $comment ? $comment->comment_agent : null;
        }
      ),
      new Field(
        'wordpress:comment:comment-type',
        Field::TYPE_STRING,
        __('Comment type', 'mailpoet'),
        function (CommentPayload $payload) {
          $comment = $payload->getComment();
          //phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
          return $comment ? $comment->comment_type : null;
        }
      ),
      new Field(
        'wordpress:comment:comment-parent',
        Field::TYPE_INTEGER,
        __('Comment parent ID', 'mailpoet'),
        function (CommentPayload $payload) {
          $comment = $payload->getComment();
          //phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
          return $comment ? (int)$comment->comment_parent : null;
        }
      ),
      new Field(
        'wordpress:comment:has-children',
        Field::TYPE_BOOLEAN,
        __('Comment has replies', 'mailpoet'),
        function (CommentPayload $payload) {
          $comment = $payload->getComment();
          //phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
          return $comment ? count($comment->get_children()) > 0 : false;
        }
      ),
    ];
  }

  private function getCommentStatuses(): array {
    $statuses = $this->wp->getCommentStatuses();
    return array_values(array_map(
      function($name, $id): array {
        return [
          'id' => $id,
          'name' => $name,
        ];
      },
      $statuses,
      array_keys($statuses)
    ));
  }
}
