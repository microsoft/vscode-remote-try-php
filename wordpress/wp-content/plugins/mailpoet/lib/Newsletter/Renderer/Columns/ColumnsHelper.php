<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter\Renderer\Columns;

if (!defined('ABSPATH')) exit;


class ColumnsHelper {
  public static $columnsWidth = [
    1 => [660],
    2 => [330, 330],
    "1_2" => [220, 440],
    "2_1" => [440, 220],
    3 => [220, 220, 220],
  ];

  public static $columnsClass = [
    1 => 'cols-one',
    2 => 'cols-two',
    3 => 'cols-three',
  ];

  public static $columnsAlignment = [
    1 => null,
    2 => 'left',
    3 => 'right',
  ];

  /** @return int[] */
  public static function columnWidth($columnsCount, $columnsLayout) {
    if (isset(self::$columnsWidth[$columnsLayout])) {
      return self::$columnsWidth[$columnsLayout];
    }
    return self::$columnsWidth[$columnsCount];
  }

  public static function columnClass($columnsCount) {
    return self::$columnsClass[$columnsCount];
  }

  public static function columnClasses() {
    return self::$columnsClass;
  }

  public static function columnAlignment($columnsCount) {
    return self::$columnsAlignment[$columnsCount];
  }
}
