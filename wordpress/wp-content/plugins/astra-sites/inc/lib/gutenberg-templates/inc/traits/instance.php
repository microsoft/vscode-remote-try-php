<?php
/**
 * Trait.
 *
 * @package {{package}}
 * @since 0.0.1
 */

namespace Gutenberg_Templates\Inc\Traits;

/**
 * Trait Instance.
 */
trait Instance {

	/**
	 * Instance object.
	 *
	 * @var self Class Instance.
	 */
	private static $instance = null;

	/**
	 * Initiator
	 *
	 * @since 0.0.1
	 * @return self Initialized object of class.
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}

