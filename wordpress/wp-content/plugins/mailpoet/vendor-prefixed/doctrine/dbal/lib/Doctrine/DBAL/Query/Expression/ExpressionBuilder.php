<?php
namespace MailPoetVendor\Doctrine\DBAL\Query\Expression;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Connection;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use function func_get_arg;
use function func_get_args;
use function func_num_args;
use function implode;
use function sprintf;
class ExpressionBuilder
{
 public const EQ = '=';
 public const NEQ = '<>';
 public const LT = '<';
 public const LTE = '<=';
 public const GT = '>';
 public const GTE = '>=';
 private $connection;
 public function __construct(Connection $connection)
 {
 $this->connection = $connection;
 }
 public function and($expression, ...$expressions) : CompositeExpression
 {
 return CompositeExpression::and($expression, ...$expressions);
 }
 public function or($expression, ...$expressions) : CompositeExpression
 {
 return CompositeExpression::or($expression, ...$expressions);
 }
 public function andX($x = null)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/3851', 'ExpressionBuilder::andX() is deprecated, use ExpressionBuilder::and() instead.');
 return new CompositeExpression(CompositeExpression::TYPE_AND, func_get_args());
 }
 public function orX($x = null)
 {
 Deprecation::trigger('doctrine/dbal', 'https://github.com/doctrine/dbal/pull/3851', 'ExpressionBuilder::orX() is deprecated, use ExpressionBuilder::or() instead.');
 return new CompositeExpression(CompositeExpression::TYPE_OR, func_get_args());
 }
 public function comparison($x, $operator, $y)
 {
 return $x . ' ' . $operator . ' ' . $y;
 }
 public function eq($x, $y)
 {
 return $this->comparison($x, self::EQ, $y);
 }
 public function neq($x, $y)
 {
 return $this->comparison($x, self::NEQ, $y);
 }
 public function lt($x, $y)
 {
 return $this->comparison($x, self::LT, $y);
 }
 public function lte($x, $y)
 {
 return $this->comparison($x, self::LTE, $y);
 }
 public function gt($x, $y)
 {
 return $this->comparison($x, self::GT, $y);
 }
 public function gte($x, $y)
 {
 return $this->comparison($x, self::GTE, $y);
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
 return $this->comparison($x, 'LIKE', $y) . (func_num_args() >= 3 ? sprintf(' ESCAPE %s', func_get_arg(2)) : '');
 }
 public function notLike($x, $y)
 {
 return $this->comparison($x, 'NOT LIKE', $y) . (func_num_args() >= 3 ? sprintf(' ESCAPE %s', func_get_arg(2)) : '');
 }
 public function in($x, $y)
 {
 return $this->comparison($x, 'IN', '(' . implode(', ', (array) $y) . ')');
 }
 public function notIn($x, $y)
 {
 return $this->comparison($x, 'NOT IN', '(' . implode(', ', (array) $y) . ')');
 }
 public function literal($input, $type = null)
 {
 return $this->connection->quote($input, $type);
 }
}
