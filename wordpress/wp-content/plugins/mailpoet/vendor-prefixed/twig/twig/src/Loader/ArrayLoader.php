<?php
namespace MailPoetVendor\Twig\Loader;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Error\LoaderError;
use MailPoetVendor\Twig\Source;
final class ArrayLoader implements LoaderInterface
{
 private $templates = [];
 public function __construct(array $templates = [])
 {
 $this->templates = $templates;
 }
 public function setTemplate(string $name, string $template) : void
 {
 $this->templates[$name] = $template;
 }
 public function getSourceContext(string $name) : Source
 {
 if (!isset($this->templates[$name])) {
 throw new LoaderError(\sprintf('Template "%s" is not defined.', $name));
 }
 return new Source($this->templates[$name], $name);
 }
 public function exists(string $name) : bool
 {
 return isset($this->templates[$name]);
 }
 public function getCacheKey(string $name) : string
 {
 if (!isset($this->templates[$name])) {
 throw new LoaderError(\sprintf('Template "%s" is not defined.', $name));
 }
 return $name . ':' . $this->templates[$name];
 }
 public function isFresh(string $name, int $time) : bool
 {
 if (!isset($this->templates[$name])) {
 throw new LoaderError(\sprintf('Template "%s" is not defined.', $name));
 }
 return \true;
 }
}
