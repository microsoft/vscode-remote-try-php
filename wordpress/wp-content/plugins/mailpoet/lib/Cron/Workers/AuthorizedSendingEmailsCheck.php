<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Cron\Workers;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Services\AuthorizedEmailsController;
use MailPoet\Services\Bridge;

class AuthorizedSendingEmailsCheck extends SimpleWorker {
  const TASK_TYPE = 'authorized_email_addresses_check';
  const AUTOMATIC_SCHEDULING = false;

  /** @var AuthorizedEmailsController */
  private $authorizedEmailsController;

  public function __construct(
    AuthorizedEmailsController $authorizedEmailsController
  ) {
    $this->authorizedEmailsController = $authorizedEmailsController;
    parent::__construct();
  }

  public function checkProcessingRequirements() {
    return Bridge::isMPSendingServiceEnabled();
  }

  public function processTaskStrategy(ScheduledTaskEntity $task, $timer) {
    $this->authorizedEmailsController->checkAuthorizedEmailAddresses();
    return true;
  }
}
