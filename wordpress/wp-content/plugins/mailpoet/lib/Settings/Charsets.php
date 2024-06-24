<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Settings;

if (!defined('ABSPATH')) exit;


class Charsets {
  public static function getAll() {
    return [
      'UTF-8', 'UTF-7', 'BIG5', 'ISO-2022-JP',
      'ISO-8859-1', 'ISO-8859-2', 'ISO-8859-3',
      'ISO-8859-4', 'ISO-8859-5', 'ISO-8859-6',
      'ISO-8859-7', 'ISO-8859-8', 'ISO-8859-9',
      'ISO-8859-10', 'ISO-8859-13', 'ISO-8859-14',
      'ISO-8859-15', 'Windows-1251', 'Windows-1252',
    ];
  }
}
