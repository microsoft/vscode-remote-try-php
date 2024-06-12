<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use MailPoetVendor\Symfony\Component\DependencyInjection\Argument\ServiceLocator as ArgumentServiceLocator;
use MailPoetVendor\Symfony\Component\DependencyInjection\Exception\EnvNotFoundException;
use MailPoetVendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use MailPoetVendor\Symfony\Component\DependencyInjection\Exception\ParameterCircularReferenceException;
use MailPoetVendor\Symfony\Component\DependencyInjection\Exception\RuntimeException;
use MailPoetVendor\Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use MailPoetVendor\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use MailPoetVendor\Symfony\Component\DependencyInjection\ParameterBag\EnvPlaceholderParameterBag;
use MailPoetVendor\Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use MailPoetVendor\Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use MailPoetVendor\Symfony\Contracts\Service\ResetInterface;
// Help opcache.preload discover always-needed symbols
\class_exists(RewindableGenerator::class);
\class_exists(ArgumentServiceLocator::class);
class Container implements ContainerInterface, ResetInterface
{
 protected $parameterBag;
 protected $services = [];
 protected $privates = [];
 protected $fileMap = [];
 protected $methodMap = [];
 protected $factories = [];
 protected $aliases = [];
 protected $loading = [];
 protected $resolving = [];
 protected $syntheticIds = [];
 private $envCache = [];
 private $compiled = \false;
 private $getEnv;
 public function __construct(ParameterBagInterface $parameterBag = null)
 {
 $this->parameterBag = $parameterBag ?? new EnvPlaceholderParameterBag();
 }
 public function compile()
 {
 $this->parameterBag->resolve();
 $this->parameterBag = new FrozenParameterBag($this->parameterBag->all());
 $this->compiled = \true;
 }
 public function isCompiled()
 {
 return $this->compiled;
 }
 public function getParameterBag()
 {
 return $this->parameterBag;
 }
 public function getParameter(string $name)
 {
 return $this->parameterBag->get($name);
 }
 public function hasParameter(string $name)
 {
 return $this->parameterBag->has($name);
 }
 public function setParameter(string $name, $value)
 {
 $this->parameterBag->set($name, $value);
 }
 public function set(string $id, ?object $service)
 {
 // Runs the internal initializer; used by the dumped container to include always-needed files
 if (isset($this->privates['service_container']) && $this->privates['service_container'] instanceof \Closure) {
 $initialize = $this->privates['service_container'];
 unset($this->privates['service_container']);
 $initialize();
 }
 if ('service_container' === $id) {
 throw new InvalidArgumentException('You cannot set service "service_container".');
 }
 if (!(isset($this->fileMap[$id]) || isset($this->methodMap[$id]))) {
 if (isset($this->syntheticIds[$id]) || !isset($this->getRemovedIds()[$id])) {
 // no-op
 } elseif (null === $service) {
 throw new InvalidArgumentException(\sprintf('The "%s" service is private, you cannot unset it.', $id));
 } else {
 throw new InvalidArgumentException(\sprintf('The "%s" service is private, you cannot replace it.', $id));
 }
 } elseif (isset($this->services[$id])) {
 throw new InvalidArgumentException(\sprintf('The "%s" service is already initialized, you cannot replace it.', $id));
 }
 if (isset($this->aliases[$id])) {
 unset($this->aliases[$id]);
 }
 if (null === $service) {
 unset($this->services[$id]);
 return;
 }
 $this->services[$id] = $service;
 }
 public function has(string $id)
 {
 if (isset($this->aliases[$id])) {
 $id = $this->aliases[$id];
 }
 if (isset($this->services[$id])) {
 return \true;
 }
 if ('service_container' === $id) {
 return \true;
 }
 return isset($this->fileMap[$id]) || isset($this->methodMap[$id]);
 }
 public function get(string $id, int $invalidBehavior = 1)
 {
 return $this->services[$id] ?? $this->services[$id = $this->aliases[$id] ?? $id] ?? ('service_container' === $id ? $this : ($this->factories[$id] ?? [$this, 'make'])($id, $invalidBehavior));
 }
 private function make(string $id, int $invalidBehavior)
 {
 if (isset($this->loading[$id])) {
 throw new ServiceCircularReferenceException($id, \array_merge(\array_keys($this->loading), [$id]));
 }
 $this->loading[$id] = \true;
 try {
 if (isset($this->fileMap[$id])) {
 return 4 === $invalidBehavior ? null : $this->load($this->fileMap[$id]);
 } elseif (isset($this->methodMap[$id])) {
 return 4 === $invalidBehavior ? null : $this->{$this->methodMap[$id]}();
 }
 } catch (\Exception $e) {
 unset($this->services[$id]);
 throw $e;
 } finally {
 unset($this->loading[$id]);
 }
 if (1 === $invalidBehavior) {
 if (!$id) {
 throw new ServiceNotFoundException($id);
 }
 if (isset($this->syntheticIds[$id])) {
 throw new ServiceNotFoundException($id, null, null, [], \sprintf('The "%s" service is synthetic, it needs to be set at boot time before it can be used.', $id));
 }
 if (isset($this->getRemovedIds()[$id])) {
 throw new ServiceNotFoundException($id, null, null, [], \sprintf('The "%s" service or alias has been removed or inlined when the container was compiled. You should either make it public, or stop using the container directly and use dependency injection instead.', $id));
 }
 $alternatives = [];
 foreach ($this->getServiceIds() as $knownId) {
 if ('' === $knownId || '.' === $knownId[0]) {
 continue;
 }
 $lev = \levenshtein($id, $knownId);
 if ($lev <= \strlen($id) / 3 || \str_contains($knownId, $id)) {
 $alternatives[] = $knownId;
 }
 }
 throw new ServiceNotFoundException($id, null, null, $alternatives);
 }
 return null;
 }
 public function initialized(string $id)
 {
 if (isset($this->aliases[$id])) {
 $id = $this->aliases[$id];
 }
 if ('service_container' === $id) {
 return \false;
 }
 return isset($this->services[$id]);
 }
 public function reset()
 {
 $services = $this->services + $this->privates;
 $this->services = $this->factories = $this->privates = [];
 foreach ($services as $service) {
 try {
 if ($service instanceof ResetInterface) {
 $service->reset();
 }
 } catch (\Throwable $e) {
 continue;
 }
 }
 }
 public function getServiceIds()
 {
 return \array_map('strval', \array_unique(\array_merge(['service_container'], \array_keys($this->fileMap), \array_keys($this->methodMap), \array_keys($this->aliases), \array_keys($this->services))));
 }
 public function getRemovedIds()
 {
 return [];
 }
 public static function camelize(string $id)
 {
 return \strtr(\ucwords(\strtr($id, ['_' => ' ', '.' => '_ ', '\\' => '_ '])), [' ' => '']);
 }
 public static function underscore(string $id)
 {
 return \strtolower(\preg_replace(['/([A-Z]+)([A-Z][a-z])/', '/([a-z\\d])([A-Z])/'], ['\\1_\\2', '\\1_\\2'], \str_replace('_', '.', $id)));
 }
 protected function load(string $file)
 {
 return require $file;
 }
 protected function getEnv(string $name)
 {
 if (isset($this->resolving[$envName = "env({$name})"])) {
 throw new ParameterCircularReferenceException(\array_keys($this->resolving));
 }
 if (isset($this->envCache[$name]) || \array_key_exists($name, $this->envCache)) {
 return $this->envCache[$name];
 }
 if (!$this->has($id = 'container.env_var_processors_locator')) {
 $this->set($id, new ServiceLocator([]));
 }
 if (!$this->getEnv) {
 $this->getEnv = \Closure::fromCallable([$this, 'getEnv']);
 }
 $processors = $this->get($id);
 if (\false !== ($i = \strpos($name, ':'))) {
 $prefix = \substr($name, 0, $i);
 $localName = \substr($name, 1 + $i);
 } else {
 $prefix = 'string';
 $localName = $name;
 }
 $processor = $processors->has($prefix) ? $processors->get($prefix) : new EnvVarProcessor($this);
 $this->resolving[$envName] = \true;
 try {
 return $this->envCache[$name] = $processor->getEnv($prefix, $localName, $this->getEnv);
 } finally {
 unset($this->resolving[$envName]);
 }
 }
 protected final function getService($registry, string $id, ?string $method, $load)
 {
 if ('service_container' === $id) {
 return $this;
 }
 if (\is_string($load)) {
 throw new RuntimeException($load);
 }
 if (null === $method) {
 return \false !== $registry ? $this->{$registry}[$id] ?? null : null;
 }
 if (\false !== $registry) {
 return $this->{$registry}[$id] ?? ($this->{$registry}[$id] = $load ? $this->load($method) : $this->{$method}());
 }
 if (!$load) {
 return $this->{$method}();
 }
 return ($factory = $this->factories[$id] ?? $this->factories['service_container'][$id] ?? null) ? $factory() : $this->load($method);
 }
 private function __clone()
 {
 }
}
