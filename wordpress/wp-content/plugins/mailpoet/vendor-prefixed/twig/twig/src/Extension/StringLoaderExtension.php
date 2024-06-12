<?php
namespace MailPoetVendor\Twig\Extension;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\TwigFunction;
final class StringLoaderExtension extends AbstractExtension
{
 public function getFunctions() : array
 {
 return [new TwigFunction('template_from_string', '\\MailPoetVendor\\twig_template_from_string', ['needs_environment' => \true])];
 }
}
namespace MailPoetVendor;
use MailPoetVendor\Twig\Environment;
use MailPoetVendor\Twig\TemplateWrapper;
function twig_template_from_string(Environment $env, $template, string $name = null) : TemplateWrapper
{
 return $env->createTemplate((string) $template, $name);
}
