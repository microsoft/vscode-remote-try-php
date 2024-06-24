<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\API\JSON;

if (!defined('ABSPATH')) exit;


class SuccessResponse extends Response {
  public $data;

  public function __construct(
    $data = [],
    $meta = [],
    $status = self::STATUS_OK
  ) {
    parent::__construct($status, $meta);
    $this->data = $data;
  }

  public function getData() {
    if ($this->data === null) return [];

    return [
      'data' => $this->data,
    ];
  }
}
