<?php
namespace MailPoetVendor\Symfony\Contracts\Service;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Psr\Container\ContainerInterface;
trait ServiceSubscriberTrait
{
 protected $container;
 public static function getSubscribedServices() : array
 {
 static $services;
 if (null !== $services) {
 return $services;
 }
 $services = \is_callable(['parent', __FUNCTION__]) ? parent::getSubscribedServices() : [];
 foreach ((new \ReflectionClass(self::class))->getMethods() as $method) {
 if ($method->isStatic() || $method->isAbstract() || $method->isGenerator() || $method->isInternal() || $method->getNumberOfRequiredParameters()) {
 continue;
 }
 if (self::class !== $method->getDeclaringClass()->name) {
 continue;
 }
 if (!($returnType = $method->getReturnType()) instanceof \ReflectionNamedType) {
 continue;
 }
 if ($returnType->isBuiltin()) {
 continue;
 }
 $services[self::class . '::' . $method->name] = '?' . $returnType->getName();
 }
 return $services;
 }
 public function setContainer(ContainerInterface $container)
 {
 $this->container = $container;
 if (\is_callable(['parent', __FUNCTION__])) {
 return parent::setContainer($container);
 }
 return null;
 }
}
