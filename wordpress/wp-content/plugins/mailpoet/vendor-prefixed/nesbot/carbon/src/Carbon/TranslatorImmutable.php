<?php
namespace MailPoetVendor\Carbon;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Carbon\Exceptions\ImmutableException;
use MailPoetVendor\Symfony\Component\Config\ConfigCacheFactoryInterface;
use MailPoetVendor\Symfony\Component\Translation\Formatter\MessageFormatterInterface;
class TranslatorImmutable extends Translator
{
 private $constructed = \false;
 public function __construct($locale, MessageFormatterInterface $formatter = null, $cacheDir = null, $debug = \false)
 {
 parent::__construct($locale, $formatter, $cacheDir, $debug);
 $this->constructed = \true;
 }
 public function setDirectories(array $directories)
 {
 $this->disallowMutation(__METHOD__);
 return parent::setDirectories($directories);
 }
 public function setLocale($locale)
 {
 $this->disallowMutation(__METHOD__);
 return parent::setLocale($locale);
 }
 public function setMessages($locale, $messages)
 {
 $this->disallowMutation(__METHOD__);
 return parent::setMessages($locale, $messages);
 }
 public function setTranslations($messages)
 {
 $this->disallowMutation(__METHOD__);
 return parent::setTranslations($messages);
 }
 public function setConfigCacheFactory(ConfigCacheFactoryInterface $configCacheFactory) : void
 {
 $this->disallowMutation(__METHOD__);
 parent::setConfigCacheFactory($configCacheFactory);
 }
 public function resetMessages($locale = null)
 {
 $this->disallowMutation(__METHOD__);
 return parent::resetMessages($locale);
 }
 public function setFallbackLocales(array $locales)
 {
 $this->disallowMutation(__METHOD__);
 parent::setFallbackLocales($locales);
 }
 private function disallowMutation($method)
 {
 if ($this->constructed) {
 throw new ImmutableException($method . ' not allowed on ' . static::class);
 }
 }
}
