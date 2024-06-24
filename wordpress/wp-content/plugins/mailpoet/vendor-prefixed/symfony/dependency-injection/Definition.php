<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\DependencyInjection\Argument\BoundArgument;
use MailPoetVendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use MailPoetVendor\Symfony\Component\DependencyInjection\Exception\OutOfBoundsException;
class Definition
{
 private const DEFAULT_DEPRECATION_TEMPLATE = 'The "%service_id%" service is deprecated. You should stop using it, as it will be removed in the future.';
 private $class;
 private $file;
 private $factory;
 private $shared = \true;
 private $deprecation = [];
 private $properties = [];
 private $calls = [];
 private $instanceof = [];
 private $autoconfigured = \false;
 private $configurator;
 private $tags = [];
 private $public = \false;
 private $synthetic = \false;
 private $abstract = \false;
 private $lazy = \false;
 private $decoratedService;
 private $autowired = \false;
 private $changes = [];
 private $bindings = [];
 private $errors = [];
 protected $arguments = [];
 public $innerServiceId;
 public $decorationOnInvalid;
 public function __construct(?string $class = null, array $arguments = [])
 {
 if (null !== $class) {
 $this->setClass($class);
 }
 $this->arguments = $arguments;
 }
 public function getChanges()
 {
 return $this->changes;
 }
 public function setChanges(array $changes)
 {
 $this->changes = $changes;
 return $this;
 }
 public function setFactory($factory)
 {
 $this->changes['factory'] = \true;
 if (\is_string($factory) && \str_contains($factory, '::')) {
 $factory = \explode('::', $factory, 2);
 } elseif ($factory instanceof Reference) {
 $factory = [$factory, '__invoke'];
 }
 $this->factory = $factory;
 return $this;
 }
 public function getFactory()
 {
 return $this->factory;
 }
 public function setDecoratedService(?string $id, ?string $renamedId = null, int $priority = 0, int $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE)
 {
 if ($renamedId && $id === $renamedId) {
 throw new InvalidArgumentException(\sprintf('The decorated service inner name for "%s" must be different than the service name itself.', $id));
 }
 $this->changes['decorated_service'] = \true;
 if (null === $id) {
 $this->decoratedService = null;
 } else {
 $this->decoratedService = [$id, $renamedId, $priority];
 if (ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE !== $invalidBehavior) {
 $this->decoratedService[] = $invalidBehavior;
 }
 }
 return $this;
 }
 public function getDecoratedService()
 {
 return $this->decoratedService;
 }
 public function setClass(?string $class)
 {
 $this->changes['class'] = \true;
 $this->class = $class;
 return $this;
 }
 public function getClass()
 {
 return $this->class;
 }
 public function setArguments(array $arguments)
 {
 $this->arguments = $arguments;
 return $this;
 }
 public function setProperties(array $properties)
 {
 $this->properties = $properties;
 return $this;
 }
 public function getProperties()
 {
 return $this->properties;
 }
 public function setProperty(string $name, $value)
 {
 $this->properties[$name] = $value;
 return $this;
 }
 public function addArgument($argument)
 {
 $this->arguments[] = $argument;
 return $this;
 }
 public function replaceArgument($index, $argument)
 {
 if (0 === \count($this->arguments)) {
 throw new OutOfBoundsException(\sprintf('Cannot replace arguments for class "%s" if none have been configured yet.', $this->class));
 }
 if (\is_int($index) && ($index < 0 || $index > \count($this->arguments) - 1)) {
 throw new OutOfBoundsException(\sprintf('The index "%d" is not in the range [0, %d] of the arguments of class "%s".', $index, \count($this->arguments) - 1, $this->class));
 }
 if (!\array_key_exists($index, $this->arguments)) {
 throw new OutOfBoundsException(\sprintf('The argument "%s" doesn\'t exist in class "%s".', $index, $this->class));
 }
 $this->arguments[$index] = $argument;
 return $this;
 }
 public function setArgument($key, $value)
 {
 $this->arguments[$key] = $value;
 return $this;
 }
 public function getArguments()
 {
 return $this->arguments;
 }
 public function getArgument($index)
 {
 if (!\array_key_exists($index, $this->arguments)) {
 throw new OutOfBoundsException(\sprintf('The argument "%s" doesn\'t exist in class "%s".', $index, $this->class));
 }
 return $this->arguments[$index];
 }
 public function setMethodCalls(array $calls = [])
 {
 $this->calls = [];
 foreach ($calls as $call) {
 $this->addMethodCall($call[0], $call[1], $call[2] ?? \false);
 }
 return $this;
 }
 public function addMethodCall(string $method, array $arguments = [], bool $returnsClone = \false)
 {
 if (empty($method)) {
 throw new InvalidArgumentException('Method name cannot be empty.');
 }
 $this->calls[] = $returnsClone ? [$method, $arguments, \true] : [$method, $arguments];
 return $this;
 }
 public function removeMethodCall(string $method)
 {
 foreach ($this->calls as $i => $call) {
 if ($call[0] === $method) {
 unset($this->calls[$i]);
 }
 }
 return $this;
 }
 public function hasMethodCall(string $method)
 {
 foreach ($this->calls as $call) {
 if ($call[0] === $method) {
 return \true;
 }
 }
 return \false;
 }
 public function getMethodCalls()
 {
 return $this->calls;
 }
 public function setInstanceofConditionals(array $instanceof)
 {
 $this->instanceof = $instanceof;
 return $this;
 }
 public function getInstanceofConditionals()
 {
 return $this->instanceof;
 }
 public function setAutoconfigured(bool $autoconfigured)
 {
 $this->changes['autoconfigured'] = \true;
 $this->autoconfigured = $autoconfigured;
 return $this;
 }
 public function isAutoconfigured()
 {
 return $this->autoconfigured;
 }
 public function setTags(array $tags)
 {
 $this->tags = $tags;
 return $this;
 }
 public function getTags()
 {
 return $this->tags;
 }
 public function getTag(string $name)
 {
 return $this->tags[$name] ?? [];
 }
 public function addTag(string $name, array $attributes = [])
 {
 $this->tags[$name][] = $attributes;
 return $this;
 }
 public function hasTag(string $name)
 {
 return isset($this->tags[$name]);
 }
 public function clearTag(string $name)
 {
 unset($this->tags[$name]);
 return $this;
 }
 public function clearTags()
 {
 $this->tags = [];
 return $this;
 }
 public function setFile(?string $file)
 {
 $this->changes['file'] = \true;
 $this->file = $file;
 return $this;
 }
 public function getFile()
 {
 return $this->file;
 }
 public function setShared(bool $shared)
 {
 $this->changes['shared'] = \true;
 $this->shared = $shared;
 return $this;
 }
 public function isShared()
 {
 return $this->shared;
 }
 public function setPublic(bool $boolean)
 {
 $this->changes['public'] = \true;
 $this->public = $boolean;
 return $this;
 }
 public function isPublic()
 {
 return $this->public;
 }
 public function setPrivate(bool $boolean)
 {
 trigger_deprecation('symfony/dependency-injection', '5.2', 'The "%s()" method is deprecated, use "setPublic()" instead.', __METHOD__);
 return $this->setPublic(!$boolean);
 }
 public function isPrivate()
 {
 return !$this->public;
 }
 public function setLazy(bool $lazy)
 {
 $this->changes['lazy'] = \true;
 $this->lazy = $lazy;
 return $this;
 }
 public function isLazy()
 {
 return $this->lazy;
 }
 public function setSynthetic(bool $boolean)
 {
 $this->synthetic = $boolean;
 if (!isset($this->changes['public'])) {
 $this->setPublic(\true);
 }
 return $this;
 }
 public function isSynthetic()
 {
 return $this->synthetic;
 }
 public function setAbstract(bool $boolean)
 {
 $this->abstract = $boolean;
 return $this;
 }
 public function isAbstract()
 {
 return $this->abstract;
 }
 public function setDeprecated()
 {
 $args = \func_get_args();
 if (\func_num_args() < 3) {
 trigger_deprecation('symfony/dependency-injection', '5.1', 'The signature of method "%s()" requires 3 arguments: "string $package, string $version, string $message", not defining them is deprecated.', __METHOD__);
 $status = $args[0] ?? \true;
 if (!$status) {
 trigger_deprecation('symfony/dependency-injection', '5.1', 'Passing a null message to un-deprecate a node is deprecated.');
 }
 $message = (string) ($args[1] ?? null);
 $package = $version = '';
 } else {
 $status = \true;
 $package = (string) $args[0];
 $version = (string) $args[1];
 $message = (string) $args[2];
 }
 if ('' !== $message) {
 if (\preg_match('#[\\r\\n]|\\*/#', $message)) {
 throw new InvalidArgumentException('Invalid characters found in deprecation template.');
 }
 if (!\str_contains($message, '%service_id%')) {
 throw new InvalidArgumentException('The deprecation template must contain the "%service_id%" placeholder.');
 }
 }
 $this->changes['deprecated'] = \true;
 $this->deprecation = $status ? ['package' => $package, 'version' => $version, 'message' => $message ?: self::DEFAULT_DEPRECATION_TEMPLATE] : [];
 return $this;
 }
 public function isDeprecated()
 {
 return (bool) $this->deprecation;
 }
 public function getDeprecationMessage(string $id)
 {
 trigger_deprecation('symfony/dependency-injection', '5.1', 'The "%s()" method is deprecated, use "getDeprecation()" instead.', __METHOD__);
 return $this->getDeprecation($id)['message'];
 }
 public function getDeprecation(string $id) : array
 {
 return ['package' => $this->deprecation['package'], 'version' => $this->deprecation['version'], 'message' => \str_replace('%service_id%', $id, $this->deprecation['message'])];
 }
 public function setConfigurator($configurator)
 {
 $this->changes['configurator'] = \true;
 if (\is_string($configurator) && \str_contains($configurator, '::')) {
 $configurator = \explode('::', $configurator, 2);
 } elseif ($configurator instanceof Reference) {
 $configurator = [$configurator, '__invoke'];
 }
 $this->configurator = $configurator;
 return $this;
 }
 public function getConfigurator()
 {
 return $this->configurator;
 }
 public function isAutowired()
 {
 return $this->autowired;
 }
 public function setAutowired(bool $autowired)
 {
 $this->changes['autowired'] = \true;
 $this->autowired = $autowired;
 return $this;
 }
 public function getBindings()
 {
 return $this->bindings;
 }
 public function setBindings(array $bindings)
 {
 foreach ($bindings as $key => $binding) {
 if (0 < \strpos($key, '$') && $key !== ($k = \preg_replace('/[ \\t]*\\$/', ' $', $key))) {
 unset($bindings[$key]);
 $bindings[$key = $k] = $binding;
 }
 if (!$binding instanceof BoundArgument) {
 $bindings[$key] = new BoundArgument($binding);
 }
 }
 $this->bindings = $bindings;
 return $this;
 }
 public function addError($error)
 {
 if ($error instanceof self) {
 $this->errors = \array_merge($this->errors, $error->errors);
 } else {
 $this->errors[] = $error;
 }
 return $this;
 }
 public function getErrors()
 {
 foreach ($this->errors as $i => $error) {
 if ($error instanceof \Closure) {
 $this->errors[$i] = (string) $error();
 } elseif (!\is_string($error)) {
 $this->errors[$i] = (string) $error;
 }
 }
 return $this->errors;
 }
 public function hasErrors() : bool
 {
 return (bool) $this->errors;
 }
}
