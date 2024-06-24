<?php
namespace MailPoetVendor\Twig\Extension;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\ExpressionParser;
use MailPoetVendor\Twig\Node\Expression\Binary\AbstractBinary;
use MailPoetVendor\Twig\Node\Expression\Unary\AbstractUnary;
use MailPoetVendor\Twig\NodeVisitor\NodeVisitorInterface;
use MailPoetVendor\Twig\TokenParser\TokenParserInterface;
use MailPoetVendor\Twig\TwigFilter;
use MailPoetVendor\Twig\TwigFunction;
use MailPoetVendor\Twig\TwigTest;
interface ExtensionInterface
{
 public function getTokenParsers();
 public function getNodeVisitors();
 public function getFilters();
 public function getTests();
 public function getFunctions();
 public function getOperators();
}
