<?php
namespace MailPoetVendor\Twig\TokenParser;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Node\FlushNode;
use MailPoetVendor\Twig\Node\Node;
use MailPoetVendor\Twig\Token;
final class FlushTokenParser extends AbstractTokenParser
{
 public function parse(Token $token) : Node
 {
 $this->parser->getStream()->expect(
 3
 );
 return new FlushNode($token->getLine(), $this->getTag());
 }
 public function getTag() : string
 {
 return 'flush';
 }
}
