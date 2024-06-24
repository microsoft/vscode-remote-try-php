<?php
namespace MailPoetVendor\Twig;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Error\SyntaxError;
final class TokenStream
{
 private $tokens;
 private $current = 0;
 private $source;
 public function __construct(array $tokens, ?Source $source = null)
 {
 $this->tokens = $tokens;
 $this->source = $source ?: new Source('', '');
 }
 public function __toString()
 {
 return \implode("\n", $this->tokens);
 }
 public function injectTokens(array $tokens)
 {
 $this->tokens = \array_merge(\array_slice($this->tokens, 0, $this->current), $tokens, \array_slice($this->tokens, $this->current));
 }
 public function next() : Token
 {
 if (!isset($this->tokens[++$this->current])) {
 throw new SyntaxError('Unexpected end of template.', $this->tokens[$this->current - 1]->getLine(), $this->source);
 }
 return $this->tokens[$this->current - 1];
 }
 public function nextIf($primary, $secondary = null)
 {
 return $this->tokens[$this->current]->test($primary, $secondary) ? $this->next() : null;
 }
 public function expect($type, $value = null, ?string $message = null) : Token
 {
 $token = $this->tokens[$this->current];
 if (!$token->test($type, $value)) {
 $line = $token->getLine();
 throw new SyntaxError(\sprintf('%sUnexpected token "%s"%s ("%s" expected%s).', $message ? $message . '. ' : '', Token::typeToEnglish($token->getType()), $token->getValue() ? \sprintf(' of value "%s"', $token->getValue()) : '', Token::typeToEnglish($type), $value ? \sprintf(' with value "%s"', $value) : ''), $line, $this->source);
 }
 $this->next();
 return $token;
 }
 public function look(int $number = 1) : Token
 {
 if (!isset($this->tokens[$this->current + $number])) {
 throw new SyntaxError('Unexpected end of template.', $this->tokens[$this->current + $number - 1]->getLine(), $this->source);
 }
 return $this->tokens[$this->current + $number];
 }
 public function test($primary, $secondary = null) : bool
 {
 return $this->tokens[$this->current]->test($primary, $secondary);
 }
 public function isEOF() : bool
 {
 return -1 === $this->tokens[$this->current]->getType();
 }
 public function getCurrent() : Token
 {
 return $this->tokens[$this->current];
 }
 public function getSourceContext() : Source
 {
 return $this->source;
 }
}
