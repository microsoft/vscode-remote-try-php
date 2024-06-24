<?php
namespace MailPoetVendor\Twig\Extension;
if (!defined('ABSPATH')) exit;
interface GlobalsInterface
{
 public function getGlobals() : array;
}
