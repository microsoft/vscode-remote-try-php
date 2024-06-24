<?php declare(strict_types = 1);

namespace MailPoet\Segments\DynamicSegments;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\DynamicSegmentFilterData;
use MailPoet\Entities\DynamicSegmentFilterEntity;
use MailPoet\Segments\DynamicSegments\Exceptions\InvalidFilterException;
use MailPoet\Segments\DynamicSegments\Filters\AutomationsEvents;
use MailPoet\Segments\DynamicSegments\Filters\EmailAction;
use MailPoet\Segments\DynamicSegments\Filters\EmailActionClickAny;
use MailPoet\Segments\DynamicSegments\Filters\EmailOpensAbsoluteCountAction;
use MailPoet\Segments\DynamicSegments\Filters\EmailsReceived;
use MailPoet\Segments\DynamicSegments\Filters\Filter;
use MailPoet\Segments\DynamicSegments\Filters\MailPoetCustomFields;
use MailPoet\Segments\DynamicSegments\Filters\NumberOfClicks;
use MailPoet\Segments\DynamicSegments\Filters\SubscriberDateField;
use MailPoet\Segments\DynamicSegments\Filters\SubscriberScore;
use MailPoet\Segments\DynamicSegments\Filters\SubscriberSegment;
use MailPoet\Segments\DynamicSegments\Filters\SubscriberSubscribedViaForm;
use MailPoet\Segments\DynamicSegments\Filters\SubscriberTag;
use MailPoet\Segments\DynamicSegments\Filters\SubscriberTextField;
use MailPoet\Segments\DynamicSegments\Filters\UserRole;
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

class FilterFactory {
  /** @var EmailAction */
  private $emailAction;

  /** @var UserRole */
  private $userRole;

  /** @var WooCommerceAverageSpent */
  private $wooCommerceAverageSpent;

  /** @var WooCommerceProduct */
  private $wooCommerceProduct;

  /** @var WooCommerceCategory */
  private $wooCommerceCategory;

  /** @var WooCommerceCountry */
  private $wooCommerceCountry;

  /** @var WooCommerceNumberOfOrders */
  private $wooCommerceNumberOfOrders;

  /** @var WooCommercePurchaseDate */
  private $wooCommercePurchaseDate;

  /** @var WooCommerceSingleOrderValue */
  private $wooCommerceSingleOrderValue;

  /** @var WooCommerceTotalSpent */
  private $wooCommerceTotalSpent;

  /** @var WooCommerceMembership */
  private $wooCommerceMembership;

  /** @var WooCommerceSubscription */
  private $wooCommerceSubscription;

  /** @var EmailOpensAbsoluteCountAction */
  private $emailOpensAbsoluteCount;

  /** @var SubscriberScore */
  private $subscriberScore;

  /** @var MailPoetCustomFields */
  private $mailPoetCustomFields;

  /** @var SubscriberSegment */
  private $subscriberSegment;

  /** @var SubscriberTag */
  private $subscriberTag;

  /** @var EmailActionClickAny */
  private $emailActionClickAny;

  /** @var SubscriberSubscribedViaForm */
  private $subscribedViaForm;

  /** @var SubscriberTextField */
  private $subscriberTextField;

  /** @var WooCommerceUsedPaymentMethod */
  private $wooCommerceUsedPaymentMethod;

  /** @var WooCommerceUsedShippingMethod */
  private $wooCommerceUsedShippingMethod;

  /** @var WooCommerceCustomerTextField */
  private $wooCommerceCustomerTextField;

  /** @var SubscriberDateField */
  private $subscriberDateField;

  /** @var AutomationsEvents */
  private $automationsEvents;

  /** @var WooCommerceNumberOfReviews */
  private $wooCommerceNumberOfReviews;

  /** @var WooCommerceUsedCouponCode  */
  private $wooCommerceUsedCouponCode;

  /** @var WooCommerceFirstOrder */
  private $wooCommerceFirstOrder;

  /** @var EmailsReceived */
  private $emailsReceived;

  /** @var NumberOfClicks */
  private $numberOfClicks;

  private WooCommercePurchasedWithAttribute $wooCommercePurchasedWithAttribute;

  private WooCommerceTag $wooCommerceTag;

  public function __construct(
    EmailAction $emailAction,
    EmailActionClickAny $emailActionClickAny,
    UserRole $userRole,
    MailPoetCustomFields $mailPoetCustomFields,
    WooCommerceProduct $wooCommerceProduct,
    WooCommerceCategory $wooCommerceCategory,
    WooCommerceCountry $wooCommerceCountry,
    WooCommerceCustomerTextField $wooCommerceCustomerTextField,
    EmailOpensAbsoluteCountAction $emailOpensAbsoluteCount,
    WooCommerceNumberOfOrders $wooCommerceNumberOfOrders,
    WooCommerceNumberOfReviews $wooCommerceNumberOfReviews,
    WooCommerceTotalSpent $wooCommerceTotalSpent,
    WooCommerceMembership $wooCommerceMembership,
    WooCommerceFirstOrder $wooCommerceFirstOrder,
    WooCommercePurchaseDate $wooCommercePurchaseDate,
    WooCommerceSubscription $wooCommerceSubscription,
    SubscriberScore $subscriberScore,
    SubscriberTag $subscriberTag,
    SubscriberSegment $subscriberSegment,
    SubscriberSubscribedViaForm $subscribedViaForm,
    WooCommerceSingleOrderValue $wooCommerceSingleOrderValue,
    WooCommerceAverageSpent $wooCommerceAverageSpent,
    WooCommerceTag $wooCommerceTag,
    WooCommerceUsedCouponCode $wooCommerceUsedCouponCode,
    WooCommerceUsedPaymentMethod $wooCommerceUsedPaymentMethod,
    WooCommerceUsedShippingMethod $wooCommerceUsedShippingMethod,
    SubscriberTextField $subscriberTextField,
    SubscriberDateField $subscriberDateField,
    AutomationsEvents $automationsEvents,
    EmailsReceived $emailsReceived,
    NumberOfClicks $numberOfClicks,
    WooCommercePurchasedWithAttribute $wooCommercePurchasedWithAttribute
  ) {
    $this->emailAction = $emailAction;
    $this->userRole = $userRole;
    $this->wooCommerceProduct = $wooCommerceProduct;
    $this->wooCommerceCategory = $wooCommerceCategory;
    $this->wooCommerceCountry = $wooCommerceCountry;
    $this->wooCommerceNumberOfOrders = $wooCommerceNumberOfOrders;
    $this->wooCommerceNumberOfReviews = $wooCommerceNumberOfReviews;
    $this->wooCommerceMembership = $wooCommerceMembership;
    $this->wooCommercePurchaseDate = $wooCommercePurchaseDate;
    $this->wooCommerceSubscription = $wooCommerceSubscription;
    $this->emailOpensAbsoluteCount = $emailOpensAbsoluteCount;
    $this->wooCommerceTotalSpent = $wooCommerceTotalSpent;
    $this->subscriberScore = $subscriberScore;
    $this->subscriberTag = $subscriberTag;
    $this->mailPoetCustomFields = $mailPoetCustomFields;
    $this->subscriberSegment = $subscriberSegment;
    $this->emailActionClickAny = $emailActionClickAny;
    $this->wooCommerceSingleOrderValue = $wooCommerceSingleOrderValue;
    $this->subscriberTextField = $subscriberTextField;
    $this->subscribedViaForm = $subscribedViaForm;
    $this->wooCommerceAverageSpent = $wooCommerceAverageSpent;
    $this->wooCommerceUsedPaymentMethod = $wooCommerceUsedPaymentMethod;
    $this->wooCommerceUsedShippingMethod = $wooCommerceUsedShippingMethod;
    $this->wooCommerceCustomerTextField = $wooCommerceCustomerTextField;
    $this->automationsEvents = $automationsEvents;
    $this->subscriberDateField = $subscriberDateField;
    $this->wooCommerceUsedCouponCode = $wooCommerceUsedCouponCode;
    $this->wooCommerceFirstOrder = $wooCommerceFirstOrder;
    $this->emailsReceived = $emailsReceived;
    $this->numberOfClicks = $numberOfClicks;
    $this->wooCommercePurchasedWithAttribute = $wooCommercePurchasedWithAttribute;
    $this->wooCommerceTag = $wooCommerceTag;
  }

  public function getFilterForFilterEntity(DynamicSegmentFilterEntity $filter): Filter {
    $filterData = $filter->getFilterData();
    $filterType = $filterData->getFilterType();
    $action = $filterData->getAction();
    switch ($filterType) {
      case DynamicSegmentFilterData::TYPE_AUTOMATIONS:
        return $this->automationsEvents;
      case DynamicSegmentFilterData::TYPE_USER_ROLE:
        return $this->userRole($action);
      case DynamicSegmentFilterData::TYPE_EMAIL:
        return $this->email($action);
      case DynamicSegmentFilterData::TYPE_WOOCOMMERCE_MEMBERSHIP:
        return $this->wooCommerceMembership();
      case DynamicSegmentFilterData::TYPE_WOOCOMMERCE_SUBSCRIPTION:
        return $this->wooCommerceSubscription();
      case DynamicSegmentFilterData::TYPE_WOOCOMMERCE:
        return $this->wooCommerce($action);
      default:
        throw new InvalidFilterException('Invalid type', InvalidFilterException::INVALID_TYPE);
    }
  }

  /**
   * @param ?string $action
   *
   * @return MailPoetCustomFields|SubscriberScore|SubscriberSegment|UserRole|SubscriberTag|SubscriberTextField|SubscriberSubscribedViaForm|SubscriberDateField
   */
  private function userRole(?string $action) {
    if ($action === SubscriberScore::TYPE) {
      return $this->subscriberScore;
    } elseif ($action === MailPoetCustomFields::TYPE) {
      return $this->mailPoetCustomFields;
    } elseif ($action === SubscriberSegment::TYPE) {
      return $this->subscriberSegment;
    } elseif ($action === SubscriberTag::TYPE) {
      return $this->subscriberTag;
    } elseif ($action === SubscriberSubscribedViaForm::TYPE) {
      return $this->subscribedViaForm;
    } elseif (in_array($action, SubscriberTextField::TYPES)) {
      return $this->subscriberTextField;
    } elseif (in_array($action, SubscriberDateField::TYPES)) {
      return $this->subscriberDateField;
    }
    return $this->userRole;
  }

  /**
   * @param ?string $action
   * @return EmailAction|EmailActionClickAny|EmailOpensAbsoluteCountAction|EmailsReceived|NumberOfClicks
   */
  private function email(?string $action) {
    $countActions = [EmailOpensAbsoluteCountAction::TYPE, EmailOpensAbsoluteCountAction::MACHINE_TYPE];
    if (in_array($action, $countActions)) {
      return $this->emailOpensAbsoluteCount;
    } elseif ($action === EmailActionClickAny::TYPE) {
      return $this->emailActionClickAny;
    } elseif ($action === EmailsReceived::ACTION) {
      return $this->emailsReceived;
    } elseif ($action === NumberOfClicks::ACTION) {
      return $this->numberOfClicks;
    }
    return $this->emailAction;
  }

  private function wooCommerceMembership(): WooCommerceMembership {
    return $this->wooCommerceMembership;
  }

  private function wooCommerceSubscription(): WooCommerceSubscription {
    return $this->wooCommerceSubscription;
  }

  /**
   * @param ?string $action
   * @return Filter
   */
  private function wooCommerce(?string $action) {
    if ($action === WooCommerceProduct::ACTION_PRODUCT) {
      return $this->wooCommerceProduct;
    } elseif (in_array($action, WooCommerceNumberOfOrders::ACTIONS)) {
      return $this->wooCommerceNumberOfOrders;
    } elseif ($action === WooCommerceTotalSpent::ACTION_TOTAL_SPENT) {
      return $this->wooCommerceTotalSpent;
    } elseif ($action === WooCommerceCountry::ACTION_CUSTOMER_COUNTRY) {
      return $this->wooCommerceCountry;
    } elseif ($action === WooCommerceSingleOrderValue::ACTION_SINGLE_ORDER_VALUE) {
      return $this->wooCommerceSingleOrderValue;
    } elseif ($action === WooCommercePurchaseDate::ACTION) {
      return $this->wooCommercePurchaseDate;
    } elseif ($action === WooCommerceAverageSpent::ACTION) {
      return $this->wooCommerceAverageSpent;
    } elseif ($action === WooCommerceUsedPaymentMethod::ACTION) {
      return $this->wooCommerceUsedPaymentMethod;
    } elseif ($action === WooCommerceUsedShippingMethod::ACTION) {
      return $this->wooCommerceUsedShippingMethod;
    } elseif ($action === WooCommerceNumberOfReviews::ACTION) {
      return $this->wooCommerceNumberOfReviews;
    } elseif (in_array($action, WooCommerceCustomerTextField::ACTIONS)) {
      return $this->wooCommerceCustomerTextField;
    } elseif ($action === WooCommerceUsedCouponCode::ACTION) {
      return $this->wooCommerceUsedCouponCode;
    } elseif ($action === WooCommerceFirstOrder::ACTION) {
      return $this->wooCommerceFirstOrder;
    } elseif ($action === WooCommercePurchasedWithAttribute::ACTION) {
      return $this->wooCommercePurchasedWithAttribute;
    } elseif ($action == WooCommerceTag::ACTION) {
      return $this->wooCommerceTag;
    }
    return $this->wooCommerceCategory;
  }
}
