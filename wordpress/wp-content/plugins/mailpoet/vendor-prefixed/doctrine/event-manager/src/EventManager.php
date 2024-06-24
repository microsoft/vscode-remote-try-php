<?php
namespace MailPoetVendor\Doctrine\Common;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use function spl_object_hash;
class EventManager
{
 private $listeners = [];
 public function dispatchEvent($eventName, ?EventArgs $eventArgs = null)
 {
 if (!isset($this->listeners[$eventName])) {
 return;
 }
 $eventArgs = $eventArgs ?? EventArgs::getEmptyInstance();
 foreach ($this->listeners[$eventName] as $listener) {
 $listener->{$eventName}($eventArgs);
 }
 }
 public function getListeners($event = null)
 {
 if ($event === null) {
 Deprecation::trigger('doctrine/event-manager', 'https://github.com/doctrine/event-manager/pull/50', 'Calling %s without an event name is deprecated. Call getAllListeners() instead.', __METHOD__);
 return $this->getAllListeners();
 }
 return $this->listeners[$event] ?? [];
 }
 public function getAllListeners() : array
 {
 return $this->listeners;
 }
 public function hasListeners($event)
 {
 return !empty($this->listeners[$event]);
 }
 public function addEventListener($events, $listener)
 {
 // Picks the hash code related to that listener
 $hash = spl_object_hash($listener);
 foreach ((array) $events as $event) {
 // Overrides listener if a previous one was associated already
 // Prevents duplicate listeners on same event (same instance only)
 $this->listeners[$event][$hash] = $listener;
 }
 }
 public function removeEventListener($events, $listener)
 {
 // Picks the hash code related to that listener
 $hash = spl_object_hash($listener);
 foreach ((array) $events as $event) {
 unset($this->listeners[$event][$hash]);
 }
 }
 public function addEventSubscriber(EventSubscriber $subscriber)
 {
 $this->addEventListener($subscriber->getSubscribedEvents(), $subscriber);
 }
 public function removeEventSubscriber(EventSubscriber $subscriber)
 {
 $this->removeEventListener($subscriber->getSubscribedEvents(), $subscriber);
 }
}
