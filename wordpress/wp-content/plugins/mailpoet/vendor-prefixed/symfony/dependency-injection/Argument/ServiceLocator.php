<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection\Argument;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\DependencyInjection\ServiceLocator as BaseServiceLocator;
class ServiceLocator extends BaseServiceLocator
{
 private $factory;
 private $serviceMap;
 private $serviceTypes;
 public function __construct(\Closure $factory, array $serviceMap, array $serviceTypes = null)
 {
 $this->factory = $factory;
 $this->serviceMap = $serviceMap;
 $this->serviceTypes = $serviceTypes;
 parent::__construct($serviceMap);
 }
 public function get(string $id)
 {
 return isset($this->serviceMap[$id]) ? ($this->factory)(...$this->serviceMap[$id]) : parent::get($id);
 }
 public function getProvidedServices() : array
 {
 return $this->serviceTypes ?? ($this->serviceTypes = \array_map(function () {
 return '?';
 }, $this->serviceMap));
 }
}
