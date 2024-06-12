<?php
namespace MailPoetVendor\Symfony\Component\CssSelector\Parser\Shortcut;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\CssSelector\Node\ClassNode;
use MailPoetVendor\Symfony\Component\CssSelector\Node\ElementNode;
use MailPoetVendor\Symfony\Component\CssSelector\Node\SelectorNode;
use MailPoetVendor\Symfony\Component\CssSelector\Parser\ParserInterface;
class ClassParser implements ParserInterface
{
 public function parse(string $source) : array
 {
 // Matches an optional namespace, optional element, and required class
 // $source = 'test|input.ab6bd_field';
 // $matches = array (size=4)
 // 0 => string 'test|input.ab6bd_field' (length=22)
 // 1 => string 'test' (length=4)
 // 2 => string 'input' (length=5)
 // 3 => string 'ab6bd_field' (length=11)
 if (\preg_match('/^(?:([a-z]++)\\|)?+([\\w-]++|\\*)?+\\.([\\w-]++)$/i', \trim($source), $matches)) {
 return [new SelectorNode(new ClassNode(new ElementNode($matches[1] ?: null, $matches[2] ?: null), $matches[3]))];
 }
 return [];
 }
}
