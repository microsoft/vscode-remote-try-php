<?php
namespace MailPoetVendor\Carbon;
if (!defined('ABSPATH')) exit;
use Closure;
use ReflectionException;
use ReflectionFunction;
use MailPoetVendor\Symfony\Component\Translation;
use MailPoetVendor\Symfony\Component\Translation\Formatter\MessageFormatterInterface;
use MailPoetVendor\Symfony\Component\Translation\Loader\ArrayLoader;
abstract class AbstractTranslator extends Translation\Translator
{
 protected static $singletons = [];
 protected $messages = [];
 protected $directories = [];
 protected $initializing = \false;
 protected $aliases = ['me' => 'sr_Latn_ME', 'scr' => 'sh'];
 public static function get($locale = null)
 {
 $locale = $locale ?: 'en';
 $key = static::class === Translator::class ? $locale : static::class . '|' . $locale;
 if (!isset(static::$singletons[$key])) {
 static::$singletons[$key] = new static($locale);
 }
 return static::$singletons[$key];
 }
 public function __construct($locale, MessageFormatterInterface $formatter = null, $cacheDir = null, $debug = \false)
 {
 parent::setLocale($locale);
 $this->initializing = \true;
 $this->directories = [__DIR__ . '/Lang'];
 $this->addLoader('array', new ArrayLoader());
 parent::__construct($locale, $formatter, $cacheDir, $debug);
 $this->initializing = \false;
 }
 public function getDirectories() : array
 {
 return $this->directories;
 }
 public function setDirectories(array $directories)
 {
 $this->directories = $directories;
 return $this;
 }
 public function addDirectory(string $directory)
 {
 $this->directories[] = $directory;
 return $this;
 }
 public function removeDirectory(string $directory)
 {
 $search = \rtrim(\strtr($directory, '\\', '/'), '/');
 return $this->setDirectories(\array_filter($this->getDirectories(), function ($item) use($search) {
 return \rtrim(\strtr($item, '\\', '/'), '/') !== $search;
 }));
 }
 public function resetMessages($locale = null)
 {
 if ($locale === null) {
 $this->messages = [];
 return \true;
 }
 foreach ($this->getDirectories() as $directory) {
 $data = @(include \sprintf('%s/%s.php', \rtrim($directory, '\\/'), $locale));
 if ($data !== \false) {
 $this->messages[$locale] = $data;
 $this->addResource('array', $this->messages[$locale], $locale);
 return \true;
 }
 }
 return \false;
 }
 public function getLocalesFiles($prefix = '')
 {
 $files = [];
 foreach ($this->getDirectories() as $directory) {
 $directory = \rtrim($directory, '\\/');
 foreach (\glob("{$directory}/{$prefix}*.php") as $file) {
 $files[] = $file;
 }
 }
 return \array_unique($files);
 }
 public function getAvailableLocales($prefix = '')
 {
 $locales = [];
 foreach ($this->getLocalesFiles($prefix) as $file) {
 $locales[] = \substr($file, \strrpos($file, '/') + 1, -4);
 }
 return \array_unique(\array_merge($locales, \array_keys($this->messages)));
 }
 protected function translate(?string $id, array $parameters = [], ?string $domain = null, ?string $locale = null) : string
 {
 if ($domain === null) {
 $domain = 'messages';
 }
 $catalogue = $this->getCatalogue($locale);
 $format = $this instanceof TranslatorStrongTypeInterface ? $this->getFromCatalogue($catalogue, (string) $id, $domain) : $this->getCatalogue($locale)->get((string) $id, $domain);
 // @codeCoverageIgnore
 if ($format instanceof Closure) {
 // @codeCoverageIgnoreStart
 try {
 $count = (new ReflectionFunction($format))->getNumberOfRequiredParameters();
 } catch (ReflectionException $exception) {
 $count = 0;
 }
 // @codeCoverageIgnoreEnd
 return $format(...\array_values($parameters), ...\array_fill(0, \max(0, $count - \count($parameters)), null));
 }
 return parent::trans($id, $parameters, $domain, $locale);
 }
 protected function loadMessagesFromFile($locale)
 {
 return isset($this->messages[$locale]) || $this->resetMessages($locale);
 }
 public function setMessages($locale, $messages)
 {
 $this->loadMessagesFromFile($locale);
 $this->addResource('array', $messages, $locale);
 $this->messages[$locale] = \array_merge($this->messages[$locale] ?? [], $messages);
 return $this;
 }
 public function setTranslations($messages)
 {
 return $this->setMessages($this->getLocale(), $messages);
 }
 public function getMessages($locale = null)
 {
 return $locale === null ? $this->messages : $this->messages[$locale];
 }
 public function setLocale($locale)
 {
 $locale = \preg_replace_callback('/[-_]([a-z]{2,}|\\d{2,})/', function ($matches) {
 // _2-letters or YUE is a region, _3+-letters is a variant
 $upper = \strtoupper($matches[1]);
 if ($upper === 'YUE' || $upper === 'ISO' || \strlen($upper) < 3) {
 return "_{$upper}";
 }
 return '_' . \ucfirst($matches[1]);
 }, \strtolower($locale));
 $previousLocale = $this->getLocale();
 if ($previousLocale === $locale && isset($this->messages[$locale])) {
 return \true;
 }
 unset(static::$singletons[$previousLocale]);
 if ($locale === 'auto') {
 $completeLocale = \setlocale(\LC_TIME, '0');
 $locale = \preg_replace('/^([^_.-]+).*$/', '$1', $completeLocale);
 $locales = $this->getAvailableLocales($locale);
 $completeLocaleChunks = \preg_split('/[_.-]+/', $completeLocale);
 $getScore = function ($language) use($completeLocaleChunks) {
 return self::compareChunkLists($completeLocaleChunks, \preg_split('/[_.-]+/', $language));
 };
 \usort($locales, function ($first, $second) use($getScore) {
 return $getScore($second) <=> $getScore($first);
 });
 $locale = $locales[0];
 }
 if (isset($this->aliases[$locale])) {
 $locale = $this->aliases[$locale];
 }
 // If subtag (ex: en_CA) first load the macro (ex: en) to have a fallback
 if (\str_contains($locale, '_') && $this->loadMessagesFromFile($macroLocale = \preg_replace('/^([^_]+).*$/', '$1', $locale))) {
 parent::setLocale($macroLocale);
 }
 if (!$this->loadMessagesFromFile($locale) && !$this->initializing) {
 return \false;
 }
 parent::setLocale($locale);
 return \true;
 }
 public function __debugInfo()
 {
 return ['locale' => $this->getLocale()];
 }
 private static function compareChunkLists($referenceChunks, $chunks)
 {
 $score = 0;
 foreach ($referenceChunks as $index => $chunk) {
 if (!isset($chunks[$index])) {
 $score++;
 continue;
 }
 if (\strtolower($chunks[$index]) === \strtolower($chunk)) {
 $score += 10;
 }
 }
 return $score;
 }
}
