<?php
namespace MailPoetVendor\Twig\TokenParser;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Error\SyntaxError;
use MailPoetVendor\Twig\Node\AutoEscapeNode;
use MailPoetVendor\Twig\Node\Expression\ConstantExpression;
use MailPoetVendor\Twig\Node\Node;
use MailPoetVendor\Twig\Token;
final class AutoEscapeTokenParser extends AbstractTokenParser
{
 public function parse(Token $token) : Node
 {
 $lineno = $token->getLine();
 $stream = $this->parser->getStream();
 if ($stream->test(
 3
 )) {
 $value = 'html';
 } else {
 $expr = $this->parser->getExpressionParser()->parseExpression();
 if (!$expr instanceof ConstantExpression) {
 throw new SyntaxError('An escaping strategy must be a string or false.', $stream->getCurrent()->getLine(), $stream->getSourceContext());
 }
 $value = $expr->getAttribute('value');
 }
 $stream->expect(
 3
 );
 $body = $this->parser->subparse([$this, 'decideBlockEnd'], \true);
 $stream->expect(
 3
 );
 return new AutoEscapeNode($value, $body, $lineno, $this->getTag());
 }
 public function decideBlockEnd(Token $token) : bool
 {
 return $token->test('endautoescape');
 }
 public function getTag() : string
 {
 return 'autoescape';
 }
}
