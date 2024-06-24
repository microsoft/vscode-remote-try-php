<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM;
if (!defined('ABSPATH')) exit;
class NoResultException extends UnexpectedResultException
{
 public function __construct()
 {
 parent::__construct('No result was found for query although at least one row was expected.');
 }
}
