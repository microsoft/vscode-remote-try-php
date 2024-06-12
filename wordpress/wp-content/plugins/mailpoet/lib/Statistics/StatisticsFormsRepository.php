<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Statistics;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\Repository;
use MailPoet\Entities\FormEntity;
use MailPoet\Entities\StatisticsFormEntity;
use MailPoet\Entities\SubscriberEntity;

/**
 * @extends Repository<StatisticsFormEntity>
 */
class StatisticsFormsRepository extends Repository {
  protected function getEntityClassName() {
    return StatisticsFormEntity::class;
  }

  public function getTotalSignups(int $formId): int {
    return $this->countBy(['form' => $formId]);
  }

  public function record(FormEntity $form, SubscriberEntity $subscriber): ?StatisticsFormEntity {
    if ($form->getId() > 0 && $subscriber->getId() > 0) {
      // check if we already have a record for today
      $statisticsForm = $this->findOneBy(['form' => $form, 'subscriber' => $subscriber]);

      if (!$statisticsForm) {
        // create a new entry
        $statisticsForm = new StatisticsFormEntity($form, $subscriber);
        $this->persist($statisticsForm);
        $this->flush();
      }
      return $statisticsForm;
    }
    return null;
  }
}
