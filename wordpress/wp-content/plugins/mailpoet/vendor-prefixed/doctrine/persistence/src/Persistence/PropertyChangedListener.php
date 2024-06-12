<?php
namespace MailPoetVendor\Doctrine\Persistence;
if (!defined('ABSPATH')) exit;
interface PropertyChangedListener
{
 public function propertyChanged($sender, $propertyName, $oldValue, $newValue);
}
