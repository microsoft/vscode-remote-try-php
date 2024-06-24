<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
class Alias
{
 private const DEFAULT_DEPRECATION_TEMPLATE = 'The "%alias_id%" service alias is deprecated. You should stop using it, as it will be removed in the future.';
 private $id;
 private $public;
 private $deprecation = [];
 public function __construct(string $id, bool $public = \false)
 {
 $this->id = $id;
 $this->public = $public;
 }
 public function isPublic()
 {
 return $this->public;
 }
 public function setPublic(bool $boolean)
 {
 $this->public = $boolean;
 return $this;
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
 if (!\str_contains($message, '%alias_id%')) {
 throw new InvalidArgumentException('The deprecation template must contain the "%alias_id%" placeholder.');
 }
 }
 $this->deprecation = $status ? ['package' => $package, 'version' => $version, 'message' => $message ?: self::DEFAULT_DEPRECATION_TEMPLATE] : [];
 return $this;
 }
 public function isDeprecated() : bool
 {
 return (bool) $this->deprecation;
 }
 public function getDeprecationMessage(string $id) : string
 {
 trigger_deprecation('symfony/dependency-injection', '5.1', 'The "%s()" method is deprecated, use "getDeprecation()" instead.', __METHOD__);
 return $this->getDeprecation($id)['message'];
 }
 public function getDeprecation(string $id) : array
 {
 return ['package' => $this->deprecation['package'], 'version' => $this->deprecation['version'], 'message' => \str_replace('%alias_id%', $id, $this->deprecation['message'])];
 }
 public function __toString()
 {
 return $this->id;
 }
}
