<?php
namespace MailPoetVendor\Twig\TokenParser;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Node\EmbedNode;
use MailPoetVendor\Twig\Node\Expression\ConstantExpression;
use MailPoetVendor\Twig\Node\Expression\NameExpression;
use MailPoetVendor\Twig\Node\Node;
use MailPoetVendor\Twig\Token;
final class EmbedTokenParser extends IncludeTokenParser
{
 public function parse(Token $token) : Node
 {
 $stream = $this->parser->getStream();
 $parent = $this->parser->getExpressionParser()->parseExpression();
 list($variables, $only, $ignoreMissing) = $this->parseArguments();
 $parentToken = $fakeParentToken = new Token(
 7,
 '__parent__',
 $token->getLine()
 );
 if ($parent instanceof ConstantExpression) {
 $parentToken = new Token(
 7,
 $parent->getAttribute('value'),
 $token->getLine()
 );
 } elseif ($parent instanceof NameExpression) {
 $parentToken = new Token(
 5,
 $parent->getAttribute('name'),
 $token->getLine()
 );
 }
 // inject a fake parent to make the parent() function work
 $stream->injectTokens([new Token(
 1,
 '',
 $token->getLine()
 ), new Token(
 5,
 'extends',
 $token->getLine()
 ), $parentToken, new Token(
 3,
 '',
 $token->getLine()
 )]);
 $module = $this->parser->parse($stream, [$this, 'decideBlockEnd'], \true);
 // override the parent with the correct one
 if ($fakeParentToken === $parentToken) {
 $module->setNode('parent', $parent);
 }
 $this->parser->embedTemplate($module);
 $stream->expect(
 3
 );
 return new EmbedNode($module->getTemplateName(), $module->getAttribute('index'), $variables, $only, $ignoreMissing, $token->getLine(), $this->getTag());
 }
 public function decideBlockEnd(Token $token) : bool
 {
 return $token->test('endembed');
 }
 public function getTag() : string
 {
 return 'embed';
 }
}
