<?php
namespace MailPoetVendor\Twig\TokenParser;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Error\SyntaxError;
use MailPoetVendor\Twig\Node\BlockNode;
use MailPoetVendor\Twig\Node\BlockReferenceNode;
use MailPoetVendor\Twig\Node\Node;
use MailPoetVendor\Twig\Node\PrintNode;
use MailPoetVendor\Twig\Token;
final class BlockTokenParser extends AbstractTokenParser
{
 public function parse(Token $token) : Node
 {
 $lineno = $token->getLine();
 $stream = $this->parser->getStream();
 $name = $stream->expect(
 5
 )->getValue();
 if ($this->parser->hasBlock($name)) {
 throw new SyntaxError(\sprintf("The block '%s' has already been defined line %d.", $name, $this->parser->getBlock($name)->getTemplateLine()), $stream->getCurrent()->getLine(), $stream->getSourceContext());
 }
 $this->parser->setBlock($name, $block = new BlockNode($name, new Node([]), $lineno));
 $this->parser->pushLocalScope();
 $this->parser->pushBlockStack($name);
 if ($stream->nextIf(
 3
 )) {
 $body = $this->parser->subparse([$this, 'decideBlockEnd'], \true);
 if ($token = $stream->nextIf(
 5
 )) {
 $value = $token->getValue();
 if ($value != $name) {
 throw new SyntaxError(\sprintf('Expected endblock for block "%s" (but "%s" given).', $name, $value), $stream->getCurrent()->getLine(), $stream->getSourceContext());
 }
 }
 } else {
 $body = new Node([new PrintNode($this->parser->getExpressionParser()->parseExpression(), $lineno)]);
 }
 $stream->expect(
 3
 );
 $block->setNode('body', $body);
 $this->parser->popBlockStack();
 $this->parser->popLocalScope();
 return new BlockReferenceNode($name, $lineno, $this->getTag());
 }
 public function decideBlockEnd(Token $token) : bool
 {
 return $token->test('endblock');
 }
 public function getTag() : string
 {
 return 'block';
 }
}
