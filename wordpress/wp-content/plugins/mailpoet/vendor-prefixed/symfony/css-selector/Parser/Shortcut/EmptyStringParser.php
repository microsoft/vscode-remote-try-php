<?php
namespace MailPoetVendor\Symfony\Component\CssSelector\Parser\Shortcut;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\CssSelector\Node\ElementNode;
use MailPoetVendor\Symfony\Component\CssSelector\Node\SelectorNode;
use MailPoetVendor\Symfony\Component\CssSelector\Parser\ParserInterface;
class EmptyStringParser implements ParserInterface
{
 public function parse(string $source) : array
 {
 // Matches an empty string
 if ('' == $source) {
 return [new SelectorNode(new ElementNode(null, '*'))];
 }
 return [];
 }
}
