<?php
namespace MailPoetVendor\Symfony\Component\CssSelector\Parser\Shortcut;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\CssSelector\Node\ElementNode;
use MailPoetVendor\Symfony\Component\CssSelector\Node\SelectorNode;
use MailPoetVendor\Symfony\Component\CssSelector\Parser\ParserInterface;
class ElementParser implements ParserInterface
{
 public function parse(string $source) : array
 {
 // Matches an optional namespace, required element or `*`
 // $source = 'testns|testel';
 // $matches = array (size=3)
 // 0 => string 'testns|testel' (length=13)
 // 1 => string 'testns' (length=6)
 // 2 => string 'testel' (length=6)
 if (\preg_match('/^(?:([a-z]++)\\|)?([\\w-]++|\\*)$/i', \trim($source), $matches)) {
 return [new SelectorNode(new ElementNode($matches[1] ?: null, $matches[2]))];
 }
 return [];
 }
}
