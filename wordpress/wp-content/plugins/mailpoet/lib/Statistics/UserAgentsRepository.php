<?php declare(strict_types = 1);

namespace MailPoet\Statistics;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\Repository;
use MailPoet\Entities\UserAgentEntity;

/**
 * @extends Repository<UserAgentEntity>
 */
class UserAgentsRepository extends Repository {
  protected function getEntityClassName() {
    return UserAgentEntity::class;
  }

  public function findOrCreate(string $userAgent): UserAgentEntity {
    $hash = (string)crc32($userAgent);
    $userAgentEntity = $this->findOneBy(['hash' => $hash]);
    if ($userAgentEntity) return $userAgentEntity;
    $userAgentEntity = new UserAgentEntity($userAgent);
    $this->persist($userAgentEntity);
    return $userAgentEntity;
  }
}
