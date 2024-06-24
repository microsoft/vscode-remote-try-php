<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST\Functions;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Query\AST\Node;
use MailPoetVendor\Doctrine\ORM\Query\Lexer;
use MailPoetVendor\Doctrine\ORM\Query\Parser;
use MailPoetVendor\Doctrine\ORM\Query\SqlWalker;
class ConcatFunction extends FunctionNode
{
 public $firstStringPrimary;
 public $secondStringPrimary;
 public $concatExpressions = [];
 public function getSql(SqlWalker $sqlWalker)
 {
 $platform = $sqlWalker->getConnection()->getDatabasePlatform();
 $args = [];
 foreach ($this->concatExpressions as $expression) {
 $args[] = $sqlWalker->walkStringPrimary($expression);
 }
 return $platform->getConcatExpression(...$args);
 }
 public function parse(Parser $parser)
 {
 $parser->match(Lexer::T_IDENTIFIER);
 $parser->match(Lexer::T_OPEN_PARENTHESIS);
 $this->firstStringPrimary = $parser->StringPrimary();
 $this->concatExpressions[] = $this->firstStringPrimary;
 $parser->match(Lexer::T_COMMA);
 $this->secondStringPrimary = $parser->StringPrimary();
 $this->concatExpressions[] = $this->secondStringPrimary;
 while ($parser->getLexer()->isNextToken(Lexer::T_COMMA)) {
 $parser->match(Lexer::T_COMMA);
 $this->concatExpressions[] = $parser->StringPrimary();
 }
 $parser->match(Lexer::T_CLOSE_PARENTHESIS);
 }
}
