<?php
namespace MailPoetVendor\Symfony\Component\CssSelector\Parser;
if (!defined('ABSPATH')) exit;
class Reader
{
 private $source;
 private $length;
 private $position = 0;
 public function __construct(string $source)
 {
 $this->source = $source;
 $this->length = \strlen($source);
 }
 public function isEOF() : bool
 {
 return $this->position >= $this->length;
 }
 public function getPosition() : int
 {
 return $this->position;
 }
 public function getRemainingLength() : int
 {
 return $this->length - $this->position;
 }
 public function getSubstring(int $length, int $offset = 0) : string
 {
 return \substr($this->source, $this->position + $offset, $length);
 }
 public function getOffset(string $string)
 {
 $position = \strpos($this->source, $string, $this->position);
 return \false === $position ? \false : $position - $this->position;
 }
 public function findPattern(string $pattern)
 {
 $source = \substr($this->source, $this->position);
 if (\preg_match($pattern, $source, $matches)) {
 return $matches;
 }
 return \false;
 }
 public function moveForward(int $length)
 {
 $this->position += $length;
 }
 public function moveToEnd()
 {
 $this->position = $this->length;
 }
}
