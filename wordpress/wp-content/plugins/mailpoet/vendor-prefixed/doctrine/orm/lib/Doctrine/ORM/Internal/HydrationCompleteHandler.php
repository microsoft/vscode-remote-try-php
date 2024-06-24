<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Internal;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\ORM\EntityManagerInterface;
use MailPoetVendor\Doctrine\ORM\Event\ListenersInvoker;
use MailPoetVendor\Doctrine\ORM\Event\PostLoadEventArgs;
use MailPoetVendor\Doctrine\ORM\Events;
use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata;
final class HydrationCompleteHandler
{
 private $listenersInvoker;
 private $em;
 private $deferredPostLoadInvocations = [];
 public function __construct(ListenersInvoker $listenersInvoker, EntityManagerInterface $em)
 {
 $this->listenersInvoker = $listenersInvoker;
 $this->em = $em;
 }
 public function deferPostLoadInvoking(ClassMetadata $class, $entity) : void
 {
 $invoke = $this->listenersInvoker->getSubscribedSystems($class, Events::postLoad);
 if ($invoke === ListenersInvoker::INVOKE_NONE) {
 return;
 }
 $this->deferredPostLoadInvocations[] = [$class, $invoke, $entity];
 }
 public function hydrationComplete() : void
 {
 $toInvoke = $this->deferredPostLoadInvocations;
 $this->deferredPostLoadInvocations = [];
 foreach ($toInvoke as $classAndEntity) {
 [$class, $invoke, $entity] = $classAndEntity;
 $this->listenersInvoker->invoke($class, Events::postLoad, $entity, new PostLoadEventArgs($entity, $this->em), $invoke);
 }
 }
}
