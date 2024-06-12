<?php
namespace MailPoetVendor\Twig\Util;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Environment;
use MailPoetVendor\Twig\Error\SyntaxError;
use MailPoetVendor\Twig\Source;
final class DeprecationCollector
{
 private $twig;
 public function __construct(Environment $twig)
 {
 $this->twig = $twig;
 }
 public function collectDir(string $dir, string $ext = '.twig') : array
 {
 $iterator = new \RegexIterator(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir), \RecursiveIteratorIterator::LEAVES_ONLY), '{' . \preg_quote($ext) . '$}');
 return $this->collect(new TemplateDirIterator($iterator));
 }
 public function collect(\Traversable $iterator) : array
 {
 $deprecations = [];
 \set_error_handler(function ($type, $msg) use(&$deprecations) {
 if (\E_USER_DEPRECATED === $type) {
 $deprecations[] = $msg;
 }
 });
 foreach ($iterator as $name => $contents) {
 try {
 $this->twig->parse($this->twig->tokenize(new Source($contents, $name)));
 } catch (SyntaxError $e) {
 // ignore templates containing syntax errors
 }
 }
 \restore_error_handler();
 return $deprecations;
 }
}
