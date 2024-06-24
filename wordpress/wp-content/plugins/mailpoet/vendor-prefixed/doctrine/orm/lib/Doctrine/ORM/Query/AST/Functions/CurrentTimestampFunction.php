<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST\Functions;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\Query\Lexer;
use MailPoetVendor\Doctrine\ORM\Query\Parser;
use MailPoetVendor\Doctrine\ORM\Query\SqlWalker;
class CurrentTimestampFunction extends FunctionNode
{
 public function getSql(SqlWalker $sqlWalker)
 {
 return $sqlWalker->getConnection()->getDatabasePlatform()->getCurrentTimestampSQL();
 }
 public function parse(Parser $parser)
 {
 $parser->match(Lexer::T_IDENTIFIER);
 $parser->match(Lexer::T_OPEN_PARENTHESIS);
 $parser->match(Lexer::T_CLOSE_PARENTHESIS);
 }
}
