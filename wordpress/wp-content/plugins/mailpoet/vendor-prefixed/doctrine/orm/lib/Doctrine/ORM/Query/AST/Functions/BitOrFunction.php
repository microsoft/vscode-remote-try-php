<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST\Functions;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Query\AST\Node;
use MailPoetVendor\Doctrine\ORM\Query\Lexer;
use MailPoetVendor\Doctrine\ORM\Query\Parser;
use MailPoetVendor\Doctrine\ORM\Query\SqlWalker;
class BitOrFunction extends FunctionNode
{
 public $firstArithmetic;
 public $secondArithmetic;
 public function getSql(SqlWalker $sqlWalker)
 {
 $platform = $sqlWalker->getConnection()->getDatabasePlatform();
 return $platform->getBitOrComparisonExpression($this->firstArithmetic->dispatch($sqlWalker), $this->secondArithmetic->dispatch($sqlWalker));
 }
 public function parse(Parser $parser)
 {
 $parser->match(Lexer::T_IDENTIFIER);
 $parser->match(Lexer::T_OPEN_PARENTHESIS);
 $this->firstArithmetic = $parser->ArithmeticPrimary();
 $parser->match(Lexer::T_COMMA);
 $this->secondArithmetic = $parser->ArithmeticPrimary();
 $parser->match(Lexer::T_CLOSE_PARENTHESIS);
 }
}
