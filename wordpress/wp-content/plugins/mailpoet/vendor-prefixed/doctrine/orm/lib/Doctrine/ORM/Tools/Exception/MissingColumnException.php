<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Tools\Exception;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Exception\ORMException;
use function sprintf;
final class MissingColumnException extends ORMException
{
 public static function fromColumnSourceAndTarget(string $column, string $source, string $target) : self
 {
 return new self(sprintf('Column name "%s" referenced for relation from %s towards %s does not exist.', $column, $source, $target));
 }
}
