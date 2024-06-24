<?php
namespace MailPoetVendor\Twig\Loader;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Error\LoaderError;
use MailPoetVendor\Twig\Source;
interface LoaderInterface
{
 public function getSourceContext(string $name) : Source;
 public function getCacheKey(string $name) : string;
 public function isFresh(string $name, int $time) : bool;
 public function exists(string $name);
}
