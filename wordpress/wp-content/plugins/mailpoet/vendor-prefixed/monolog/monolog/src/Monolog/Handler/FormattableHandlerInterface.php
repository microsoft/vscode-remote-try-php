<?php
declare (strict_types=1);
namespace MailPoetVendor\Monolog\Handler;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Monolog\Formatter\FormatterInterface;
interface FormattableHandlerInterface
{
 public function setFormatter(FormatterInterface $formatter) : HandlerInterface;
 public function getFormatter() : FormatterInterface;
}
