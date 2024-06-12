<?php
namespace MailPoetVendor\Twig\TokenParser;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Node\Expression\AssignNameExpression;
use MailPoetVendor\Twig\Node\ImportNode;
use MailPoetVendor\Twig\Node\Node;
use MailPoetVendor\Twig\Token;
final class FromTokenParser extends AbstractTokenParser
{
 public function parse(Token $token) : Node
 {
 $macro = $this->parser->getExpressionParser()->parseExpression();
 $stream = $this->parser->getStream();
 $stream->expect(
 5,
 'import'
 );
 $targets = [];
 do {
 $name = $stream->expect(
 5
 )->getValue();
 $alias = $name;
 if ($stream->nextIf('as')) {
 $alias = $stream->expect(
 5
 )->getValue();
 }
 $targets[$name] = $alias;
 if (!$stream->nextIf(
 9,
 ','
 )) {
 break;
 }
 } while (\true);
 $stream->expect(
 3
 );
 $var = new AssignNameExpression($this->parser->getVarName(), $token->getLine());
 $node = new ImportNode($macro, $var, $token->getLine(), $this->getTag(), $this->parser->isMainScope());
 foreach ($targets as $name => $alias) {
 $this->parser->addImportedSymbol('function', $alias, 'macro_' . $name, $var);
 }
 return $node;
 }
 public function getTag() : string
 {
 return 'from';
 }
}
