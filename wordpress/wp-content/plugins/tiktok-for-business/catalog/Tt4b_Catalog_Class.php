<?php
/**
 * Copyright (c) Bytedance, Inc. and its affiliates. All Rights Reserved
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 *
 * @package TikTok
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

require_once __DIR__ . '/../utils/utilities.php';

class Tt4b_Catalog_Class {



	/**
	 * The TikTok Mapi Class used to make various requests to TikTok
	 *
	 * @var Tt4b_Mapi_Class
	 */
	protected $mapi;

	/**
	 * The woocommerce logger
	 *
	 * @var Logger
	 */
	protected $logger;

	/**
	 * Constructor
	 *
	 * @param Tt4b_Mapi_Class $mapi   The Tt4b_Mapi_Class
	 * @param Logger          $logger
	 *
	 * @return void
	 */
	public function __construct( Tt4b_Mapi_Class $mapi, Logger $logger ) {
		$this->mapi   = $mapi;
		$this->logger = $logger;
	}

	/**
	 * Initializes actions related to Tt4b_Catalog_Class such as catalog sync functionality used by action_scheduler
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'tt4b_catalog_sync_helper', [ $this, 'catalog_sync_helper' ], 2, 7 );
		add_action( 'tt4b_catalog_sync', [ $this, 'catalog_sync' ], 1, 4 );
		add_action( 'tt4b_delete_products_helper', [ $this, 'delete_products_helper' ], 1, 6 );
	}

	/**
	 * Returns the amount of catalog items are in approved/processing/rejected.
	 *
	 * @param string $access_token The MAPI issued access token
	 * @param string $bc_id        The users business center ID
	 * @param string $catalog_id   The users catalog ID
	 *
	 * @return array(processing, approved, rejected)
	 */
	public static function get_catalog_processing_status(
		$access_token,
		$bc_id,
		$catalog_id
	) {
		// returns a counter of how many items are approved, processing, or rejected
		// from the TikTok catalog/product/get/ endpoint
		$logger = new Logger();
		$mapi   = new Tt4b_Mapi_Class( $logger );

		$url    = 'catalog/overview/';
		$params = [
			'bc_id'      => $bc_id,
			'catalog_id' => $catalog_id,
		];
		$base   = [
			'processing' => 0,
			'approved'   => 0,
			'rejected'   => 0,
		];

		$result = $mapi->mapi_get( $url, $access_token, $params, 'v1.2' );
		$obj    = json_decode( $result, true );

		if ( ! isset( $obj['data'] ) ) {
			$logger->log( __METHOD__, 'get_catalog_processing_status data not set' );
			return $base;
		}

		if ( 'OK' !== $obj['message'] ) {
			$logger->log( __METHOD__, 'get_catalog_processing_status not OK response' );
			return $base;
		}

		$processing = $obj['data']['processing'];
		$approved   = $obj['data']['approved'];
		$rejected   = $obj['data']['rejected'];

		return [
			'processing' => $processing,
			'approved'   => $approved,
			'rejected'   => $rejected,
		];
	}

	/**
	 * Begins catalog sync, if there is not one currently enqueued. Schedules recurring catalog sync on an hourly basis.
	 *
	 * @param string $catalog_id   The users catalog ID
	 * @param string $bc_id        The users business center ID
	 * @param string $store_name   The users store name
	 * @param string $access_token The MAPI issued access token
	 *
	 * @return void
	 */
	public function initiate_catalog_sync( $catalog_id, $bc_id, $store_name, $access_token ) {
		// check for woo install
		if ( ! did_action( 'woocommerce_loaded' ) > 0 ) {
			return;
		}

		$this->logger->log( __METHOD__, "initiate_catalog_sync executing for $store_name" );

		$tt4b_catalog_sync_payload = [
			'catalog_id'   => $catalog_id,
			'bc_id'        => $bc_id,
			'store_name'   => $store_name,
			'access_token' => $access_token,
		];
		if ( false === as_has_scheduled_action( 'tt4b_catalog_sync_helper' ) ) {
			self::check_and_start_async_action( 'tt4b_catalog_sync', $tt4b_catalog_sync_payload, 'tt4b_management_catalog_sync' );
		}
		if ( false === as_has_scheduled_action(
			'tt4b_catalog_sync',
			[
				'catalog_id'   => $catalog_id,
				'bc_id'        => $bc_id,
				'store_name'   => $store_name,
				'access_token' => $access_token,
			],
			'tt4b_scheduled_catalog_sync'
		)
		) {
			as_schedule_cron_action(
				'today',
				'0 0-23 * * *',
				'tt4b_catalog_sync',
				[
					'catalog_id'   => $catalog_id,
					'bc_id'        => $bc_id,
					'store_name'   => $store_name,
					'access_token' => $access_token,
				],
				'tt4b_scheduled_catalog_sync'
			);
		}
	}

	/**
	 * Sync merchant catalog from woocommerce store to TikTok catalog manager via creation of catalog_sync_helper functions for batches of products
	 *
	 * @param string $catalog_id   The users catalog ID
	 * @param string $bc_id        The users business center ID
	 * @param string $store_name   The users store name
	 * @param string $access_token The MAPI issued access token
	 *
	 * @return void
	 */
	public function catalog_sync( $catalog_id, $bc_id, $store_name, $access_token ) {
		// check for woo install
		if ( ! did_action( 'woocommerce_loaded' ) > 0 ) {
			return;
		}
		$this->logger->log( __METHOD__, "catalog_sync executing for $store_name" );

		if ( '' === $catalog_id ) {
			$this->logger->log( __METHOD__, 'missing catalog_id for full catalog sync' );
			return;
		}
		if ( '' === $bc_id ) {
			$this->logger->log( __METHOD__, 'missing bc_id for full catalog sync' );
			return;
		}
		if ( '' === $access_token || false === $access_token ) {
			$this->logger->log( __METHOD__, 'missing access token for full catalog sync' );
			return;
		}
		// store_name just used for brand, can default it.
		if ( '' === $store_name ) {
			$store_name = 'WOO_COMMERCE';
		}
		$args                                = [
			'date_modified' => '>=' . get_option( 'tt4b_last_product_sync_time' ) . '',
			'paginate'      => true,
			'limit'         => 100,
		];
		$result                              = wc_get_products( $args );
		$pages                               = $result->max_num_pages;
		$tt4b_delete_products_helper_payload = [
			'catalog_id'        => $catalog_id,
			'bc_id'             => $bc_id,
			'store_name'        => $store_name,
			'access_token'      => $access_token,
			'page_total'        => $pages,
			'last_catalog_sync' => get_option( 'tt4b_last_product_sync_time' ),
		];
		$formatted_last_catalog_sync         = wp_date( 'j F Y H:i:s', get_option( 'tt4b_last_product_sync_time' ) );
		$this->logger->log( __METHOD__, "adding and updating products from wc_get_products since $formatted_last_catalog_sync" );
		update_option( 'tt4b_last_product_sync_time', time() );
		self::check_and_start_async_action( 'tt4b_delete_products_helper', $tt4b_delete_products_helper_payload, '' );
	}

	/**
	 * Helper function used to delete products on tiktok catalog manager that have been removed (trashed or deleted) from the woocommerce store
	 * This function should run before any products sync to avoid issues when products are trashed and then untrashed
	 *
	 * @param string  $catalog_id        The users catalog ID
	 * @param string  $bc_id             The users business center ID
	 * @param string  $store_name        The users store name
	 * @param string  $access_token      The MAPI issued access token
	 * @param integer $page_total        The maximum number of pages of 100 products to sync for this job
	 * @param integer $last_catalog_sync The unix timestamp of the last catalog sync, used to fetch products modified and created since that timestamp
	 *
	 * @return void
	 */
	public function delete_products_helper( $catalog_id, $bc_id, $store_name, $access_token, $page_total, $last_catalog_sync ) {
		$sku_ids = (array) get_option( 'tt4b_product_delete_queue', [] );
		// check count of SKUs to delete, only send payload if SKUs array is not empty
		if ( 0 === count( $sku_ids ) ) {
			$this->logger->log( __METHOD__, 'no products retrieved from tt4b_product_delete_queue option, skipping product deletion' );
		} else {
			$dpa_product_information = [
				'bc_id'      => $bc_id,
				'catalog_id' => $catalog_id,
				'sku_ids'    => $sku_ids,
			];
			// send payload and reset option to empty array
			$this->mapi->mapi_post( 'catalog/product/delete/', $access_token, $dpa_product_information, 'v1.3' );
			$this->logger->log( __METHOD__, "$sku_ids products posted to tiktok catalog for deletion" );
			update_option( 'tt4b_product_delete_queue', [] );
		}
		$tt4b_catalog_sync_helper_payload = [
			'catalog_id'        => $catalog_id,
			'bc_id'             => $bc_id,
			'store_name'        => $store_name,
			'access_token'      => $access_token,
			'page'              => 1,
			'page_total'        => $page_total,
			'last_catalog_sync' => $last_catalog_sync,
		];
		// after product deletion is skipped or completed, proceed to initiate catalog sync helpers
		self::check_and_start_async_action( 'tt4b_catalog_sync_helper', $tt4b_catalog_sync_helper_payload, '' );
	}

	/**
	 * Helper function used to post batches of products from woocommerce store to tiktok catalog manager
	 *
	 * @param string  $catalog_id        The users catalog ID
	 * @param string  $bc_id             The users business center ID
	 * @param string  $store_name        The users store name
	 * @param string  $access_token      The MAPI issued access token
	 * @param integer $page              The page of products from the user catalog
	 * @param integer $page_total        The maximum number of pages of 100 products to sync for this job
	 * @param integer $last_catalog_sync The unix timestamp of the last catalog sync, used to fetch products modified and created since that timestamp
	 *
	 * @return void
	 */
	public function catalog_sync_helper( $catalog_id, $bc_id, $store_name, $access_token, $page, $page_total, $last_catalog_sync ) {
		$args            = [
			'date_modified' => '>=' . $last_catalog_sync . '',
			'limit'         => 100,
			'page'          => $page,
		];
		$dpa_products    = [];
		$variation_count = 0;
		$products        = wc_get_products( $args );
		if ( 0 === count( $products ) ) {
			$this->logger->log( __METHOD__, 'no products retrieved from wc_get_products' );
		}
		$failed_products_count = 0;
		foreach ( $products as $product ) {
			if ( is_null( $product ) ) {
				++$failed_products_count;
				continue;
			}
			$this->logger->log( __METHOD__, "product retrieved: $product" );
			$dpa_product = $this->generate_product_info( $store_name, $product );
			if ( [] === $dpa_product ) {
				++$failed_products_count;
				continue;
			}

			// if adding parent variable product is successful, add children variation products
			if ( $product->is_type( 'variable' ) ) {
				$variations       = $product->get_available_variations( 'objects' );
				$variations_count = count( $variations );
				if ( isset( $dpa_product['sku_id'] ) && '' != $dpa_product['sku_id'] ) {
					$this->logger->log( __METHOD__, "variable product detected with $variations_count variations" );
					$dpa_variations = $this->variable_product_sync( $dpa_product['sku_id'], $dpa_product['description'] !== $dpa_product['title'] ? $dpa_product['description'] : '', $store_name, $variations );
				}
				// track count of variations with all fields
				$variation_count += count( $dpa_variations );
				$dpa_products     = array_merge( $dpa_products, $dpa_variations );
			}

			$dpa_products[] = $dpa_product;
		}
		// Remove variations from count of dpa_products to just return all parent products and simple products
		$count = count( $dpa_products ) - $variation_count;
		$this->logger->log( __METHOD__, "product_page: $page product_count: $count, variation_count: $variation_count" );
		$dpa_product_information = [
			'bc_id'      => $bc_id,
			'catalog_id' => $catalog_id,
			'products'   => $dpa_products,
		];
		$this->mapi->mapi_post( 'catalog/product/upload/', $access_token, $dpa_product_information, 'v1.3' );
		++$page;
		$tt4b_catalog_sync_helper_payload = [
			'catalog_id'        => $catalog_id,
			'bc_id'             => $bc_id,
			'store_name'        => $store_name,
			'access_token'      => $access_token,
			'page'              => $page,
			'page_total'        => $page_total,
			'last_catalog_sync' => $last_catalog_sync,
		];
		if ( $page <= $page_total ) {
			self::check_and_start_async_action( 'tt4b_catalog_sync_helper', $tt4b_catalog_sync_helper_payload, '' );
		}
	}

	/**
	 * Prepare child product_variations associated with parent variable product to be synced to TikTok catalog
	 *
	 * @param string                 $parent_sku
	 * @param string                 $parent_description
	 * @param string                 $store_name
	 * @param WC_Product_Variation[] $product_variations
	 *
	 * @return array
	 */
	public function variable_product_sync( $parent_sku, $parent_description, $store_name, $product_variations ) {
		$dpa_variation_products  = [];
		$failed_variations_count = 0;
		if ( 0 === count( $product_variations ) ) {
			$this->logger->log( __METHOD__, 'empty array of variable products provided' );
		}
		foreach ( $product_variations as $variation ) {
			if ( is_null( $variation ) ) {
				++$failed_variations_count;
				continue;
			}
			$this->logger->log( __METHOD__, "variation retrieved: $variation" );
			$dpa_variation_product = $this->generate_product_info( $store_name, $variation, $parent_sku, $parent_description );

			$dpa_variation_products[] = $dpa_variation_product;
		}
		return $dpa_variation_products;
	}

	/**
	 * Generate the needed product_data in array format for products, and product variants accordingly
	 *
	 * @param string     $store_name
	 * @param WC_Product $product
	 * @param string     $parent_sku         optional - provided for child products (variants) to associate child product to parent product
	 * @param string     $parent_description optional - provided for child products in case the child doesn't have a unique description
	 *
	 * @return array
	 */
	public function generate_product_info( $store_name, $product, $parent_sku = '', $parent_description = '' ) {
		$title       = $product->get_name();
		$description = $product->get_short_description();
		if ( '' === $description && '' === $parent_description ) {
			$description = $title;
		} elseif ( '' === $description && '' !== $parent_description ) {
			$description = $parent_description;
		}
		$condition = 'NEW';

		$availability = 'IN_STOCK';
		$stock_status = $product->is_in_stock();
		if ( false === $stock_status ) {
			$availability = 'OUT_OF_STOCK';
		}
		$sku_id = (string) $product->get_sku();
		if ( '' === $sku_id ) {
			$sku_id = (string) $product->get_id();
		}
		// if parent_id is not provided then the item_group_id is equal to the current product's sku_id
		// if parent_id is provided meaning product is child of parent_id, the item_group_id should be the parent_sku
		$item_group_id = $sku_id;
		if ( '' !== $parent_sku ) {
			$item_group_id = $parent_sku;
			// if the current product SKU is the same as it's parent SKU, concatenate $parent_sku with the child post ID
			// otherwise use the SKU of the variation
			$sku_id = variation_content_id_helper( Method::CATALOG, $parent_sku, $sku_id, $product->get_id() );
			// if there is a variation description only for this variant, use that instead of either
			// the parent description or the title for the description field in the TikTok Catalog
			$variantDescription = $product->get_description();
			if ( '' !== $variantDescription ) {
				$description = $variantDescription;
			}
		}
		$link      = get_permalink( $product->get_id() );
		$image_id  = $product->get_image_id();
		$image_url = wp_get_attachment_image_url( $image_id, 'full' );
		$price     = $product->get_price();
		// if regular price is not empty, false, or string use that instead
		$regularPrice = $product->get_regular_price();
		if ( '' !== $regularPrice && false !== $regularPrice && '0' !== $regularPrice ) {
			$price = $regularPrice;
		}
		$sale_price = $product->get_sale_price();
		if ( '0' === $sale_price || '' === $sale_price ) {
			$sale_price = $price;
		}
		// Get product gallery images - max 10
		$gallery_image_ids  = array_slice( $product->get_gallery_image_ids(), 0, 10, true );
		$gallery_image_urls = [];
		foreach ( $gallery_image_ids as $gallery_image_id ) {
			$gallery_image_urls[] = wp_get_attachment_image_url( $gallery_image_id, 'full' );
		}

		// if any of the values are empty, the whole request will fail, so skip the product.
		$missing_fields = [];
		if ( '' === $sku_id || false === $sku_id ) {
			$missing_fields[] = 'sku_id';
		}
		if ( '' === $title || false === $title ) {
			$missing_fields[] = 'title';
		}
		if ( '' === $image_url || false === $image_url ) {
			$missing_fields[] = 'image_url';
		}
		if ( '' === $price || false === $price || '0' === $price ) {
			$missing_fields[] = 'price';
		}
		if ( count( $missing_fields ) > 0 ) {
			$debug_message = sprintf(
				'sku_id: %s title: %s is missing the following fields for product sync: %s',
				$sku_id,
				$title,
				join( ',', $missing_fields )
			);
			$this->logger->log( __METHOD__, $debug_message );
			return [];
		}

		$dpa_product = [
			'sku_id'         => $sku_id,
			'item_group_id'  => $item_group_id,
			'title'          => $title,
			'availability'   => $availability,
			'description'    => $description,
			'image_url'      => $image_url,
			'brand'          => $store_name,
			'product_detail' => [
				'condition' => $condition,
			],
			'price_info'     => [
				'price'      => $price,
				'sale_price' => $sale_price,
			],
			'landing_page'   => [
				'landing_page_url' => $link,
			],
		];

		// add additional product images if available
		if ( count( $gallery_image_urls ) > 0 ) {
			$dpa_product['additional_image_link'] = $gallery_image_urls;
		}

		return $dpa_product;
	}

	/**
	 * Check if async action should be added according to name, payload, and group
	 *
	 * @param string $action_name name of the action to run
	 * @param array  $payload     array payload for the action
	 *
	 * @param string $group       action group, pass empty string if no group
	 */
	private function check_and_start_async_action( $action_name, $payload, $group ) {
		if ( '' == $group ) {
			if ( false === as_has_scheduled_action(
				$action_name,
				$payload
			)
			) {
				as_enqueue_async_action(
					$action_name,
					$payload
				);
			}
		} elseif ( false === as_has_scheduled_action(
			$action_name,
			$payload,
			$group
		)
			) {

				as_enqueue_async_action(
					$action_name,
					$payload,
					$group
				);
		}
	}
}
