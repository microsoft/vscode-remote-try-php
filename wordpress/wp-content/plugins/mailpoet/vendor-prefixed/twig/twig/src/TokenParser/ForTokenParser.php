<?php
namespace MailPoetVendor\Twig\TokenParser;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Node\Expression\AssignNameExpression;
use MailPoetVendor\Twig\Node\ForNode;
use MailPoetVendor\Twig\Node\Node;
use MailPoetVendor\Twig\Token;
final class ForTokenParser extends AbstractTokenParser
{
 public function parse(Token $token) : Node
 {
 $lineno = $token->getLine();
 $stream = $this->parser->getStream();
 $targets = $this->parser->getExpressionParser()->parseAssignmentExpression();
 $stream->expect(
 8,
 'in'
 );
 $seq = $this->parser->getExpressionParser()->parseExpression();
 $stream->expect(
 3
 );
 $body = $this->parser->subparse([$this, 'decideForFork']);
 if ('else' == $stream->next()->getValue()) {
 $stream->expect(
 3
 );
 $else = $this->parser->subparse([$this, 'decideForEnd'], \true);
 } else {
 $else = null;
 }
 $stream->expect(
 3
 );
 if (\count($targets) > 1) {
 $keyTarget = $targets->getNode(0);
 $keyTarget = new AssignNameExpression($keyTarget->getAttribute('name'), $keyTarget->getTemplateLine());
 $valueTarget = $targets->getNode(1);
 } else {
 $keyTarget = new AssignNameExpression('_key', $lineno);
 $valueTarget = $targets->getNode(0);
 }
 $valueTarget = new AssignNameExpression($valueTarget->getAttribute('name'), $valueTarget->getTemplateLine());
 return new ForNode($keyTarget, $valueTarget, $seq, null, $body, $else, $lineno, $this->getTag());
 }
 public function decideForFork(Token $token) : bool
 {
 return $token->test(['else', 'endfor']);
 }
 public function decideForEnd(Token $token) : bool
 {
 return $token->test('endfor');
 }
 public function getTag() : string
 {
 return 'for';
 }
}
