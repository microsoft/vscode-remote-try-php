<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST\Functions;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Query\AST\SimpleArithmeticExpression;
use MailPoetVendor\Doctrine\ORM\Query\Lexer;
use MailPoetVendor\Doctrine\ORM\Query\Parser;
use MailPoetVendor\Doctrine\ORM\Query\SqlWalker;
use function sprintf;
class SqrtFunction extends FunctionNode
{
 public $simpleArithmeticExpression;
 public function getSql(SqlWalker $sqlWalker)
 {
 return sprintf('SQRT(%s)', $sqlWalker->walkSimpleArithmeticExpression($this->simpleArithmeticExpression));
 }
 public function parse(Parser $parser)
 {
 $parser->match(Lexer::T_IDENTIFIER);
 $parser->match(Lexer::T_OPEN_PARENTHESIS);
 $this->simpleArithmeticExpression = $parser->SimpleArithmeticExpression();
 $parser->match(Lexer::T_CLOSE_PARENTHESIS);
 }
}
