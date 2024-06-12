<?php
namespace MailPoetVendor\Doctrine\Persistence\Mapping;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Persistence\Proxy;
interface ProxyClassNameResolver
{
 public function resolveClassName(string $className) : string;
}
