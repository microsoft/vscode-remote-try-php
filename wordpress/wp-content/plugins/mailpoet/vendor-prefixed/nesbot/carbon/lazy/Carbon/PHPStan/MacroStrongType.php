<?php
declare (strict_types=1);
namespace MailPoetVendor\Carbon\PHPStan;
if (!defined('ABSPATH')) exit;
if (!\class_exists(LazyMacro::class, \false)) {
 abstract class LazyMacro extends AbstractReflectionMacro
 {
 public function getFileName() : ?string
 {
 $file = $this->reflectionFunction->getFileName();
 return (($file ? \realpath($file) : null) ?: $file) ?: null;
 }
 public function getStartLine() : ?int
 {
 return $this->reflectionFunction->getStartLine();
 }
 public function getEndLine() : ?int
 {
 return $this->reflectionFunction->getEndLine();
 }
 }
}
