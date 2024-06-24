<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\WooCommerce;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Registry;
use MailPoet\Automation\Integrations\WooCommerce\Subjects\AbandonedCartSubject;
use MailPoet\Automation\Integrations\WooCommerce\Subjects\CustomerSubject;
use MailPoet\Automation\Integrations\WooCommerce\Subjects\OrderStatusChangeSubject;
use MailPoet\Automation\Integrations\WooCommerce\Subjects\OrderSubject;
use MailPoet\Automation\Integrations\WooCommerce\SubjectTransformers\WordPressUserSubjectToWooCommerceCustomerSubjectTransformer;
use MailPoet\Automation\Integrations\WooCommerce\Triggers\AbandonedCart\AbandonedCartTrigger;
use MailPoet\Automation\Integrations\WooCommerce\Triggers\BuysAProductTrigger;
use MailPoet\Automation\Integrations\WooCommerce\Triggers\BuysFromACategoryTrigger;
use MailPoet\Automation\Integrations\WooCommerce\Triggers\Orders\OrderCancelledTrigger;
use MailPoet\Automation\Integrations\WooCommerce\Triggers\Orders\OrderCompletedTrigger;
use MailPoet\Automation\Integrations\WooCommerce\Triggers\Orders\OrderCreatedTrigger;
use MailPoet\Automation\Integrations\WooCommerce\Triggers\Orders\OrderStatusChangedTrigger;

class WooCommerceIntegration {

  /** @var OrderStatusChangedTrigger */
  private $orderStatusChangedTrigger;

  /** @var OrderCreatedTrigger */
  private $orderCreatedTrigger;

  /** @var OrderCompletedTrigger */
  private $orderCompletedTrigger;

  private $orderCancelledTrigger;

  /** @var AbandonedCartTrigger  */
  private $abandonedCartTrigger;

  /** @var BuysAProductTrigger  */
  private $buysAProductTrigger;

  /** @var BuysFromACategoryTrigger */
  private $buysFromACategoryTrigger;

  /** @var AbandonedCartSubject */
  private $abandonedCartSubject;

  /** @var OrderStatusChangeSubject */
  private $orderStatusChangeSubject;

  /** @var OrderSubject */
  private $orderSubject;

  /** @var CustomerSubject */
  private $customerSubject;

  /** @var ContextFactory */
  private $contextFactory;

  /** @var WordPressUserSubjectToWooCommerceCustomerSubjectTransformer */
  private $wordPressUserToWooCommerceCustomerTransformer;

  /** @var WooCommerce */
  private $wooCommerce;

  public function __construct(
    OrderStatusChangedTrigger $orderStatusChangedTrigger,
    OrderCreatedTrigger $orderCreatedTrigger,
    OrderCompletedTrigger $orderCompletedTrigger,
    OrderCancelledTrigger $orderCancelledTrigger,
    AbandonedCartTrigger $abandonedCartTrigger,
    BuysAProductTrigger $buysAProductTrigger,
    BuysFromACategoryTrigger $buysFromACategoryTrigger,
    AbandonedCartSubject $abandonedCartSubject,
    OrderStatusChangeSubject $orderStatusChangeSubject,
    OrderSubject $orderSubject,
    CustomerSubject $customerSubject,
    ContextFactory $contextFactory,
    WordPressUserSubjectToWooCommerceCustomerSubjectTransformer $wordPressUserToWooCommerceCustomerTransformer,
    WooCommerce $wooCommerce
  ) {
    $this->orderStatusChangedTrigger = $orderStatusChangedTrigger;
    $this->orderCreatedTrigger = $orderCreatedTrigger;
    $this->orderCompletedTrigger = $orderCompletedTrigger;
    $this->orderCancelledTrigger = $orderCancelledTrigger;
    $this->abandonedCartTrigger = $abandonedCartTrigger;
    $this->buysAProductTrigger = $buysAProductTrigger;
    $this->buysFromACategoryTrigger = $buysFromACategoryTrigger;
    $this->abandonedCartSubject = $abandonedCartSubject;
    $this->orderStatusChangeSubject = $orderStatusChangeSubject;
    $this->orderSubject = $orderSubject;
    $this->customerSubject = $customerSubject;
    $this->contextFactory = $contextFactory;
    $this->wordPressUserToWooCommerceCustomerTransformer = $wordPressUserToWooCommerceCustomerTransformer;
    $this->wooCommerce = $wooCommerce;
  }

  public function register(Registry $registry): void {
    if (!$this->wooCommerce->isWooCommerceActive()) {
      return;
    }

    $registry->addContextFactory('woocommerce', function () {
      return $this->contextFactory->getContextData();
    });

    $registry->addSubject($this->abandonedCartSubject);
    $registry->addSubject($this->orderSubject);
    $registry->addSubject($this->orderStatusChangeSubject);
    $registry->addSubject($this->customerSubject);
    $registry->addTrigger($this->orderStatusChangedTrigger);
    $registry->addTrigger($this->orderCreatedTrigger);
    $registry->addTrigger($this->orderCompletedTrigger);
    $registry->addTrigger($this->orderCancelledTrigger);
    $registry->addTrigger($this->abandonedCartTrigger);
    $registry->addTrigger($this->buysAProductTrigger);
    $registry->addTrigger($this->buysFromACategoryTrigger);
    $registry->addSubjectTransformer($this->wordPressUserToWooCommerceCustomerTransformer);
  }
}
