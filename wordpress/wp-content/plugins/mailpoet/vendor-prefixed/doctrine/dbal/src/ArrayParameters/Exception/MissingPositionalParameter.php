<?php
namespace MailPoetVendor\Doctrine\DBAL\ArrayParameters\Exception;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\ArrayParameters\Exception;
use LogicException;
use function sprintf;
class MissingPositionalParameter extends LogicException implements Exception
{
 public static function new(int $index) : self
 {
 return new self(sprintf('Positional parameter at index %d does not have a bound value.', $index));
 }
}
