<?php
namespace MailPoetVendor\Twig\TokenParser;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Node\DeprecatedNode;
use MailPoetVendor\Twig\Node\Node;
use MailPoetVendor\Twig\Token;
final class DeprecatedTokenParser extends AbstractTokenParser
{
 public function parse(Token $token) : Node
 {
 $expr = $this->parser->getExpressionParser()->parseExpression();
 $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);
 return new DeprecatedNode($expr, $token->getLine(), $this->getTag());
 }
 public function getTag() : string
 {
 return 'deprecated';
 }
}
