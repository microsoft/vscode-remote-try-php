<?php
namespace MailPoetVendor\Doctrine\Common\Proxy;
if (!defined('ABSPATH')) exit;
use ReflectionProperty;
class ProxyDefinition
{
 public $proxyClassName;
 public $identifierFields;
 public $reflectionFields;
 public $initializer;
 public $cloner;
 public function __construct($proxyClassName, array $identifierFields, array $reflectionFields, $initializer, $cloner)
 {
 $this->proxyClassName = $proxyClassName;
 $this->identifierFields = $identifierFields;
 $this->reflectionFields = $reflectionFields;
 $this->initializer = $initializer;
 $this->cloner = $cloner;
 }
}
