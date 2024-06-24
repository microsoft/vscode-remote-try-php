<?php
namespace MailPoetVendor\Symfony\Component\Translation;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Translation\Exception\InvalidArgumentException;
interface TranslatorBagInterface
{
 public function getCatalogue($locale = null);
}
