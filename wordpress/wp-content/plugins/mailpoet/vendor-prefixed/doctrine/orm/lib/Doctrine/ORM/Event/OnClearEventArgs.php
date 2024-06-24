<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Event;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use MailPoetVendor\Doctrine\ORM\EntityManagerInterface;
use MailPoetVendor\Doctrine\Persistence\Event\OnClearEventArgs as BaseOnClearEventArgs;
class OnClearEventArgs extends BaseOnClearEventArgs
{
 private $entityClass;
 public function __construct(EntityManagerInterface $em, $entityClass = null)
 {
 parent::__construct($em);
 $this->entityClass = $entityClass;
 }
 public function getEntityManager()
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/issues/9875', 'Method %s() is deprecated and will be removed in Doctrine ORM 3.0. Use getObjectManager() instead.', __METHOD__);
 return $this->getObjectManager();
 }
 public function getEntityClass()
 {
 return $this->entityClass;
 }
 public function clearsAllEntities()
 {
 return $this->entityClass === null;
 }
}
