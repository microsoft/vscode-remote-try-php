<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\AutomaticEmails\WooCommerce\Events;

if (!defined('ABSPATH')) exit;


use MailPoet\AutomaticEmails\WooCommerce\WooCommerce;
use MailPoet\DI\ContainerWrapper;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Entities\SendingQueueEntity;
use MailPoet\Entities\SubscriberEntity;
use MailPoet\Logging\LoggerFactory;
use MailPoet\Newsletter\AutomaticEmailsRepository;
use MailPoet\Newsletter\Scheduler\AutomaticEmailScheduler;
use MailPoet\Subscribers\SubscribersRepository;
use MailPoet\WooCommerce\Helper as WCHelper;
use MailPoet\WP\Functions as WPFunctions;

class FirstPurchase {
  const SLUG = 'woocommerce_first_purchase';
  const ORDER_TOTAL_SHORTCODE = '[woocommerce:order_total]';
  const ORDER_DATE_SHORTCODE = '[woocommerce:order_date]';
  /**
   * @var \MailPoet\WooCommerce\Helper
   */
  private $helper;

  /** @var AutomaticEmailScheduler */
  private $scheduler;

  /** @var LoggerFactory */
  private $loggerFactory;

  /** @var AutomaticEmailsRepository */
  private $automaticEmailsRepository;

  /** @var SubscribersRepository */
  private $subscribersRepository;

  public function __construct(
    WCHelper $helper = null
  ) {
    if ($helper === null) {
      $helper = ContainerWrapper::getInstance()->get(WCHelper::class);
    }
    $this->helper = $helper;
    $this->scheduler = ContainerWrapper::getInstance()->get(AutomaticEmailScheduler::class);
    $this->loggerFactory = LoggerFactory::getInstance();
    $this->automaticEmailsRepository = ContainerWrapper::getInstance()->get(AutomaticEmailsRepository::class);
    $this->subscribersRepository = ContainerWrapper::getInstance()->get(SubscribersRepository::class);
  }

  public function init() {
    WPFunctions::get()->addFilter('mailpoet_newsletter_shortcode', [
      $this,
      'handleOrderTotalShortcode',
    ], 10, 4);
    WPFunctions::get()->addFilter('mailpoet_newsletter_shortcode', [
      $this,
      'handleOrderDateShortcode',
    ], 10, 4);

    // We have to use a set of states because an order state after checkout differs for different payment methods
    $acceptedOrderStates = WPFunctions::get()->applyFilters('mailpoet_first_purchase_order_states', ['completed', 'processing']);

    foreach ($acceptedOrderStates as $state) {
      WPFunctions::get()->addAction('woocommerce_order_status_' . $state, [
        $this,
        'scheduleEmailWhenOrderIsPlaced',
      ], 10, 1);
    }
  }

  public function getEventDetails() {
    return [
      'slug' => self::SLUG,
      'title' => __('First Purchase', 'mailpoet'),
      'description' => __('Let MailPoet send an email to customers who make their first purchase.', 'mailpoet'),
      'listingScheduleDisplayText' => __('Email sent when a customer makes their first purchase.', 'mailpoet'),
      'afterDelayText' => __('after the first purchase', 'mailpoet'),
      'badge' => [
        'text' => __('Must-have', 'mailpoet'),
        'style' => 'red',
      ],
      'timeDelayValues' => [
        'immediate' => [
          'text' => __('immediately', 'mailpoet'),
          'displayAfterTimeNumberField' => false,
        ],
        'minutes' => [
          'text' => __('minute(s)', 'mailpoet'),
          'displayAfterTimeNumberField' => true,
        ],
        'hours' => [
          'text' => __('hour(s)', 'mailpoet'),
          'displayAfterTimeNumberField' => true,
        ],
        'days' => [
          'text' => __('day(s)', 'mailpoet'),
          'displayAfterTimeNumberField' => true,
        ],
        'weeks' => [
          'text' => __('week(s)', 'mailpoet'),
          'displayAfterTimeNumberField' => true,
        ],
      ],
      'shortcodes' => [
        [
          'text' => __('Order amount', 'mailpoet'),
          'shortcode' => self::ORDER_TOTAL_SHORTCODE,
        ],
        [
          'text' => __('Order date', 'mailpoet'),
          'shortcode' => self::ORDER_DATE_SHORTCODE,
        ],
      ],
    ];
  }

  public function handleOrderDateShortcode($shortcode, $newsletter, $subscriber, $queue) {
    $result = $shortcode;
    if ($shortcode === self::ORDER_DATE_SHORTCODE) {
      $defaultValue = WPFunctions::get()->dateI18n(get_option('date_format'));
      if (!$queue) {
        $result = $defaultValue;
      } else {
        $meta = $queue->getMeta();
        $result = (!empty($meta['order_date'])) ? WPFunctions::get()->dateI18n(get_option('date_format'), $meta['order_date']) : $defaultValue;
      }
    }
    $this->loggerFactory->getLogger(self::SLUG)->info(
      'handleOrderDateShortcode called',
      [
        'newsletter_id' => ($newsletter instanceof NewsletterEntity) ? $newsletter->getId() : null,
        'subscriber_id' => ($subscriber instanceof SubscriberEntity) ? $subscriber->getId() : null,
        'task_id' => ($queue instanceof SendingQueueEntity) ? (($task = $queue->getTask()) ? $task->getId() : null) : null,
        'shortcode' => $shortcode,
        'result' => $result,
      ]
    );
    return $result;
  }

  public function handleOrderTotalShortcode($shortcode, $newsletter, $subscriber, $queue) {
    $result = $shortcode;
    if ($shortcode === self::ORDER_TOTAL_SHORTCODE) {
      $defaultValue = $this->helper->wcPrice(0);
      if (!$queue) {
        $result = $defaultValue;
      } else {
        $meta = $queue->getMeta();
        $result = (!empty($meta['order_amount'])) ? $this->helper->wcPrice($meta['order_amount']) : $defaultValue;
      }
    }
    $this->loggerFactory->getLogger(self::SLUG)->info(
      'handleOrderTotalShortcode called',
      [
        'newsletter_id' => ($newsletter instanceof NewsletterEntity) ? $newsletter->getId() : null,
        'subscriber_id' => ($subscriber instanceof SubscriberEntity) ? $subscriber->getId() : null,
        'task_id' => ($queue instanceof SendingQueueEntity) ? (($task = $queue->getTask()) ? $task->getId() : null) : null,
        'shortcode' => $shortcode,
        'result' => $result,
      ]
    );
    return $result;
  }

  public function scheduleEmailWhenOrderIsPlaced($orderId) {
    $orderDetails = $this->helper->wcGetOrder($orderId);
    if (!$orderDetails || !$orderDetails->get_billing_email()) {
      $this->loggerFactory->getLogger(self::SLUG)->info(
        'Email not scheduled because the order customer was not found',
        ['order_id' => $orderId]
      );
      return;
    }

    $customerEmail = $orderDetails->get_billing_email();
    $customerOrderCount = $this->getCustomerOrderCount($customerEmail);
    if ($customerOrderCount > 1) {
      $this->loggerFactory->getLogger(self::SLUG)->info(
        'Email not scheduled because this is not the first order of the customer',
        [
          'order_id' => $orderId,
          'customer_email' => $customerEmail,
          'order_count' => $customerOrderCount,
        ]
      );
      return;
    }

    $meta = [
      'order_amount' => $orderDetails->get_total(),
      'order_date' => $orderDetails->get_date_created()->getTimestamp(),
      'order_id' => $orderDetails->get_id(),
    ];

    $subscriber = $this->subscribersRepository->getWooCommerceSegmentSubscriber($customerEmail);

    if (!$subscriber instanceof SubscriberEntity) {
      $this->loggerFactory->getLogger(self::SLUG)->info(
        'Email not scheduled because the customer was not found as WooCommerce list subscriber',
        ['order_id' => $orderId, 'customer_email' => $customerEmail]
      );
      return;
    }

    $checkEmailWasNotScheduled = function (NewsletterEntity $newsletter) use ($subscriber) {
      return !$this->automaticEmailsRepository->wasScheduledForSubscriber((int)$newsletter->getId(), (int)$subscriber->getId());
    };

    $this->loggerFactory->getLogger(self::SLUG)->info(
      'Email scheduled',
      [
        'order_id' => $orderId,
        'customer_email' => $customerEmail,
        'subscriber_id' => $subscriber->getId(),
      ]
    );
    $this->scheduler->scheduleAutomaticEmail(WooCommerce::SLUG, self::SLUG, $checkEmailWasNotScheduled, $subscriber, $meta);
  }

  public function getCustomerOrderCount($customerEmail) {
    // registered user
    $user = WPFunctions::get()->getUserBy('email', $customerEmail);
    if ($user) {
      return $this->helper->wcGetCustomerOrderCount($user->ID);
    }
    // guest user
    return $this->getGuestCustomerOrderCountByEmail($customerEmail);
  }

  private function getGuestCustomerOrderCountByEmail(string $customerEmail): int {
    $ordersCount = $this->helper->wcGetOrders(
      [
        'status' => 'all',
        'type' => 'shop_order',
        'billing_email' => $customerEmail,
        'limit' => 1,
        'return' => 'ids',
        'paginate' => true,
      ]
    )->total;
    return intval($ordersCount);
  }
}
