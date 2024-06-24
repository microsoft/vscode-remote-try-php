<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection\Argument;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use MailPoetVendor\Symfony\Component\DependencyInjection\Reference;
class ServiceClosureArgument implements ArgumentInterface
{
 private $values;
 public function __construct(Reference $reference)
 {
 $this->values = [$reference];
 }
 public function getValues()
 {
 return $this->values;
 }
 public function setValues(array $values)
 {
 if ([0] !== \array_keys($values) || !($values[0] instanceof Reference || null === $values[0])) {
 throw new InvalidArgumentException('A ServiceClosureArgument must hold one and only one Reference.');
 }
 $this->values = $values;
 }
}
