<?php
namespace MailPoetVendor\Twig\Node;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Compiler;
use MailPoetVendor\Twig\Source;
class Node implements \Countable, \IteratorAggregate
{
 protected $nodes;
 protected $attributes;
 protected $lineno;
 protected $tag;
 private $name;
 private $sourceContext;
 public function __construct(array $nodes = [], array $attributes = [], int $lineno = 0, string $tag = null)
 {
 foreach ($nodes as $name => $node) {
 if (!$node instanceof self) {
 throw new \InvalidArgumentException(\sprintf('Using "%s" for the value of node "%s" of "%s" is not supported. You must pass a \\Twig\\Node\\Node instance.', \is_object($node) ? \get_class($node) : (null === $node ? 'null' : \gettype($node)), $name, static::class));
 }
 }
 $this->nodes = $nodes;
 $this->attributes = $attributes;
 $this->lineno = $lineno;
 $this->tag = $tag;
 }
 public function __toString()
 {
 $attributes = [];
 foreach ($this->attributes as $name => $value) {
 $attributes[] = \sprintf('%s: %s', $name, \str_replace("\n", '', \var_export($value, \true)));
 }
 $repr = [static::class . '(' . \implode(', ', $attributes)];
 if (\count($this->nodes)) {
 foreach ($this->nodes as $name => $node) {
 $len = \strlen($name) + 4;
 $noderepr = [];
 foreach (\explode("\n", (string) $node) as $line) {
 $noderepr[] = \str_repeat(' ', $len) . $line;
 }
 $repr[] = \sprintf(' %s: %s', $name, \ltrim(\implode("\n", $noderepr)));
 }
 $repr[] = ')';
 } else {
 $repr[0] .= ')';
 }
 return \implode("\n", $repr);
 }
 public function compile(Compiler $compiler)
 {
 foreach ($this->nodes as $node) {
 $node->compile($compiler);
 }
 }
 public function getTemplateLine() : int
 {
 return $this->lineno;
 }
 public function getNodeTag() : ?string
 {
 return $this->tag;
 }
 public function hasAttribute(string $name) : bool
 {
 return \array_key_exists($name, $this->attributes);
 }
 public function getAttribute(string $name)
 {
 if (!\array_key_exists($name, $this->attributes)) {
 throw new \LogicException(\sprintf('Attribute "%s" does not exist for Node "%s".', $name, static::class));
 }
 return $this->attributes[$name];
 }
 public function setAttribute(string $name, $value) : void
 {
 $this->attributes[$name] = $value;
 }
 public function removeAttribute(string $name) : void
 {
 unset($this->attributes[$name]);
 }
 public function hasNode(string $name) : bool
 {
 return isset($this->nodes[$name]);
 }
 public function getNode(string $name) : self
 {
 if (!isset($this->nodes[$name])) {
 throw new \LogicException(\sprintf('Node "%s" does not exist for Node "%s".', $name, static::class));
 }
 return $this->nodes[$name];
 }
 public function setNode(string $name, self $node) : void
 {
 $this->nodes[$name] = $node;
 }
 public function removeNode(string $name) : void
 {
 unset($this->nodes[$name]);
 }
 #[\ReturnTypeWillChange]
 public function count()
 {
 return \count($this->nodes);
 }
 public function getIterator() : \Traversable
 {
 return new \ArrayIterator($this->nodes);
 }
 public function getTemplateName() : ?string
 {
 return $this->sourceContext ? $this->sourceContext->getName() : null;
 }
 public function setSourceContext(Source $source) : void
 {
 $this->sourceContext = $source;
 foreach ($this->nodes as $node) {
 $node->setSourceContext($source);
 }
 }
 public function getSourceContext() : ?Source
 {
 return $this->sourceContext;
 }
}
