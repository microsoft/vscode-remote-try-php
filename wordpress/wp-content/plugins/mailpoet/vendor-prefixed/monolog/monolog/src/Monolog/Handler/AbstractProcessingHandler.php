<?php
declare (strict_types=1);
namespace MailPoetVendor\Monolog\Handler;
if (!defined('ABSPATH')) exit;
abstract class AbstractProcessingHandler extends AbstractHandler implements ProcessableHandlerInterface, FormattableHandlerInterface
{
 use ProcessableHandlerTrait;
 use FormattableHandlerTrait;
 public function handle(array $record) : bool
 {
 if (!$this->isHandling($record)) {
 return \false;
 }
 if ($this->processors) {
 $record = $this->processRecord($record);
 }
 $record['formatted'] = $this->getFormatter()->format($record);
 $this->write($record);
 return \false === $this->bubble;
 }
 protected abstract function write(array $record) : void;
 public function reset()
 {
 parent::reset();
 $this->resetProcessors();
 }
}
