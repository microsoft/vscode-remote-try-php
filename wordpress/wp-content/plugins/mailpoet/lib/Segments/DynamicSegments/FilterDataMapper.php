<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Segments\DynamicSegments;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\DynamicSegmentFilterData;
use MailPoet\Segments\DynamicSegments\Exceptions\InvalidFilterException;
use MailPoet\Segments\DynamicSegments\Filters\AutomationsEvents;
use MailPoet\Segments\DynamicSegments\Filters\DateFilterHelper;
use MailPoet\Segments\DynamicSegments\Filters\EmailAction;
use MailPoet\Segments\DynamicSegments\Filters\EmailActionClickAny;
use MailPoet\Segments\DynamicSegments\Filters\EmailOpensAbsoluteCountAction;
use MailPoet\Segments\DynamicSegments\Filters\EmailsReceived;
use MailPoet\Segments\DynamicSegments\Filters\FilterHelper;
use MailPoet\Segments\DynamicSegments\Filters\MailPoetCustomFields;
use MailPoet\Segments\DynamicSegments\Filters\NumberOfClicks;
use MailPoet\Segments\DynamicSegments\Filters\SubscriberDateField;
use MailPoet\Segments\DynamicSegments\Filters\SubscriberScore;
use MailPoet\Segments\DynamicSegments\Filters\SubscriberSegment;
use MailPoet\Segments\DynamicSegments\Filters\SubscriberSubscribedViaForm;
use MailPoet\Segments\DynamicSegments\Filters\SubscriberTag;
use MailPoet\Segments\DynamicSegments\Filters\SubscriberTextField;
use MailPoet\Segments\DynamicSegments\Filters\WooCommerceAverageSpent;
use MailPoet\Segments\DynamicSegments\Filters\WooCommerceCategory;
use MailPoet\Segments\DynamicSegments\Filters\WooCommerceCountry;
use MailPoet\Segments\DynamicSegments\Filters\WooCommerceCustomerTextField;
use MailPoet\Segments\DynamicSegments\Filters\WooCommerceFirstOrder;
use MailPoet\Segments\DynamicSegments\Filters\WooCommerceMembership;
use MailPoet\Segments\DynamicSegments\Filters\WooCommerceNumberOfOrders;
use MailPoet\Segments\DynamicSegments\Filters\WooCommerceNumberOfReviews;
use MailPoet\Segments\DynamicSegments\Filters\WooCommerceProduct;
use MailPoet\Segments\DynamicSegments\Filters\WooCommercePurchaseDate;
use MailPoet\Segments\DynamicSegments\Filters\WooCommercePurchasedWithAttribute;
use MailPoet\Segments\DynamicSegments\Filters\WooCommerceSingleOrderValue;
use MailPoet\Segments\DynamicSegments\Filters\WooCommerceSubscription;
use MailPoet\Segments\DynamicSegments\Filters\WooCommerceTag;
use MailPoet\Segments\DynamicSegments\Filters\WooCommerceTotalSpent;
use MailPoet\Segments\DynamicSegments\Filters\WooCommerceUsedCouponCode;
use MailPoet\Segments\DynamicSegments\Filters\WooCommerceUsedPaymentMethod;
use MailPoet\Segments\DynamicSegments\Filters\WooCommerceUsedShippingMethod;
use MailPoet\WP\Functions as WPFunctions;

class FilterDataMapper {
  private WPFunctions $wp;

  private DateFilterHelper $dateFilterHelper;

  private WooCommerceNumberOfReviews $wooCommerceNumberOfReviews;

  private FilterHelper $filterHelper;

  private WooCommerceUsedCouponCode $wooCommerceUsedCouponCode;

  private WooCommerceTag $wooCommerceTag;

  private WooCommercePurchasedWithAttribute $wooCommercePurchasedWithAttribute;

  public function __construct(
    WPFunctions $wp,
    DateFilterHelper $dateFilterHelper,
    FilterHelper $filterHelper,
    WooCommerceNumberOfReviews $wooCommerceNumberOfReviews,
    WooCommerceUsedCouponCode $wooCommerceUsedCouponCode,
    WooCommercePurchasedWithAttribute $wooCommercePurchasedWithAttribute,
    WooCommerceTag $wooCommerceTag
  ) {
    $this->wp = $wp;
    $this->dateFilterHelper = $dateFilterHelper;
    $this->filterHelper = $filterHelper;
    $this->wooCommerceNumberOfReviews = $wooCommerceNumberOfReviews;
    $this->wooCommerceUsedCouponCode = $wooCommerceUsedCouponCode;
    $this->wooCommercePurchasedWithAttribute = $wooCommercePurchasedWithAttribute;
    $this->wooCommerceTag = $wooCommerceTag;
  }

  /**
   * @param array $data
   * @return DynamicSegmentFilterData[]
   */
  public function map(array $data = []): array {
    if (!isset($data['filters']) || count($data['filters'] ?? []) < 1) {
      throw new InvalidFilterException('Filters are missing', InvalidFilterException::MISSING_FILTER);
    }
    $processFilter = function ($filter, $data) {
      $filter['connect'] = $data['filters_connect'] ?? DynamicSegmentFilterData::CONNECT_TYPE_AND;
      return $this->createFilter($filter);
    };
    $wpFilterName = 'mailpoet_dynamic_segments_filters_map';
    if ($this->wp->hasFilter($wpFilterName)) {
      return $this->wp->applyFilters($wpFilterName, $data, $processFilter);
    }
    $filter = reset($data['filters']);
    return [$processFilter($filter, $data)];
  }

  private function createFilter(array $filterData): DynamicSegmentFilterData {
    if (isset($filterData['days']) && !isset($filterData['timeframe'])) {
      // Backwards compatibility for filters created before time period component had "over all time" option
      $filterData['timeframe'] = DynamicSegmentFilterData::TIMEFRAME_IN_THE_LAST;
    }
    switch ($this->getSegmentType($filterData)) {
      case DynamicSegmentFilterData::TYPE_AUTOMATIONS:
        return $this->createAutomations($filterData);
      case DynamicSegmentFilterData::TYPE_USER_ROLE:
        return $this->createSubscriber($filterData);
      case DynamicSegmentFilterData::TYPE_EMAIL:
        return $this->createEmail($filterData);
      case DynamicSegmentFilterData::TYPE_WOOCOMMERCE:
        return $this->createWooCommerce($filterData);
      case DynamicSegmentFilterData::TYPE_WOOCOMMERCE_MEMBERSHIP:
        return $this->createWooCommerceMembership($filterData);
      case DynamicSegmentFilterData::TYPE_WOOCOMMERCE_SUBSCRIPTION:
        return $this->createWooCommerceSubscription($filterData);
      default:
        throw new InvalidFilterException('Invalid type', InvalidFilterException::INVALID_TYPE);
    }
  }

  /**
   * @throws InvalidFilterException
   */
  private function getSegmentType(array $data): string {
    if (!isset($data['segmentType'])) {
      throw new InvalidFilterException('Segment type is not set', InvalidFilterException::MISSING_TYPE);
    }
    return $data['segmentType'];
  }

  private function createAutomations(array $data): DynamicSegmentFilterData {
    if (empty($data['action'])) {
      throw new InvalidFilterException('Missing automations filter action', InvalidFilterException::MISSING_ACTION);
    }

    if (in_array($data['action'], AutomationsEvents::SUPPORTED_ACTIONS)) {
      if (
        !isset($data['operator']) || !in_array($data['operator'], [
          DynamicSegmentFilterData::OPERATOR_ANY,
          DynamicSegmentFilterData::OPERATOR_ALL,
          DynamicSegmentFilterData::OPERATOR_NONE,
        ])
      ) {
        throw new InvalidFilterException('Missing operator', InvalidFilterException::MISSING_OPERATOR);
      }
      if (
        !isset($data['automation_ids'])
        || !is_array($data['automation_ids'])
        || count($data['automation_ids']) < 1
      ) {
        throw new InvalidFilterException('Missing automation IDs', InvalidFilterException::MISSING_VALUE);
      }
      return new DynamicSegmentFilterData(DynamicSegmentFilterData::TYPE_AUTOMATIONS, $data['action'], [
        'action' => $data['action'],
        'automation_ids' => $data['automation_ids'],
        'operator' => $data['operator'],
        'connect' => $data['connect'],
      ]);
    }

    throw new InvalidFilterException('Unknown automations action', InvalidFilterException::MISSING_ACTION);
  }

  private function createSubscriber(array $data): DynamicSegmentFilterData {
    if (empty($data['action'])) {
      $data['action'] = DynamicSegmentFilterData::TYPE_USER_ROLE;
    }
    if ($data['action'] === SubscriberScore::TYPE) {
      if (!isset($data['value'])) {
        throw new InvalidFilterException('Missing engagement score value', InvalidFilterException::MISSING_VALUE);
      }
      return new DynamicSegmentFilterData(DynamicSegmentFilterData::TYPE_USER_ROLE, $data['action'], [
        'value' => $data['value'],
        'operator' => $data['operator'] ?? SubscriberScore::HIGHER_THAN,
        'connect' => $data['connect'],
      ]);
    }
    if ($data['action'] === SubscriberSegment::TYPE) {
      if (empty($data['segments'])) {
        throw new InvalidFilterException('Missing segments', InvalidFilterException::MISSING_VALUE);
      }
      return new DynamicSegmentFilterData(DynamicSegmentFilterData::TYPE_USER_ROLE, $data['action'], [
        'segments' => array_map(function ($segmentId) {
          return intval($segmentId);
        }, $data['segments']),
        'operator' => $data['operator'] ?? DynamicSegmentFilterData::OPERATOR_ANY,
        'connect' => $data['connect'],
      ]);
    }
    if ($data['action'] === MailPoetCustomFields::TYPE) {
      if (empty($data['custom_field_id'])) {
        throw new InvalidFilterException('Missing custom field id', InvalidFilterException::MISSING_VALUE);
      }
      if (empty($data['custom_field_type'])) {
        throw new InvalidFilterException('Missing custom field type', InvalidFilterException::MISSING_VALUE);
      }
      if (!isset($data['value'])) {
        throw new InvalidFilterException('Missing value', InvalidFilterException::MISSING_VALUE);
      }
      $filterData = [
        'value' => $data['value'],
        'custom_field_id' => $data['custom_field_id'],
        'custom_field_type' => $data['custom_field_type'],
        'connect' => $data['connect'],
      ];
      if (!empty($data['date_type'])) {
        $filterData['date_type'] = $data['date_type'];
      }
      if (!empty($data['operator'])) {
        $filterData['operator'] = $data['operator'];
      }
      return new DynamicSegmentFilterData(DynamicSegmentFilterData::TYPE_USER_ROLE, $data['action'], $filterData);
    }
    if ($data['action'] === SubscriberTag::TYPE) {
      if (empty($data['tags'])) {
        throw new InvalidFilterException('Missing tags', InvalidFilterException::MISSING_VALUE);
      }
      return new DynamicSegmentFilterData(DynamicSegmentFilterData::TYPE_USER_ROLE, $data['action'], [
        'tags' => array_map(function ($tagId) {
          return intval($tagId);
        }, $data['tags']),
        'operator' => $data['operator'] ?? DynamicSegmentFilterData::OPERATOR_ANY,
        'connect' => $data['connect'],
      ]);
    }
    if ($data['action'] === SubscriberSubscribedViaForm::TYPE) {
      if (!isset($data['form_ids']) || empty($data['form_ids'])) {
        throw new InvalidFilterException('Missing at least one form ID', InvalidFilterException::MISSING_VALUE);
      }
      if (!isset($data['operator']) || !in_array($data['operator'], [DynamicSegmentFilterData::OPERATOR_ANY, DynamicSegmentFilterData::OPERATOR_NONE])) {
        throw new InvalidFilterException('Missing valid operator', InvalidFilterException::MISSING_VALUE);
      }
      return new DynamicSegmentFilterData(DynamicSegmentFilterData::TYPE_USER_ROLE, $data['action'], [
        'form_ids' => array_map(function($formId) {
          return intval($formId);
        }, $data['form_ids']),
        'operator' => $data['operator'],
        'connect' => $data['connect'],
      ]);
    }
    if (in_array($data['action'], SubscriberTextField::TYPES)) {
      if (empty($data['value'])) {
        throw new InvalidFilterException('Missing value', InvalidFilterException::MISSING_VALUE);
      }
      if (empty($data['operator'])) {
        throw new InvalidFilterException('Missing operator', InvalidFilterException::MISSING_OPERATOR);
      }
      if (!in_array($data['operator'], DynamicSegmentFilterData::TEXT_FIELD_OPERATORS)) {
        throw new InvalidFilterException('Invalid operator', InvalidFilterException::MISSING_OPERATOR);
      }
      return new DynamicSegmentFilterData(DynamicSegmentFilterData::TYPE_USER_ROLE, $data['action'], [
        'value' => $data['value'],
        'operator' => $data['operator'],
        'action' => $data['action'],
        'connect' => $data['connect'],
      ]);
    }
    if (in_array($data['action'], SubscriberDateField::TYPES)) {
      if (empty($data['value'])) {
        throw new InvalidFilterException('Missing date value', InvalidFilterException::MISSING_VALUE);
      }
      if (empty($data['operator'])) {
        throw new InvalidFilterException('Missing operator', InvalidFilterException::MISSING_OPERATOR);
      }
      if (!in_array($data['operator'], $this->dateFilterHelper->getValidOperators())) {
        throw new InvalidFilterException('Invalid operator', InvalidFilterException::MISSING_OPERATOR);
      }
      return new DynamicSegmentFilterData(DynamicSegmentFilterData::TYPE_USER_ROLE, $data['action'], [
        'value' => $data['value'],
        'operator' => $data['operator'],
        'connect' => $data['connect'],
      ]);
    }
    if (empty($data['wordpressRole'])) {
      throw new InvalidFilterException('Missing role', InvalidFilterException::MISSING_ROLE);
    }
    return new DynamicSegmentFilterData(DynamicSegmentFilterData::TYPE_USER_ROLE, $data['action'], [
      'wordpressRole' => $data['wordpressRole'],
      'operator' => $data['operator'] ?? DynamicSegmentFilterData::OPERATOR_ANY,
      'connect' => $data['connect'],
    ]);
  }

  /**
   * @throws InvalidFilterException
   */
  private function createEmail(array $data): DynamicSegmentFilterData {
    if (empty($data['action'])) {
      throw new InvalidFilterException('Missing action', InvalidFilterException::MISSING_ACTION);
    }
    if (!in_array($data['action'], EmailAction::ALLOWED_ACTIONS)) {
      throw new InvalidFilterException('Invalid email action', InvalidFilterException::INVALID_EMAIL_ACTION);
    }
    if (
      ($data['action'] === EmailOpensAbsoluteCountAction::TYPE)
      || ($data['action'] === EmailOpensAbsoluteCountAction::MACHINE_TYPE)
    ) {
      return $this->createEmailOpensAbsoluteCount($data);
    }
    if ($data['action'] === EmailActionClickAny::TYPE) {
      return new DynamicSegmentFilterData(DynamicSegmentFilterData::TYPE_EMAIL, $data['action'], [
        'connect' => $data['connect'],
      ]);
    }

    $filterData = [
      'connect' => $data['connect'],
      'operator' => $data['operator'] ?? DynamicSegmentFilterData::OPERATOR_ANY,
    ];

    if ($data['action'] === EmailsReceived::ACTION) {
      $this->filterHelper->validateDaysPeriodData($data);
      if (!isset($data['emails'])) {
        throw new InvalidFilterException('Missing email count value', InvalidFilterException::MISSING_VALUE);
      }
      $filterData['emails'] = $data['emails'];
      $filterData['operator'] = $data['operator'];
      $filterData['timeframe'] = $data['timeframe'];
      $filterData['connect'] = $data['connect'];
      $filterData['days'] = $data['days'] ?? 0;
      return new DynamicSegmentFilterData(DynamicSegmentFilterData::TYPE_EMAIL, $data['action'], $filterData);
    }

    if ($data['action'] === NumberOfClicks::ACTION) {
      $this->filterHelper->validateDaysPeriodData($data);
      if (!isset($data['clicks'])) {
        throw new InvalidFilterException('Missing click count value', InvalidFilterException::MISSING_VALUE);
      }
      $filterData['clicks'] = $data['clicks'];
      $filterData['operator'] = $data['operator'];
      $filterData['timeframe'] = $data['timeframe'];
      $filterData['connect'] = $data['connect'];
      $filterData['days'] = $data['days'] ?? 0;
      return new DynamicSegmentFilterData(DynamicSegmentFilterData::TYPE_EMAIL, $data['action'], $filterData);
    }

    if (($data['action'] === EmailAction::ACTION_CLICKED)) {
      if (empty($data['newsletter_id'])) {
        throw new InvalidFilterException('Missing newsletter id', InvalidFilterException::MISSING_NEWSLETTER_ID);
      }
      $filterData['newsletter_id'] = $data['newsletter_id'];
    } else {
      if (empty($data['newsletters']) || !is_array($data['newsletters'])) {
        throw new InvalidFilterException('Missing newsletter', InvalidFilterException::MISSING_NEWSLETTER_ID);
      }
      $filterData['newsletters'] = array_map(function ($segmentId) {
        return intval($segmentId);
      }, $data['newsletters']);
    }

    $filterType = DynamicSegmentFilterData::TYPE_EMAIL;
    $action = $data['action'];
    if (isset($data['link_ids']) && is_array($data['link_ids'])) {
      $filterData['link_ids'] = array_map('intval', $data['link_ids']);
      if (!isset($data['operator'])) {
        throw new InvalidFilterException('Missing operator', InvalidFilterException::MISSING_OPERATOR);
      }
      $filterData['operator'] = $data['operator'];
    }
    return new DynamicSegmentFilterData($filterType, $action, $filterData);
  }

  /**
   * @throws InvalidFilterException
   */
  private function createEmailOpensAbsoluteCount(array $data): DynamicSegmentFilterData {
    if (!isset($data['opens'])) {
      throw new InvalidFilterException('Missing number of opens', InvalidFilterException::MISSING_VALUE);
    }
    $this->filterHelper->validateDaysPeriodData($data);
    $filterData = [
      'opens' => $data['opens'],
      'days' => $data['days'] ?? 0,
      'operator' => $data['operator'] ?? 'more',
      'timeframe' => $data['timeframe'] ?? DynamicSegmentFilterData::TIMEFRAME_IN_THE_LAST, // backwards compatibility
      'connect' => $data['connect'],
    ];
    $filterType = DynamicSegmentFilterData::TYPE_EMAIL;
    $action = $data['action'];
    return new DynamicSegmentFilterData($filterType, $action, $filterData);
  }

  /**
   * @throws InvalidFilterException
   */
  private function createWooCommerce(array $data): DynamicSegmentFilterData {
    if (empty($data['action'])) {
      throw new InvalidFilterException('Missing action', InvalidFilterException::MISSING_ACTION);
    }
    $filterData = [
      'connect' => $data['connect'],
    ];
    $filterType = DynamicSegmentFilterData::TYPE_WOOCOMMERCE;
    $action = $data['action'];
    if ($data['action'] === WooCommerceCategory::ACTION_CATEGORY) {
      if (!isset($data['category_ids'])) {
        throw new InvalidFilterException('Missing category', InvalidFilterException::MISSING_CATEGORY_ID);
      }
      if (!isset($data['operator'])) {
        throw new InvalidFilterException('Missing operator', InvalidFilterException::MISSING_OPERATOR);
      }
      $filterData['operator'] = $data['operator'];
      $filterData['category_ids'] = $data['category_ids'];
    } elseif ($data['action'] === WooCommerceProduct::ACTION_PRODUCT) {
      if (!isset($data['product_ids'])) {
        throw new InvalidFilterException('Missing product', InvalidFilterException::MISSING_PRODUCT_ID);
      }
      if (!isset($data['operator'])) {
        throw new InvalidFilterException('Missing operator', InvalidFilterException::MISSING_OPERATOR);
      }
      $filterData['operator'] = $data['operator'];
      $filterData['product_ids'] = $data['product_ids'];
    } elseif ($data['action'] === WooCommerceCountry::ACTION_CUSTOMER_COUNTRY) {
      if (!isset($data['country_code'])) {
        throw new InvalidFilterException('Missing country', InvalidFilterException::MISSING_COUNTRY);
      }
      $filterData['country_code'] = $data['country_code'];
      $filterData['operator'] = $data['operator'] ?? DynamicSegmentFilterData::OPERATOR_ANY;
    } elseif (in_array($data['action'], WooCommerceNumberOfOrders::ACTIONS)) {
      $this->filterHelper->validateDaysPeriodData($data);
      if (
        !isset($data['number_of_orders_type'])
        || !isset($data['number_of_orders_count']) || $data['number_of_orders_count'] < 0
      ) {
        throw new InvalidFilterException('Missing required fields', InvalidFilterException::MISSING_NUMBER_OF_ORDERS_FIELDS);
      }
      $filterData['number_of_orders_type'] = $data['number_of_orders_type'];
      $filterData['number_of_orders_count'] = $data['number_of_orders_count'];
      $filterData['days'] = $data['days'] ?? 0;
      $filterData['timeframe'] = $data['timeframe'];
    } elseif ($data['action'] === WooCommerceNumberOfReviews::ACTION) {
      $this->wooCommerceNumberOfReviews->validateFilterData($data);
      $filterData['days'] = $data['days'];
      $filterData['count_type'] = $data['count_type'];
      $filterData['count'] = $data['count'];
      $filterData['rating'] = $data['rating'];
      $filterData['timeframe'] = $data['timeframe'];
    } elseif ($data['action'] === WooCommerceTotalSpent::ACTION_TOTAL_SPENT) {
      $this->filterHelper->validateDaysPeriodData($data);
      if (
        !isset($data['total_spent_type'])
        || !isset($data['total_spent_amount']) || $data['total_spent_amount'] < 0
      ) {
        throw new InvalidFilterException('Missing required fields', InvalidFilterException::MISSING_TOTAL_SPENT_FIELDS);
      }
      $filterData['total_spent_type'] = $data['total_spent_type'];
      $filterData['total_spent_amount'] = $data['total_spent_amount'];
      $filterData['days'] = $data['days'] ?? 0;
      $filterData['timeframe'] = $data['timeframe'];
    } elseif ($data['action'] === WooCommerceSingleOrderValue::ACTION_SINGLE_ORDER_VALUE) {
      $this->filterHelper->validateDaysPeriodData($data);
      if (
        !isset($data['single_order_value_type'])
        || !isset($data['single_order_value_amount']) || $data['single_order_value_amount'] < 0
      ) {
        throw new InvalidFilterException('Missing required fields', InvalidFilterException::MISSING_SINGLE_ORDER_VALUE_FIELDS);
      }
      $filterData['single_order_value_type'] = $data['single_order_value_type'];
      $filterData['single_order_value_amount'] = $data['single_order_value_amount'];
      $filterData['days'] = $data['days'] ?? 0;
      $filterData['timeframe'] = $data['timeframe'];
    } elseif (in_array($data['action'], [WooCommercePurchaseDate::ACTION, WooCommerceFirstOrder::ACTION])) {
      $filterData['operator'] = $data['operator'];
      $filterData['value'] = $data['value'];
    } elseif ($data['action'] === WooCommerceAverageSpent::ACTION) {
      $this->filterHelper->validateDaysPeriodData($data);
      if (
        !isset($data['average_spent_type'])
        || !isset($data['average_spent_amount']) || $data['average_spent_amount'] < 0
      ) {
        throw new InvalidFilterException('Missing required fields', InvalidFilterException::MISSING_AVERAGE_SPENT_FIELDS);
      }
      $filterData['days'] = $data['days'] ?? 0;
      $filterData['timeframe'] = $data['timeframe'];
      $filterData['average_spent_amount'] = $data['average_spent_amount'];
      $filterData['average_spent_type'] = $data['average_spent_type'];
    } elseif ($data['action'] === WooCommerceUsedPaymentMethod::ACTION) {
      if (!isset($data['operator']) || !in_array($data['operator'], WooCommerceUsedPaymentMethod::VALID_OPERATORS, true)) {
        throw new InvalidFilterException('Missing operator', InvalidFilterException::MISSING_OPERATOR);
      }
      if (!isset($data['payment_methods']) || !is_array($data['payment_methods']) || empty($data['payment_methods'])) {
        throw new InvalidFilterException('Missing payment gateways', InvalidFilterException::MISSING_VALUE);
      }
      $this->filterHelper->validateDaysPeriodData($data);
      $filterData['operator'] = $data['operator'];
      $filterData['payment_methods'] = $data['payment_methods'];
      $filterData['days'] = intval($data['days'] ?? 0);
      $filterData['timeframe'] = $data['timeframe'];
    } elseif ($data['action'] === WooCommerceUsedShippingMethod::ACTION) {
      if (!isset($data['operator']) || !in_array($data['operator'], WooCommerceUsedShippingMethod::VALID_OPERATORS, true)) {
        throw new InvalidFilterException('Missing operator', InvalidFilterException::MISSING_OPERATOR);
      }
      if (!isset($data['shipping_methods']) || !is_array($data['shipping_methods']) || empty($data['shipping_methods'])) {
        throw new InvalidFilterException('Missing shipping methods', InvalidFilterException::MISSING_VALUE);
      }
      $this->filterHelper->validateDaysPeriodData($data);
      $filterData['operator'] = $data['operator'];
      $filterData['shipping_methods'] = $data['shipping_methods'];
      $filterData['days'] = intval($data['days'] ?? 0);
      $filterData['timeframe'] = $data['timeframe'];
    } elseif (in_array($data['action'], WooCommerceCustomerTextField::ACTIONS)) {
      if (empty($data['value'])) {
        throw new InvalidFilterException('Missing value', InvalidFilterException::MISSING_VALUE);
      }
      if (empty($data['operator'])) {
        throw new InvalidFilterException('Missing operator', InvalidFilterException::MISSING_OPERATOR);
      }
      if (!in_array($data['operator'], DynamicSegmentFilterData::TEXT_FIELD_OPERATORS)) {
        throw new InvalidFilterException('Invalid operator', InvalidFilterException::MISSING_OPERATOR);
      }
      $filterData['value'] = $data['value'];
      $filterData['operator'] = $data['operator'];
      $filterData['action'] = $data['action'];
    } elseif ($data['action'] === WooCommerceUsedCouponCode::ACTION) {
      $this->wooCommerceUsedCouponCode->validateFilterData($data);
      $filterData['operator'] = $data['operator'];
      $filterData['coupon_code_ids'] = $data['coupon_code_ids'];
      $filterData['days'] = $data['days'];
      $filterData['timeframe'] = $data['timeframe'];
    } elseif ($data['action'] === WooCommercePurchasedWithAttribute::ACTION) {
      $this->wooCommercePurchasedWithAttribute->validateFilterData($data);
      $filterData['operator'] = $data['operator'];
      $filterData['attribute_taxonomy_slug'] = $data['attribute_taxonomy_slug'] ?? null;
      $filterData['attribute_term_ids'] = $data['attribute_term_ids'] ?? null;
      $filterData['attribute_type'] = $data['attribute_type'];
      $filterData['attribute_local_name'] = $data['attribute_local_name'] ?? null;
      $filterData['attribute_local_values'] = $data['attribute_local_values'] ?? null;
    } elseif ($data['action'] === WooCommerceTag::ACTION) {
      $this->wooCommerceTag->validateFilterData($data);
      $filterData['operator'] = $data['operator'];
      $filterData['tag_ids'] = $data['tag_ids'];
    } else {
      throw new InvalidFilterException("Unknown action " . $data['action'], InvalidFilterException::MISSING_ACTION);
    }
    return new DynamicSegmentFilterData($filterType, $action, $filterData);
  }

  /**
   * @throws InvalidFilterException
   */
  private function createWooCommerceMembership(array $data): DynamicSegmentFilterData {
    if (empty($data['action'])) {
      throw new InvalidFilterException('Missing action', InvalidFilterException::MISSING_ACTION);
    }
    $filterData = [
      'connect' => $data['connect'],
    ];
    $filterType = DynamicSegmentFilterData::TYPE_WOOCOMMERCE_MEMBERSHIP;
    $action = $data['action'];
    if ($data['action'] === WooCommerceMembership::ACTION_MEMBER_OF) {
      if (!isset($data['plan_ids']) || !is_array($data['plan_ids'])) {
        throw new InvalidFilterException('Missing plan', InvalidFilterException::MISSING_PLAN_ID);
      }
      if (!isset($data['operator'])) {
        throw new InvalidFilterException('Missing operator', InvalidFilterException::MISSING_OPERATOR);
      }
      $filterData['operator'] = $data['operator'];
      $filterData['plan_ids'] = $data['plan_ids'];
    } else {
      throw new InvalidFilterException("Unknown action " . $data['action'], InvalidFilterException::MISSING_ACTION);
    }
    return new DynamicSegmentFilterData($filterType, $action, $filterData);
  }

  /**
   * @throws InvalidFilterException
   */
  private function createWooCommerceSubscription(array $data): DynamicSegmentFilterData {
    if (empty($data['action'])) {
      throw new InvalidFilterException('Missing action', InvalidFilterException::MISSING_ACTION);
    }
    $filterData = [
      'connect' => $data['connect'],
    ];
    $filterType = DynamicSegmentFilterData::TYPE_WOOCOMMERCE_SUBSCRIPTION;
    $action = $data['action'];
    if ($data['action'] === WooCommerceSubscription::ACTION_HAS_ACTIVE) {
      if (!isset($data['product_ids']) || !is_array($data['product_ids'])) {
        throw new InvalidFilterException('Missing product', InvalidFilterException::MISSING_PRODUCT_ID);
      }
      if (!isset($data['operator'])) {
        throw new InvalidFilterException('Missing operator', InvalidFilterException::MISSING_OPERATOR);
      }
      $filterData['operator'] = $data['operator'];
      $filterData['product_ids'] = $data['product_ids'];
    } else {
      throw new InvalidFilterException("Unknown action " . $data['action'], InvalidFilterException::MISSING_ACTION);
    }
    return new DynamicSegmentFilterData($filterType, $action, $filterData);
  }
}
