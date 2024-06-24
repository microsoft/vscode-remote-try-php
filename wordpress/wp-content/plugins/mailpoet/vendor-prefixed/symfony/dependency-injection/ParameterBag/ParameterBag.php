<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection\ParameterBag;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\DependencyInjection\Exception\ParameterCircularReferenceException;
use MailPoetVendor\Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use MailPoetVendor\Symfony\Component\DependencyInjection\Exception\RuntimeException;
class ParameterBag implements ParameterBagInterface
{
 protected $parameters = [];
 protected $resolved = \false;
 public function __construct(array $parameters = [])
 {
 $this->add($parameters);
 }
 public function clear()
 {
 $this->parameters = [];
 }
 public function add(array $parameters)
 {
 foreach ($parameters as $key => $value) {
 $this->set($key, $value);
 }
 }
 public function all()
 {
 return $this->parameters;
 }
 public function get(string $name)
 {
 if (!\array_key_exists($name, $this->parameters)) {
 if (!$name) {
 throw new ParameterNotFoundException($name);
 }
 $alternatives = [];
 foreach ($this->parameters as $key => $parameterValue) {
 $lev = \levenshtein($name, $key);
 if ($lev <= \strlen($name) / 3 || \str_contains($key, $name)) {
 $alternatives[] = $key;
 }
 }
 $nonNestedAlternative = null;
 if (!\count($alternatives) && \str_contains($name, '.')) {
 $namePartsLength = \array_map('strlen', \explode('.', $name));
 $key = \substr($name, 0, -1 * (1 + \array_pop($namePartsLength)));
 while (\count($namePartsLength)) {
 if ($this->has($key)) {
 if (\is_array($this->get($key))) {
 $nonNestedAlternative = $key;
 }
 break;
 }
 $key = \substr($key, 0, -1 * (1 + \array_pop($namePartsLength)));
 }
 }
 throw new ParameterNotFoundException($name, null, null, null, $alternatives, $nonNestedAlternative);
 }
 return $this->parameters[$name];
 }
 public function set(string $name, $value)
 {
 $this->parameters[$name] = $value;
 }
 public function has(string $name)
 {
 return \array_key_exists($name, $this->parameters);
 }
 public function remove(string $name)
 {
 unset($this->parameters[$name]);
 }
 public function resolve()
 {
 if ($this->resolved) {
 return;
 }
 $parameters = [];
 foreach ($this->parameters as $key => $value) {
 try {
 $value = $this->resolveValue($value);
 $parameters[$key] = $this->unescapeValue($value);
 } catch (ParameterNotFoundException $e) {
 $e->setSourceKey($key);
 throw $e;
 }
 }
 $this->parameters = $parameters;
 $this->resolved = \true;
 }
 public function resolveValue($value, array $resolving = [])
 {
 if (\is_array($value)) {
 $args = [];
 foreach ($value as $k => $v) {
 $args[\is_string($k) ? $this->resolveValue($k, $resolving) : $k] = $this->resolveValue($v, $resolving);
 }
 return $args;
 }
 if (!\is_string($value) || 2 > \strlen($value)) {
 return $value;
 }
 return $this->resolveString($value, $resolving);
 }
 public function resolveString(string $value, array $resolving = [])
 {
 // we do this to deal with non string values (Boolean, integer, ...)
 // as the preg_replace_callback throw an exception when trying
 // a non-string in a parameter value
 if (\preg_match('/^%([^%\\s]+)%$/', $value, $match)) {
 $key = $match[1];
 if (isset($resolving[$key])) {
 throw new ParameterCircularReferenceException(\array_keys($resolving));
 }
 $resolving[$key] = \true;
 return $this->resolved ? $this->get($key) : $this->resolveValue($this->get($key), $resolving);
 }
 return \preg_replace_callback('/%%|%([^%\\s]+)%/', function ($match) use($resolving, $value) {
 // skip %%
 if (!isset($match[1])) {
 return '%%';
 }
 $key = $match[1];
 if (isset($resolving[$key])) {
 throw new ParameterCircularReferenceException(\array_keys($resolving));
 }
 $resolved = $this->get($key);
 if (!\is_string($resolved) && !\is_numeric($resolved)) {
 throw new RuntimeException(\sprintf('A string value must be composed of strings and/or numbers, but found parameter "%s" of type "%s" inside string value "%s".', $key, \get_debug_type($resolved), $value));
 }
 $resolved = (string) $resolved;
 $resolving[$key] = \true;
 return $this->isResolved() ? $resolved : $this->resolveString($resolved, $resolving);
 }, $value);
 }
 public function isResolved()
 {
 return $this->resolved;
 }
 public function escapeValue($value)
 {
 if (\is_string($value)) {
 return \str_replace('%', '%%', $value);
 }
 if (\is_array($value)) {
 $result = [];
 foreach ($value as $k => $v) {
 $result[$k] = $this->escapeValue($v);
 }
 return $result;
 }
 return $value;
 }
 public function unescapeValue($value)
 {
 if (\is_string($value)) {
 return \str_replace('%%', '%', $value);
 }
 if (\is_array($value)) {
 $result = [];
 foreach ($value as $k => $v) {
 $result[$k] = $this->unescapeValue($v);
 }
 return $result;
 }
 return $value;
 }
}
