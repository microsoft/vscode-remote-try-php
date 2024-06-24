<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection\ParameterBag;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Psr\Container\ContainerInterface;
use MailPoetVendor\Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
interface ContainerBagInterface extends ContainerInterface
{
 public function all();
 public function resolveValue($value);
 public function escapeValue($value);
 public function unescapeValue($value);
}
