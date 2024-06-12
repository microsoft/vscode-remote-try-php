<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection;
if (!defined('ABSPATH')) exit;
trait ContainerAwareTrait
{
 protected $container;
 public function setContainer(ContainerInterface $container = null)
 {
 $this->container = $container;
 }
}
