<?php declare(strict_types = 1);

namespace MailPoet\Statistics;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\Repository;
use MailPoet\Entities\StatisticsBounceEntity;

/**
 * @extends Repository<StatisticsBounceEntity>
 */
class StatisticsBouncesRepository extends Repository {
  protected function getEntityClassName(): string {
    return StatisticsBounceEntity::class;
  }
}
