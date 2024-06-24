<?php
namespace MailPoetVendor\Twig\TokenParser;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Node\Expression\TempNameExpression;
use MailPoetVendor\Twig\Node\Node;
use MailPoetVendor\Twig\Node\PrintNode;
use MailPoetVendor\Twig\Node\SetNode;
use MailPoetVendor\Twig\Token;
final class ApplyTokenParser extends AbstractTokenParser
{
 public function parse(Token $token) : Node
 {
 $lineno = $token->getLine();
 $name = $this->parser->getVarName();
 $ref = new TempNameExpression($name, $lineno);
 $ref->setAttribute('always_defined', \true);
 $filter = $this->parser->getExpressionParser()->parseFilterExpressionRaw($ref, $this->getTag());
 $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);
 $body = $this->parser->subparse([$this, 'decideApplyEnd'], \true);
 $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);
 return new Node([new SetNode(\true, $ref, $body, $lineno, $this->getTag()), new PrintNode($filter, $lineno, $this->getTag())]);
 }
 public function decideApplyEnd(Token $token) : bool
 {
 return $token->test('endapply');
 }
 public function getTag() : string
 {
 return 'apply';
 }
}
