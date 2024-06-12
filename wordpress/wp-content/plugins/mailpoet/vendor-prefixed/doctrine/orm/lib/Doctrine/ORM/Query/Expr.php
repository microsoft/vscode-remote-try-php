<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query;
if (!defined('ABSPATH')) exit;
use Traversable;
use function func_get_args;
use function implode;
use function is_bool;
use function is_iterable;
use function is_numeric;
use function is_string;
use function iterator_to_array;
use function str_replace;
class Expr
{
 public function andX($x = null)
 {
 return new Expr\Andx(func_get_args());
 }
 public function orX($x = null)
 {
 return new Expr\Orx(func_get_args());
 }
 public function asc($expr)
 {
 return new Expr\OrderBy($expr, 'ASC');
 }
 public function desc($expr)
 {
 return new Expr\OrderBy($expr, 'DESC');
 }
 public function eq($x, $y)
 {
 return new Expr\Comparison($x, Expr\Comparison::EQ, $y);
 }
 public function neq($x, $y)
 {
 return new Expr\Comparison($x, Expr\Comparison::NEQ, $y);
 }
 public function lt($x, $y)
 {
 return new Expr\Comparison($x, Expr\Comparison::LT, $y);
 }
 public function lte($x, $y)
 {
 return new Expr\Comparison($x, Expr\Comparison::LTE, $y);
 }
 public function gt($x, $y)
 {
 return new Expr\Comparison($x, Expr\Comparison::GT, $y);
 }
 public function gte($x, $y)
 {
 return new Expr\Comparison($x, Expr\Comparison::GTE, $y);
 }
 public function avg($x)
 {
 return new Expr\Func('AVG', [$x]);
 }
 public function max($x)
 {
 return new Expr\Func('MAX', [$x]);
 }
 public function min($x)
 {
 return new Expr\Func('MIN', [$x]);
 }
 public function count($x)
 {
 return new Expr\Func('COUNT', [$x]);
 }
 public function countDistinct($x)
 {
 return 'COUNT(DISTINCT ' . implode(', ', func_get_args()) . ')';
 }
 public function exists($subquery)
 {
 return new Expr\Func('EXISTS', [$subquery]);
 }
 public function all($subquery)
 {
 return new Expr\Func('ALL', [$subquery]);
 }
 public function some($subquery)
 {
 return new Expr\Func('SOME', [$subquery]);
 }
 public function any($subquery)
 {
 return new Expr\Func('ANY', [$subquery]);
 }
 public function not($restriction)
 {
 return new Expr\Func('NOT', [$restriction]);
 }
 public function abs($x)
 {
 return new Expr\Func('ABS', [$x]);
 }
 public function mod($x, $y) : Expr\Func
 {
 return new Expr\Func('MOD', [$x, $y]);
 }
 public function prod($x, $y)
 {
 return new Expr\Math($x, '*', $y);
 }
 public function diff($x, $y)
 {
 return new Expr\Math($x, '-', $y);
 }
 public function sum($x, $y)
 {
 return new Expr\Math($x, '+', $y);
 }
 public function quot($x, $y)
 {
 return new Expr\Math($x, '/', $y);
 }
 public function sqrt($x)
 {
 return new Expr\Func('SQRT', [$x]);
 }
 public function in($x, $y)
 {
 if (is_iterable($y)) {
 if ($y instanceof Traversable) {
 $y = iterator_to_array($y);
 }
 foreach ($y as &$literal) {
 if (!$literal instanceof Expr\Literal) {
 $literal = $this->quoteLiteral($literal);
 }
 }
 }
 return new Expr\Func($x . ' IN', (array) $y);
 }
 public function notIn($x, $y)
 {
 if (is_iterable($y)) {
 if ($y instanceof Traversable) {
 $y = iterator_to_array($y);
 }
 foreach ($y as &$literal) {
 if (!$literal instanceof Expr\Literal) {
 $literal = $this->quoteLiteral($literal);
 }
 }
 }
 return new Expr\Func($x . ' NOT IN', (array) $y);
 }
 public function isNull($x)
 {
 return $x . ' IS NULL';
 }
 public function isNotNull($x)
 {
 return $x . ' IS NOT NULL';
 }
 public function like($x, $y)
 {
 return new Expr\Comparison($x, 'LIKE', $y);
 }
 public function notLike($x, $y)
 {
 return new Expr\Comparison($x, 'NOT LIKE', $y);
 }
 public function concat($x, $y)
 {
 return new Expr\Func('CONCAT', func_get_args());
 }
 public function substring($x, $from, $len = null)
 {
 $args = [$x, $from];
 if ($len !== null) {
 $args[] = $len;
 }
 return new Expr\Func('SUBSTRING', $args);
 }
 public function lower($x)
 {
 return new Expr\Func('LOWER', [$x]);
 }
 public function upper($x)
 {
 return new Expr\Func('UPPER', [$x]);
 }
 public function length($x)
 {
 return new Expr\Func('LENGTH', [$x]);
 }
 public function literal($literal)
 {
 return new Expr\Literal($this->quoteLiteral($literal));
 }
 private function quoteLiteral($literal) : string
 {
 if (is_numeric($literal) && !is_string($literal)) {
 return (string) $literal;
 } elseif (is_bool($literal)) {
 return $literal ? 'true' : 'false';
 }
 return "'" . str_replace("'", "''", $literal) . "'";
 }
 public function between($val, $x, $y)
 {
 return $val . ' BETWEEN ' . $x . ' AND ' . $y;
 }
 public function trim($x)
 {
 return new Expr\Func('TRIM', $x);
 }
 public function isMemberOf($x, $y)
 {
 return new Expr\Comparison($x, 'MEMBER OF', $y);
 }
 public function isInstanceOf($x, $y)
 {
 return new Expr\Comparison($x, 'INSTANCE OF', $y);
 }
}
