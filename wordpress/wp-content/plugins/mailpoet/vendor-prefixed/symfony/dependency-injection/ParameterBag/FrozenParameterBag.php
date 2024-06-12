<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection\ParameterBag;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\DependencyInjection\Exception\LogicException;
class FrozenParameterBag extends ParameterBag
{
 public function __construct(array $parameters = [])
 {
 $this->parameters = $parameters;
 $this->resolved = \true;
 }
 public function clear()
 {
 throw new LogicException('Impossible to call clear() on a frozen ParameterBag.');
 }
 public function add(array $parameters)
 {
 throw new LogicException('Impossible to call add() on a frozen ParameterBag.');
 }
 public function set(string $name, $value)
 {
 throw new LogicException('Impossible to call set() on a frozen ParameterBag.');
 }
 public function remove(string $name)
 {
 throw new LogicException('Impossible to call remove() on a frozen ParameterBag.');
 }
}
