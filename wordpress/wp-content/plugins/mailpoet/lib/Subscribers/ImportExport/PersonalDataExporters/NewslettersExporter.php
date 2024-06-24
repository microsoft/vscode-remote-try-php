<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Subscribers\ImportExport\PersonalDataExporters;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\SubscriberEntity;
use MailPoet\Newsletter\NewslettersRepository;
use MailPoet\Newsletter\Statistics\NewsletterStatisticsRepository;
use MailPoet\Newsletter\Url as NewsletterUrl;
use MailPoet\Subscribers\SubscribersRepository;
use MailPoet\WP\DateTime;

class NewslettersExporter {

  const LIMIT = 100;

  /** @var NewsletterUrl */
  private $newsletterUrl;

  /*** @var SubscribersRepository */
  private $subscribersRepository;

  /*** @var NewslettersRepository */
  private $newslettersRepository;

  /*** @var NewsletterStatisticsRepository */
  private $newsletterStatisticsRepository;

  public function __construct(
    NewsletterUrl $newsletterUrl,
    SubscribersRepository $subscribersRepository,
    NewslettersRepository $newslettersRepository,
    NewsletterStatisticsRepository $newsletterStatisticsRepository
  ) {
    $this->newsletterUrl = $newsletterUrl;
    $this->subscribersRepository = $subscribersRepository;
    $this->newslettersRepository = $newslettersRepository;
    $this->newsletterStatisticsRepository = $newsletterStatisticsRepository;
  }

  public function export($email, $page = 1) {
    $data = $this->exportSubscriber($this->subscribersRepository->findOneBy(['email' => trim($email)]), $page);
    return [
      'data' => $data,
      'done' => count($data) < self::LIMIT,
    ];
  }

  private function exportSubscriber(?SubscriberEntity $subscriber, $page) {
    if (!$subscriber) return [];

    $result = [];

    $statistics = $this->newsletterStatisticsRepository->getAllForSubscriber(
      $subscriber,
      self::LIMIT,
      self::LIMIT * ($page - 1)
    );

    $newsletters = $this->loadNewsletters($statistics);

    foreach ($statistics as $row) {
      $result[] = $this->exportNewsletter($row, $newsletters, $subscriber);
    }

    return $result;
  }

  private function exportNewsletter($statisticsRow, $newsletters, $subscriber) {
    $newsletterData = [];
    $newsletterData[] = [
      'name' => __('Email subject', 'mailpoet'),
      'value' => $statisticsRow['newsletter_rendered_subject'],
    ];
    $newsletterData[] = [
      'name' => __('Sent at', 'mailpoet'),
      'value' => $statisticsRow['sent_at']
        ? $statisticsRow['sent_at']->format(DateTime::DEFAULT_DATE_TIME_FORMAT)
        : '',
    ];
    if (!empty($statisticsRow['opened_at'])) {
      $newsletterData[] = [
        'name' => __('Opened', 'mailpoet'),
        'value' => 'Yes',
      ];
      $newsletterData[] = [
        'name' => __('Opened at', 'mailpoet'),
        'value' => $statisticsRow['opened_at']->format(DateTime::DEFAULT_DATE_TIME_FORMAT),
      ];
    } else {
      $newsletterData[] = [
        'name' => __('Opened', 'mailpoet'),
        'value' => __('No', 'mailpoet'),
      ];
    }
    if (isset($newsletters[$statisticsRow['newsletter_id']])) {
      $newsletterData[] = [
        'name' => __('Email preview', 'mailpoet'),
        'value' => $this->newsletterUrl->getViewInBrowserUrl(
          $newsletters[$statisticsRow['newsletter_id']],
          $subscriber
        ),
      ];
    }
    return [
      'group_id' => 'mailpoet-newsletters',
      'group_label' => __('MailPoet Emails Sent', 'mailpoet'),
      'item_id' => 'newsletter-' . $statisticsRow['newsletter_id'],
      'data' => $newsletterData,
    ];
  }

  private function loadNewsletters($statistics) {
    $newsletterIds = array_map(function ($statisticsRow) {
      return $statisticsRow['newsletter_id'];
    }, $statistics);

    if (empty($newsletterIds)) return [];

    $newsletters = $this->newslettersRepository->findBy(['id' => $newsletterIds]);

    $result = [];
    foreach ($newsletters as $newsletter) {
      $result[$newsletter->getId()] = $newsletter;
    }
    return $result;
  }
}
