<?php
declare (strict_types=1);
namespace MailPoetVendor\Doctrine\ORM\Id;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\DBAL\Connections\PrimaryReadReplicaConnection;
use MailPoetVendor\Doctrine\DBAL\Platforms\AbstractPlatform;
use MailPoetVendor\Doctrine\Deprecations\Deprecation;
use MailPoetVendor\Doctrine\ORM\EntityManagerInterface;
use MailPoetVendor\Doctrine\ORM\Exception\NotSupported;
use function method_exists;
class UuidGenerator extends AbstractIdGenerator
{
 public function __construct()
 {
 Deprecation::trigger('doctrine/orm', 'https://github.com/doctrine/orm/issues/7312', '%s is deprecated with no replacement, use an application-side generator instead', self::class);
 if (!method_exists(AbstractPlatform::class, 'getGuidExpression')) {
 throw NotSupported::createForDbal3();
 }
 }
 public function generateId(EntityManagerInterface $em, $entity)
 {
 $connection = $em->getConnection();
 $sql = 'SELECT ' . $connection->getDatabasePlatform()->getGuidExpression();
 if ($connection instanceof PrimaryReadReplicaConnection) {
 $connection->ensureConnectedToPrimary();
 }
 return $connection->executeQuery($sql)->fetchOne();
 }
}
