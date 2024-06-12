<?php
namespace MailPoetVendor\Twig\TokenParser;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Error\SyntaxError;
use MailPoetVendor\Twig\Node\Expression\ConstantExpression;
use MailPoetVendor\Twig\Node\Node;
use MailPoetVendor\Twig\Token;
final class UseTokenParser extends AbstractTokenParser
{
 public function parse(Token $token) : Node
 {
 $template = $this->parser->getExpressionParser()->parseExpression();
 $stream = $this->parser->getStream();
 if (!$template instanceof ConstantExpression) {
 throw new SyntaxError('The template references in a "use" statement must be a string.', $stream->getCurrent()->getLine(), $stream->getSourceContext());
 }
 $targets = [];
 if ($stream->nextIf('with')) {
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
 $targets[$name] = new ConstantExpression($alias, -1);
 if (!$stream->nextIf(
 9,
 ','
 )) {
 break;
 }
 } while (\true);
 }
 $stream->expect(
 3
 );
 $this->parser->addTrait(new Node(['template' => $template, 'targets' => new Node($targets)]));
 return new Node();
 }
 public function getTag() : string
 {
 return 'use';
 }
}
