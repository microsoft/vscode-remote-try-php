<?php
namespace MailPoetVendor\Twig\TokenParser;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Error\SyntaxError;
use MailPoetVendor\Twig\Node\Node;
use MailPoetVendor\Twig\Parser;
use MailPoetVendor\Twig\Token;
interface TokenParserInterface
{
 public function setParser(Parser $parser) : void;
 public function parse(Token $token);
 public function getTag();
}
