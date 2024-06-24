<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Tools\Pagination;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Query\AST\Functions\FunctionNode;
use MailPoetVendor\Doctrine\ORM\Query\AST\OrderByClause;
use MailPoetVendor\Doctrine\ORM\Query\Parser;
use MailPoetVendor\Doctrine\ORM\Query\SqlWalker;
use MailPoetVendor\Doctrine\ORM\Tools\Pagination\Exception\RowNumberOverFunctionNotEnabled;
use function trim;
class RowNumberOverFunction extends FunctionNode
{
 public $orderByClause;
 public function getSql(SqlWalker $sqlWalker)
 {
 return 'ROW_NUMBER() OVER(' . trim($sqlWalker->walkOrderByClause($this->orderByClause)) . ')';
 }
 public function parse(Parser $parser)
 {
 throw RowNumberOverFunctionNotEnabled::create();
 }
}
