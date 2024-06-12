<?php
namespace MailPoetVendor\Symfony\Component\CssSelector\Parser\Handler;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\CssSelector\Parser\Reader;
use MailPoetVendor\Symfony\Component\CssSelector\Parser\Token;
use MailPoetVendor\Symfony\Component\CssSelector\Parser\Tokenizer\TokenizerEscaping;
use MailPoetVendor\Symfony\Component\CssSelector\Parser\Tokenizer\TokenizerPatterns;
use MailPoetVendor\Symfony\Component\CssSelector\Parser\TokenStream;
class HashHandler implements HandlerInterface
{
 private $patterns;
 private $escaping;
 public function __construct(TokenizerPatterns $patterns, TokenizerEscaping $escaping)
 {
 $this->patterns = $patterns;
 $this->escaping = $escaping;
 }
 public function handle(Reader $reader, TokenStream $stream) : bool
 {
 $match = $reader->findPattern($this->patterns->getHashPattern());
 if (!$match) {
 return \false;
 }
 $value = $this->escaping->escapeUnicode($match[1]);
 $stream->push(new Token(Token::TYPE_HASH, $value, $reader->getPosition()));
 $reader->moveForward(\strlen($match[0]));
 return \true;
 }
}
