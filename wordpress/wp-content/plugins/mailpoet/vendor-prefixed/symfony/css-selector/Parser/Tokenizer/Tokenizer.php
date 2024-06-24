<?php
namespace MailPoetVendor\Symfony\Component\CssSelector\Parser\Tokenizer;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\CssSelector\Parser\Handler;
use MailPoetVendor\Symfony\Component\CssSelector\Parser\Reader;
use MailPoetVendor\Symfony\Component\CssSelector\Parser\Token;
use MailPoetVendor\Symfony\Component\CssSelector\Parser\TokenStream;
class Tokenizer
{
 private $handlers;
 public function __construct()
 {
 $patterns = new TokenizerPatterns();
 $escaping = new TokenizerEscaping($patterns);
 $this->handlers = [new Handler\WhitespaceHandler(), new Handler\IdentifierHandler($patterns, $escaping), new Handler\HashHandler($patterns, $escaping), new Handler\StringHandler($patterns, $escaping), new Handler\NumberHandler($patterns), new Handler\CommentHandler()];
 }
 public function tokenize(Reader $reader) : TokenStream
 {
 $stream = new TokenStream();
 while (!$reader->isEOF()) {
 foreach ($this->handlers as $handler) {
 if ($handler->handle($reader, $stream)) {
 continue 2;
 }
 }
 $stream->push(new Token(Token::TYPE_DELIMITER, $reader->getSubstring(1), $reader->getPosition()));
 $reader->moveForward(1);
 }
 return $stream->push(new Token(Token::TYPE_FILE_END, null, $reader->getPosition()))->freeze();
 }
}
