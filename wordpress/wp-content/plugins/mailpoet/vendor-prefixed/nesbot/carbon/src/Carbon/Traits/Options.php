<?php
namespace MailPoetVendor\Carbon\Traits;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Carbon\CarbonInterface;
use DateTimeInterface;
use Throwable;
trait Options
{
 use Localization;
 public static $PHPIntSize = \PHP_INT_SIZE;
 protected static $weekStartsAt = CarbonInterface::MONDAY;
 protected static $weekEndsAt = CarbonInterface::SUNDAY;
 protected static $weekendDays = [CarbonInterface::SATURDAY, CarbonInterface::SUNDAY];
 protected static $regexFormats = [
 'd' => '(3[01]|[12][0-9]|0[1-9])',
 'D' => '(Sun|Mon|Tue|Wed|Thu|Fri|Sat)',
 'j' => '([123][0-9]|[1-9])',
 'l' => '([a-zA-Z]{2,})',
 'N' => '([1-7])',
 'S' => '(st|nd|rd|th)',
 'w' => '([0-6])',
 'z' => '(36[0-5]|3[0-5][0-9]|[12][0-9]{2}|[1-9]?[0-9])',
 'W' => '(5[012]|[1-4][0-9]|0?[1-9])',
 'F' => '([a-zA-Z]{2,})',
 'm' => '(1[012]|0[1-9])',
 'M' => '([a-zA-Z]{3})',
 'n' => '(1[012]|[1-9])',
 't' => '(2[89]|3[01])',
 'L' => '(0|1)',
 'o' => '([1-9][0-9]{0,4})',
 'Y' => '([1-9]?[0-9]{4})',
 'y' => '([0-9]{2})',
 'a' => '(am|pm)',
 'A' => '(AM|PM)',
 'B' => '([0-9]{3})',
 'g' => '(1[012]|[1-9])',
 'G' => '(2[0-3]|1?[0-9])',
 'h' => '(1[012]|0[1-9])',
 'H' => '(2[0-3]|[01][0-9])',
 'i' => '([0-5][0-9])',
 's' => '([0-5][0-9])',
 'u' => '([0-9]{1,6})',
 'v' => '([0-9]{1,3})',
 'e' => '([a-zA-Z]{1,5})|([a-zA-Z]*\\/[a-zA-Z]*)',
 'I' => '(0|1)',
 'O' => '([+-](1[012]|0[0-9])[0134][05])',
 'P' => '([+-](1[012]|0[0-9]):[0134][05])',
 'p' => '(Z|[+-](1[012]|0[0-9]):[0134][05])',
 'T' => '([a-zA-Z]{1,5})',
 'Z' => '(-?[1-5]?[0-9]{1,4})',
 'U' => '([0-9]*)',
 // The formats below are combinations of the above formats.
 'c' => '(([1-9]?[0-9]{4})-(1[012]|0[1-9])-(3[01]|[12][0-9]|0[1-9])T(2[0-3]|[01][0-9]):([0-5][0-9]):([0-5][0-9])[+-](1[012]|0[0-9]):([0134][05]))',
 // Y-m-dTH:i:sP
 'r' => '(([a-zA-Z]{3}), ([123][0-9]|0[1-9]) ([a-zA-Z]{3}) ([1-9]?[0-9]{4}) (2[0-3]|[01][0-9]):([0-5][0-9]):([0-5][0-9]) [+-](1[012]|0[0-9])([0134][05]))',
 ];
 protected static $regexFormatModifiers = ['*' => '.+', ' ' => '[ ]', '#' => '[;:\\/.,()-]', '?' => '([^a]|[a])', '!' => '', '|' => '', '+' => ''];
 protected static $monthsOverflow = \true;
 protected static $yearsOverflow = \true;
 protected static $strictModeEnabled = \true;
 protected static $formatFunction;
 protected static $createFromFormatFunction;
 protected static $parseFunction;
 protected $localMonthsOverflow;
 protected $localYearsOverflow;
 protected $localStrictModeEnabled;
 protected $localHumanDiffOptions;
 protected $localToStringFormat;
 protected $localSerializer;
 protected $localMacros;
 protected $localGenericMacros;
 protected $localFormatFunction;
 public static function useStrictMode($strictModeEnabled = \true)
 {
 static::$strictModeEnabled = $strictModeEnabled;
 }
 public static function isStrictModeEnabled()
 {
 return static::$strictModeEnabled;
 }
 public static function useMonthsOverflow($monthsOverflow = \true)
 {
 static::$monthsOverflow = $monthsOverflow;
 }
 public static function resetMonthsOverflow()
 {
 static::$monthsOverflow = \true;
 }
 public static function shouldOverflowMonths()
 {
 return static::$monthsOverflow;
 }
 public static function useYearsOverflow($yearsOverflow = \true)
 {
 static::$yearsOverflow = $yearsOverflow;
 }
 public static function resetYearsOverflow()
 {
 static::$yearsOverflow = \true;
 }
 public static function shouldOverflowYears()
 {
 return static::$yearsOverflow;
 }
 public function settings(array $settings)
 {
 $this->localStrictModeEnabled = $settings['strictMode'] ?? null;
 $this->localMonthsOverflow = $settings['monthOverflow'] ?? null;
 $this->localYearsOverflow = $settings['yearOverflow'] ?? null;
 $this->localHumanDiffOptions = $settings['humanDiffOptions'] ?? null;
 $this->localToStringFormat = $settings['toStringFormat'] ?? null;
 $this->localSerializer = $settings['toJsonFormat'] ?? null;
 $this->localMacros = $settings['macros'] ?? null;
 $this->localGenericMacros = $settings['genericMacros'] ?? null;
 $this->localFormatFunction = $settings['formatFunction'] ?? null;
 if (isset($settings['locale'])) {
 $locales = $settings['locale'];
 if (!\is_array($locales)) {
 $locales = [$locales];
 }
 $this->locale(...$locales);
 }
 if (isset($settings['innerTimezone'])) {
 return $this->setTimezone($settings['innerTimezone']);
 }
 if (isset($settings['timezone'])) {
 return $this->shiftTimezone($settings['timezone']);
 }
 return $this;
 }
 public function getSettings()
 {
 $settings = [];
 $map = ['localStrictModeEnabled' => 'strictMode', 'localMonthsOverflow' => 'monthOverflow', 'localYearsOverflow' => 'yearOverflow', 'localHumanDiffOptions' => 'humanDiffOptions', 'localToStringFormat' => 'toStringFormat', 'localSerializer' => 'toJsonFormat', 'localMacros' => 'macros', 'localGenericMacros' => 'genericMacros', 'locale' => 'locale', 'tzName' => 'timezone', 'localFormatFunction' => 'formatFunction'];
 foreach ($map as $property => $key) {
 $value = $this->{$property} ?? null;
 if ($value !== null) {
 $settings[$key] = $value;
 }
 }
 return $settings;
 }
 public function __debugInfo()
 {
 $infos = \array_filter(\get_object_vars($this), function ($var) {
 return $var;
 });
 foreach (['dumpProperties', 'constructedObjectId'] as $property) {
 if (isset($infos[$property])) {
 unset($infos[$property]);
 }
 }
 $this->addExtraDebugInfos($infos);
 return $infos;
 }
 protected function addExtraDebugInfos(&$infos) : void
 {
 if ($this instanceof DateTimeInterface) {
 try {
 if (!isset($infos['date'])) {
 $infos['date'] = $this->format(CarbonInterface::MOCK_DATETIME_FORMAT);
 }
 if (!isset($infos['timezone'])) {
 $infos['timezone'] = $this->tzName;
 }
 } catch (Throwable $exception) {
 // noop
 }
 }
 }
}
