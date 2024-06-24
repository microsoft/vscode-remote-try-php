<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Exception\ORMException;
class TransactionRequiredException extends ORMException
{
 public static function transactionRequired()
 {
 return new self('An open transaction is required for this operation.');
 }
}
