<?php
namespace MailPoetVendor\Symfony\Component\DependencyInjection;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Psr\Container\ContainerInterface as PsrContainerInterface;
use MailPoetVendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use MailPoetVendor\Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use MailPoetVendor\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
interface ContainerInterface extends PsrContainerInterface
{
 public const RUNTIME_EXCEPTION_ON_INVALID_REFERENCE = 0;
 public const EXCEPTION_ON_INVALID_REFERENCE = 1;
 public const NULL_ON_INVALID_REFERENCE = 2;
 public const IGNORE_ON_INVALID_REFERENCE = 3;
 public const IGNORE_ON_UNINITIALIZED_REFERENCE = 4;
 public function set(string $id, ?object $service);
 public function get(string $id, int $invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE);
 public function has(string $id);
 public function initialized(string $id);
 public function getParameter(string $name);
 public function hasParameter(string $name);
 public function setParameter(string $name, $value);
}
