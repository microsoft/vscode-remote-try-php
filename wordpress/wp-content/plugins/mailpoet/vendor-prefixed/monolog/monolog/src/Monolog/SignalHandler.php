<?php
declare (strict_types=1);
namespace MailPoetVendor\Monolog;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Psr\Log\LoggerInterface;
use MailPoetVendor\Psr\Log\LogLevel;
use ReflectionExtension;
class SignalHandler
{
 private $logger;
 private $previousSignalHandler = [];
 private $signalLevelMap = [];
 private $signalRestartSyscalls = [];
 public function __construct(LoggerInterface $logger)
 {
 $this->logger = $logger;
 }
 public function registerSignalHandler(int $signo, $level = LogLevel::CRITICAL, bool $callPrevious = \true, bool $restartSyscalls = \true, ?bool $async = \true) : self
 {
 if (!\extension_loaded('pcntl') || !\function_exists('pcntl_signal')) {
 return $this;
 }
 $level = Logger::toMonologLevel($level);
 if ($callPrevious) {
 $handler = \pcntl_signal_get_handler($signo);
 $this->previousSignalHandler[$signo] = $handler;
 } else {
 unset($this->previousSignalHandler[$signo]);
 }
 $this->signalLevelMap[$signo] = $level;
 $this->signalRestartSyscalls[$signo] = $restartSyscalls;
 if ($async !== null) {
 \pcntl_async_signals($async);
 }
 \pcntl_signal($signo, [$this, 'handleSignal'], $restartSyscalls);
 return $this;
 }
 public function handleSignal(int $signo, $siginfo = null) : void
 {
 static $signals = [];
 if (!$signals && \extension_loaded('pcntl')) {
 $pcntl = new ReflectionExtension('pcntl');
 // HHVM 3.24.2 returns an empty array.
 foreach ($pcntl->getConstants() ?: \get_defined_constants(\true)['Core'] as $name => $value) {
 if (\substr($name, 0, 3) === 'SIG' && $name[3] !== '_' && \is_int($value)) {
 $signals[$value] = $name;
 }
 }
 }
 $level = $this->signalLevelMap[$signo] ?? LogLevel::CRITICAL;
 $signal = $signals[$signo] ?? $signo;
 $context = $siginfo ?? [];
 $this->logger->log($level, \sprintf('Program received signal %s', $signal), $context);
 if (!isset($this->previousSignalHandler[$signo])) {
 return;
 }
 if ($this->previousSignalHandler[$signo] === \SIG_DFL) {
 if (\extension_loaded('pcntl') && \function_exists('pcntl_signal') && \function_exists('pcntl_sigprocmask') && \function_exists('pcntl_signal_dispatch') && \extension_loaded('posix') && \function_exists('posix_getpid') && \function_exists('posix_kill')) {
 $restartSyscalls = $this->signalRestartSyscalls[$signo] ?? \true;
 \pcntl_signal($signo, \SIG_DFL, $restartSyscalls);
 \pcntl_sigprocmask(\SIG_UNBLOCK, [$signo], $oldset);
 \posix_kill(\posix_getpid(), $signo);
 \pcntl_signal_dispatch();
 \pcntl_sigprocmask(\SIG_SETMASK, $oldset);
 \pcntl_signal($signo, [$this, 'handleSignal'], $restartSyscalls);
 }
 } elseif (\is_callable($this->previousSignalHandler[$signo])) {
 $this->previousSignalHandler[$signo]($signo, $siginfo);
 }
 }
}
