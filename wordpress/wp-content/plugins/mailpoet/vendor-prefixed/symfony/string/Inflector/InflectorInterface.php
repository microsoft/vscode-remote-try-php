<?php
namespace MailPoetVendor\Symfony\Component\String\Inflector;
if (!defined('ABSPATH')) exit;
interface InflectorInterface
{
 public function singularize(string $plural) : array;
 public function pluralize(string $singular) : array;
}
