<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Psr\Container\ContainerInterface;
use MailPoetVendor\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
final class ReverseContainer
{
 private $serviceContainer;
 private $reversibleLocator;
 private $tagName;
 private $getServiceId;
 public function __construct(Container $serviceContainer, ContainerInterface $reversibleLocator, string $tagName = 'container.reversible')
 {
 $this->serviceContainer = $serviceContainer;
 $this->reversibleLocator = $reversibleLocator;
 $this->tagName = $tagName;
 $this->getServiceId = \Closure::bind(function (object $service) : ?string {
 return (\array_search($service, $this->services, \true) ?: \array_search($service, $this->privates, \true)) ?: null;
 }, $serviceContainer, Container::class);
 }
 public function getId(object $service) : ?string
 {
 if ($this->serviceContainer === $service) {
 return 'service_container';
 }
 if (null === ($id = ($this->getServiceId)($service))) {
 return null;
 }
 if ($this->serviceContainer->has($id) || $this->reversibleLocator->has($id)) {
 return $id;
 }
 return null;
 }
 public function getService(string $id) : object
 {
 if ($this->serviceContainer->has($id)) {
 return $this->serviceContainer->get($id);
 }
 if ($this->reversibleLocator->has($id)) {
 return $this->reversibleLocator->get($id);
 }
 if (isset($this->serviceContainer->getRemovedIds()[$id])) {
 throw new ServiceNotFoundException($id, null, null, [], \sprintf('The "%s" service is private and cannot be accessed by reference. You should either make it public, or tag it as "%s".', $id, $this->tagName));
 }
 // will throw a ServiceNotFoundException
 $this->serviceContainer->get($id);
 }
}
