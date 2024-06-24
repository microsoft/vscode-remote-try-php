<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST\Functions;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Query\AST\SimpleArithmeticExpression;
use MailPoetVendor\Doctrine\ORM\Query\Lexer;
use MailPoetVendor\Doctrine\ORM\Query\Parser;
use MailPoetVendor\Doctrine\ORM\Query\SqlWalker;
class ModFunction extends FunctionNode
{
 public $firstSimpleArithmeticExpression;
 public $secondSimpleArithmeticExpression;
 public function getSql(SqlWalker $sqlWalker)
 {
 return $sqlWalker->getConnection()->getDatabasePlatform()->getModExpression($sqlWalker->walkSimpleArithmeticExpression($this->firstSimpleArithmeticExpression), $sqlWalker->walkSimpleArithmeticExpression($this->secondSimpleArithmeticExpression));
 }
 public function parse(Parser $parser)
 {
 $parser->match(Lexer::T_IDENTIFIER);
 $parser->match(Lexer::T_OPEN_PARENTHESIS);
 $this->firstSimpleArithmeticExpression = $parser->SimpleArithmeticExpression();
 $parser->match(Lexer::T_COMMA);
 $this->secondSimpleArithmeticExpression = $parser->SimpleArithmeticExpression();
 $parser->match(Lexer::T_CLOSE_PARENTHESIS);
 }
}
