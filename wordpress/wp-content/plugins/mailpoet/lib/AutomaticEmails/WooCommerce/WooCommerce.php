<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\AutomaticEmails\WooCommerce;

if (!defined('ABSPATH')) exit;


use MailPoet\AutomaticEmails\AutomaticEmails;
use MailPoet\WooCommerce\Helper as WooCommerceHelper;
use MailPoet\WP\Functions as WPFunctions;
use MailPoet\WP\Notice;

class WooCommerce {
  const SLUG = 'woocommerce';
  const EVENTS_FILTER = 'mailpoet_woocommerce_events';

  /** @var WooCommerceHelper */
  private $woocommerceHelper;

  /** @var string[] */
  public $availableEvents = [
    'AbandonedCart',
    'FirstPurchase',
    'PurchasedInCategory',
    'PurchasedProduct',
  ];

  /** @var bool */
  private $woocommerceEnabled;

  /** @var WPFunctions */
  private $wp;

  /** @var WooCommerceEventFactory */
  private $eventFactory;

  public function __construct(
    WPFunctions $wp,
    WooCommerceHelper $woocommerceHelper,
    WooCommerceEventFactory $eventFactory
  ) {
    $this->wp = $wp;
    $this->woocommerceHelper = $woocommerceHelper;
    $this->woocommerceEnabled = $this->isWoocommerceEnabled();
    $this->eventFactory = $eventFactory;
  }

  public function init() {
    $this->wp->addFilter(
      AutomaticEmails::FILTER_PREFIX . self::SLUG,
      [
        $this,
        'setupGroup',
      ]
    );
    $this->wp->addFilter(
      self::EVENTS_FILTER,
      [
        $this,
        'setupEvents',
      ]
    );
  }

  public function setupGroup() {
    return [
      'slug' => self::SLUG,
      'title' => __('WooCommerce', 'mailpoet'),
      'description' => __('Automatically send an email based on your customers’ purchase behavior. Enhance your customer service and start increasing sales with WooCommerce follow up emails.', 'mailpoet'),
      'events' => $this->wp->applyFilters(self::EVENTS_FILTER, []),
    ];
  }

  public function setupEvents($events) {
    $customEventDetails = (!$this->woocommerceEnabled) ? [
      'actionButtonTitle' => __('WooCommerce is required', 'mailpoet'),
      'actionButtonLink' => 'https://wordpress.org/plugins/woocommerce/',
    ] : [];

    foreach ($this->availableEvents as $event) {
      $eventInstance = in_array($event, $this->availableEvents, true)
        ? $this->eventFactory->createEvent($event)
        : null;

      if (!$eventInstance) {
        $this->displayEventWarning($event);
        continue;
      }

      if (method_exists($eventInstance, 'init')) {
        $eventInstance->init();
      } else {
        $this->displayEventWarning($event);
        continue;
      }

      if (method_exists($eventInstance, 'getEventDetails')) {
        $eventDetails = array_merge($eventInstance->getEventDetails(), $customEventDetails);
      } else {
        $this->displayEventWarning($event);
        continue;
      }
      $events[] = $eventDetails;
    }

    return $events;
  }

  public function isWoocommerceEnabled() {
    return $this->woocommerceHelper->isWooCommerceActive();
  }

  private function displayEventWarning($event) {
    $notice = sprintf(
      '%s %s',
      // translators: %s is the name of the event.
      sprintf(__('WooCommerce %s event is misconfigured.', 'mailpoet'), $event),
      __('Please contact our technical support for assistance.', 'mailpoet')
    );
    Notice::displayWarning($notice);
  }
}
