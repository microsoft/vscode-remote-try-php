<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\API\JSON\v1;

if (!defined('ABSPATH')) exit;


use MailPoet\API\JSON\Endpoint as APIEndpoint;
use MailPoet\Config\AccessControl;
use MailPoet\Cron\Workers\StatsNotifications\NewsletterLinkRepository;

class NewsletterLinks extends APIEndpoint {

  public $permissions = [
    'global' => AccessControl::PERMISSION_MANAGE_SEGMENTS,
  ];

  /** @var NewsletterLinkRepository */
  private $newsletterLinkRepository;

  public function __construct(
    NewsletterLinkRepository $newsletterLinkRepository
  ) {
    $this->newsletterLinkRepository = $newsletterLinkRepository;
  }

  public function get($data = []) {
    $links = $this->newsletterLinkRepository->findBy(['newsletter' => $data['newsletterId']]);
    $response = [];
    foreach ($links as $link) {
      $response[] = [
        'id' => $link->getId(),
        'url' => $link->getUrl(),
      ];
    }
    return $this->successResponse($response);
  }
}
