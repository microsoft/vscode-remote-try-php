<?php
namespace MailPoetVendor\Twig\TokenParser;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Error\SyntaxError;
use MailPoetVendor\Twig\Node\IfNode;
use MailPoetVendor\Twig\Node\Node;
use MailPoetVendor\Twig\Token;
final class IfTokenParser extends AbstractTokenParser
{
 public function parse(Token $token) : Node
 {
 $lineno = $token->getLine();
 $expr = $this->parser->getExpressionParser()->parseExpression();
 $stream = $this->parser->getStream();
 $stream->expect(
 3
 );
 $body = $this->parser->subparse([$this, 'decideIfFork']);
 $tests = [$expr, $body];
 $else = null;
 $end = \false;
 while (!$end) {
 switch ($stream->next()->getValue()) {
 case 'else':
 $stream->expect(
 3
 );
 $else = $this->parser->subparse([$this, 'decideIfEnd']);
 break;
 case 'elseif':
 $expr = $this->parser->getExpressionParser()->parseExpression();
 $stream->expect(
 3
 );
 $body = $this->parser->subparse([$this, 'decideIfFork']);
 $tests[] = $expr;
 $tests[] = $body;
 break;
 case 'endif':
 $end = \true;
 break;
 default:
 throw new SyntaxError(\sprintf('Unexpected end of template. Twig was looking for the following tags "else", "elseif", or "endif" to close the "if" block started at line %d).', $lineno), $stream->getCurrent()->getLine(), $stream->getSourceContext());
 }
 }
 $stream->expect(
 3
 );
 return new IfNode(new Node($tests), $else, $lineno, $this->getTag());
 }
 public function decideIfFork(Token $token) : bool
 {
 return $token->test(['elseif', 'else', 'endif']);
 }
 public function decideIfEnd(Token $token) : bool
 {
 return $token->test(['endif']);
 }
 public function getTag() : string
 {
 return 'if';
 }
}
