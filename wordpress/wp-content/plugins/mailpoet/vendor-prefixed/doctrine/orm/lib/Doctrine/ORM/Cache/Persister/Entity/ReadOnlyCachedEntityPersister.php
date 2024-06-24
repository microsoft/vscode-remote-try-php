<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Cache\Persister\Entity;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\Util\ClassUtils;
use MailPoetVendor\Doctrine\ORM\Cache\Exception\CannotUpdateReadOnlyEntity;
class ReadOnlyCachedEntityPersister extends NonStrictReadWriteCachedEntityPersister
{
 public function update($entity)
 {
 throw CannotUpdateReadOnlyEntity::fromEntity(ClassUtils::getClass($entity));
 }
}
