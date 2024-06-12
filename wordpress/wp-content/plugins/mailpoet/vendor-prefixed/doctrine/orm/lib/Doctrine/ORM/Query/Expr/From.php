<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\Expr;
if (!defined('ABSPATH')) exit;
class From
{
 protected $from;
 protected $alias;
 protected $indexBy;
 public function __construct($from, $alias, $indexBy = null)
 {
 $this->from = $from;
 $this->alias = $alias;
 $this->indexBy = $indexBy;
 }
 public function getFrom()
 {
 return $this->from;
 }
 public function getAlias()
 {
 return $this->alias;
 }
 public function getIndexBy()
 {
 return $this->indexBy;
 }
 public function __toString()
 {
 return $this->from . ' ' . $this->alias . ($this->indexBy ? ' INDEX BY ' . $this->indexBy : '');
 }
}
