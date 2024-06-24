<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Tools\Exception;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Exception\ORMException;
use MailPoetVendor\Doctrine\ORM\Exception\SchemaToolException;
final class NotSupported extends ORMException implements SchemaToolException
{
 public static function create() : self
 {
 return new self('This behaviour is (currently) not supported by Doctrine 2');
 }
}
