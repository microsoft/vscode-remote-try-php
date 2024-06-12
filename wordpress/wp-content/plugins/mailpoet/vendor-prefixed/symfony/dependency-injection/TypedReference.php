<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection;
if (!defined('ABSPATH')) exit;
class TypedReference extends Reference
{
 private $type;
 private $name;
 public function __construct(string $id, string $type, int $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, string $name = null)
 {
 $this->name = $type === $id ? $name : null;
 parent::__construct($id, $invalidBehavior);
 $this->type = $type;
 }
 public function getType()
 {
 return $this->type;
 }
 public function getName() : ?string
 {
 return $this->name;
 }
}
