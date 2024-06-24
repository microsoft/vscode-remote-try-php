<?php
namespace MailPoetVendor\Symfony\Component\CssSelector\Parser\Handler;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\CssSelector\Parser\Reader;
use MailPoetVendor\Symfony\Component\CssSelector\Parser\Token;
use MailPoetVendor\Symfony\Component\CssSelector\Parser\TokenStream;
class WhitespaceHandler implements HandlerInterface
{
 public function handle(Reader $reader, TokenStream $stream) : bool
 {
 $match = $reader->findPattern('~^[ \\t\\r\\n\\f]+~');
 if (\false === $match) {
 return \false;
 }
 $stream->push(new Token(Token::TYPE_WHITESPACE, $match[0], $reader->getPosition()));
 $reader->moveForward(\strlen($match[0]));
 return \true;
 }
}
