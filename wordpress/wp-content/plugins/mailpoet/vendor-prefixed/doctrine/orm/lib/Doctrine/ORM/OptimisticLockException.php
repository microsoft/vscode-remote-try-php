<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM;
if (!defined('ABSPATH')) exit;
use DateTimeInterface;
use MailPoetVendor\Doctrine\ORM\Exception\ORMException;
class OptimisticLockException extends ORMException
{
 private $entity;
 public function __construct($msg, $entity)
 {
 parent::__construct($msg);
 $this->entity = $entity;
 }
 public function getEntity()
 {
 return $this->entity;
 }
 public static function lockFailed($entity)
 {
 return new self('The optimistic lock on an entity failed.', $entity);
 }
 public static function lockFailedVersionMismatch($entity, $expectedLockVersion, $actualLockVersion)
 {
 $expectedLockVersion = $expectedLockVersion instanceof DateTimeInterface ? $expectedLockVersion->getTimestamp() : $expectedLockVersion;
 $actualLockVersion = $actualLockVersion instanceof DateTimeInterface ? $actualLockVersion->getTimestamp() : $actualLockVersion;
 return new self('The optimistic lock failed, version ' . $expectedLockVersion . ' was expected, but is actually ' . $actualLockVersion, $entity);
 }
 public static function notVersioned($entityName)
 {
 return new self('Cannot obtain optimistic lock on unversioned entity ' . $entityName, null);
 }
}
