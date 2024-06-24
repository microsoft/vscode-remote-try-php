<?php
namespace MailPoetVendor\Symfony\Component\Translation;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Config\ConfigCacheFactory;
use MailPoetVendor\Symfony\Component\Config\ConfigCacheFactoryInterface;
use MailPoetVendor\Symfony\Component\Config\ConfigCacheInterface;
use MailPoetVendor\Symfony\Component\Translation\Exception\InvalidArgumentException;
use MailPoetVendor\Symfony\Component\Translation\Exception\LogicException;
use MailPoetVendor\Symfony\Component\Translation\Exception\NotFoundResourceException;
use MailPoetVendor\Symfony\Component\Translation\Exception\RuntimeException;
use MailPoetVendor\Symfony\Component\Translation\Formatter\ChoiceMessageFormatterInterface;
use MailPoetVendor\Symfony\Component\Translation\Formatter\IntlFormatterInterface;
use MailPoetVendor\Symfony\Component\Translation\Formatter\MessageFormatter;
use MailPoetVendor\Symfony\Component\Translation\Formatter\MessageFormatterInterface;
use MailPoetVendor\Symfony\Component\Translation\Loader\LoaderInterface;
use MailPoetVendor\Symfony\Component\Translation\TranslatorInterface as LegacyTranslatorInterface;
use MailPoetVendor\Symfony\Contracts\Translation\TranslatorInterface;
class Translator implements LegacyTranslatorInterface, TranslatorInterface, TranslatorBagInterface
{
 protected $catalogues = [];
 private $locale;
 private $fallbackLocales = [];
 private $loaders = [];
 private $resources = [];
 private $formatter;
 private $cacheDir;
 private $debug;
 private $cacheVary;
 private $configCacheFactory;
 private $parentLocales;
 private $hasIntlFormatter;
 public function __construct(?string $locale, MessageFormatterInterface $formatter = null, string $cacheDir = null, bool $debug = \false, array $cacheVary = [])
 {
 if (null === $locale) {
 @\trigger_error(\sprintf('Passing "null" as the $locale argument to %s() is deprecated since Symfony 4.4.', __METHOD__), \E_USER_DEPRECATED);
 }
 $this->setLocale($locale, \false);
 if (null === $formatter) {
 $formatter = new MessageFormatter();
 }
 $this->formatter = $formatter;
 $this->cacheDir = $cacheDir;
 $this->debug = $debug;
 $this->cacheVary = $cacheVary;
 $this->hasIntlFormatter = $formatter instanceof IntlFormatterInterface;
 }
 public function setConfigCacheFactory(ConfigCacheFactoryInterface $configCacheFactory)
 {
 $this->configCacheFactory = $configCacheFactory;
 }
 public function addLoader($format, LoaderInterface $loader)
 {
 $this->loaders[$format] = $loader;
 }
 public function addResource($format, $resource, $locale, $domain = null)
 {
 if (null === $domain) {
 $domain = 'messages';
 }
 if (null === $locale) {
 @\trigger_error(\sprintf('Passing "null" to the third argument of the "%s" method has been deprecated since Symfony 4.4 and will throw an error in 5.0.', __METHOD__), \E_USER_DEPRECATED);
 }
 $this->assertValidLocale($locale);
 $locale ?: ($locale = \class_exists(\Locale::class) ? \Locale::getDefault() : 'en');
 $this->resources[$locale][] = [$format, $resource, $domain];
 if (\in_array($locale, $this->fallbackLocales)) {
 $this->catalogues = [];
 } else {
 unset($this->catalogues[$locale]);
 }
 }
 public function setLocale($locale)
 {
 if (null === $locale && (2 > \func_num_args() || \func_get_arg(1))) {
 @\trigger_error(\sprintf('Passing "null" as the $locale argument to %s() is deprecated since Symfony 4.4.', __METHOD__), \E_USER_DEPRECATED);
 }
 $this->assertValidLocale($locale);
 $this->locale = $locale;
 }
 public function getLocale()
 {
 return $this->locale ?: (\class_exists(\Locale::class) ? \Locale::getDefault() : 'en');
 }
 public function setFallbackLocales(array $locales)
 {
 // needed as the fallback locales are linked to the already loaded catalogues
 $this->catalogues = [];
 foreach ($locales as $locale) {
 if (null === $locale) {
 @\trigger_error(\sprintf('Passing "null" as the $locale argument to %s() is deprecated since Symfony 4.4.', __METHOD__), \E_USER_DEPRECATED);
 }
 $this->assertValidLocale($locale);
 }
 $this->fallbackLocales = $this->cacheVary['fallback_locales'] = $locales;
 }
 public function getFallbackLocales()
 {
 return $this->fallbackLocales;
 }
 public function trans($id, array $parameters = [], $domain = null, $locale = null)
 {
 if ('' === ($id = (string) $id)) {
 return '';
 }
 if (null === $domain) {
 $domain = 'messages';
 }
 $catalogue = $this->getCatalogue($locale);
 $locale = $catalogue->getLocale();
 while (!$catalogue->defines($id, $domain)) {
 if ($cat = $catalogue->getFallbackCatalogue()) {
 $catalogue = $cat;
 $locale = $catalogue->getLocale();
 } else {
 break;
 }
 }
 if ($this->hasIntlFormatter && $catalogue->defines($id, $domain . MessageCatalogue::INTL_DOMAIN_SUFFIX)) {
 return $this->formatter->formatIntl($catalogue->get($id, $domain), $locale, $parameters);
 }
 return $this->formatter->format($catalogue->get($id, $domain), $locale, $parameters);
 }
 public function transChoice($id, $number, array $parameters = [], $domain = null, $locale = null)
 {
 @\trigger_error(\sprintf('The "%s()" method is deprecated since Symfony 4.2, use the trans() one instead with a "%%count%%" parameter.', __METHOD__), \E_USER_DEPRECATED);
 if ('' === ($id = (string) $id)) {
 return '';
 }
 if (!$this->formatter instanceof ChoiceMessageFormatterInterface) {
 throw new LogicException(\sprintf('The formatter "%s" does not support plural translations.', \get_class($this->formatter)));
 }
 if (null === $domain) {
 $domain = 'messages';
 }
 $catalogue = $this->getCatalogue($locale);
 $locale = $catalogue->getLocale();
 while (!$catalogue->defines($id, $domain)) {
 if ($cat = $catalogue->getFallbackCatalogue()) {
 $catalogue = $cat;
 $locale = $catalogue->getLocale();
 } else {
 break;
 }
 }
 if ($this->hasIntlFormatter && $catalogue->defines($id, $domain . MessageCatalogue::INTL_DOMAIN_SUFFIX)) {
 return $this->formatter->formatIntl($catalogue->get($id, $domain), $locale, ['%count%' => $number] + $parameters);
 }
 return $this->formatter->choiceFormat($catalogue->get($id, $domain), $number, $locale, $parameters);
 }
 public function getCatalogue($locale = null)
 {
 if (!$locale) {
 $locale = $this->getLocale();
 } else {
 $this->assertValidLocale($locale);
 }
 if (!isset($this->catalogues[$locale])) {
 $this->loadCatalogue($locale);
 }
 return $this->catalogues[$locale];
 }
 protected function getLoaders()
 {
 return $this->loaders;
 }
 protected function loadCatalogue($locale)
 {
 if (null === $this->cacheDir) {
 $this->initializeCatalogue($locale);
 } else {
 $this->initializeCacheCatalogue($locale);
 }
 }
 protected function initializeCatalogue($locale)
 {
 $this->assertValidLocale($locale);
 try {
 $this->doLoadCatalogue($locale);
 } catch (NotFoundResourceException $e) {
 if (!$this->computeFallbackLocales($locale)) {
 throw $e;
 }
 }
 $this->loadFallbackCatalogues($locale);
 }
 private function initializeCacheCatalogue(string $locale) : void
 {
 if (isset($this->catalogues[$locale])) {
 return;
 }
 $this->assertValidLocale($locale);
 $cache = $this->getConfigCacheFactory()->cache($this->getCatalogueCachePath($locale), function (ConfigCacheInterface $cache) use($locale) {
 $this->dumpCatalogue($locale, $cache);
 });
 if (isset($this->catalogues[$locale])) {
 return;
 }
 $this->catalogues[$locale] = (include $cache->getPath());
 }
 private function dumpCatalogue(string $locale, ConfigCacheInterface $cache) : void
 {
 $this->initializeCatalogue($locale);
 $fallbackContent = $this->getFallbackContent($this->catalogues[$locale]);
 $content = \sprintf(<<<EOF
<?php
use Symfony\\Component\\Translation\\MessageCatalogue;
\$catalogue = new MessageCatalogue('%s', %s);
%s
return \$catalogue;
EOF
, $locale, \var_export($this->getAllMessages($this->catalogues[$locale]), \true), $fallbackContent);
 $cache->write($content, $this->catalogues[$locale]->getResources());
 }
 private function getFallbackContent(MessageCatalogue $catalogue) : string
 {
 $fallbackContent = '';
 $current = '';
 $replacementPattern = '/[^a-z0-9_]/i';
 $fallbackCatalogue = $catalogue->getFallbackCatalogue();
 while ($fallbackCatalogue) {
 $fallback = $fallbackCatalogue->getLocale();
 $fallbackSuffix = \ucfirst(\preg_replace($replacementPattern, '_', $fallback));
 $currentSuffix = \ucfirst(\preg_replace($replacementPattern, '_', $current));
 $fallbackContent .= \sprintf(<<<'EOF'
$catalogue%s = new MessageCatalogue('%s', %s);
$catalogue%s->addFallbackCatalogue($catalogue%s);
EOF
, $fallbackSuffix, $fallback, \var_export($this->getAllMessages($fallbackCatalogue), \true), $currentSuffix, $fallbackSuffix);
 $current = $fallbackCatalogue->getLocale();
 $fallbackCatalogue = $fallbackCatalogue->getFallbackCatalogue();
 }
 return $fallbackContent;
 }
 private function getCatalogueCachePath(string $locale) : string
 {
 return $this->cacheDir . '/catalogue.' . $locale . '.' . \strtr(\substr(\base64_encode(\hash('sha256', \serialize($this->cacheVary), \true)), 0, 7), '/', '_') . '.php';
 }
 protected function doLoadCatalogue(string $locale) : void
 {
 $this->catalogues[$locale] = new MessageCatalogue($locale);
 if (isset($this->resources[$locale])) {
 foreach ($this->resources[$locale] as $resource) {
 if (!isset($this->loaders[$resource[0]])) {
 if (\is_string($resource[1])) {
 throw new RuntimeException(\sprintf('No loader is registered for the "%s" format when loading the "%s" resource.', $resource[0], $resource[1]));
 }
 throw new RuntimeException(\sprintf('No loader is registered for the "%s" format.', $resource[0]));
 }
 $this->catalogues[$locale]->addCatalogue($this->loaders[$resource[0]]->load($resource[1], $locale, $resource[2]));
 }
 }
 }
 private function loadFallbackCatalogues(string $locale) : void
 {
 $current = $this->catalogues[$locale];
 foreach ($this->computeFallbackLocales($locale) as $fallback) {
 if (!isset($this->catalogues[$fallback])) {
 $this->initializeCatalogue($fallback);
 }
 $fallbackCatalogue = new MessageCatalogue($fallback, $this->getAllMessages($this->catalogues[$fallback]));
 foreach ($this->catalogues[$fallback]->getResources() as $resource) {
 $fallbackCatalogue->addResource($resource);
 }
 $current->addFallbackCatalogue($fallbackCatalogue);
 $current = $fallbackCatalogue;
 }
 }
 protected function computeFallbackLocales($locale)
 {
 if (null === $this->parentLocales) {
 $this->parentLocales = \json_decode(\file_get_contents(__DIR__ . '/Resources/data/parents.json'), \true);
 }
 $originLocale = $locale;
 $locales = [];
 while ($locale) {
 $parent = $this->parentLocales[$locale] ?? null;
 if ($parent) {
 $locale = 'root' !== $parent ? $parent : null;
 } elseif (\function_exists('locale_parse')) {
 $localeSubTags = \locale_parse($locale);
 $locale = null;
 if (1 < \count($localeSubTags)) {
 \array_pop($localeSubTags);
 $locale = \locale_compose($localeSubTags) ?: null;
 }
 } elseif ($i = \strrpos($locale, '_') ?: \strrpos($locale, '-')) {
 $locale = \substr($locale, 0, $i);
 } else {
 $locale = null;
 }
 if (null !== $locale) {
 $locales[] = $locale;
 }
 }
 foreach ($this->fallbackLocales as $fallback) {
 if ($fallback === $originLocale) {
 continue;
 }
 $locales[] = $fallback;
 }
 return \array_unique($locales);
 }
 protected function assertValidLocale($locale)
 {
 if (!\preg_match('/^[a-z0-9@_\\.\\-]*$/i', (string) $locale)) {
 throw new InvalidArgumentException(\sprintf('Invalid "%s" locale.', $locale));
 }
 }
 private function getConfigCacheFactory() : ConfigCacheFactoryInterface
 {
 if (!$this->configCacheFactory) {
 $this->configCacheFactory = new ConfigCacheFactory($this->debug);
 }
 return $this->configCacheFactory;
 }
 private function getAllMessages(MessageCatalogueInterface $catalogue) : array
 {
 $allMessages = [];
 foreach ($catalogue->all() as $domain => $messages) {
 if ($intlMessages = $catalogue->all($domain . MessageCatalogue::INTL_DOMAIN_SUFFIX)) {
 $allMessages[$domain . MessageCatalogue::INTL_DOMAIN_SUFFIX] = $intlMessages;
 $messages = \array_diff_key($messages, $intlMessages);
 }
 if ($messages) {
 $allMessages[$domain] = $messages;
 }
 }
 return $allMessages;
 }
}
