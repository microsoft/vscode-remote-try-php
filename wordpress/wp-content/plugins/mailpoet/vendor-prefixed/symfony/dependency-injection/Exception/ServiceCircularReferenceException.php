<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection\Exception;
if (!defined('ABSPATH')) exit;
class ServiceCircularReferenceException extends RuntimeException
{
 private $serviceId;
 private $path;
 public function __construct(string $serviceId, array $path, \Throwable $previous = null)
 {
 parent::__construct(\sprintf('Circular reference detected for service "%s", path: "%s".', $serviceId, \implode(' -> ', $path)), 0, $previous);
 $this->serviceId = $serviceId;
 $this->path = $path;
 }
 public function getServiceId()
 {
 return $this->serviceId;
 }
 public function getPath()
 {
 return $this->path;
 }
}
