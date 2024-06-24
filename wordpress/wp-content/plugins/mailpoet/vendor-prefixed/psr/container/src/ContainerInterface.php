<?php
declare (strict_types=1);
namespace MailPoetVendor\Psr\Container;
if (!defined('ABSPATH')) exit;
interface ContainerInterface
{
 public function get(string $id);
 public function has(string $id);
}
