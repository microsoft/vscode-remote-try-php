<?php
namespace MailPoetVendor\Symfony\Component\Translation;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Psr\Log\LoggerInterface;
use MailPoetVendor\Symfony\Component\Translation\Exception\InvalidArgumentException;
use MailPoetVendor\Symfony\Component\Translation\TranslatorInterface as LegacyTranslatorInterface;
use MailPoetVendor\Symfony\Contracts\Translation\LocaleAwareInterface;
use MailPoetVendor\Symfony\Contracts\Translation\TranslatorInterface;
class LoggingTranslator implements TranslatorInterface, LegacyTranslatorInterface, TranslatorBagInterface
{
 private $translator;
 private $logger;
 public function __construct($translator, LoggerInterface $logger)
 {
 if (!$translator instanceof LegacyTranslatorInterface && !$translator instanceof TranslatorInterface) {
 throw new \TypeError(\sprintf('Argument 1 passed to "%s()" must be an instance of "%s", "%s" given.', __METHOD__, TranslatorInterface::class, \is_object($translator) ? \get_class($translator) : \gettype($translator)));
 }
 if (!$translator instanceof TranslatorBagInterface || !$translator instanceof LocaleAwareInterface) {
 throw new InvalidArgumentException(\sprintf('The Translator "%s" must implement TranslatorInterface, TranslatorBagInterface and LocaleAwareInterface.', \get_class($translator)));
 }
 $this->translator = $translator;
 $this->logger = $logger;
 }
 public function trans($id, array $parameters = [], $domain = null, $locale = null)
 {
 $trans = $this->translator->trans($id, $parameters, $domain, $locale);
 $this->log($id, $domain, $locale);
 return $trans;
 }
 public function transChoice($id, $number, array $parameters = [], $domain = null, $locale = null)
 {
 @\trigger_error(\sprintf('The "%s()" method is deprecated since Symfony 4.2, use the trans() one instead with a "%%count%%" parameter.', __METHOD__), \E_USER_DEPRECATED);
 if ($this->translator instanceof TranslatorInterface) {
 $trans = $this->translator->trans($id, ['%count%' => $number] + $parameters, $domain, $locale);
 } else {
 $trans = $this->translator->transChoice($id, $number, $parameters, $domain, $locale);
 }
 $this->log($id, $domain, $locale);
 return $trans;
 }
 public function setLocale($locale)
 {
 $prev = $this->translator->getLocale();
 $this->translator->setLocale($locale);
 if ($prev === $locale) {
 return;
 }
 $this->logger->debug(\sprintf('The locale of the translator has changed from "%s" to "%s".', $prev, $locale));
 }
 public function getLocale()
 {
 return $this->translator->getLocale();
 }
 public function getCatalogue($locale = null)
 {
 return $this->translator->getCatalogue($locale);
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
 private function log(?string $id, ?string $domain, ?string $locale)
 {
 if (null === $domain) {
 $domain = 'messages';
 }
 $id = (string) $id;
 $catalogue = $this->translator->getCatalogue($locale);
 if ($catalogue->defines($id, $domain)) {
 return;
 }
 if ($catalogue->has($id, $domain)) {
 $this->logger->debug('Translation use fallback catalogue.', ['id' => $id, 'domain' => $domain, 'locale' => $catalogue->getLocale()]);
 } else {
 $this->logger->warning('Translation not found.', ['id' => $id, 'domain' => $domain, 'locale' => $catalogue->getLocale()]);
 }
 }
}
