<?php
namespace MailPoetVendor\Carbon\Traits;
if (!defined('ABSPATH')) exit;
use Closure;
use Generator;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Throwable;
trait Mixin
{
 protected static $macroContextStack = [];
 public static function mixin($mixin)
 {
 \is_string($mixin) && \trait_exists($mixin) ? self::loadMixinTrait($mixin) : self::loadMixinClass($mixin);
 }
 private static function loadMixinClass($mixin)
 {
 $methods = (new ReflectionClass($mixin))->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED);
 foreach ($methods as $method) {
 if ($method->isConstructor() || $method->isDestructor()) {
 continue;
 }
 $method->setAccessible(\true);
 static::macro($method->name, $method->invoke($mixin));
 }
 }
 private static function loadMixinTrait($trait)
 {
 $context = eval(self::getAnonymousClassCodeForTrait($trait));
 $className = \get_class($context);
 foreach (self::getMixableMethods($context) as $name) {
 $closureBase = Closure::fromCallable([$context, $name]);
 static::macro($name, function () use($closureBase, $className) {
 $context = isset($this) ? $this->cast($className) : new $className();
 try {
 // @ is required to handle error if not converted into exceptions
 $closure = @$closureBase->bindTo($context);
 } catch (Throwable $throwable) {
 // @codeCoverageIgnore
 $closure = $closureBase;
 // @codeCoverageIgnore
 }
 // in case of errors not converted into exceptions
 $closure = $closure ?: $closureBase;
 return $closure(...\func_get_args());
 });
 }
 }
 private static function getAnonymousClassCodeForTrait(string $trait)
 {
 return 'return new class() extends ' . static::class . ' {use ' . $trait . ';};';
 }
 private static function getMixableMethods(self $context) : Generator
 {
 foreach (\get_class_methods($context) as $name) {
 if (\method_exists(static::class, $name)) {
 continue;
 }
 (yield $name);
 }
 }
 protected static function bindMacroContext($context, callable $callable)
 {
 static::$macroContextStack[] = $context;
 $exception = null;
 $result = null;
 try {
 $result = $callable();
 } catch (Throwable $throwable) {
 $exception = $throwable;
 }
 \array_pop(static::$macroContextStack);
 if ($exception) {
 throw $exception;
 }
 return $result;
 }
 protected static function context()
 {
 return \end(static::$macroContextStack) ?: null;
 }
 protected static function this()
 {
 return \end(static::$macroContextStack) ?: new static();
 }
}
