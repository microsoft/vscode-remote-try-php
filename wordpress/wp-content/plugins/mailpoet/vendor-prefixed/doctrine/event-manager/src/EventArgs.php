<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\Common;
if (!defined('ABSPATH')) exit;
class EventArgs
{
 private static $_emptyEventArgsInstance;
 public static function getEmptyInstance()
 {
 if (!self::$_emptyEventArgsInstance) {
 self::$_emptyEventArgsInstance = new EventArgs();
 }
 return self::$_emptyEventArgsInstance;
 }
}
