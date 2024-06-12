<?php
namespace MailPoetVendor\Twig;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Node\Expression\FilterExpression;
use MailPoetVendor\Twig\Node\Node;
final class TwigFilter
{
 private $name;
 private $callable;
 private $options;
 private $arguments = [];
 public function __construct(string $name, $callable = null, array $options = [])
 {
 $this->name = $name;
 $this->callable = $callable;
 $this->options = \array_merge(['needs_environment' => \false, 'needs_context' => \false, 'is_variadic' => \false, 'is_safe' => null, 'is_safe_callback' => null, 'pre_escape' => null, 'preserves_safety' => null, 'node_class' => FilterExpression::class, 'deprecated' => \false, 'alternative' => null], $options);
 }
 public function getName() : string
 {
 return $this->name;
 }
 public function getCallable()
 {
 return $this->callable;
 }
 public function getNodeClass() : string
 {
 return $this->options['node_class'];
 }
 public function setArguments(array $arguments) : void
 {
 $this->arguments = $arguments;
 }
 public function getArguments() : array
 {
 return $this->arguments;
 }
 public function needsEnvironment() : bool
 {
 return $this->options['needs_environment'];
 }
 public function needsContext() : bool
 {
 return $this->options['needs_context'];
 }
 public function getSafe(Node $filterArgs) : ?array
 {
 if (null !== $this->options['is_safe']) {
 return $this->options['is_safe'];
 }
 if (null !== $this->options['is_safe_callback']) {
 return $this->options['is_safe_callback']($filterArgs);
 }
 return null;
 }
 public function getPreservesSafety() : ?array
 {
 return $this->options['preserves_safety'];
 }
 public function getPreEscape() : ?string
 {
 return $this->options['pre_escape'];
 }
 public function isVariadic() : bool
 {
 return $this->options['is_variadic'];
 }
 public function isDeprecated() : bool
 {
 return (bool) $this->options['deprecated'];
 }
 public function getDeprecatedVersion() : string
 {
 return \is_bool($this->options['deprecated']) ? '' : $this->options['deprecated'];
 }
 public function getAlternative() : ?string
 {
 return $this->options['alternative'];
 }
}
