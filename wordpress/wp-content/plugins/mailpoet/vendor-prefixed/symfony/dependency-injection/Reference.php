<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection;
if (!defined('ABSPATH')) exit;
class Reference
{
 private $id;
 private $invalidBehavior;
 public function __construct(string $id, int $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE)
 {
 $this->id = $id;
 $this->invalidBehavior = $invalidBehavior;
 }
 public function __toString()
 {
 return $this->id;
 }
 public function getInvalidBehavior()
 {
 return $this->invalidBehavior;
 }
}
