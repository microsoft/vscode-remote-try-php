<?php
namespace MailPoetVendor\Twig;
if (!defined('ABSPATH')) exit;
final class TemplateWrapper
{
 private $env;
 private $template;
 public function __construct(Environment $env, Template $template)
 {
 $this->env = $env;
 $this->template = $template;
 }
 public function render(array $context = []) : string
 {
 // using func_get_args() allows to not expose the blocks argument
 // as it should only be used by internal code
 return $this->template->render($context, \func_get_args()[1] ?? []);
 }
 public function display(array $context = [])
 {
 // using func_get_args() allows to not expose the blocks argument
 // as it should only be used by internal code
 $this->template->display($context, \func_get_args()[1] ?? []);
 }
 public function hasBlock(string $name, array $context = []) : bool
 {
 return $this->template->hasBlock($name, $context);
 }
 public function getBlockNames(array $context = []) : array
 {
 return $this->template->getBlockNames($context);
 }
 public function renderBlock(string $name, array $context = []) : string
 {
 $context = $this->env->mergeGlobals($context);
 $level = \ob_get_level();
 if ($this->env->isDebug()) {
 \ob_start();
 } else {
 \ob_start(function () {
 return '';
 });
 }
 try {
 $this->template->displayBlock($name, $context);
 } catch (\Throwable $e) {
 while (\ob_get_level() > $level) {
 \ob_end_clean();
 }
 throw $e;
 }
 return \ob_get_clean();
 }
 public function displayBlock(string $name, array $context = [])
 {
 $this->template->displayBlock($name, $this->env->mergeGlobals($context));
 }
 public function getSourceContext() : Source
 {
 return $this->template->getSourceContext();
 }
 public function getTemplateName() : string
 {
 return $this->template->getTemplateName();
 }
 public function unwrap()
 {
 return $this->template;
 }
}
