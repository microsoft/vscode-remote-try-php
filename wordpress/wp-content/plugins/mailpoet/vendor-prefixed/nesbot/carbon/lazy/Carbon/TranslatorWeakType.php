<?php
namespace MailPoetVendor\Carbon;
if (!defined('ABSPATH')) exit;
if (!\class_exists(LazyTranslator::class, \false)) {
 class LazyTranslator extends AbstractTranslator
 {
 public function trans($id, array $parameters = [], $domain = null, $locale = null)
 {
 return $this->translate($id, $parameters, $domain, $locale);
 }
 }
}
