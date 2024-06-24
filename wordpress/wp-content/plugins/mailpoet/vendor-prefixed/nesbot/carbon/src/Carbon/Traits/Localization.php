<?php
namespace MailPoetVendor\Carbon\Traits;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Carbon\CarbonInterface;
use MailPoetVendor\Carbon\Exceptions\InvalidTypeException;
use MailPoetVendor\Carbon\Exceptions\NotLocaleAwareException;
use MailPoetVendor\Carbon\Language;
use MailPoetVendor\Carbon\Translator;
use MailPoetVendor\Carbon\TranslatorStrongTypeInterface;
use Closure;
use MailPoetVendor\Symfony\Component\Translation\TranslatorBagInterface;
use MailPoetVendor\Symfony\Component\Translation\TranslatorInterface;
use MailPoetVendor\Symfony\Contracts\Translation\LocaleAwareInterface;
use MailPoetVendor\Symfony\Contracts\Translation\TranslatorInterface as ContractsTranslatorInterface;
// @codeCoverageIgnoreStart
if (\interface_exists('MailPoetVendor\\Symfony\\Contracts\\Translation\\TranslatorInterface') && !\interface_exists('MailPoetVendor\\Symfony\\Component\\Translation\\TranslatorInterface')) {
 \class_alias('MailPoetVendor\\Symfony\\Contracts\\Translation\\TranslatorInterface', 'MailPoetVendor\\Symfony\\Component\\Translation\\TranslatorInterface');
}
// @codeCoverageIgnoreEnd
trait Localization
{
 protected static $translator;
 protected $localTranslator;
 protected static $humanDiffOptions = CarbonInterface::NO_ZERO_DIFF;
 public static function setHumanDiffOptions($humanDiffOptions)
 {
 static::$humanDiffOptions = $humanDiffOptions;
 }
 public static function enableHumanDiffOption($humanDiffOption)
 {
 static::$humanDiffOptions = static::getHumanDiffOptions() | $humanDiffOption;
 }
 public static function disableHumanDiffOption($humanDiffOption)
 {
 static::$humanDiffOptions = static::getHumanDiffOptions() & ~$humanDiffOption;
 }
 public static function getHumanDiffOptions()
 {
 return static::$humanDiffOptions;
 }
 public static function getTranslator()
 {
 return static::translator();
 }
 public static function setTranslator(TranslatorInterface $translator)
 {
 static::$translator = $translator;
 }
 public function hasLocalTranslator()
 {
 return isset($this->localTranslator);
 }
 public function getLocalTranslator()
 {
 return $this->localTranslator ?: static::translator();
 }
 public function setLocalTranslator(TranslatorInterface $translator)
 {
 $this->localTranslator = $translator;
 return $this;
 }
 public static function getTranslationMessageWith($translator, string $key, ?string $locale = null, ?string $default = null)
 {
 if (!($translator instanceof TranslatorBagInterface && $translator instanceof TranslatorInterface)) {
 throw new InvalidTypeException('Translator does not implement ' . TranslatorInterface::class . ' and ' . TranslatorBagInterface::class . '. ' . (\is_object($translator) ? \get_class($translator) : \gettype($translator)) . ' has been given.');
 }
 if (!$locale && $translator instanceof LocaleAwareInterface) {
 $locale = $translator->getLocale();
 }
 $result = self::getFromCatalogue($translator, $translator->getCatalogue($locale), $key);
 return $result === $key ? $default : $result;
 }
 public function getTranslationMessage(string $key, ?string $locale = null, ?string $default = null, $translator = null)
 {
 return static::getTranslationMessageWith($translator ?: $this->getLocalTranslator(), $key, $locale, $default);
 }
 public static function translateWith(TranslatorInterface $translator, string $key, array $parameters = [], $number = null) : string
 {
 $message = static::getTranslationMessageWith($translator, $key, null, $key);
 if ($message instanceof Closure) {
 return (string) $message(...\array_values($parameters));
 }
 if ($number !== null) {
 $parameters['%count%'] = $number;
 }
 if (isset($parameters['%count%'])) {
 $parameters[':count'] = $parameters['%count%'];
 }
 // @codeCoverageIgnoreStart
 $choice = $translator instanceof ContractsTranslatorInterface ? $translator->trans($key, $parameters) : $translator->transChoice($key, $number, $parameters);
 // @codeCoverageIgnoreEnd
 return (string) $choice;
 }
 public function translate(string $key, array $parameters = [], $number = null, ?TranslatorInterface $translator = null, bool $altNumbers = \false) : string
 {
 $translation = static::translateWith($translator ?: $this->getLocalTranslator(), $key, $parameters, $number);
 if ($number !== null && $altNumbers) {
 return \str_replace($number, $this->translateNumber($number), $translation);
 }
 return $translation;
 }
 public function translateNumber(int $number) : string
 {
 $translateKey = "alt_numbers.{$number}";
 $symbol = $this->translate($translateKey);
 if ($symbol !== $translateKey) {
 return $symbol;
 }
 if ($number > 99 && $this->translate('alt_numbers.99') !== 'alt_numbers.99') {
 $start = '';
 foreach ([10000, 1000, 100] as $exp) {
 $key = "alt_numbers_pow.{$exp}";
 if ($number >= $exp && $number < $exp * 10 && ($pow = $this->translate($key)) !== $key) {
 $unit = \floor($number / $exp);
 $number -= $unit * $exp;
 $start .= ($unit > 1 ? $this->translate("alt_numbers.{$unit}") : '') . $pow;
 }
 }
 $result = '';
 while ($number) {
 $chunk = $number % 100;
 $result = $this->translate("alt_numbers.{$chunk}") . $result;
 $number = \floor($number / 100);
 }
 return "{$start}{$result}";
 }
 if ($number > 9 && $this->translate('alt_numbers.9') !== 'alt_numbers.9') {
 $result = '';
 while ($number) {
 $chunk = $number % 10;
 $result = $this->translate("alt_numbers.{$chunk}") . $result;
 $number = \floor($number / 10);
 }
 return $result;
 }
 return (string) $number;
 }
 public static function translateTimeString($timeString, $from = null, $to = null, $mode = CarbonInterface::TRANSLATE_ALL)
 {
 // Fallback source and destination locales
 $from = $from ?: static::getLocale();
 $to = $to ?: 'en';
 if ($from === $to) {
 return $timeString;
 }
 // Standardize apostrophe
 $timeString = \strtr($timeString, ['’' => "'"]);
 $fromTranslations = [];
 $toTranslations = [];
 foreach (['from', 'to'] as $key) {
 $language = ${$key};
 $translator = Translator::get($language);
 $translations = $translator->getMessages();
 if (!isset($translations[$language])) {
 return $timeString;
 }
 $translationKey = $key . 'Translations';
 $messages = $translations[$language];
 $months = $messages['months'] ?? [];
 $weekdays = $messages['weekdays'] ?? [];
 $meridiem = $messages['meridiem'] ?? ['AM', 'PM'];
 if (isset($messages['ordinal_words'])) {
 $timeString = self::replaceOrdinalWords($timeString, $key === 'from' ? \array_flip($messages['ordinal_words']) : $messages['ordinal_words']);
 }
 if ($key === 'from') {
 foreach (['months', 'weekdays'] as $variable) {
 $list = $messages[$variable . '_standalone'] ?? null;
 if ($list) {
 foreach (${$variable} as $index => &$name) {
 $name .= '|' . $messages[$variable . '_standalone'][$index];
 }
 }
 }
 }
 ${$translationKey} = \array_merge($mode & CarbonInterface::TRANSLATE_MONTHS ? static::getTranslationArray($months, 12, $timeString) : [], $mode & CarbonInterface::TRANSLATE_MONTHS ? static::getTranslationArray($messages['months_short'] ?? [], 12, $timeString) : [], $mode & CarbonInterface::TRANSLATE_DAYS ? static::getTranslationArray($weekdays, 7, $timeString) : [], $mode & CarbonInterface::TRANSLATE_DAYS ? static::getTranslationArray($messages['weekdays_short'] ?? [], 7, $timeString) : [], $mode & CarbonInterface::TRANSLATE_DIFF ? static::translateWordsByKeys(['diff_now', 'diff_today', 'diff_yesterday', 'diff_tomorrow', 'diff_before_yesterday', 'diff_after_tomorrow'], $messages, $key) : [], $mode & CarbonInterface::TRANSLATE_UNITS ? static::translateWordsByKeys(['year', 'month', 'week', 'day', 'hour', 'minute', 'second'], $messages, $key) : [], $mode & CarbonInterface::TRANSLATE_MERIDIEM ? \array_map(function ($hour) use($meridiem) {
 if (\is_array($meridiem)) {
 return $meridiem[$hour < 12 ? 0 : 1];
 }
 return $meridiem($hour, 0, \false);
 }, \range(0, 23)) : []);
 }
 return \substr(\preg_replace_callback('/(?<=[\\d\\s+.\\/,_-])(' . \implode('|', $fromTranslations) . ')(?=[\\d\\s+.\\/,_-])/iu', function ($match) use($fromTranslations, $toTranslations) {
 [$chunk] = $match;
 foreach ($fromTranslations as $index => $word) {
 if (\preg_match("/^{$word}\$/iu", $chunk)) {
 return $toTranslations[$index] ?? '';
 }
 }
 return $chunk;
 // @codeCoverageIgnore
 }, " {$timeString} "), 1, -1);
 }
 public function translateTimeStringTo($timeString, $to = null)
 {
 return static::translateTimeString($timeString, $this->getTranslatorLocale(), $to);
 }
 public function locale(string $locale = null, ...$fallbackLocales)
 {
 if ($locale === null) {
 return $this->getTranslatorLocale();
 }
 if (!$this->localTranslator || $this->getTranslatorLocale($this->localTranslator) !== $locale) {
 $translator = Translator::get($locale);
 if (!empty($fallbackLocales)) {
 $translator->setFallbackLocales($fallbackLocales);
 foreach ($fallbackLocales as $fallbackLocale) {
 $messages = Translator::get($fallbackLocale)->getMessages();
 if (isset($messages[$fallbackLocale])) {
 $translator->setMessages($fallbackLocale, $messages[$fallbackLocale]);
 }
 }
 }
 $this->localTranslator = $translator;
 }
 return $this;
 }
 public static function getLocale()
 {
 return static::getLocaleAwareTranslator()->getLocale();
 }
 public static function setLocale($locale)
 {
 return static::getLocaleAwareTranslator()->setLocale($locale) !== \false;
 }
 public static function setFallbackLocale($locale)
 {
 $translator = static::getTranslator();
 if (\method_exists($translator, 'setFallbackLocales')) {
 $translator->setFallbackLocales([$locale]);
 if ($translator instanceof Translator) {
 $preferredLocale = $translator->getLocale();
 $translator->setMessages($preferredLocale, \array_replace_recursive($translator->getMessages()[$locale] ?? [], Translator::get($locale)->getMessages()[$locale] ?? [], $translator->getMessages($preferredLocale)));
 }
 }
 }
 public static function getFallbackLocale()
 {
 $translator = static::getTranslator();
 if (\method_exists($translator, 'getFallbackLocales')) {
 return $translator->getFallbackLocales()[0] ?? null;
 }
 return null;
 }
 public static function executeWithLocale($locale, $func)
 {
 $currentLocale = static::getLocale();
 $result = $func(static::setLocale($locale) ? static::getLocale() : \false, static::translator());
 static::setLocale($currentLocale);
 return $result;
 }
 public static function localeHasShortUnits($locale)
 {
 return static::executeWithLocale($locale, function ($newLocale, TranslatorInterface $translator) {
 return $newLocale && (($y = static::translateWith($translator, 'y')) !== 'y' && $y !== static::translateWith($translator, 'year')) || ($y = static::translateWith($translator, 'd')) !== 'd' && $y !== static::translateWith($translator, 'day') || ($y = static::translateWith($translator, 'h')) !== 'h' && $y !== static::translateWith($translator, 'hour');
 });
 }
 public static function localeHasDiffSyntax($locale)
 {
 return static::executeWithLocale($locale, function ($newLocale, TranslatorInterface $translator) {
 if (!$newLocale) {
 return \false;
 }
 foreach (['ago', 'from_now', 'before', 'after'] as $key) {
 if ($translator instanceof TranslatorBagInterface && self::getFromCatalogue($translator, $translator->getCatalogue($newLocale), $key) instanceof Closure) {
 continue;
 }
 if ($translator->trans($key) === $key) {
 return \false;
 }
 }
 return \true;
 });
 }
 public static function localeHasDiffOneDayWords($locale)
 {
 return static::executeWithLocale($locale, function ($newLocale, TranslatorInterface $translator) {
 return $newLocale && $translator->trans('diff_now') !== 'diff_now' && $translator->trans('diff_yesterday') !== 'diff_yesterday' && $translator->trans('diff_tomorrow') !== 'diff_tomorrow';
 });
 }
 public static function localeHasDiffTwoDayWords($locale)
 {
 return static::executeWithLocale($locale, function ($newLocale, TranslatorInterface $translator) {
 return $newLocale && $translator->trans('diff_before_yesterday') !== 'diff_before_yesterday' && $translator->trans('diff_after_tomorrow') !== 'diff_after_tomorrow';
 });
 }
 public static function localeHasPeriodSyntax($locale)
 {
 return static::executeWithLocale($locale, function ($newLocale, TranslatorInterface $translator) {
 return $newLocale && $translator->trans('period_recurrences') !== 'period_recurrences' && $translator->trans('period_interval') !== 'period_interval' && $translator->trans('period_start_date') !== 'period_start_date' && $translator->trans('period_end_date') !== 'period_end_date';
 });
 }
 public static function getAvailableLocales()
 {
 $translator = static::getLocaleAwareTranslator();
 return $translator instanceof Translator ? $translator->getAvailableLocales() : [$translator->getLocale()];
 }
 public static function getAvailableLocalesInfo()
 {
 $languages = [];
 foreach (static::getAvailableLocales() as $id) {
 $languages[$id] = new Language($id);
 }
 return $languages;
 }
 protected static function translator()
 {
 if (static::$translator === null) {
 static::$translator = Translator::get();
 }
 return static::$translator;
 }
 protected function getTranslatorLocale($translator = null) : ?string
 {
 if (\func_num_args() === 0) {
 $translator = $this->getLocalTranslator();
 }
 $translator = static::getLocaleAwareTranslator($translator);
 return $translator ? $translator->getLocale() : null;
 }
 protected static function getLocaleAwareTranslator($translator = null)
 {
 if (\func_num_args() === 0) {
 $translator = static::translator();
 }
 if ($translator && !($translator instanceof LocaleAwareInterface || \method_exists($translator, 'getLocale'))) {
 throw new NotLocaleAwareException($translator);
 // @codeCoverageIgnore
 }
 return $translator;
 }
 private static function getFromCatalogue($translator, $catalogue, string $id, string $domain = 'messages')
 {
 return $translator instanceof TranslatorStrongTypeInterface ? $translator->getFromCatalogue($catalogue, $id, $domain) : $catalogue->get($id, $domain);
 }
 private static function cleanWordFromTranslationString($word)
 {
 $word = \str_replace([':count', '%count', ':time'], '', $word);
 $word = \strtr($word, ['’' => "'"]);
 $word = \preg_replace('/({\\d+(,(\\d+|Inf))?}|[\\[\\]]\\d+(,(\\d+|Inf))?[\\[\\]])/', '', $word);
 return \trim($word);
 }
 private static function translateWordsByKeys($keys, $messages, $key) : array
 {
 return \array_map(function ($wordKey) use($messages, $key) {
 $message = $key === 'from' && isset($messages[$wordKey . '_regexp']) ? $messages[$wordKey . '_regexp'] : $messages[$wordKey] ?? null;
 if (!$message) {
 return '>>DO NOT REPLACE<<';
 }
 $parts = \explode('|', $message);
 return $key === 'to' ? self::cleanWordFromTranslationString(\end($parts)) : '(?:' . \implode('|', \array_map([static::class, 'cleanWordFromTranslationString'], $parts)) . ')';
 }, $keys);
 }
 private static function getTranslationArray($translation, $length, $timeString) : array
 {
 $filler = '>>DO NOT REPLACE<<';
 if (\is_array($translation)) {
 return \array_pad($translation, $length, $filler);
 }
 $list = [];
 $date = static::now();
 for ($i = 0; $i < $length; $i++) {
 $list[] = $translation($date, $timeString, $i) ?? $filler;
 }
 return $list;
 }
 private static function replaceOrdinalWords(string $timeString, array $ordinalWords) : string
 {
 return \preg_replace_callback('/(?<![a-z])[a-z]+(?![a-z])/i', function (array $match) use($ordinalWords) {
 return $ordinalWords[\mb_strtolower($match[0])] ?? $match[0];
 }, $timeString);
 }
}
