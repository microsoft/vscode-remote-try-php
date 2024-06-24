<?php
namespace MailPoetVendor\Symfony\Component\Validator\Mapping\Loader;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Mapping\ClassMetadata;
interface LoaderInterface
{
 public function loadClassMetadata(ClassMetadata $metadata);
}
