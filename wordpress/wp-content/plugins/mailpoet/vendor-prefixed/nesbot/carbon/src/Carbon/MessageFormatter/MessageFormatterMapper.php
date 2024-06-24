<?php
namespace MailPoetVendor\Carbon\MessageFormatter;
if (!defined('ABSPATH')) exit;
use ReflectionMethod;
use MailPoetVendor\Symfony\Component\Translation\Formatter\MessageFormatter;
use MailPoetVendor\Symfony\Component\Translation\Formatter\MessageFormatterInterface;
// @codeCoverageIgnoreStart
$transMethod = new ReflectionMethod(MessageFormatterInterface::class, 'format');
require $transMethod->getParameters()[0]->hasType() ? __DIR__ . '/../../../lazy/Carbon/MessageFormatter/MessageFormatterMapperStrongType.php' : __DIR__ . '/../../../lazy/Carbon/MessageFormatter/MessageFormatterMapperWeakType.php';
// @codeCoverageIgnoreEnd
final class MessageFormatterMapper extends LazyMessageFormatter
{
 protected $formatter;
 public function __construct(?MessageFormatterInterface $formatter = null)
 {
 $this->formatter = $formatter ?? new MessageFormatter();
 }
 protected function transformLocale(?string $locale) : ?string
 {
 return $locale ? \preg_replace('/[_@][A-Za-z][a-z]{2,}/', '', $locale) : $locale;
 }
}
