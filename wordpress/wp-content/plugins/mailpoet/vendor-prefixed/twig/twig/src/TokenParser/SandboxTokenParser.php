<?php
namespace MailPoetVendor\Twig\TokenParser;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Error\SyntaxError;
use MailPoetVendor\Twig\Node\IncludeNode;
use MailPoetVendor\Twig\Node\Node;
use MailPoetVendor\Twig\Node\SandboxNode;
use MailPoetVendor\Twig\Node\TextNode;
use MailPoetVendor\Twig\Token;
final class SandboxTokenParser extends AbstractTokenParser
{
 public function parse(Token $token) : Node
 {
 $stream = $this->parser->getStream();
 $stream->expect(
 3
 );
 $body = $this->parser->subparse([$this, 'decideBlockEnd'], \true);
 $stream->expect(
 3
 );
 // in a sandbox tag, only include tags are allowed
 if (!$body instanceof IncludeNode) {
 foreach ($body as $node) {
 if ($node instanceof TextNode && \ctype_space($node->getAttribute('data'))) {
 continue;
 }
 if (!$node instanceof IncludeNode) {
 throw new SyntaxError('Only "include" tags are allowed within a "sandbox" section.', $node->getTemplateLine(), $stream->getSourceContext());
 }
 }
 }
 return new SandboxNode($body, $token->getLine(), $this->getTag());
 }
 public function decideBlockEnd(Token $token) : bool
 {
 return $token->test('endsandbox');
 }
 public function getTag() : string
 {
 return 'sandbox';
 }
}
