<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection;
if (!defined('ABSPATH')) exit;
interface EnvVarLoaderInterface
{
 public function loadEnvVars() : array;
}
