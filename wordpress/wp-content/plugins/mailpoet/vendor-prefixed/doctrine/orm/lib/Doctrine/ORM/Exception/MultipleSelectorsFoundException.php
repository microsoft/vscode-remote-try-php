<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Exception;
if (!defined('ABSPATH')) exit;
use function implode;
use function sprintf;
final class MultipleSelectorsFoundException extends ORMException
{
 public const MULTIPLE_SELECTORS_FOUND_EXCEPTION = 'Multiple selectors found: %s. Please select only one.';
 public static function create(array $selectors) : self
 {
 return new self(sprintf(self::MULTIPLE_SELECTORS_FOUND_EXCEPTION, implode(', ', $selectors)));
 }
}
