<?php
declare (strict_types=1);
namespace MailPoetVendor\Monolog;
if (!defined('ABSPATH')) exit;
use DateTimeZone;
use MailPoetVendor\Monolog\Handler\HandlerInterface;
use MailPoetVendor\Psr\Log\LoggerInterface;
use MailPoetVendor\Psr\Log\InvalidArgumentException;
use MailPoetVendor\Psr\Log\LogLevel;
use Throwable;
use Stringable;
class Logger implements LoggerInterface, ResettableInterface
{
 public const DEBUG = 100;
 public const INFO = 200;
 public const NOTICE = 250;
 public const WARNING = 300;
 public const ERROR = 400;
 public const CRITICAL = 500;
 public const ALERT = 550;
 public const EMERGENCY = 600;
 public const API = 2;
 protected static $levels = [self::DEBUG => 'DEBUG', self::INFO => 'INFO', self::NOTICE => 'NOTICE', self::WARNING => 'WARNING', self::ERROR => 'ERROR', self::CRITICAL => 'CRITICAL', self::ALERT => 'ALERT', self::EMERGENCY => 'EMERGENCY'];
 protected $name;
 protected $handlers;
 protected $processors;
 protected $microsecondTimestamps = \true;
 protected $timezone;
 protected $exceptionHandler;
 public function __construct(string $name, array $handlers = [], array $processors = [], ?DateTimeZone $timezone = null)
 {
 $this->name = $name;
 $this->setHandlers($handlers);
 $this->processors = $processors;
 $this->timezone = $timezone ?: new DateTimeZone(\date_default_timezone_get() ?: 'UTC');
 }
 public function getName() : string
 {
 return $this->name;
 }
 public function withName(string $name) : self
 {
 $new = clone $this;
 $new->name = $name;
 return $new;
 }
 public function pushHandler(HandlerInterface $handler) : self
 {
 \array_unshift($this->handlers, $handler);
 return $this;
 }
 public function popHandler() : HandlerInterface
 {
 if (!$this->handlers) {
 throw new \LogicException('You tried to pop from an empty handler stack.');
 }
 return \array_shift($this->handlers);
 }
 public function setHandlers(array $handlers) : self
 {
 $this->handlers = [];
 foreach (\array_reverse($handlers) as $handler) {
 $this->pushHandler($handler);
 }
 return $this;
 }
 public function getHandlers() : array
 {
 return $this->handlers;
 }
 public function pushProcessor(callable $callback) : self
 {
 \array_unshift($this->processors, $callback);
 return $this;
 }
 public function popProcessor() : callable
 {
 if (!$this->processors) {
 throw new \LogicException('You tried to pop from an empty processor stack.');
 }
 return \array_shift($this->processors);
 }
 public function getProcessors() : array
 {
 return $this->processors;
 }
 public function useMicrosecondTimestamps(bool $micro) : self
 {
 $this->microsecondTimestamps = $micro;
 return $this;
 }
 public function addRecord(int $level, string $message, array $context = []) : bool
 {
 $record = null;
 foreach ($this->handlers as $handler) {
 if (null === $record) {
 // skip creating the record as long as no handler is going to handle it
 if (!$handler->isHandling(['level' => $level])) {
 continue;
 }
 $levelName = static::getLevelName($level);
 $record = ['message' => $message, 'context' => $context, 'level' => $level, 'level_name' => $levelName, 'channel' => $this->name, 'datetime' => new DateTimeImmutable($this->microsecondTimestamps, $this->timezone), 'extra' => []];
 try {
 foreach ($this->processors as $processor) {
 $record = $processor($record);
 }
 } catch (Throwable $e) {
 $this->handleException($e, $record);
 return \true;
 }
 }
 // once the record exists, send it to all handlers as long as the bubbling chain is not interrupted
 try {
 if (\true === $handler->handle($record)) {
 break;
 }
 } catch (Throwable $e) {
 $this->handleException($e, $record);
 return \true;
 }
 }
 return null !== $record;
 }
 public function close() : void
 {
 foreach ($this->handlers as $handler) {
 $handler->close();
 }
 }
 public function reset() : void
 {
 foreach ($this->handlers as $handler) {
 if ($handler instanceof ResettableInterface) {
 $handler->reset();
 }
 }
 foreach ($this->processors as $processor) {
 if ($processor instanceof ResettableInterface) {
 $processor->reset();
 }
 }
 }
 public static function getLevels() : array
 {
 return \array_flip(static::$levels);
 }
 public static function getLevelName(int $level) : string
 {
 if (!isset(static::$levels[$level])) {
 throw new InvalidArgumentException('Level "' . $level . '" is not defined, use one of: ' . \implode(', ', \array_keys(static::$levels)));
 }
 return static::$levels[$level];
 }
 public static function toMonologLevel($level) : int
 {
 if (\is_string($level)) {
 if (\is_numeric($level)) {
 return \intval($level);
 }
 // Contains chars of all log levels and avoids using strtoupper() which may have
 // strange results depending on locale (for example, "i" will become "İ" in Turkish locale)
 $upper = \strtr($level, 'abcdefgilmnortuwy', 'ABCDEFGILMNORTUWY');
 if (\defined(__CLASS__ . '::' . $upper)) {
 return \constant(__CLASS__ . '::' . $upper);
 }
 throw new InvalidArgumentException('Level "' . $level . '" is not defined, use one of: ' . \implode(', ', \array_keys(static::$levels) + static::$levels));
 }
 if (!\is_int($level)) {
 throw new InvalidArgumentException('Level "' . \var_export($level, \true) . '" is not defined, use one of: ' . \implode(', ', \array_keys(static::$levels) + static::$levels));
 }
 return $level;
 }
 public function isHandling(int $level) : bool
 {
 $record = ['level' => $level];
 foreach ($this->handlers as $handler) {
 if ($handler->isHandling($record)) {
 return \true;
 }
 }
 return \false;
 }
 public function setExceptionHandler(?callable $callback) : self
 {
 $this->exceptionHandler = $callback;
 return $this;
 }
 public function getExceptionHandler() : ?callable
 {
 return $this->exceptionHandler;
 }
 public function log($level, $message, array $context = []) : void
 {
 if (!\is_int($level) && !\is_string($level)) {
 throw new \InvalidArgumentException('$level is expected to be a string or int');
 }
 $level = static::toMonologLevel($level);
 $this->addRecord($level, (string) $message, $context);
 }
 public function debug($message, array $context = []) : void
 {
 $this->addRecord(static::DEBUG, (string) $message, $context);
 }
 public function info($message, array $context = []) : void
 {
 $this->addRecord(static::INFO, (string) $message, $context);
 }
 public function notice($message, array $context = []) : void
 {
 $this->addRecord(static::NOTICE, (string) $message, $context);
 }
 public function warning($message, array $context = []) : void
 {
 $this->addRecord(static::WARNING, (string) $message, $context);
 }
 public function error($message, array $context = []) : void
 {
 $this->addRecord(static::ERROR, (string) $message, $context);
 }
 public function critical($message, array $context = []) : void
 {
 $this->addRecord(static::CRITICAL, (string) $message, $context);
 }
 public function alert($message, array $context = []) : void
 {
 $this->addRecord(static::ALERT, (string) $message, $context);
 }
 public function emergency($message, array $context = []) : void
 {
 $this->addRecord(static::EMERGENCY, (string) $message, $context);
 }
 public function setTimezone(DateTimeZone $tz) : self
 {
 $this->timezone = $tz;
 return $this;
 }
 public function getTimezone() : DateTimeZone
 {
 return $this->timezone;
 }
 protected function handleException(Throwable $e, array $record) : void
 {
 if (!$this->exceptionHandler) {
 throw $e;
 }
 ($this->exceptionHandler)($e, $record);
 }
}
