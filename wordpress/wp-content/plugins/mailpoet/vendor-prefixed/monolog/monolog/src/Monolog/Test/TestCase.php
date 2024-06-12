<?php
declare (strict_types=1);
namespace MailPoetVendor\Monolog\Test;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Monolog\Logger;
use MailPoetVendor\Monolog\DateTimeImmutable;
use MailPoetVendor\Monolog\Formatter\FormatterInterface;
class TestCase extends \MailPoetVendor\PHPUnit\Framework\TestCase
{
 protected function getRecord(int $level = Logger::WARNING, string $message = 'test', array $context = []) : array
 {
 return ['message' => (string) $message, 'context' => $context, 'level' => $level, 'level_name' => Logger::getLevelName($level), 'channel' => 'test', 'datetime' => new DateTimeImmutable(\true), 'extra' => []];
 }
 protected function getMultipleRecords() : array
 {
 return [$this->getRecord(Logger::DEBUG, 'debug message 1'), $this->getRecord(Logger::DEBUG, 'debug message 2'), $this->getRecord(Logger::INFO, 'information'), $this->getRecord(Logger::WARNING, 'warning'), $this->getRecord(Logger::ERROR, 'error')];
 }
 protected function getIdentityFormatter() : FormatterInterface
 {
 $formatter = $this->createMock(FormatterInterface::class);
 $formatter->expects($this->any())->method('format')->will($this->returnCallback(function ($record) {
 return $record['message'];
 }));
 return $formatter;
 }
}
