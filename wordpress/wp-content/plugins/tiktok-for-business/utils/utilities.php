<?php
/**
 * Copyright (c) Bytedance, Inc. and its affiliates. All Rights Reserved
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 *
 * @package TikTok
 */

require_once 'Method.php';

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Determine variation product ID for TT catalog sync
 *
 * @param int    $method     Method const int from abstract util Class Method.
 * @param string $parent_id  parent product's sku_id.
 * @param string $sku_id     variation product's sku_id.
 * @param string $product_id variation product's WP DB ID.
 *
 * @return string
 */
function variation_content_id_helper( $method, $parent_id, $sku_id, $product_id ) {
	if ( METHOD::CATALOG == $method || Method::DELETE == $method || Method::PURCHASE == $method ) {
		if ( $sku_id == $parent_id ) {
			$sku_id = $parent_id . '-' . $product_id;
		}
		return $sku_id;
	} elseif ( Method::ADDTOCART == $method || Method::STARTCHECKOUT == $method ) {
		$content_id = $sku_id;
		if ( $sku_id == $parent_id ) {
			$content_id = $parent_id . '-' . $product_id;
		}
		return $content_id;
	} else {
		return $sku_id;
	}
}
