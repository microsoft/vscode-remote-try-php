<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\WP;

if (!defined('ABSPATH')) exit;


class Readme {
  public static function parseChangelog($readmeTxt, $limit = null) {
    // Extract changelog section of the readme.txt
    preg_match('/== Changelog ==(.*?)(\n==|$)/is', $readmeTxt, $changelog);

    if (empty($changelog[1])) {
      return false;
    }

    // Get changelog entries
    $entries = preg_split('/\n(?=\=)/', trim($changelog[1]), -1, PREG_SPLIT_NO_EMPTY);

    if (empty($entries)) {
      return false;
    }

    $c = 0;
    $changelog = [];

    foreach ($entries as $entry) {
      // Locate version header and changes list
      preg_match('/=(.*?)=(.*)/s', $entry, $parts);

      if (empty($parts[1]) || empty($parts[2])) {
        return false;
      }

      $header = trim($parts[1]);
      $list = trim($parts[2]);

      // Get individual items from the list
      $list = preg_split('/(^|\n)[\* ]*/', $list, -1, PREG_SPLIT_NO_EMPTY);

      $changelog[] = [
        'version' => $header,
        'changes' => $list,
      ];

      if (++$c == $limit) {
        break;
      }
    }

    return $changelog;
  }
}
