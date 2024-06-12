<?php
namespace MailPoetVendor\Twig\TokenParser;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Node\Expression\AssignNameExpression;
use MailPoetVendor\Twig\Node\ImportNode;
use MailPoetVendor\Twig\Node\Node;
use MailPoetVendor\Twig\Token;
final class ImportTokenParser extends AbstractTokenParser
{
 public function parse(Token $token) : Node
 {
 $macro = $this->parser->getExpressionParser()->parseExpression();
 $this->parser->getStream()->expect(
 5,
 'as'
 );
 $var = new AssignNameExpression($this->parser->getStream()->expect(
 5
 )->getValue(), $token->getLine());
 $this->parser->getStream()->expect(
 3
 );
 $this->parser->addImportedSymbol('template', $var->getAttribute('name'));
 return new ImportNode($macro, $var, $token->getLine(), $this->getTag(), $this->parser->isMainScope());
 }
 public function getTag() : string
 {
 return 'import';
 }
}
