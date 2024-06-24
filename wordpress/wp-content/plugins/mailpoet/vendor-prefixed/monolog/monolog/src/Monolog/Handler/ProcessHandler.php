<?php
declare (strict_types=1);
namespace MailPoetVendor\Monolog\Handler;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Monolog\Logger;
class ProcessHandler extends AbstractProcessingHandler
{
 private $process;
 private $command;
 private $cwd;
 private $pipes = [];
 protected const DESCRIPTOR_SPEC = [
 0 => ['pipe', 'r'],
 // STDIN is a pipe that the child will read from
 1 => ['pipe', 'w'],
 // STDOUT is a pipe that the child will write to
 2 => ['pipe', 'w'],
 ];
 public function __construct(string $command, $level = Logger::DEBUG, bool $bubble = \true, ?string $cwd = null)
 {
 if ($command === '') {
 throw new \InvalidArgumentException('The command argument must be a non-empty string.');
 }
 if ($cwd === '') {
 throw new \InvalidArgumentException('The optional CWD argument must be a non-empty string or null.');
 }
 parent::__construct($level, $bubble);
 $this->command = $command;
 $this->cwd = $cwd;
 }
 protected function write(array $record) : void
 {
 $this->ensureProcessIsStarted();
 $this->writeProcessInput($record['formatted']);
 $errors = $this->readProcessErrors();
 if (empty($errors) === \false) {
 throw new \UnexpectedValueException(\sprintf('Errors while writing to process: %s', $errors));
 }
 }
 private function ensureProcessIsStarted() : void
 {
 if (\is_resource($this->process) === \false) {
 $this->startProcess();
 $this->handleStartupErrors();
 }
 }
 private function startProcess() : void
 {
 $this->process = \proc_open($this->command, static::DESCRIPTOR_SPEC, $this->pipes, $this->cwd);
 foreach ($this->pipes as $pipe) {
 \stream_set_blocking($pipe, \false);
 }
 }
 private function handleStartupErrors() : void
 {
 $selected = $this->selectErrorStream();
 if (\false === $selected) {
 throw new \UnexpectedValueException('Something went wrong while selecting a stream.');
 }
 $errors = $this->readProcessErrors();
 if (\is_resource($this->process) === \false || empty($errors) === \false) {
 throw new \UnexpectedValueException(\sprintf('The process "%s" could not be opened: ' . $errors, $this->command));
 }
 }
 protected function selectErrorStream()
 {
 $empty = [];
 $errorPipes = [$this->pipes[2]];
 return \stream_select($errorPipes, $empty, $empty, 1);
 }
 protected function readProcessErrors() : string
 {
 return (string) \stream_get_contents($this->pipes[2]);
 }
 protected function writeProcessInput(string $string) : void
 {
 \fwrite($this->pipes[0], $string);
 }
 public function close() : void
 {
 if (\is_resource($this->process)) {
 foreach ($this->pipes as $pipe) {
 \fclose($pipe);
 }
 \proc_close($this->process);
 $this->process = null;
 }
 }
}
