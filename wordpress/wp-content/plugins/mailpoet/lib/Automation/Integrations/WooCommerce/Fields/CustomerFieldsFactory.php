<?php declare(strict_types = 1);

namespace MailPoet\Automation\Integrations\WooCommerce\Fields;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\Field;
use MailPoet\Automation\Integrations\WooCommerce\Payloads\CustomerPayload;

class CustomerFieldsFactory {
  /** @var CustomerOrderFieldsFactory */
  private $customerOrderFieldsFactory;

  /** @var CustomerReviewFieldsFactory */
  private $customerReviewFieldsFactory;

  public function __construct(
    CustomerOrderFieldsFactory $customerOrderFieldsFactory,
    CustomerReviewFieldsFactory $customerReviewFieldsFactory
  ) {
    $this->customerOrderFieldsFactory = $customerOrderFieldsFactory;
    $this->customerReviewFieldsFactory = $customerReviewFieldsFactory;
  }

  /** @return Field[] */
  public function getFields(): array {
    return array_merge(
      [
        new Field(
          'woocommerce:customer:billing-company',
          Field::TYPE_STRING,
          __('Billing company', 'mailpoet'),
          function (CustomerPayload $payload) {
            return $payload->getBillingCompany();
          }
        ),
        new Field(
          'woocommerce:customer:billing-phone',
          Field::TYPE_STRING,
          __('Billing phone', 'mailpoet'),
          function (CustomerPayload $payload) {
            return $payload->getBillingPhone();
          }
        ),
        new Field(
          'woocommerce:customer:billing-city',
          Field::TYPE_STRING,
          __('Billing city', 'mailpoet'),
          function (CustomerPayload $payload) {
            return $payload->getBillingCity();
          }
        ),
        new Field(
          'woocommerce:customer:billing-postcode',
          Field::TYPE_STRING,
          __('Billing postcode', 'mailpoet'),
          function (CustomerPayload $payload) {
            return $payload->getBillingPostcode();
          }
        ),
        new Field(
          'woocommerce:customer:billing-state',
          Field::TYPE_STRING,
          __('Billing state/county', 'mailpoet'),
          function (CustomerPayload $payload) {
            return $payload->getBillingState();
          }
        ),
        new Field(
          'woocommerce:customer:billing-country',
          Field::TYPE_ENUM,
          __('Billing country', 'mailpoet'),
          function (CustomerPayload $payload) {
            return $payload->getBillingCountry();
          },
          [
            'options' => $this->getBillingCountryOptions(),
          ]
        ),
        new Field(
          'woocommerce:customer:shipping-company',
          Field::TYPE_STRING,
          __('Shipping company', 'mailpoet'),
          function (CustomerPayload $payload) {
            return $payload->getShippingCompany();
          }
        ),
        new Field(
          'woocommerce:customer:shipping-phone',
          Field::TYPE_STRING,
          __('Shipping phone', 'mailpoet'),
          function (CustomerPayload $payload) {
            return $payload->getShippingPhone();
          }
        ),
        new Field(
          'woocommerce:customer:shipping-city',
          Field::TYPE_STRING,
          __('Shipping city', 'mailpoet'),
          function (CustomerPayload $payload) {
            return $payload->getShippingCity();
          }
        ),
        new Field(
          'woocommerce:customer:shipping-postcode',
          Field::TYPE_STRING,
          __('Shipping postcode', 'mailpoet'),
          function (CustomerPayload $payload) {
            return $payload->getShippingPostcode();
          }
        ),
        new Field(
          'woocommerce:customer:shipping-state',
          Field::TYPE_STRING,
          __('Shipping state/county', 'mailpoet'),
          function (CustomerPayload $payload) {
            return $payload->getShippingState();
          }
        ),
        new Field(
          'woocommerce:customer:shipping-country',
          Field::TYPE_ENUM,
          __('Shipping country', 'mailpoet'),
          function (CustomerPayload $payload) {
            return $payload->getShippingCountry();
          },
          [
            'options' => $this->getShippingCountryOptions(),
          ]
        ),
      ],
      $this->customerOrderFieldsFactory->getFields(),
      $this->customerReviewFieldsFactory->getFields()
    );
  }

  private function getBillingCountryOptions(): array {
    $options = [];
    foreach (WC()->countries->get_allowed_countries() as $code => $name) {
      $options[] = ['id' => $code, 'name' => $name];
    }
    return $options;
  }

  private function getShippingCountryOptions(): array {
    $options = [];
    foreach (WC()->countries->get_shipping_countries() as $code => $name) {
      $options[] = ['id' => $code, 'name' => $name];
    }
    return $options;
  }
}
