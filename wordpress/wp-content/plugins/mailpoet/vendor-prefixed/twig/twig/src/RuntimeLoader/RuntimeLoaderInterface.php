<?php
namespace MailPoetVendor\Twig\RuntimeLoader;
if (!defined('ABSPATH')) exit;
interface RuntimeLoaderInterface
{
 public function load(string $class);
}
