<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Subscribers\ImportExport\PersonalDataExporters;

if (!defined('ABSPATH')) exit;


use MailPoet\Statistics\StatisticsOpensRepository;

class NewsletterOpensExporter extends NewsletterStatsBaseExporter {
  protected $statsClassName = StatisticsOpensRepository::class;

  protected function getEmailStats(array $row): array {
    $newsletterData = [];
    $newsletterData[] = [
      'name' => __('Email subject', 'mailpoet'),
      'value' => $row['newsletterRenderedSubject'],
    ];
    $newsletterData[] = [
      'name' => __('Timestamp of the open event', 'mailpoet'),
      'value' => $row['createdAt']->format("Y-m-d H:i:s"),
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
      'group_id' => 'mailpoet-newsletter-opens',
      'group_label' => __('MailPoet Emails Opens', 'mailpoet'),
      'item_id' => 'newsletter-' . $row['id'],
      'data' => $newsletterData,
    ];
  }
}
