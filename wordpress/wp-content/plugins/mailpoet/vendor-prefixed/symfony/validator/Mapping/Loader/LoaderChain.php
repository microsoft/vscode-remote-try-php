<?php
namespace MailPoetVendor\Symfony\Component\Validator\Mapping\Loader;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Exception\MappingException;
use MailPoetVendor\Symfony\Component\Validator\Mapping\ClassMetadata;
class LoaderChain implements LoaderInterface
{
 protected $loaders;
 public function __construct(array $loaders)
 {
 foreach ($loaders as $loader) {
 if (!$loader instanceof LoaderInterface) {
 throw new MappingException(\sprintf('Class "%s" is expected to implement LoaderInterface.', \get_debug_type($loader)));
 }
 }
 $this->loaders = $loaders;
 }
 public function loadClassMetadata(ClassMetadata $metadata)
 {
 $success = \false;
 foreach ($this->loaders as $loader) {
 $success = $loader->loadClassMetadata($metadata) || $success;
 }
 return $success;
 }
 public function getLoaders()
 {
 return $this->loaders;
 }
}
