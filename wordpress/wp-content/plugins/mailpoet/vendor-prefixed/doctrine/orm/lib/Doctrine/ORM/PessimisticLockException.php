<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Exception\ORMException;
class PessimisticLockException extends ORMException
{
 public static function lockFailed()
 {
 return new self('The pessimistic lock failed.');
 }
}
