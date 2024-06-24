<?php
declare (strict_types=1);
namespace MailPoetVendor\Monolog\Handler;
if (!defined('ABSPATH')) exit;
use Throwable;
class FallbackGroupHandler extends GroupHandler
{
 public function handle(array $record) : bool
 {
 if ($this->processors) {
 $record = $this->processRecord($record);
 }
 foreach ($this->handlers as $handler) {
 try {
 $handler->handle($record);
 break;
 } catch (Throwable $e) {
 // What throwable?
 }
 }
 return \false === $this->bubble;
 }
 public function handleBatch(array $records) : void
 {
 if ($this->processors) {
 $processed = [];
 foreach ($records as $record) {
 $processed[] = $this->processRecord($record);
 }
 $records = $processed;
 }
 foreach ($this->handlers as $handler) {
 try {
 $handler->handleBatch($records);
 break;
 } catch (Throwable $e) {
 // What throwable?
 }
 }
 }
}
