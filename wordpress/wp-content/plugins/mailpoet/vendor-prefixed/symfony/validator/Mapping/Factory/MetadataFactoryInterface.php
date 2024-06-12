<?php
namespace MailPoetVendor\Symfony\Component\Validator\Mapping\Factory;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Exception\NoSuchMetadataException;
use MailPoetVendor\Symfony\Component\Validator\Mapping\MetadataInterface;
interface MetadataFactoryInterface
{
 public function getMetadataFor($value);
 public function hasMetadataFor($value);
}
