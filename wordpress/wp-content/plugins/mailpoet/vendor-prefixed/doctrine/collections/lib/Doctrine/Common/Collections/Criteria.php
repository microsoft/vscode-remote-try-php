<?php
namespace MailPoetVendor\Doctrine\Common\Collections;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\Collections\Expr\CompositeExpression;
use MailPoetVendor\Doctrine\Common\Collections\Expr\Expression;
use function array_map;
use function strtoupper;
class Criteria
{
 public const ASC = 'ASC';
 public const DESC = 'DESC';
 private static $expressionBuilder;
 private $expression;
 private $orderings = [];
 private $firstResult;
 private $maxResults;
 public static function create()
 {
 return new static();
 }
 public static function expr()
 {
 if (self::$expressionBuilder === null) {
 self::$expressionBuilder = new ExpressionBuilder();
 }
 return self::$expressionBuilder;
 }
 public function __construct(?Expression $expression = null, ?array $orderings = null, $firstResult = null, $maxResults = null)
 {
 $this->expression = $expression;
 $this->setFirstResult($firstResult);
 $this->setMaxResults($maxResults);
 if ($orderings === null) {
 return;
 }
 $this->orderBy($orderings);
 }
 public function where(Expression $expression)
 {
 $this->expression = $expression;
 return $this;
 }
 public function andWhere(Expression $expression)
 {
 if ($this->expression === null) {
 return $this->where($expression);
 }
 $this->expression = new CompositeExpression(CompositeExpression::TYPE_AND, [$this->expression, $expression]);
 return $this;
 }
 public function orWhere(Expression $expression)
 {
 if ($this->expression === null) {
 return $this->where($expression);
 }
 $this->expression = new CompositeExpression(CompositeExpression::TYPE_OR, [$this->expression, $expression]);
 return $this;
 }
 public function getWhereExpression()
 {
 return $this->expression;
 }
 public function getOrderings()
 {
 return $this->orderings;
 }
 public function orderBy(array $orderings)
 {
 $this->orderings = array_map(static function (string $ordering) : string {
 return strtoupper($ordering) === Criteria::ASC ? Criteria::ASC : Criteria::DESC;
 }, $orderings);
 return $this;
 }
 public function getFirstResult()
 {
 return $this->firstResult;
 }
 public function setFirstResult($firstResult)
 {
 $this->firstResult = $firstResult;
 return $this;
 }
 public function getMaxResults()
 {
 return $this->maxResults;
 }
 public function setMaxResults($maxResults)
 {
 $this->maxResults = $maxResults;
 return $this;
 }
}
