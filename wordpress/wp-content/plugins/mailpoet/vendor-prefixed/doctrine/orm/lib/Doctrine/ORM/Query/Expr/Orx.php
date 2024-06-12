<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\Expr;
if (!defined('ABSPATH')) exit;
class Orx extends Composite
{
 protected $separator = ' OR ';
 protected $allowedClasses = [Comparison::class, Func::class, Andx::class, self::class];
 protected $parts = [];
 public function getParts()
 {
 return $this->parts;
 }
}
