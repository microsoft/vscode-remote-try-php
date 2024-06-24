<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping;
if (!defined('ABSPATH')) exit;
interface EntityListenerResolver
{
 public function clear($className = null);
 public function resolve($className);
 public function register($object);
}
