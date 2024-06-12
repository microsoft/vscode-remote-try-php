<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection\Argument;
if (!defined('ABSPATH')) exit;
interface ArgumentInterface
{
 public function getValues();
 public function setValues(array $values);
}
