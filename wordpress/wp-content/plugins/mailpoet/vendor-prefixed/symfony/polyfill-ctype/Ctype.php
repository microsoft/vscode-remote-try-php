<?php
namespace MailPoetVendor\Symfony\Polyfill\Ctype;
if (!defined('ABSPATH')) exit;
final class Ctype
{
 public static function ctype_alnum($text)
 {
 $text = self::convert_int_to_char_for_ctype($text);
 return \is_string($text) && '' !== $text && !\preg_match('/[^A-Za-z0-9]/', $text);
 }
 public static function ctype_alpha($text)
 {
 $text = self::convert_int_to_char_for_ctype($text);
 return \is_string($text) && '' !== $text && !\preg_match('/[^A-Za-z]/', $text);
 }
 public static function ctype_cntrl($text)
 {
 $text = self::convert_int_to_char_for_ctype($text);
 return \is_string($text) && '' !== $text && !\preg_match('/[^\\x00-\\x1f\\x7f]/', $text);
 }
 public static function ctype_digit($text)
 {
 $text = self::convert_int_to_char_for_ctype($text);
 return \is_string($text) && '' !== $text && !\preg_match('/[^0-9]/', $text);
 }
 public static function ctype_graph($text)
 {
 $text = self::convert_int_to_char_for_ctype($text);
 return \is_string($text) && '' !== $text && !\preg_match('/[^!-~]/', $text);
 }
 public static function ctype_lower($text)
 {
 $text = self::convert_int_to_char_for_ctype($text);
 return \is_string($text) && '' !== $text && !\preg_match('/[^a-z]/', $text);
 }
 public static function ctype_print($text)
 {
 $text = self::convert_int_to_char_for_ctype($text);
 return \is_string($text) && '' !== $text && !\preg_match('/[^ -~]/', $text);
 }
 public static function ctype_punct($text)
 {
 $text = self::convert_int_to_char_for_ctype($text);
 return \is_string($text) && '' !== $text && !\preg_match('/[^!-\\/\\:-@\\[-`\\{-~]/', $text);
 }
 public static function ctype_space($text)
 {
 $text = self::convert_int_to_char_for_ctype($text);
 return \is_string($text) && '' !== $text && !\preg_match('/[^\\s]/', $text);
 }
 public static function ctype_upper($text)
 {
 $text = self::convert_int_to_char_for_ctype($text);
 return \is_string($text) && '' !== $text && !\preg_match('/[^A-Z]/', $text);
 }
 public static function ctype_xdigit($text)
 {
 $text = self::convert_int_to_char_for_ctype($text);
 return \is_string($text) && '' !== $text && !\preg_match('/[^A-Fa-f0-9]/', $text);
 }
 private static function convert_int_to_char_for_ctype($int)
 {
 if (!\is_int($int)) {
 return $int;
 }
 if ($int < -128 || $int > 255) {
 return (string) $int;
 }
 if ($int < 0) {
 $int += 256;
 }
 return \chr($int);
 }
}
