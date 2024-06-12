<?php
namespace MailPoetVendor\Symfony\Component\CssSelector\Parser;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\CssSelector\Node\SelectorNode;
interface ParserInterface
{
 public function parse(string $source) : array;
}
