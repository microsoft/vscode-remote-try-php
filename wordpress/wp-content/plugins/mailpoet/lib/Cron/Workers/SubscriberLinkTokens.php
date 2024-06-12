<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Cron\Workers;

if (!defined('ABSPATH')) exit;


use MailPoet\DI\ContainerWrapper;
use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Subscribers\SubscribersRepository;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Carbon\Carbon;
use MailPoetVendor\Doctrine\ORM\EntityManager;

if (!defined('ABSPATH')) exit;

class SubscriberLinkTokens extends SimpleWorker {
  const TASK_TYPE = 'subscriber_link_tokens';
  const BATCH_SIZE = 10000;
  const AUTOMATIC_SCHEDULING = false;

  public function processTaskStrategy(ScheduledTaskEntity $task, $timer) {
    $entityManager = ContainerWrapper::getInstance()->get(EntityManager::class);
    $subscribersRepository = ContainerWrapper::getInstance()->get(SubscribersRepository::class);
    $subscribersTable = $entityManager->getClassMetadata(SubscriberEntity::class)->getTableName();
    $connection = $entityManager->getConnection();

    $count = $subscribersRepository->countBy(['linkToken' => null]);

    if ($count) {
      $authKey = defined('AUTH_KEY') ? AUTH_KEY : '';

      $connection->executeStatement(
        "UPDATE {$subscribersTable} SET link_token = SUBSTRING(MD5(CONCAT(:authKey, email)), 1, :tokenLength) WHERE link_token IS NULL LIMIT :limit",
        ['authKey' => $authKey, 'tokenLength' => SubscriberEntity::OBSOLETE_LINK_TOKEN_LENGTH, 'limit' => self::BATCH_SIZE],
        ['authKey' => \PDO::PARAM_STR, 'tokenLength' => \PDO::PARAM_INT, 'limit' => \PDO::PARAM_INT]
      );

      $this->schedule();
    }
    return true;
  }

  public function getNextRunDate() {
    $wp = new WPFunctions();
    return Carbon::createFromTimestamp($wp->currentTime('timestamp'));
  }
}
