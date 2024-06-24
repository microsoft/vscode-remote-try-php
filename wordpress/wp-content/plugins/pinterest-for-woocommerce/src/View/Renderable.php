<?php
/**
 * Interface Renderable
 *
 * @package Automattic\WooCommerce\Pinterest\Infrastructure
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Pinterest\View;

defined( 'ABSPATH' ) || exit;

/**
 * Used to designate an object that can be rendered (e.g. views, blocks, shortcodes, etc.).
 */
interface Renderable {

	/**
	 * Render the renderable.
	 *
	 * @param array $context Optional. Contextual information to use while
	 *                       rendering. Defaults to an empty array.
	 *
	 * @return string Rendered result.
	 */
	public function render( array $context = array() ): string;
}
