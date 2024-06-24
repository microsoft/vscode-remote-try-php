<?php
/**
 * Interface ViewFactory
 *
 * @package Automattic\WooCommerce\Pinterest\View
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Pinterest\View;

defined( 'ABSPATH' ) || exit;

/**
 * Interface ViewFactory
 */
interface ViewFactory {

	/**
	 * Create a new view object.
	 *
	 * @param string $path Path to the view file to render.
	 *
	 * @return View Instantiated view object.
	 */
	public function create( string $path ): View;
}
