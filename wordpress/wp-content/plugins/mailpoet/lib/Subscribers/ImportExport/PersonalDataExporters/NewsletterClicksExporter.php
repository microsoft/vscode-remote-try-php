<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Subscribers\ImportExport\PersonalDataExporters;

if (!defined('ABSPATH')) exit;


use MailPoet\Statistics\StatisticsClicksRepository;

class NewsletterClicksExporter extends NewsletterStatsBaseExporter {
  protected $statsClassName = StatisticsClicksRepository::class;

  protected function getEmailStats(array $row) {
    $newsletterData = [];
    $newsletterData[] = [
      'name' => __('Email subject', 'mailpoet'),
      'value' => $row['newsletterRenderedSubject'],
    ];
    $newsletterData[] = [
      'name' => __('Timestamp of the click event', 'mailpoet'),
      'value' => $row['createdAt']->format("Y-m-d H:i:s"),
    ];
    $newsletterData[] = [
      'name' => __('URL', 'mailpoet'),
      'value' => $row['url'],
    ];

    if (!is_null($row['userAgent'])) {
      $userAgent = $row['userAgent'];
    } else {
      $userAgent = __('Unknown', 'mailpoet');
    }

    $newsletterData[] = [
      'name' => __('User-agent', 'mailpoet'),
      'value' => $userAgent,
    ];

    return [
      'group_id' => 'mailpoet-newsletter-clicks',
      'group_label' => __('MailPoet Emails Clicks', 'mailpoet'),
      'item_id' => 'newsletter-' . $row['id'],
      'data' => $newsletterData,
    ];
  }
}
