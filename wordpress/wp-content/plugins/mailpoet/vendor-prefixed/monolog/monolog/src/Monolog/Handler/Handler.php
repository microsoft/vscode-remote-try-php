<?php
declare (strict_types=1);
namespace MailPoetVendor\Monolog\Handler;
if (!defined('ABSPATH')) exit;
abstract class Handler implements HandlerInterface
{
 public function handleBatch(array $records) : void
 {
 foreach ($records as $record) {
 $this->handle($record);
 }
 }
 public function close() : void
 {
 }
 public function __destruct()
 {
 try {
 $this->close();
 } catch (\Throwable $e) {
 // do nothing
 }
 }
 public function __sleep()
 {
 $this->close();
 $reflClass = new \ReflectionClass($this);
 $keys = [];
 foreach ($reflClass->getProperties() as $reflProp) {
 if (!$reflProp->isStatic()) {
 $keys[] = $reflProp->getName();
 }
 }
 return $keys;
 }
}
