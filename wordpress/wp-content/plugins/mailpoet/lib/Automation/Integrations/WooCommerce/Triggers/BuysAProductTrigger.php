<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\WooCommerce\Triggers;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Control\FilterHandler;
use MailPoet\Automation\Engine\Data\Field;
use MailPoet\Automation\Engine\Data\Filter;
use MailPoet\Automation\Engine\Data\FilterGroup;
use MailPoet\Automation\Engine\Data\StepRunArgs;
use MailPoet\Automation\Engine\Data\StepValidationArgs;
use MailPoet\Automation\Engine\Data\Subject;
use MailPoet\Automation\Engine\Hooks;
use MailPoet\Automation\Engine\Integration\Trigger;
use MailPoet\Automation\Engine\Storage\AutomationRunStorage;
use MailPoet\Automation\Engine\WordPress;
use MailPoet\Automation\Integrations\Core\Filters\EnumArrayFilter;
use MailPoet\Automation\Integrations\Core\Filters\EnumFilter;
use MailPoet\Automation\Integrations\WooCommerce\Subjects\CustomerSubject;
use MailPoet\Automation\Integrations\WooCommerce\Subjects\OrderStatusChangeSubject;
use MailPoet\Automation\Integrations\WooCommerce\Subjects\OrderSubject;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;
use MailPoet\WooCommerce\Helper as WooCommerceHelper;

class BuysAProductTrigger implements Trigger {
  public const KEY = 'woocommerce:buys-a-product';

  /** @var WordPress */
  private $wp;

  /** @var WooCommerceHelper  */
  private $wc;

  /** @var AutomationRunStorage  */
  private $automationRunStorage;

  /** @var FilterHandler */
  private $filterHandler;

  public function __construct(
    WordPress $wp,
    WooCommerceHelper $wc,
    AutomationRunStorage $automationRunStorage,
    FilterHandler $filterHandler
  ) {
    $this->wp = $wp;
    $this->wc = $wc;
    $this->automationRunStorage = $automationRunStorage;
    $this->filterHandler = $filterHandler;
  }

  public function getKey(): string {
    return self::KEY;
  }

  public function getName(): string {
    // translators: automation trigger title
    return __('Customer buys a product', 'mailpoet');
  }

  public function getArgsSchema(): ObjectSchema {
    return Builder::object([
      'product_ids' => Builder::array(
        Builder::integer()
      )->minItems(1)->required(),
      'to' => Builder::string()->required()->default('wc-completed'),
    ]);
  }

  public function getSubjectKeys(): array {
    return [
      OrderSubject::KEY,
      OrderStatusChangeSubject::KEY,
      CustomerSubject::KEY,
    ];
  }

  public function validate(StepValidationArgs $args): void {
  }

  public function registerHooks(): void {
    $this->wp->addAction(
      'woocommerce_order_status_changed',
      [
        $this,
        'handle',
      ],
      10,
      3
    );
  }

  /**
   * @param int $orderId
   * @param string $from
   * @param string $to
   * @return void
   */
  public function handle($orderId, $from, $to): void {
    $order = $this->wc->wcGetOrder($orderId);
    if (!$order) {
      return;
    }
    $this->wp->doAction(Hooks::TRIGGER, $this, [
      new Subject(OrderSubject::KEY, ['order_id' => $orderId]),
      new Subject(CustomerSubject::KEY, ['customer_id' => $order->get_customer_id(), 'order_id' => $orderId]),
      new Subject(OrderStatusChangeSubject::KEY, ['from' => $from, 'to' => $to]),
    ]);
  }

  public function isTriggeredBy(StepRunArgs $args): bool {

    //Trigger the run only once.
    $orderSubjectData = $args->getSingleSubjectEntryByClass(OrderSubject::class)->getSubjectData();
    if ($this->automationRunStorage->getCountByAutomationAndSubject($args->getAutomation(), $orderSubjectData) > 0) {
      return false;
    }
    $group = new FilterGroup(
      '',
      FilterGroup::OPERATOR_AND,
      $this->getFilters($args)
    );
    return $this->filterHandler->matchesGroup($group, $args);
  }

  protected function getFilters(StepRunArgs $args): array {
    $triggerArgs = $args->getStep()->getArgs();
    $filters = [
      Filter::fromArray([
        'id' => '',
        'field_type' => Field::TYPE_ENUM_ARRAY,
        'field_key' => 'woocommerce:order:products',
        'condition' => EnumArrayFilter::CONDITION_MATCHES_ANY_OF,
        'args' => [
          'value' => $triggerArgs['product_ids'] ?? [],
        ],
      ]),
    ];
    $status = str_replace('wc-', '', $triggerArgs['to'] ?? 'completed');
    if ($status === 'any') {
      return $filters;
    }

    $filters[] = Filter::fromArray([
      'id' => '',
      'field_type' => Field::TYPE_ENUM,
      'field_key' => 'woocommerce:order:status',
      'condition' => EnumFilter::IS_ANY_OF,
      'args' => [
        'value' => [$status],
      ],
    ]);
    return $filters;
  }
}
