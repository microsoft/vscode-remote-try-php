<?php declare(strict_types = 1);

namespace MailPoet\Analytics;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\StatisticsUnsubscribeEntity;
use MailPoet\Statistics\StatisticsUnsubscribesRepository;

class UnsubscribeReporter {

  public const TOTAL = 'Unsubscribe > Total in last 6 months';
  public const COUNT_PER_METHOD_PATTERN = 'Unsubscribe > Count in last 6 months with method: %s';

  /*** @var StatisticsUnsubscribesRepository */
  private $statisticsUnsubscribesRepository;

  public function __construct(
    StatisticsUnsubscribesRepository $statisticsUnsubscribesRepository
  ) {
    $this->statisticsUnsubscribesRepository = $statisticsUnsubscribesRepository;
  }

  public function getProperties(): array {
    $properties = [
      self::TOTAL => $this->statisticsUnsubscribesRepository->getTotalForMonths(6),
    ];

    foreach ($this->statisticsUnsubscribesRepository->getCountPerMethodForMonths(6) as $methodStats) {
      $properties[sprintf(self::COUNT_PER_METHOD_PATTERN, $this->getMethodName($methodStats['method']))] = $methodStats['count'];
    }

    return $properties;
  }

  private function getMethodName(?string $methodKey): string {
    if ($methodKey === StatisticsUnsubscribeEntity::METHOD_ONE_CLICK) {
      return '1 Click';
    }

    if ($methodKey === StatisticsUnsubscribeEntity::METHOD_LINK) {
      return 'Link';
    }

    return 'Unknown';
  }
}
