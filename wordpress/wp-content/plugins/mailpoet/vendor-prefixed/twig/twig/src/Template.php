<?php
namespace MailPoetVendor\Twig;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Error\Error;
use MailPoetVendor\Twig\Error\LoaderError;
use MailPoetVendor\Twig\Error\RuntimeError;
abstract class Template
{
 public const ANY_CALL = 'any';
 public const ARRAY_CALL = 'array';
 public const METHOD_CALL = 'method';
 protected $parent;
 protected $parents = [];
 protected $env;
 protected $blocks = [];
 protected $traits = [];
 protected $extensions = [];
 protected $sandbox;
 public function __construct(Environment $env)
 {
 $this->env = $env;
 $this->extensions = $env->getExtensions();
 }
 public abstract function getTemplateName();
 public abstract function getDebugInfo();
 public abstract function getSourceContext();
 public function getParent(array $context)
 {
 if (null !== $this->parent) {
 return $this->parent;
 }
 try {
 $parent = $this->doGetParent($context);
 if (\false === $parent) {
 return \false;
 }
 if ($parent instanceof self || $parent instanceof TemplateWrapper) {
 return $this->parents[$parent->getSourceContext()->getName()] = $parent;
 }
 if (!isset($this->parents[$parent])) {
 $this->parents[$parent] = $this->loadTemplate($parent);
 }
 } catch (LoaderError $e) {
 $e->setSourceContext(null);
 $e->guess();
 throw $e;
 }
 return $this->parents[$parent];
 }
 protected function doGetParent(array $context)
 {
 return \false;
 }
 public function isTraitable()
 {
 return \true;
 }
 public function displayParentBlock($name, array $context, array $blocks = [])
 {
 if (isset($this->traits[$name])) {
 $this->traits[$name][0]->displayBlock($name, $context, $blocks, \false);
 } elseif (\false !== ($parent = $this->getParent($context))) {
 $parent->displayBlock($name, $context, $blocks, \false);
 } else {
 throw new RuntimeError(\sprintf('The template has no parent and no traits defining the "%s" block.', $name), -1, $this->getSourceContext());
 }
 }
 public function displayBlock($name, array $context, array $blocks = [], $useBlocks = \true, self $templateContext = null)
 {
 if ($useBlocks && isset($blocks[$name])) {
 $template = $blocks[$name][0];
 $block = $blocks[$name][1];
 } elseif (isset($this->blocks[$name])) {
 $template = $this->blocks[$name][0];
 $block = $this->blocks[$name][1];
 } else {
 $template = null;
 $block = null;
 }
 // avoid RCEs when sandbox is enabled
 if (null !== $template && !$template instanceof self) {
 throw new \LogicException('A block must be a method on a \\Twig\\Template instance.');
 }
 if (null !== $template) {
 try {
 $template->{$block}($context, $blocks);
 } catch (Error $e) {
 if (!$e->getSourceContext()) {
 $e->setSourceContext($template->getSourceContext());
 }
 // this is mostly useful for \Twig\Error\LoaderError exceptions
 // see \Twig\Error\LoaderError
 if (-1 === $e->getTemplateLine()) {
 $e->guess();
 }
 throw $e;
 } catch (\Exception $e) {
 $e = new RuntimeError(\sprintf('An exception has been thrown during the rendering of a template ("%s").', $e->getMessage()), -1, $template->getSourceContext(), $e);
 $e->guess();
 throw $e;
 }
 } elseif (\false !== ($parent = $this->getParent($context))) {
 $parent->displayBlock($name, $context, \array_merge($this->blocks, $blocks), \false, $templateContext ?? $this);
 } elseif (isset($blocks[$name])) {
 throw new RuntimeError(\sprintf('Block "%s" should not call parent() in "%s" as the block does not exist in the parent template "%s".', $name, $blocks[$name][0]->getTemplateName(), $this->getTemplateName()), -1, $blocks[$name][0]->getSourceContext());
 } else {
 throw new RuntimeError(\sprintf('Block "%s" on template "%s" does not exist.', $name, $this->getTemplateName()), -1, ($templateContext ?? $this)->getSourceContext());
 }
 }
 public function renderParentBlock($name, array $context, array $blocks = [])
 {
 if ($this->env->isDebug()) {
 \ob_start();
 } else {
 \ob_start(function () {
 return '';
 });
 }
 $this->displayParentBlock($name, $context, $blocks);
 return \ob_get_clean();
 }
 public function renderBlock($name, array $context, array $blocks = [], $useBlocks = \true)
 {
 if ($this->env->isDebug()) {
 \ob_start();
 } else {
 \ob_start(function () {
 return '';
 });
 }
 $this->displayBlock($name, $context, $blocks, $useBlocks);
 return \ob_get_clean();
 }
 public function hasBlock($name, array $context, array $blocks = [])
 {
 if (isset($blocks[$name])) {
 return $blocks[$name][0] instanceof self;
 }
 if (isset($this->blocks[$name])) {
 return \true;
 }
 if (\false !== ($parent = $this->getParent($context))) {
 return $parent->hasBlock($name, $context);
 }
 return \false;
 }
 public function getBlockNames(array $context, array $blocks = [])
 {
 $names = \array_merge(\array_keys($blocks), \array_keys($this->blocks));
 if (\false !== ($parent = $this->getParent($context))) {
 $names = \array_merge($names, $parent->getBlockNames($context));
 }
 return \array_unique($names);
 }
 protected function loadTemplate($template, $templateName = null, $line = null, $index = null)
 {
 try {
 if (\is_array($template)) {
 return $this->env->resolveTemplate($template);
 }
 if ($template instanceof self || $template instanceof TemplateWrapper) {
 return $template;
 }
 if ($template === $this->getTemplateName()) {
 $class = static::class;
 if (\false !== ($pos = \strrpos($class, '___', -1))) {
 $class = \substr($class, 0, $pos);
 }
 } else {
 $class = $this->env->getTemplateClass($template);
 }
 return $this->env->loadTemplate($class, $template, $index);
 } catch (Error $e) {
 if (!$e->getSourceContext()) {
 $e->setSourceContext($templateName ? new Source('', $templateName) : $this->getSourceContext());
 }
 if ($e->getTemplateLine() > 0) {
 throw $e;
 }
 if (!$line) {
 $e->guess();
 } else {
 $e->setTemplateLine($line);
 }
 throw $e;
 }
 }
 public function unwrap()
 {
 return $this;
 }
 public function getBlocks()
 {
 return $this->blocks;
 }
 public function display(array $context, array $blocks = [])
 {
 $this->displayWithErrorHandling($this->env->mergeGlobals($context), \array_merge($this->blocks, $blocks));
 }
 public function render(array $context)
 {
 $level = \ob_get_level();
 if ($this->env->isDebug()) {
 \ob_start();
 } else {
 \ob_start(function () {
 return '';
 });
 }
 try {
 $this->display($context);
 } catch (\Throwable $e) {
 while (\ob_get_level() > $level) {
 \ob_end_clean();
 }
 throw $e;
 }
 return \ob_get_clean();
 }
 protected function displayWithErrorHandling(array $context, array $blocks = [])
 {
 try {
 $this->doDisplay($context, $blocks);
 } catch (Error $e) {
 if (!$e->getSourceContext()) {
 $e->setSourceContext($this->getSourceContext());
 }
 // this is mostly useful for \Twig\Error\LoaderError exceptions
 // see \Twig\Error\LoaderError
 if (-1 === $e->getTemplateLine()) {
 $e->guess();
 }
 throw $e;
 } catch (\Exception $e) {
 $e = new RuntimeError(\sprintf('An exception has been thrown during the rendering of a template ("%s").', $e->getMessage()), -1, $this->getSourceContext(), $e);
 $e->guess();
 throw $e;
 }
 }
 protected abstract function doDisplay(array $context, array $blocks = []);
}
