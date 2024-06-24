<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection\Argument;
if (!defined('ABSPATH')) exit;
final class AbstractArgument
{
 private $text;
 private $context;
 public function __construct(string $text = '')
 {
 $this->text = \trim($text, '. ');
 }
 public function setContext(string $context) : void
 {
 $this->context = $context . ' is abstract' . ('' === $this->text ? '' : ': ');
 }
 public function getText() : string
 {
 return $this->text;
 }
 public function getTextWithContext() : string
 {
 return $this->context . $this->text . '.';
 }
}
