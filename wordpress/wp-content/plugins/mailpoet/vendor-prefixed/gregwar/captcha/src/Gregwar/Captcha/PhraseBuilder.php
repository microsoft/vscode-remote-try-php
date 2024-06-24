<?php
namespace MailPoetVendor\Gregwar\Captcha;
if (!defined('ABSPATH')) exit;
class PhraseBuilder implements PhraseBuilderInterface
{
 public $length;
 public $charset;
 public function __construct($length = 5, $charset = 'abcdefghijklmnpqrstuvwxyz123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ')
 {
 $this->length = $length;
 $this->charset = $charset;
 }
 public function build($length = null, $charset = null)
 {
 if ($length !== null) {
 $this->length = $length;
 }
 if ($charset !== null) {
 $this->charset = $charset;
 }
 $phrase = '';
 $chars = \str_split($this->charset);
 for ($i = 0; $i < $this->length; $i++) {
 $phrase .= $chars[\array_rand($chars)];
 }
 return $phrase;
 }
 public function niceize($str)
 {
 return self::doNiceize($str);
 }
 public static function doNiceize($str)
 {
 return \strtr(\strtolower($str), '01', 'ol');
 }
 public static function comparePhrases($str1, $str2)
 {
 return self::doNiceize($str1) === self::doNiceize($str2);
 }
}
