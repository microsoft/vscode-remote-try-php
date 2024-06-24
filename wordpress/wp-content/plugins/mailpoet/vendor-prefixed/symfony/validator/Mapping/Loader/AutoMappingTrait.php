<?php
namespace MailPoetVendor\Symfony\Component\Validator\Mapping\Loader;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Mapping\AutoMappingStrategy;
use MailPoetVendor\Symfony\Component\Validator\Mapping\ClassMetadata;
trait AutoMappingTrait
{
 private function isAutoMappingEnabledForClass(ClassMetadata $metadata, string $classValidatorRegexp = null) : bool
 {
 // Check if AutoMapping constraint is set first
 if (AutoMappingStrategy::NONE !== ($strategy = $metadata->getAutoMappingStrategy())) {
 return AutoMappingStrategy::ENABLED === $strategy;
 }
 // Fallback on the config
 return null !== $classValidatorRegexp && \preg_match($classValidatorRegexp, $metadata->getClassName());
 }
}
