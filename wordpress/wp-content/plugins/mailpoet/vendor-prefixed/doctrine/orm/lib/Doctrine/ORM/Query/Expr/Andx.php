<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\Expr;
if (!defined('ABSPATH')) exit;
class Andx extends Composite
{
 protected $separator = ' AND ';
 protected $allowedClasses = [Comparison::class, Func::class, Orx::class, self::class];
 protected $parts = [];
 public function getParts()
 {
 return $this->parts;
 }
}
