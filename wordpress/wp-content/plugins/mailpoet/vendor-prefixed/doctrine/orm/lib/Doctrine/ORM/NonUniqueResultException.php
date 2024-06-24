<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM;
if (!defined('ABSPATH')) exit;
class NonUniqueResultException extends UnexpectedResultException
{
 public const DEFAULT_MESSAGE = 'More than one result was found for query although one row or none was expected.';
 public function __construct(?string $message = null)
 {
 parent::__construct($message ?? self::DEFAULT_MESSAGE);
 }
}
