<?php
namespace MailPoetVendor\Symfony\Component\String\Slugger;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\String\AbstractUnicodeString;
interface SluggerInterface
{
 public function slug(string $string, string $separator = '-', ?string $locale = null) : AbstractUnicodeString;
}
