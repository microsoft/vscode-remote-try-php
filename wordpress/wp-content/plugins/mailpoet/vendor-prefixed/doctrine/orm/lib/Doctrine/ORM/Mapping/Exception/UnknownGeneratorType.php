<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Mapping\Exception;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Exception\ORMException;
final class UnknownGeneratorType extends ORMException
{
 public static function create(int $generatorType) : self
 {
 return new self('Unknown generator type: ' . $generatorType);
 }
}
