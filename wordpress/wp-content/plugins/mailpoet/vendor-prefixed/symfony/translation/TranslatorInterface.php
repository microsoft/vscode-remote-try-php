<?php
namespace MailPoetVendor\Symfony\Component\Translation;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Translation\Exception\InvalidArgumentException;
use MailPoetVendor\Symfony\Contracts\Translation\LocaleAwareInterface;
interface TranslatorInterface extends LocaleAwareInterface
{
 public function trans($id, array $parameters = [], $domain = null, $locale = null);
 public function transChoice($id, $number, array $parameters = [], $domain = null, $locale = null);
 public function setLocale($locale);
 public function getLocale();
}
