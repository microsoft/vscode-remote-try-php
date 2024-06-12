<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Query\AST\Functions;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Types\Type;
use MailPoetVendor\Doctrine\DBAL\Types\Types;
use MailPoetVendor\Doctrine\ORM\Query\AST\Node;
use MailPoetVendor\Doctrine\ORM\Query\AST\TypedExpression;
use MailPoetVendor\Doctrine\ORM\Query\Lexer;
use MailPoetVendor\Doctrine\ORM\Query\Parser;
use MailPoetVendor\Doctrine\ORM\Query\SqlWalker;
class LengthFunction extends FunctionNode implements TypedExpression
{
 public $stringPrimary;
 public function getSql(SqlWalker $sqlWalker)
 {
 return $sqlWalker->getConnection()->getDatabasePlatform()->getLengthExpression($sqlWalker->walkSimpleArithmeticExpression($this->stringPrimary));
 }
 public function parse(Parser $parser)
 {
 $parser->match(Lexer::T_IDENTIFIER);
 $parser->match(Lexer::T_OPEN_PARENTHESIS);
 $this->stringPrimary = $parser->StringPrimary();
 $parser->match(Lexer::T_CLOSE_PARENTHESIS);
 }
 public function getReturnType() : Type
 {
 return Type::getType(Types::INTEGER);
 }
}
