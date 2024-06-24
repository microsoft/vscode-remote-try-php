<?php
namespace MailPoetVendor\Twig\RuntimeLoader;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Psr\Container\ContainerInterface;
class ContainerRuntimeLoader implements RuntimeLoaderInterface
{
 private $container;
 public function __construct(ContainerInterface $container)
 {
 $this->container = $container;
 }
 public function load(string $class)
 {
 return $this->container->has($class) ? $this->container->get($class) : null;
 }
}
