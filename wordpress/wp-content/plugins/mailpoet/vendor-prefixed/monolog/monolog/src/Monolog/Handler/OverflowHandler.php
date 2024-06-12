<?php
declare (strict_types=1);
namespace MailPoetVendor\Monolog\Handler;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Monolog\Logger;
use MailPoetVendor\Monolog\Formatter\FormatterInterface;
class OverflowHandler extends AbstractHandler implements FormattableHandlerInterface
{
 private $handler;
 private $thresholdMap = [Logger::DEBUG => 0, Logger::INFO => 0, Logger::NOTICE => 0, Logger::WARNING => 0, Logger::ERROR => 0, Logger::CRITICAL => 0, Logger::ALERT => 0, Logger::EMERGENCY => 0];
 private $buffer = [];
 public function __construct(HandlerInterface $handler, array $thresholdMap = [], $level = Logger::DEBUG, bool $bubble = \true)
 {
 $this->handler = $handler;
 foreach ($thresholdMap as $thresholdLevel => $threshold) {
 $this->thresholdMap[$thresholdLevel] = $threshold;
 }
 parent::__construct($level, $bubble);
 }
 public function handle(array $record) : bool
 {
 if ($record['level'] < $this->level) {
 return \false;
 }
 $level = $record['level'];
 if (!isset($this->thresholdMap[$level])) {
 $this->thresholdMap[$level] = 0;
 }
 if ($this->thresholdMap[$level] > 0) {
 // The overflow threshold is not yet reached, so we're buffering the record and lowering the threshold by 1
 $this->thresholdMap[$level]--;
 $this->buffer[$level][] = $record;
 return \false === $this->bubble;
 }
 if ($this->thresholdMap[$level] == 0) {
 // This current message is breaking the threshold. Flush the buffer and continue handling the current record
 foreach ($this->buffer[$level] ?? [] as $buffered) {
 $this->handler->handle($buffered);
 }
 $this->thresholdMap[$level]--;
 unset($this->buffer[$level]);
 }
 $this->handler->handle($record);
 return \false === $this->bubble;
 }
 public function setFormatter(FormatterInterface $formatter) : HandlerInterface
 {
 if ($this->handler instanceof FormattableHandlerInterface) {
 $this->handler->setFormatter($formatter);
 return $this;
 }
 throw new \UnexpectedValueException('The nested handler of type ' . \get_class($this->handler) . ' does not support formatters.');
 }
 public function getFormatter() : FormatterInterface
 {
 if ($this->handler instanceof FormattableHandlerInterface) {
 return $this->handler->getFormatter();
 }
 throw new \UnexpectedValueException('The nested handler of type ' . \get_class($this->handler) . ' does not support formatters.');
 }
}
