<?php
namespace MailPoetVendor\Twig\TokenParser;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Node\Node;
use MailPoetVendor\Twig\Node\WithNode;
use MailPoetVendor\Twig\Token;
final class WithTokenParser extends AbstractTokenParser
{
 public function parse(Token $token) : Node
 {
 $stream = $this->parser->getStream();
 $variables = null;
 $only = \false;
 if (!$stream->test(
 3
 )) {
 $variables = $this->parser->getExpressionParser()->parseExpression();
 $only = (bool) $stream->nextIf(
 5,
 'only'
 );
 }
 $stream->expect(
 3
 );
 $body = $this->parser->subparse([$this, 'decideWithEnd'], \true);
 $stream->expect(
 3
 );
 return new WithNode($body, $variables, $only, $token->getLine(), $this->getTag());
 }
 public function decideWithEnd(Token $token) : bool
 {
 return $token->test('endwith');
 }
 public function getTag() : string
 {
 return 'with';
 }
}
