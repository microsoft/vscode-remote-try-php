<?php
namespace MailPoetVendor\Twig\Sandbox;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Twig\Source;
interface SourcePolicyInterface
{
 public function enableSandbox(Source $source) : bool;
}
