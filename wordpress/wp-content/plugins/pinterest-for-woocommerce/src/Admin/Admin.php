<?php
/**
 * Class Admin
 *
 * @package Automattic\WooCommerce\Pinterest\Admin
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Pinterest\Admin;

use Automattic\WooCommerce\Pinterest\Product\GoogleCategorySearch;
use Automattic\WooCommerce\Pinterest\View\ViewException;
use Automattic\WooCommerce\Pinterest\View\ViewFactory;

/**
 * Class Admin
 */
class Admin {

	/**
	 * View factory.
	 *
	 * @var ViewFactory
	 */
	protected $view_factory;

	/**
	 * Admin constructor.
	 *
	 * @param ViewFactory $view_factory View factory.
	 */
	public function __construct( ViewFactory $view_factory ) {
		$this->view_factory = $view_factory;
	}

	/**
	 * Register a service.
	 */
	public function register(): void {
		add_action(
			'admin_enqueue_scripts',
			function() {
				$this->enqueue_assets();
			}
		);

		( new GoogleCategorySearch() )->register();
	}

	/**
	 * Enqueues any assets.
	 */
	protected function enqueue_assets() {
		$screen = get_current_screen();

		if ( $screen && 'product' === $screen->id ) {
			wp_enqueue_style(
				'pinterest-product-attributes-css',
				Pinterest_For_Woocommerce()->plugin_url() . '/assets/build/style-product-attributes.css',
				array(),
				PINTEREST_FOR_WOOCOMMERCE_VERSION
			);
		}
	}

	/**
	 * Get the admin view.
	 *
	 * @param string $view              Name of the view.
	 * @param array  $context_variables Array of variables to pass to the view.
	 *
	 * @return string The rendered view
	 *
	 * @throws ViewException If the view doesn't exist or can't be loaded.
	 */
	public function get_view( string $view, array $context_variables = array() ): string {
		return $this->view_factory->create( $view )
							->render( $context_variables );
	}

}
