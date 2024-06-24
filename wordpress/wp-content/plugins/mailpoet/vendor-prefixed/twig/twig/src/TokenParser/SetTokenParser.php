<?php
namespace MailPoetVendor\Twig\TokenParser;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Error\SyntaxError;
use MailPoetVendor\Twig\Node\Node;
use MailPoetVendor\Twig\Node\SetNode;
use MailPoetVendor\Twig\Token;
final class SetTokenParser extends AbstractTokenParser
{
 public function parse(Token $token) : Node
 {
 $lineno = $token->getLine();
 $stream = $this->parser->getStream();
 $names = $this->parser->getExpressionParser()->parseAssignmentExpression();
 $capture = \false;
 if ($stream->nextIf(
 8,
 '='
 )) {
 $values = $this->parser->getExpressionParser()->parseMultitargetExpression();
 $stream->expect(
 3
 );
 if (\count($names) !== \count($values)) {
 throw new SyntaxError('When using set, you must have the same number of variables and assignments.', $stream->getCurrent()->getLine(), $stream->getSourceContext());
 }
 } else {
 $capture = \true;
 if (\count($names) > 1) {
 throw new SyntaxError('When using set with a block, you cannot have a multi-target.', $stream->getCurrent()->getLine(), $stream->getSourceContext());
 }
 $stream->expect(
 3
 );
 $values = $this->parser->subparse([$this, 'decideBlockEnd'], \true);
 $stream->expect(
 3
 );
 }
 return new SetNode($capture, $names, $values, $lineno, $this->getTag());
 }
 public function decideBlockEnd(Token $token) : bool
 {
 return $token->test('endset');
 }
 public function getTag() : string
 {
 return 'set';
 }
}
