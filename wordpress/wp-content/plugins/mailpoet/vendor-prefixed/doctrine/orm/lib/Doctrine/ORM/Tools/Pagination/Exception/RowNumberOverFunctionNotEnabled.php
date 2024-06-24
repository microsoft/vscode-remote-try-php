<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Tools\Pagination\Exception;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Exception\ORMException;
final class RowNumberOverFunctionNotEnabled extends ORMException
{
 public static function create() : self
 {
 return new self('The RowNumberOverFunction is not intended for, nor is it enabled for use in DQL.');
 }
}
