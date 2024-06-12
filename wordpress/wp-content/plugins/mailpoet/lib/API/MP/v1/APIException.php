<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\API\MP\v1;

if (!defined('ABSPATH')) exit;


class APIException extends \Exception {
  const FAILED_TO_SAVE_SUBSCRIBER_FIELD = 1;
  const SEGMENT_REQUIRED = 3;
  const SUBSCRIBER_NOT_EXISTS = 4;
  const LIST_NOT_EXISTS = 5;
  const SUBSCRIBING_TO_WP_LIST_NOT_ALLOWED = 6;
  const SUBSCRIBING_TO_WC_LIST_NOT_ALLOWED = 7;
  const SUBSCRIBING_TO_LIST_NOT_ALLOWED = 8;
  const CONFIRMATION_FAILED_TO_SEND = 10;
  const EMAIL_ADDRESS_REQUIRED = 11;
  const SUBSCRIBER_EXISTS = 12;
  const FAILED_TO_SAVE_SUBSCRIBER = 13;
  const LIST_NAME_REQUIRED = 14;
  const LIST_EXISTS = 15;
  const FAILED_TO_SAVE_LIST = 16;
  const WELCOME_FAILED_TO_SEND = 17;
  const LIST_ID_REQUIRED = 18;
  const FAILED_TO_UPDATE_LIST = 19;
  const LIST_USED_IN_EMAIL = 20;
  const LIST_USED_IN_FORM = 21;
  const FAILED_TO_DELETE_LIST = 22;
  const LIST_TYPE_IS_NOT_SUPPORTED = 23;
  const SUBSCRIBER_ALREADY_UNSUBSCRIBED = 24;
}
