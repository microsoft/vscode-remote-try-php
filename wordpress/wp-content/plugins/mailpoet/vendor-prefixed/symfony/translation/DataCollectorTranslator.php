<?php
namespace MailPoetVendor\Symfony\Component\Translation;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;
use MailPoetVendor\Symfony\Component\Translation\Exception\InvalidArgumentException;
use MailPoetVendor\Symfony\Component\Translation\TranslatorInterface as LegacyTranslatorInterface;
use MailPoetVendor\Symfony\Contracts\Translation\LocaleAwareInterface;
use MailPoetVendor\Symfony\Contracts\Translation\TranslatorInterface;
class DataCollectorTranslator implements LegacyTranslatorInterface, TranslatorInterface, TranslatorBagInterface, WarmableInterface
{
 public const MESSAGE_DEFINED = 0;
 public const MESSAGE_MISSING = 1;
 public const MESSAGE_EQUALS_FALLBACK = 2;
 private $translator;
 private $messages = [];
 public function __construct($translator)
 {
 if (!$translator instanceof LegacyTranslatorInterface && !$translator instanceof TranslatorInterface) {
 throw new \TypeError(\sprintf('Argument 1 passed to "%s()" must be an instance of "%s", "%s" given.', __METHOD__, TranslatorInterface::class, \is_object($translator) ? \get_class($translator) : \gettype($translator)));
 }
 if (!$translator instanceof TranslatorBagInterface || !$translator instanceof LocaleAwareInterface) {
 throw new InvalidArgumentException(\sprintf('The Translator "%s" must implement TranslatorInterface, TranslatorBagInterface and LocaleAwareInterface.', \get_class($translator)));
 }
 $this->translator = $translator;
 }
 public function trans($id, array $parameters = [], $domain = null, $locale = null)
 {
 $trans = $this->translator->trans($id, $parameters, $domain, $locale);
 $this->collectMessage($locale, $domain, $id, $trans, $parameters);
 return $trans;
 }
 public function transChoice($id, $number, array $parameters = [], $domain = null, $locale = null)
 {
 if ($this->translator instanceof TranslatorInterface) {
 $trans = $this->translator->trans($id, ['%count%' => $number] + $parameters, $domain, $locale);
 } else {
 $trans = $this->translator->transChoice($id, $number, $parameters, $domain, $locale);
 }
 $this->collectMessage($locale, $domain, $id, $trans, ['%count%' => $number] + $parameters);
 return $trans;
 }
 public function setLocale($locale)
 {
 $this->translator->setLocale($locale);
 }
 public function getLocale()
 {
 return $this->translator->getLocale();
 }
 public function getCatalogue($locale = null)
 {
 return $this->translator->getCatalogue($locale);
 }
 public function warmUp($cacheDir)
 {
 if ($this->translator instanceof WarmableInterface) {
 $this->translator->warmUp($cacheDir);
 }
 }
 public function getFallbackLocales()
 {
 if ($this->translator instanceof Translator || \method_exists($this->translator, 'getFallbackLocales')) {
 return $this->translator->getFallbackLocales();
 }
 return [];
 }
 public function __call($method, $args)
 {
 return $this->translator->{$method}(...$args);
 }
 public function getCollectedMessages()
 {
 return $this->messages;
 }
 private function collectMessage(?string $locale, ?string $domain, ?string $id, string $translation, ?array $parameters = [])
 {
 if (null === $domain) {
 $domain = 'messages';
 }
 $id = (string) $id;
 $catalogue = $this->translator->getCatalogue($locale);
 $locale = $catalogue->getLocale();
 $fallbackLocale = null;
 if ($catalogue->defines($id, $domain)) {
 $state = self::MESSAGE_DEFINED;
 } elseif ($catalogue->has($id, $domain)) {
 $state = self::MESSAGE_EQUALS_FALLBACK;
 $fallbackCatalogue = $catalogue->getFallbackCatalogue();
 while ($fallbackCatalogue) {
 if ($fallbackCatalogue->defines($id, $domain)) {
 $fallbackLocale = $fallbackCatalogue->getLocale();
 break;
 }
 $fallbackCatalogue = $fallbackCatalogue->getFallbackCatalogue();
 }
 } else {
 $state = self::MESSAGE_MISSING;
 }
 $this->messages[] = ['locale' => $locale, 'fallbackLocale' => $fallbackLocale, 'domain' => $domain, 'id' => $id, 'translation' => $translation, 'parameters' => $parameters, 'state' => $state, 'transChoiceNumber' => isset($parameters['%count%']) && \is_numeric($parameters['%count%']) ? $parameters['%count%'] : null];
 }
}
