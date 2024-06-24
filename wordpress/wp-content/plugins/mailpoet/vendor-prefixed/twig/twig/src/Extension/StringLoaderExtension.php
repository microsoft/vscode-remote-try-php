<?php
namespace MailPoetVendor\Twig\Extension;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Environment;
use MailPoetVendor\Twig\TemplateWrapper;
use MailPoetVendor\Twig\TwigFunction;
final class StringLoaderExtension extends AbstractExtension
{
 public function getFunctions() : array
 {
 return [new TwigFunction('template_from_string', [self::class, 'templateFromString'], ['needs_environment' => \true])];
 }
 public static function templateFromString(Environment $env, $template, ?string $name = null) : TemplateWrapper
 {
 return $env->createTemplate((string) $template, $name);
 }
}
