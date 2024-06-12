<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\Expr;
if (!defined('ABSPATH')) exit;
class Literal extends Base
{
 protected $preSeparator = '';
 protected $postSeparator = '';
 protected $parts = [];
 public function getParts()
 {
 return $this->parts;
 }
}
