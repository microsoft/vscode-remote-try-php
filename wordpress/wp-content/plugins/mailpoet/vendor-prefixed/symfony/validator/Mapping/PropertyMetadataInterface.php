<?php
namespace MailPoetVendor\Symfony\Component\Validator\Mapping;
if (!defined('ABSPATH')) exit;
interface PropertyMetadataInterface extends MetadataInterface
{
 public function getPropertyName();
 public function getPropertyValue($containingValue);
}
