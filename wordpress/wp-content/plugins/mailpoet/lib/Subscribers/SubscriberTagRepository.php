<?php declare(strict_types = 1);

namespace MailPoet\Subscribers;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\Repository;
use MailPoet\Entities\SubscriberTagEntity;

/**
 * @extends Repository<SubscriberTagEntity>
 */
class SubscriberTagRepository extends Repository {
  protected function getEntityClassName() {
    return SubscriberTagEntity::class;
  }
}
