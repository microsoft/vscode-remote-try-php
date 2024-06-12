<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\AdminPages\Pages;

if (!defined('ABSPATH')) exit;


use MailPoet\AdminPages\AssetsController;
use MailPoet\AdminPages\PageRenderer;
use MailPoet\API\JSON\ResponseBuilders\CustomFieldsResponseBuilder;
use MailPoet\Automation\Engine\Data\Automation;
use MailPoet\Automation\Engine\Storage\AutomationStorage;
use MailPoet\CustomFields\CustomFieldsRepository;
use MailPoet\Entities\DynamicSegmentFilterData;
use MailPoet\Entities\FormEntity;
use MailPoet\Entities\SegmentEntity;
use MailPoet\Form\FormsRepository;
use MailPoet\Listing\PageLimit;
use MailPoet\Newsletter\NewslettersRepository;
use MailPoet\Segments\SegmentDependencyValidator;
use MailPoet\Segments\SegmentsRepository;
use MailPoet\WooCommerce\Helper as WooCommerceHelper;
use MailPoet\WP\AutocompletePostListLoader as WPPostListLoader;
use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Doctrine\Common\Collections\Criteria;

class DynamicSegments {
  /** @var AssetsController */
  private $assetsController;

  /** @var PageRenderer */
  private $pageRenderer;

  /** @var PageLimit */
  private $listingPageLimit;

  /** @var WPFunctions */
  private $wp;

  /** @var WooCommerceHelper */
  private $woocommerceHelper;

  /** @var WPPostListLoader */
  private $wpPostListLoader;

  /** @var SegmentDependencyValidator */
  private $segmentDependencyValidator;

  /** @var CustomFieldsRepository */
  private $customFieldsRepository;

  /** @var CustomFieldsResponseBuilder */
  private $customFieldsResponseBuilder;

  /** @var SegmentsRepository */
  private $segmentsRepository;

  /** @var NewslettersRepository */
  private $newslettersRepository;

  /** @var FormsRepository */
  private $formsRepository;

  /** @var AutomationStorage */
  private $automationStorage;

  public function __construct(
    AssetsController $assetsController,
    PageRenderer $pageRenderer,
    PageLimit $listingPageLimit,
    WPFunctions $wp,
    WooCommerceHelper $woocommerceHelper,
    WPPostListLoader $wpPostListLoader,
    CustomFieldsRepository $customFieldsRepository,
    CustomFieldsResponseBuilder $customFieldsResponseBuilder,
    SegmentDependencyValidator $segmentDependencyValidator,
    SegmentsRepository $segmentsRepository,
    NewslettersRepository $newslettersRepository,
    FormsRepository $formsRepository,
    AutomationStorage $automationStorage
  ) {
    $this->assetsController = $assetsController;
    $this->pageRenderer = $pageRenderer;
    $this->listingPageLimit = $listingPageLimit;
    $this->wp = $wp;
    $this->woocommerceHelper = $woocommerceHelper;
    $this->wpPostListLoader = $wpPostListLoader;
    $this->segmentDependencyValidator = $segmentDependencyValidator;
    $this->customFieldsRepository = $customFieldsRepository;
    $this->customFieldsResponseBuilder = $customFieldsResponseBuilder;
    $this->segmentsRepository = $segmentsRepository;
    $this->newslettersRepository = $newslettersRepository;
    $this->formsRepository = $formsRepository;
    $this->automationStorage = $automationStorage;
  }

  /**
   * @return void
   */
  public function render() {
    $data = [];
    $data['dynamic_segment_count'] = $this->segmentsRepository->countBy([
      'deletedAt' => null,
      'type' => SegmentEntity::TYPE_DYNAMIC,
    ]);
    $data['items_per_page'] = $this->listingPageLimit->getLimitPerPage('segments');

    $customFields = $this->customFieldsRepository->findBy([], ['name' => 'asc']);
    $data['custom_fields'] = $this->customFieldsResponseBuilder->buildBatch($customFields);

    $wpRoles = $this->wp->getEditableRoles();
    $data['wordpress_editable_roles_list'] = array_map(function($roleId, $role) {
      return [
        'role_id' => $roleId,
        'role_name' => $role['name'],
      ];
    }, array_keys($wpRoles), $wpRoles);

    $data['newsletters_list'] = $this->getNewslettersList();

    $data['static_segments_list'] = [];
    $criteria = new Criteria();
    $criteria->where(Criteria::expr()->isNull('deletedAt'));
    $criteria->andWhere(Criteria::expr()->neq('type', SegmentEntity::TYPE_DYNAMIC));
    $criteria->orderBy(['name' => 'ASC']);
    $segments = $this->segmentsRepository->matching($criteria);
    foreach ($segments as $segment) {
      $data['static_segments_list'][] = [
        'id' => $segment->getId(),
        'name' => $segment->getName(),
        'type' => $segment->getType(),
        'description' => $segment->getDescription(),
      ];
    }

    $data['product_attributes'] = [];
    if ($this->woocommerceHelper->isWooCommerceActive()) {
      $productAttributes = $this->woocommerceHelper->wcGetAttributeTaxonomies();

      foreach ($productAttributes as $attribute) {
        $taxonomy = 'pa_' . $attribute->attribute_name;// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
        $attributeTerms = $this->wp->getTerms(
          [
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
          ]
        );

        if (!isset($attributeTerms['errors'])) {
          $data['product_attributes'][$taxonomy] = [ // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
            'id' => $attribute->attribute_id, // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
            'label' => $attribute->attribute_label, // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
            'terms' => $attributeTerms,
            'taxonomy' => $taxonomy,
          ];
        }
      }

      // Fetch local attributes used for product variations
      $data['local_product_attributes'] = [];
      $localAttributes = $this->getLocalAttributesUsedInProductVariations();
      foreach ($localAttributes as $localAttribute => $values) {
        $data['local_product_attributes'][$localAttribute] = [
          'name' => $localAttribute,
          'values' => $values,
        ];
      }
    }

    $data['product_categories'] = $this->wpPostListLoader->getWooCommerceCategories();
    $data['product_tags'] = $this->wpPostListLoader->getWooCommerceTags();

    $data['products'] = $this->wpPostListLoader->getProducts();
    $data['membership_plans'] = $this->wpPostListLoader->getMembershipPlans();
    $data['subscription_products'] = $this->wpPostListLoader->getSubscriptionProducts();
    $wcCountries = $this->woocommerceHelper->isWooCommerceActive() ? $this->woocommerceHelper->getAllowedCountries() : [];
    $data['woocommerce_countries'] = array_map(function ($code, $name) {
      return [
        'name' => $name,
        'code' => $code,
      ];
    }, array_keys($wcCountries), $wcCountries);
    $data['can_use_woocommerce_memberships'] = $this->segmentDependencyValidator->canUseDynamicFilterType(
      DynamicSegmentFilterData::TYPE_WOOCOMMERCE_MEMBERSHIP
    );
    $data['can_use_woocommerce_subscriptions'] = $this->segmentDependencyValidator->canUseDynamicFilterType(
      DynamicSegmentFilterData::TYPE_WOOCOMMERCE_SUBSCRIPTION
    );
    $wooCurrencySymbol = $this->woocommerceHelper->isWooCommerceActive() ? $this->woocommerceHelper->getWoocommerceCurrencySymbol() : '';
    $data['woocommerce_currency_symbol'] = html_entity_decode($wooCurrencySymbol);
    $data['signup_forms'] = array_map(function(FormEntity $form) {
      return [
        'id' => $form->getId(),
        'name' => $form->getName(),
      ];
    }, $this->formsRepository->findAll());

    $data['woocommerce_payment_methods'] = [];
    $data['woocommerce_shipping_methods'] = [];

    if ($this->woocommerceHelper->isWooCommerceActive()) {
      $allGateways = $this->woocommerceHelper->getPaymentGateways()->payment_gateways();
      $paymentMethods = [];
      foreach ($allGateways as $gatewayId => $gateway) {
        $paymentMethods[] = [
          'id' => $gatewayId,
          'name' => $gateway->get_method_title(),
        ];
      }
      $data['woocommerce_payment_methods'] = $paymentMethods;

      $data['woocommerce_shipping_methods'] = array_values($this->woocommerceHelper->getShippingMethodInstancesData());
    }
    $data['automations'] = array_map(function(Automation $automation) {
      return [
        'id' => (string)$automation->getId(),
        'name' => $automation->getName(),
      ];
    }, $this->automationStorage->getAutomations());

    $this->assetsController->setupDynamicSegmentsDependencies();
    $this->pageRenderer->displayPage('segments/dynamic.html', $data);
  }

  private function getLocalAttributesUsedInProductVariations(): array {
    $attributes = [];

    if (!$this->woocommerceHelper->isWooCommerceActive()) {
      return $attributes;
    }
    global $wpdb;

    $query = "
    SELECT DISTINCT pm.meta_key, pm.meta_value
    FROM {$wpdb->postmeta} pm
    INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
    WHERE pm.meta_key LIKE 'attribute_%'
    AND p.post_type = 'product_variation'
    GROUP BY pm.meta_key, pm.meta_value";

    $results = $wpdb->get_results($query, ARRAY_A);

    foreach ($results as $result) {
      $attribute = substr($result['meta_key'], 10);
      if (!isset($attributes[$attribute])) {
        $attributes[$attribute] = [];
      }
      $attributes[$attribute][] = $result['meta_value'];
    }

    return $attributes;
  }

  private function getNewslettersList(): array {
    $result = [];
    foreach ($this->newslettersRepository->getStandardNewsletterList() as $newsletter) {
      $result[] = [
        'id' => (string)$newsletter->getId(),
        'subject' => $newsletter->getSubject(),
        'name' => $newsletter->getCampaignNameOrSubject(),
        'sent_at' => ($sentAt = $newsletter->getSentAt()) ? $sentAt->format('Y-m-d H:i:s') : null,
      ];
    }
    return $result;
  }
}
