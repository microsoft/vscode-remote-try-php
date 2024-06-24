<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection\Argument;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use MailPoetVendor\Symfony\Component\DependencyInjection\Reference;
trait ReferenceSetArgumentTrait
{
 private $values;
 public function __construct(array $values)
 {
 $this->setValues($values);
 }
 public function getValues()
 {
 return $this->values;
 }
 public function setValues(array $values)
 {
 foreach ($values as $k => $v) {
 if (null !== $v && !$v instanceof Reference) {
 throw new InvalidArgumentException(\sprintf('A "%s" must hold only Reference instances, "%s" given.', __CLASS__, \get_debug_type($v)));
 }
 }
 $this->values = $values;
 }
}
