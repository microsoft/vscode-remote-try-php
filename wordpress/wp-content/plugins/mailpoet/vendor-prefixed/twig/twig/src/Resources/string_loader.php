<?php
namespace MailPoetVendor;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Environment;
use MailPoetVendor\Twig\Extension\StringLoaderExtension;
use MailPoetVendor\Twig\TemplateWrapper;
function twig_template_from_string(Environment $env, $template, ?string $name = null) : TemplateWrapper
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return StringLoaderExtension::templateFromString($env, $template, $name);
}
