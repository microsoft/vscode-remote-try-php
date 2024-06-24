<?php
namespace MailPoetVendor\Symfony\Component\Validator\Mapping;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraints\GroupSequence;
use MailPoetVendor\Symfony\Component\Validator\GroupSequenceProviderInterface;
interface ClassMetadataInterface extends MetadataInterface
{
 public function getConstrainedProperties();
 public function hasGroupSequence();
 public function getGroupSequence();
 public function isGroupSequenceProvider();
 public function hasPropertyMetadata(string $property);
 public function getPropertyMetadata(string $property);
 public function getClassName();
}
