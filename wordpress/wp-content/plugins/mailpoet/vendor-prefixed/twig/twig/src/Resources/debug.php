<?php
namespace MailPoetVendor;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Environment;
use MailPoetVendor\Twig\Extension\DebugExtension;
function twig_var_dump(Environment $env, $context, ...$vars)
{
 trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);
 DebugExtension::dump($env, $context, ...$vars);
}
