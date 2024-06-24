<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST\Functions;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Query\AST\Node;
use MailPoetVendor\Doctrine\ORM\Query\Lexer;
use MailPoetVendor\Doctrine\ORM\Query\Parser;
use MailPoetVendor\Doctrine\ORM\Query\SqlWalker;
class DateDiffFunction extends FunctionNode
{
 public $date1;
 public $date2;
 public function getSql(SqlWalker $sqlWalker)
 {
 return $sqlWalker->getConnection()->getDatabasePlatform()->getDateDiffExpression($this->date1->dispatch($sqlWalker), $this->date2->dispatch($sqlWalker));
 }
 public function parse(Parser $parser)
 {
 $parser->match(Lexer::T_IDENTIFIER);
 $parser->match(Lexer::T_OPEN_PARENTHESIS);
 $this->date1 = $parser->ArithmeticPrimary();
 $parser->match(Lexer::T_COMMA);
 $this->date2 = $parser->ArithmeticPrimary();
 $parser->match(Lexer::T_CLOSE_PARENTHESIS);
 }
}
