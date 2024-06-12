<?php
declare (strict_types=1);
namespace MailPoetVendor\Carbon\PHPStan;
if (!defined('ABSPATH')) exit;
if (!\class_exists(LazyMacro::class, \false)) {
 abstract class LazyMacro extends AbstractReflectionMacro
 {
 public function getFileName()
 {
 $file = $this->reflectionFunction->getFileName();
 return (($file ? \realpath($file) : null) ?: $file) ?: null;
 }
 public function getStartLine()
 {
 return $this->reflectionFunction->getStartLine();
 }
 public function getEndLine()
 {
 return $this->reflectionFunction->getEndLine();
 }
 }
}
