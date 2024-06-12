<?php
namespace MailPoetVendor\Symfony\Contracts\Translation;
if (!defined('ABSPATH')) exit;
interface TranslatorInterface
{
 public function trans($id, array $parameters = [], $domain = null, $locale = null);
}
