<?php
namespace MailPoetVendor\Symfony\Component\Translation;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Translation\TranslatorInterface as LegacyTranslatorInterface;
use MailPoetVendor\Symfony\Contracts\Translation\TranslatorInterface;
use MailPoetVendor\Symfony\Contracts\Translation\TranslatorTrait;
class IdentityTranslator implements LegacyTranslatorInterface, TranslatorInterface
{
 use TranslatorTrait {
 trans as private doTrans;
 setLocale as private doSetLocale;
 }
 private $selector;
 public function __construct(MessageSelector $selector = null)
 {
 $this->selector = $selector;
 if (__CLASS__ !== static::class) {
 @\trigger_error(\sprintf('Calling "%s()" is deprecated since Symfony 4.2.', __METHOD__), \E_USER_DEPRECATED);
 }
 }
 public function trans($id, array $parameters = [], $domain = null, $locale = null)
 {
 return $this->doTrans($id, $parameters, $domain, $locale);
 }
 public function setLocale($locale)
 {
 $this->doSetLocale($locale);
 }
 public function transChoice($id, $number, array $parameters = [], $domain = null, $locale = null)
 {
 @\trigger_error(\sprintf('The "%s()" method is deprecated since Symfony 4.2, use the trans() one instead with a "%%count%%" parameter.', __METHOD__), \E_USER_DEPRECATED);
 if ($this->selector) {
 return \strtr($this->selector->choose((string) $id, $number, $locale ?: $this->getLocale()), $parameters);
 }
 return $this->trans($id, ['%count%' => $number] + $parameters, $domain, $locale);
 }
 private function getPluralizationRule(float $number, string $locale) : int
 {
 return PluralizationRules::get($number, $locale, \false);
 }
}
