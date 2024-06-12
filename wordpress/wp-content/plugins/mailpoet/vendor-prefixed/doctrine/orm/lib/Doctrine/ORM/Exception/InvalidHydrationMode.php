<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Exception;
if (!defined('ABSPATH')) exit;
use function sprintf;
final class InvalidHydrationMode extends ORMException implements ManagerException
{
 public static function fromMode(string $mode) : self
 {
 return new self(sprintf('"%s" is an invalid hydration mode.', $mode));
 }
}
