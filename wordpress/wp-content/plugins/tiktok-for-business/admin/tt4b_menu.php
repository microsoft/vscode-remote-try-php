<?php
/**
 * Copyright (c) Bytedance, Inc. and its affiliates. All Rights Reserved
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 *
 * @package TikTok
 */
require_once __DIR__ . '/../utils/utilities.php';
require_once 'Tt4b_Menu_Class.php';
add_action( 'admin_menu', [ 'tt4b_menu_class', 'tt4b_admin_menu' ] );
add_action( 'wp_loaded', [ 'tt4b_menu_class', 'tt4b_store_access_token' ] );
add_action( 'before_delete_post', 'tt4b_product_delete', 10, 2 );
add_action( 'wp_trash_post', 'tt4b_product_trashed' );
add_action( 'woocommerce_before_delete_product_variation', 'tt4b_variation_delete' );


/**
 * Trash a product
 *
 * @param string $post_id The product_id.
 *
 * @return void
 */
function tt4b_product_trashed( $post_id ) {
	$post = get_post( $post_id );
	tt4b_product_delete( $post_id, $post );
}

/**
 * Delete or trash a product variation
 *
 * @param string $post_id The product/variation id to be deleted.
 *
 * @return void
 */
function tt4b_variation_delete( $post_id ) {
	$post = get_post( $post_id );
	tt4b_product_delete( $post_id, $post );
}

/**
 * Delete a product or product_variation
 *
 * @param string  $post_id The product_id.
 * @param WP_Post $post    The post.
 *
 * @return void
 */
function tt4b_product_delete( $post_id, $post ) {
	if ( 'product' !== $post->post_type && 'product_variation' !== $post->post_type ) {
		return;
	}
	$product = wc_get_product( $post_id );
	if ( is_null( $product ) ) {
		return;
	}
	$logger = new Logger();

	$access_token = get_option( 'tt4b_access_token' );
	$catalog_id   = get_option( 'tt4b_catalog_id' );
	$bc_id        = get_option( 'tt4b_bc_id' );
	if ( false === $access_token ) {
		$logger->log( __METHOD__, 'missing access token for tt4b_product_sync' );
		return;
	}
	if ( '' === $catalog_id ) {
		$logger->log( __METHOD__, 'missing catalog_id for tt4b_product_sync' );
		return;
	}
	if ( '' === $bc_id ) {
		$logger->log( __METHOD__, 'missing bc_id for tt4b_product_sync' );
		return;
	}

	$sku_id = (string) $product->get_sku();
	if ( '' === $sku_id ) {
		$sku_id = (string) $product->get_id();
	}

	// if it's a child product, delete the child product only
	$parent_product_id = $product->get_parent_id();
	if ( $parent_product_id > 0 ) {
		$parent_product = wc_get_product( $parent_product_id );
		$parent_sku     = $parent_product->get_sku() ? $parent_product->get_sku() : $parent_product->get_id();
		// if the child product sku is the same as the parent product, make sure to use the same concatenation logic as in catalog sync
		// otherwise use the unique child product sku for deletion
		$sku_id = variation_content_id_helper( Method::DELETE, $parent_sku, $sku_id, $product->get_id() );
	}

	// add the sku to array of skus to be deleted - stored as an option to be processed during scheduled syncs & management page syncs
	$sku_ids = [ $sku_id ];
	$logger->log(
		__METHOD__,
		sprintf(
			'adding SKU_ID to delete: %d',
			count( $sku_ids )
		)
	);
	$toDelete = (array) get_option( 'tt4b_product_delete_queue', [] );
	$toDelete = array_merge( $toDelete, $sku_ids );
	update_option( 'tt4b_product_delete_queue', $toDelete );
}
