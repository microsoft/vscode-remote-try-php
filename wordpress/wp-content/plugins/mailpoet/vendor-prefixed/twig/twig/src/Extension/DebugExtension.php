<?php
namespace MailPoetVendor\Twig\Extension;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\TwigFunction;
final class DebugExtension extends AbstractExtension
{
 public function getFunctions() : array
 {
 // dump is safe if var_dump is overridden by xdebug
 $isDumpOutputHtmlSafe = \extension_loaded('xdebug') && (\false === \ini_get('xdebug.overload_var_dump') || \ini_get('xdebug.overload_var_dump')) && (\false === \ini_get('html_errors') || \ini_get('html_errors')) || 'cli' === \PHP_SAPI;
 return [new TwigFunction('dump', '\\MailPoetVendor\\twig_var_dump', ['is_safe' => $isDumpOutputHtmlSafe ? ['html'] : [], 'needs_context' => \true, 'needs_environment' => \true, 'is_variadic' => \true])];
 }
}
namespace MailPoetVendor;
use MailPoetVendor\Twig\Environment;
use MailPoetVendor\Twig\Template;
use MailPoetVendor\Twig\TemplateWrapper;
function twig_var_dump(Environment $env, $context, ...$vars)
{
 if (!$env->isDebug()) {
 return;
 }
 \ob_start();
 if (!$vars) {
 $vars = [];
 foreach ($context as $key => $value) {
 if (!$value instanceof Template && !$value instanceof TemplateWrapper) {
 $vars[$key] = $value;
 }
 }
 \var_dump($vars);
 } else {
 \var_dump(...$vars);
 }
 return \ob_get_clean();
}
