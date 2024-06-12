<?php
declare (strict_types=1);
namespace MailPoetVendor\Carbon\PHPStan;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\PHPStan\BetterReflection\Reflection;
use ReflectionMethod;
if (!\class_exists(AbstractReflectionMacro::class, \false)) {
 abstract class AbstractReflectionMacro extends AbstractMacro
 {
 public function getReflection() : ?ReflectionMethod
 {
 if ($this->reflectionFunction instanceof Reflection\ReflectionMethod) {
 return new Reflection\Adapter\ReflectionMethod($this->reflectionFunction);
 }
 return $this->reflectionFunction instanceof ReflectionMethod ? $this->reflectionFunction : null;
 }
 }
}
