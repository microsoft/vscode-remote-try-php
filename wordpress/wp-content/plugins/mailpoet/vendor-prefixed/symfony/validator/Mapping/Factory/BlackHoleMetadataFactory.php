<?php
namespace MailPoetVendor\Symfony\Component\Validator\Mapping\Factory;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Exception\LogicException;
class BlackHoleMetadataFactory implements MetadataFactoryInterface
{
 public function getMetadataFor($value)
 {
 throw new LogicException('This class does not support metadata.');
 }
 public function hasMetadataFor($value)
 {
 return \false;
 }
}
