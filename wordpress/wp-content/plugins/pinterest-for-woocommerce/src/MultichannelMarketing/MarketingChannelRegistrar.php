<?php
/**
 * Service class to register the Pinterest marketing channel.
 *
 * @package     Automattic\WooCommerce\Pinterest\MultichannelMarketing
 * @version     1.3.0
 */

namespace Automattic\WooCommerce\Pinterest\MultichannelMarketing;

use Automattic\WooCommerce\Admin\Marketing\MarketingChannels;
use Automattic\WooCommerce\Pinterest\Logger;
use Exception;
use Psr\Container\ContainerExceptionInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Class MarketingChannelRegistrar
 */
class MarketingChannelRegistrar {

	/**
	 * Register as a WooCommerce marketing channel.
	 */
	public static function register(): void {
		try {
			/** @var MarketingChannels $marketing_channels */
			$marketing_channels = \wc_get_container()->get( MarketingChannels::class );
			$pinterest_channel  = PinterestChannel::get_instance();
			$marketing_channels->register( $pinterest_channel );
		} catch ( Exception | ContainerExceptionInterface $e ) {
			// Log and silently fail.
			Logger::log( esc_html__( 'Marketing channel registration failed: ', 'pinterest-for-woocommerce' ) . $e->getMessage() );
		}
	}

}
