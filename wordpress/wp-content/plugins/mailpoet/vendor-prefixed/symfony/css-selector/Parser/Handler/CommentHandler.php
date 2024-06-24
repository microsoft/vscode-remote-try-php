<?php
namespace MailPoetVendor\Symfony\Component\CssSelector\Parser\Handler;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\CssSelector\Parser\Reader;
use MailPoetVendor\Symfony\Component\CssSelector\Parser\TokenStream;
class CommentHandler implements HandlerInterface
{
 public function handle(Reader $reader, TokenStream $stream) : bool
 {
 if ('/*' !== $reader->getSubstring(2)) {
 return \false;
 }
 $offset = $reader->getOffset('*/');
 if (\false === $offset) {
 $reader->moveToEnd();
 } else {
 $reader->moveForward($offset + 2);
 }
 return \true;
 }
}
