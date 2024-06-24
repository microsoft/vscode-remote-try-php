<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection\ParameterBag;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\DependencyInjection\Exception\LogicException;
use MailPoetVendor\Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
interface ParameterBagInterface
{
 public function clear();
 public function add(array $parameters);
 public function all();
 public function get(string $name);
 public function remove(string $name);
 public function set(string $name, $value);
 public function has(string $name);
 public function resolve();
 public function resolveValue($value);
 public function escapeValue($value);
 public function unescapeValue($value);
}
