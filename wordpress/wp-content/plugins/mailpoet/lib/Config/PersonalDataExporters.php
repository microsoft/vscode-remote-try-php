<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config;

if (!defined('ABSPATH')) exit;


use MailPoet\CustomFields\CustomFieldsRepository;
use MailPoet\DI\ContainerWrapper;
use MailPoet\Subscribers\ImportExport\PersonalDataExporters\NewsletterClicksExporter;
use MailPoet\Subscribers\ImportExport\PersonalDataExporters\NewsletterOpensExporter;
use MailPoet\Subscribers\ImportExport\PersonalDataExporters\NewslettersExporter;
use MailPoet\Subscribers\ImportExport\PersonalDataExporters\SegmentsExporter;
use MailPoet\Subscribers\ImportExport\PersonalDataExporters\SubscriberExporter;
use MailPoet\Subscribers\SubscribersRepository;
use MailPoet\WP\Functions as WPFunctions;

class PersonalDataExporters {

  /*** @var SubscribersRepository */
  private $subscribersRepository;

  /**
   * @var CustomFieldsRepository
   */
  private $customFieldsRepository;

  public function __construct(
    SubscribersRepository $subscribersRepository,
    CustomFieldsRepository $customFieldsRepository
  ) {
    $this->subscribersRepository = $subscribersRepository;
    $this->customFieldsRepository = $customFieldsRepository;
  }

  public function init() {
    WPFunctions::get()->addFilter('wp_privacy_personal_data_exporters', [$this, 'registerSubscriberExporter']);
    WPFunctions::get()->addFilter('wp_privacy_personal_data_exporters', [$this, 'registerSegmentsExporter']);
    WPFunctions::get()->addFilter('wp_privacy_personal_data_exporters', [$this, 'registerNewslettersExporter']);
    WPFunctions::get()->addFilter('wp_privacy_personal_data_exporters', [$this, 'registerNewsletterClicksExporter']);
    WPFunctions::get()->addFilter('wp_privacy_personal_data_exporters', [$this, 'registerNewsletterOpensExporter']);
  }

  public function registerSegmentsExporter($exporters) {
    $exporters[] = [
      'exporter_friendly_name' => __('MailPoet Lists', 'mailpoet'),
      'callback' => [new SegmentsExporter($this->subscribersRepository), 'export'],
    ];
    return $exporters;
  }

  public function registerSubscriberExporter($exporters) {
    $exporters[] = [
      'exporter_friendly_name' => __('MailPoet Subscriber Data', 'mailpoet'),
      'callback' => [new SubscriberExporter($this->subscribersRepository, $this->customFieldsRepository), 'export'],
    ];
    return $exporters;
  }

  public function registerNewslettersExporter($exporters) {
    $newsletterExporter = ContainerWrapper::getInstance()->get(NewslettersExporter::class);
    $exporters[] = [
      'exporter_friendly_name' => __('MailPoet Emails', 'mailpoet'),
      'callback' => [$newsletterExporter, 'export'],
    ];
    return $exporters;
  }

  public function registerNewsletterClicksExporter($exporters) {
    $exporters[] = [
      'exporter_friendly_name' => __('MailPoet Email Clicks', 'mailpoet'),
      'callback' => [ContainerWrapper::getInstance()->get(NewsletterClicksExporter::class), 'export'],
    ];
    return $exporters;
  }

  public function registerNewsletterOpensExporter($exporters) {
    $exporters[] = [
      'exporter_friendly_name' => __('MailPoet Email Opens', 'mailpoet'),
      'callback' => [ContainerWrapper::getInstance()->get(NewsletterOpensExporter::class), 'export'],
    ];
    return $exporters;
  }
}
