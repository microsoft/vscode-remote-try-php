<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Event;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\EventArgs;
use MailPoetVendor\Doctrine\Common\EventManager;
use MailPoetVendor\Doctrine\ORM\EntityManagerInterface;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata;
use MailPoetVendor\Doctrine\ORM\Mapping\EntityListenerResolver;
class ListenersInvoker
{
 public const INVOKE_NONE = 0;
 public const INVOKE_LISTENERS = 1;
 public const INVOKE_CALLBACKS = 2;
 public const INVOKE_MANAGER = 4;
 private $resolver;
 private $eventManager;
 public function __construct(EntityManagerInterface $em)
 {
 $this->eventManager = $em->getEventManager();
 $this->resolver = $em->getConfiguration()->getEntityListenerResolver();
 }
 public function getSubscribedSystems(ClassMetadata $metadata, $eventName)
 {
 $invoke = self::INVOKE_NONE;
 if (isset($metadata->lifecycleCallbacks[$eventName])) {
 $invoke |= self::INVOKE_CALLBACKS;
 }
 if (isset($metadata->entityListeners[$eventName])) {
 $invoke |= self::INVOKE_LISTENERS;
 }
 if ($this->eventManager->hasListeners($eventName)) {
 $invoke |= self::INVOKE_MANAGER;
 }
 return $invoke;
 }
 public function invoke(ClassMetadata $metadata, $eventName, $entity, EventArgs $event, $invoke)
 {
 if ($invoke & self::INVOKE_CALLBACKS) {
 foreach ($metadata->lifecycleCallbacks[$eventName] as $callback) {
 $entity->{$callback}($event);
 }
 }
 if ($invoke & self::INVOKE_LISTENERS) {
 foreach ($metadata->entityListeners[$eventName] as $listener) {
 $class = $listener['class'];
 $method = $listener['method'];
 $instance = $this->resolver->resolve($class);
 $instance->{$method}($entity, $event);
 }
 }
 if ($invoke & self::INVOKE_MANAGER) {
 $this->eventManager->dispatchEvent($eventName, $event);
 }
 }
}
