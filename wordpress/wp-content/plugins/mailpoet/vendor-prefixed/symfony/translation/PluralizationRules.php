<?php
namespace MailPoetVendor\Symfony\Component\Translation;
if (!defined('ABSPATH')) exit;
class PluralizationRules
{
 private static $rules = [];
 public static function get($number, $locale)
 {
 $number = \abs($number);
 if (3 > \func_num_args() || \func_get_arg(2)) {
 @\trigger_error(\sprintf('The "%s" class is deprecated since Symfony 4.2.', __CLASS__), \E_USER_DEPRECATED);
 }
 if ('pt_BR' === $locale) {
 // temporary set a locale for brazilian
 $locale = 'xbr';
 }
 if ('en_US_POSIX' !== $locale && \strlen($locale) > 3) {
 $locale = \substr($locale, 0, -\strlen(\strrchr($locale, '_')));
 }
 if (isset(self::$rules[$locale])) {
 $return = self::$rules[$locale]($number);
 if (!\is_int($return) || $return < 0) {
 return 0;
 }
 return $return;
 }
 switch ($locale) {
 case 'az':
 case 'bo':
 case 'dz':
 case 'id':
 case 'ja':
 case 'jv':
 case 'ka':
 case 'km':
 case 'kn':
 case 'ko':
 case 'ms':
 case 'th':
 case 'tr':
 case 'vi':
 case 'zh':
 return 0;
 case 'af':
 case 'bn':
 case 'bg':
 case 'ca':
 case 'da':
 case 'de':
 case 'el':
 case 'en':
 case 'en_US_POSIX':
 case 'eo':
 case 'es':
 case 'et':
 case 'eu':
 case 'fa':
 case 'fi':
 case 'fo':
 case 'fur':
 case 'fy':
 case 'gl':
 case 'gu':
 case 'ha':
 case 'he':
 case 'hu':
 case 'is':
 case 'it':
 case 'ku':
 case 'lb':
 case 'ml':
 case 'mn':
 case 'mr':
 case 'nah':
 case 'nb':
 case 'ne':
 case 'nl':
 case 'nn':
 case 'no':
 case 'oc':
 case 'om':
 case 'or':
 case 'pa':
 case 'pap':
 case 'ps':
 case 'pt':
 case 'so':
 case 'sq':
 case 'sv':
 case 'sw':
 case 'ta':
 case 'te':
 case 'tk':
 case 'ur':
 case 'zu':
 return 1 == $number ? 0 : 1;
 case 'am':
 case 'bh':
 case 'fil':
 case 'fr':
 case 'gun':
 case 'hi':
 case 'hy':
 case 'ln':
 case 'mg':
 case 'nso':
 case 'xbr':
 case 'ti':
 case 'wa':
 return $number < 2 ? 0 : 1;
 case 'be':
 case 'bs':
 case 'hr':
 case 'ru':
 case 'sh':
 case 'sr':
 case 'uk':
 return 1 == $number % 10 && 11 != $number % 100 ? 0 : ($number % 10 >= 2 && $number % 10 <= 4 && ($number % 100 < 10 || $number % 100 >= 20) ? 1 : 2);
 case 'cs':
 case 'sk':
 return 1 == $number ? 0 : ($number >= 2 && $number <= 4 ? 1 : 2);
 case 'ga':
 return 1 == $number ? 0 : (2 == $number ? 1 : 2);
 case 'lt':
 return 1 == $number % 10 && 11 != $number % 100 ? 0 : ($number % 10 >= 2 && ($number % 100 < 10 || $number % 100 >= 20) ? 1 : 2);
 case 'sl':
 return 1 == $number % 100 ? 0 : (2 == $number % 100 ? 1 : (3 == $number % 100 || 4 == $number % 100 ? 2 : 3));
 case 'mk':
 return 1 == $number % 10 ? 0 : 1;
 case 'mt':
 return 1 == $number ? 0 : (0 == $number || $number % 100 > 1 && $number % 100 < 11 ? 1 : ($number % 100 > 10 && $number % 100 < 20 ? 2 : 3));
 case 'lv':
 return 0 == $number ? 0 : (1 == $number % 10 && 11 != $number % 100 ? 1 : 2);
 case 'pl':
 return 1 == $number ? 0 : ($number % 10 >= 2 && $number % 10 <= 4 && ($number % 100 < 12 || $number % 100 > 14) ? 1 : 2);
 case 'cy':
 return 1 == $number ? 0 : (2 == $number ? 1 : (8 == $number || 11 == $number ? 2 : 3));
 case 'ro':
 return 1 == $number ? 0 : (0 == $number || $number % 100 > 0 && $number % 100 < 20 ? 1 : 2);
 case 'ar':
 return 0 == $number ? 0 : (1 == $number ? 1 : (2 == $number ? 2 : ($number % 100 >= 3 && $number % 100 <= 10 ? 3 : ($number % 100 >= 11 && $number % 100 <= 99 ? 4 : 5))));
 default:
 return 0;
 }
 }
 public static function set(callable $rule, $locale)
 {
 @\trigger_error(\sprintf('The "%s" class is deprecated since Symfony 4.2.', __CLASS__), \E_USER_DEPRECATED);
 if ('pt_BR' === $locale) {
 // temporary set a locale for brazilian
 $locale = 'xbr';
 }
 if (\strlen($locale) > 3) {
 $locale = \substr($locale, 0, -\strlen(\strrchr($locale, '_')));
 }
 self::$rules[$locale] = $rule;
 }
}
