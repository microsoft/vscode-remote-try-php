<?php
/**
 * Pinterest for WooCommerce Rich Pins
 *
 * @package     Pinterest_For_WooCommerce/Classes/
 * @version     1.0.0
 */

namespace Automattic\WooCommerce\Pinterest;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Helper methods that get/set the various feed state properties.
 */
class ProductFeedStatus {

	const STATE_PROPS = array(
		'status'                                        => 'pending_config',
		'last_activity'                                 => 0,
		'product_count'                                 => 0,
		'error_message'                                 => '',
		self::PROP_FEED_GENERATION_WALL_START_TIME      => false,
		self::PROP_FEED_GENERATION_WALL_TIME            => 0,
		self::PROP_FEED_GENERATION_RECENT_PRODUCT_COUNT => 0,
	);

	const PINTEREST_FOR_WOOCOMMERCE_FEEDS_DATA_PREFIX = PINTEREST_FOR_WOOCOMMERCE_PREFIX . '_feeds_';

	const PROP_FEED_GENERATION_WALL_START_TIME = 'feed_generation_wall_start_time';

	const PROP_FEED_GENERATION_WALL_TIME = 'feed_generation_wall_time';

	const PROP_FEED_GENERATION_RECENT_PRODUCT_COUNT = 'feed_generation_recent_product_count';

	/**
	 * The array that holds the state of the feed, used as cache.
	 *
	 * @var array
	 */
	private static $state = array();

	/**
	 * Returns the Current state of the Feed generation job.
	 * Status can be one of the following:
	 *
	 * - in_progress              Signifies that we are between iterations and generating the feed.
	 * - generated                The feed is generated, no further action is needed, unless the feed is expired.
	 * - scheduled_for_generation The feed is scheduled to be (re)generated. On this status, the next run of ProductSync::handle_feed_generation() will start the generation process.
	 * - pending_config           The feed was reset or was never configured.
	 * - error                    The generation process returned an error.
	 *
	 * @return array
	 */
	public static function get() {

		foreach ( self::STATE_PROPS as $key => $default_value ) {

			if ( ! isset( self::$state[ $key ] ) || null === self::$state[ $key ] ) {
				self::$state[ $key ] = get_transient( self::PINTEREST_FOR_WOOCOMMERCE_FEEDS_DATA_PREFIX . $key );
			}

			if ( false === self::$state[ $key ] ) {
				self::$state[ $key ] = $default_value;
			} elseif ( null === self::$state[ $key ] ) {
				self::$state[ $key ] = false;
			}
		}

		return self::$state;
	}

	/**
	 * Sets the Current state of the Feed generation job.
	 * See the docblock of self::get() for more info.
	 *
	 * @param array $state The array holding the feed state props to be saved.
	 * @return void
	 */
	public static function set( $state ) {

		$state['last_activity'] = time();

		foreach ( $state as $key => $value ) {
			self::$state[ $key ] = $value;
			set_transient( self::PINTEREST_FOR_WOOCOMMERCE_FEEDS_DATA_PREFIX . $key, ( false === $value ? null : $value ) ); // No expiration.
		}

		if ( ! empty( $state['status'] ) ) {
			/**
			 * Feed status hook.
			 *
			 * @param array $state The array holding the feed state props.
			 * phpcs:disable WooCommerce.Commenting.CommentHooks.MissingSinceComment
			 */
			do_action( 'pinterest_for_woocommerce_feed_' . $state['status'], $state );
		}
	}

	/**
	 * Removes all transients for the given feed_id.
	 *
	 * @return void
	 */
	public static function deregister() {
		foreach ( self::STATE_PROPS as $key => $default_value ) {
			self::$state[ $key ] = $default_value;
			delete_transient( self::PINTEREST_FOR_WOOCOMMERCE_FEEDS_DATA_PREFIX . $key );
		}
	}

	/**
	 * Resets a feed generation start time.
	 *
	 * @since 1.2.9
	 * @return void
	 */
	public static function reset_feed_file_generation_time() {
		self::set(
			array(
				self::PROP_FEED_GENERATION_WALL_START_TIME => time(),
			)
		);
	}

	/**
	 * Calculates and sets feed generation time.
	 *
	 * @param int $time_now - current time, e.g. time().
	 * @since 1.2.9
	 * @return void
	 */
	public static function set_feed_file_generation_time( int $time_now ) {
		$recent_feed_start_time = self::get()[ self::PROP_FEED_GENERATION_WALL_START_TIME ];
		if ( false !== $recent_feed_start_time ) {
			self::set(
				array(
					self::PROP_FEED_GENERATION_WALL_TIME => $time_now - (int) $recent_feed_start_time,
				)
			);
		}
	}

	/**
	 * Sets feed generation time into negative value to communicate feed generation failure.
	 *
	 * @since 1.2.9
	 * @return void
	 */
	public static function mark_feed_file_generation_as_failed() {
		self::set(
			array(
				self::PROP_FEED_GENERATION_WALL_TIME => -1,
			)
		);
	}
}
