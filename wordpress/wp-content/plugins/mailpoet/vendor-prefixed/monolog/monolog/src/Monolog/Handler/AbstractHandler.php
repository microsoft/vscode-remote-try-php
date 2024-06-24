<?php
declare (strict_types=1);
namespace MailPoetVendor\Monolog\Handler;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Monolog\Logger;
use MailPoetVendor\Monolog\ResettableInterface;
use MailPoetVendor\Psr\Log\LogLevel;
abstract class AbstractHandler extends Handler implements ResettableInterface
{
 protected $level = Logger::DEBUG;
 protected $bubble = \true;
 public function __construct($level = Logger::DEBUG, bool $bubble = \true)
 {
 $this->setLevel($level);
 $this->bubble = $bubble;
 }
 public function isHandling(array $record) : bool
 {
 return $record['level'] >= $this->level;
 }
 public function setLevel($level) : self
 {
 $this->level = Logger::toMonologLevel($level);
 return $this;
 }
 public function getLevel() : int
 {
 return $this->level;
 }
 public function setBubble(bool $bubble) : self
 {
 $this->bubble = $bubble;
 return $this;
 }
 public function getBubble() : bool
 {
 return $this->bubble;
 }
 public function reset()
 {
 }
}
