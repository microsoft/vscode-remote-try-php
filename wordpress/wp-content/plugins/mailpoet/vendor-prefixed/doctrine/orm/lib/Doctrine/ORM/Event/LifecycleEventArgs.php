<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Event;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use MailPoetVendor\Doctrine\ORM\EntityManagerInterface;
use MailPoetVendor\Doctrine\Persistence\Event\LifecycleEventArgs as BaseLifecycleEventArgs;
class LifecycleEventArgs extends BaseLifecycleEventArgs
{
 public function __construct($object, EntityManagerInterface $objectManager)
 {
 Deprecation::triggerIfCalledFromOutside('doctrine/orm', 'https://github.com/doctrine/orm/issues/9875', 'The %s class is deprecated and will be removed in ORM 3.0. Use %s instead.', self::class, BaseLifecycleEventArgs::class);
 parent::__construct($object, $objectManager);
 }
 public function getEntity()
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/issues/9875', 'Method %s() is deprecated and will be removed in Doctrine ORM 3.0. Use getObject() instead.', __METHOD__);
 return $this->getObject();
 }
 public function getEntityManager()
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/issues/9875', 'Method %s() is deprecated and will be removed in Doctrine ORM 3.0. Use getObjectManager() instead.', __METHOD__);
 return $this->getObjectManager();
 }
}
