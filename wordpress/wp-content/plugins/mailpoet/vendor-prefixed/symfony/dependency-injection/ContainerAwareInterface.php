<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection;
if (!defined('ABSPATH')) exit;
interface ContainerAwareInterface
{
 public function setContainer(ContainerInterface $container = null);
}
