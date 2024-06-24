<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\API\JSON;

if (!defined('ABSPATH')) exit;


final class Error {
  const UNKNOWN = 'unknown';
  const BAD_REQUEST = 'bad_request';
  const UNAUTHORIZED = 'unauthorized';
  const FORBIDDEN = 'forbidden';
  const NOT_FOUND = 'not_found';
  const REINSTALL_PLUGIN = 'reinstall_plugin';

  private function __construct() {

  }
}
