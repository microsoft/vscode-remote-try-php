<?php
namespace MailPoetVendor\Twig\TokenParser;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Node\IncludeNode;
use MailPoetVendor\Twig\Node\Node;
use MailPoetVendor\Twig\Token;
class IncludeTokenParser extends AbstractTokenParser
{
 public function parse(Token $token) : Node
 {
 $expr = $this->parser->getExpressionParser()->parseExpression();
 list($variables, $only, $ignoreMissing) = $this->parseArguments();
 return new IncludeNode($expr, $variables, $only, $ignoreMissing, $token->getLine(), $this->getTag());
 }
 protected function parseArguments()
 {
 $stream = $this->parser->getStream();
 $ignoreMissing = \false;
 if ($stream->nextIf(
 5,
 'ignore'
 )) {
 $stream->expect(
 5,
 'missing'
 );
 $ignoreMissing = \true;
 }
 $variables = null;
 if ($stream->nextIf(
 5,
 'with'
 )) {
 $variables = $this->parser->getExpressionParser()->parseExpression();
 }
 $only = \false;
 if ($stream->nextIf(
 5,
 'only'
 )) {
 $only = \true;
 }
 $stream->expect(
 3
 );
 return [$variables, $only, $ignoreMissing];
 }
 public function getTag() : string
 {
 return 'include';
 }
}
