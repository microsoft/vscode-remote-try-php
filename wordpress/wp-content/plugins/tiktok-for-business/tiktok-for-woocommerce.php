<?php
/**
 * Copyright (c) Bytedance, Inc. and its affiliates. All Rights Reserved
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 *
 * Plugin Name: TikTok
 * Plugin URI: https://wordpress.org/plugins/tiktok-for-business
 * Description: With the TikTok x WooCommerce integration, it's easier than ever to unlock innovative social commerce features for your business to drive traffic and sales to a highly engaged community. With guided & simple setup prompts, you can sync your WooCommerce product catalog and promote it with custom ads without leaving your dashboard. Also, in just 1 click you can install the most-advanced TikTok pixel to unlock advanced visibility into detailed campaign performance tracking. Reach over 1 billion users, globally, and drive more e-commerce sales when you sell via one of the world’s most downloaded applications!
 * Author: TikTok
 * Version: 1.2.6
 *
 * Requires at least: 5.7.0
 * Tested up to: 6.4
 *
 * Woo:
 * WC requires at least: 2.6.0
 * WC tested up to: 7.1
 *
 * @package TikTok
 */

require_once 'Tiktokforbusiness.php';

add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);

register_deactivation_hook( __FILE__, array( 'Tiktokforbusiness', 'tt_plugin_deactivate' ) );
register_activation_hook( __FILE__, array( 'Tiktokforbusiness', 'tt_plugin_activate' ) );

TiktokForBusiness::tiktok_for_business_get_instance();
