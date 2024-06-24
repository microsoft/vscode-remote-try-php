<?php
/**
 * Interface View
 *
 * @package Automattic\WooCommerce\Pinterest\View
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Pinterest\View;

use Automattic\WooCommerce\Pinterest\View\ViewException;

defined( 'ABSPATH' ) || exit;

interface View extends Renderable {
	/**
	 * Render the current view with a given context.
	 *
	 * @param array $context Context in which to render.
	 *
	 * @return string Rendered HTML.
	 *
	 * @throws ViewException If the view could not be loaded.
	 */
	public function render( array $context = array() ): string;

	/**
	 * Render a partial view.
	 *
	 * This can be used from within a currently rendered view, to include
	 * nested partials.
	 *
	 * The passed-in context is optional, and will fall back to the parent's
	 * context if omitted.
	 *
	 * @param string     $path    Path of the partial to render.
	 * @param array|null $context Context in which to render the partial.
	 *
	 * @return string Rendered HTML.
	 *
	 * @throws ViewException If the view could not be loaded or provided path was not valid.
	 */
	public function render_partial( string $path, array $context = null ): string;

	/**
	 * Return the raw value of a context property.
	 *
	 * By default, properties are automatically escaped when accessing them
	 * within the view. This method allows direct access to the raw value
	 * instead to bypass this automatic escaping.
	 *
	 * @param string $property Property for which to return the raw value.
	 *
	 * @return mixed Raw context property value.
	 */
	public function raw( string $property );
}
