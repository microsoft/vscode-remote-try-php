<?php
namespace MailPoetVendor;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Environment;
use MailPoetVendor\Twig\Extension\EscaperExtension;
use MailPoetVendor\Twig\Node\Node;
use MailPoetVendor\Twig\Runtime\EscaperRuntime;
function twig_raw_filter($string)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return $string;
}
function twig_escape_filter(Environment $env, $string, $strategy = 'html', $charset = null, $autoescape = \false)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return $env->getRuntime(EscaperRuntime::class)->escape($string, $strategy, $charset, $autoescape);
}
function twig_escape_filter_is_safe(Node $filterArgs)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 return EscaperExtension::escapeFilterIsSafe($filterArgs);
}
