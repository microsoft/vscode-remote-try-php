<?php
namespace MailPoetVendor\Symfony\Contracts\Service;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Psr\Container\ContainerInterface;
interface ServiceProviderInterface extends ContainerInterface
{
 public function getProvidedServices() : array;
}
