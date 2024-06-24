<?php
namespace Automattic\WooCommerce\Blocks\Templates;

/**
 * CheckoutTemplate class.
 *
 * @internal
 */
class CheckoutTemplate extends AbstractPageTemplate {

	/**
	 * The slug of the template.
	 *
	 * @var string
	 */
	const SLUG = 'page-checkout';

	/**
	 * Initialization method.
	 */
	public function init() {
		parent::init();

		add_action( 'template_redirect', array( $this, 'render_block_template' ) );
	}

	/**
	 * Returns the title of the template.
	 *
	 * @return string
	 */
	public function get_template_title() {
		return _x( 'Page: Checkout', 'Template name', 'woocommerce' );
	}

	/**
	 * Returns the description of the template.
	 *
	 * @return string
	 */
	public function get_template_description() {
		return __( 'The Checkout template guides users through the final steps of the purchase process. It enables users to enter shipping and billing information, select a payment method, and review order details.', 'woocommerce' );
	}

	/**
	 * Renders the default block template from Woo Blocks if no theme templates exist.
	 */
	public function render_block_template() {
		if (
			! is_embed() && is_checkout()
		) {
			add_filter( 'woocommerce_has_block_template', '__return_true', 10, 0 );
		}
	}

	/**
	 * Returns the page object assigned to this template/page.
	 *
	 * @return \WP_Post|null Post object or null.
	 */
	protected function get_placeholder_page() {
		$page_id = wc_get_page_id( 'checkout' );
		return $page_id ? get_post( $page_id ) : null;
	}

	/**
	 * True when viewing the checkout page or checkout endpoint.
	 *
	 * @return boolean
	 */
	protected function is_active_template() {
		global $post;
		$placeholder = $this->get_placeholder_page();
		return null !== $placeholder && $post instanceof \WP_Post && $placeholder->post_name === $post->post_name;
	}

	/**
	 * When the page should be displaying the template, add it to the hierarchy.
	 *
	 * This places the template name e.g. `cart`, at the beginning of the template hierarchy array. The hook priority
	 * is 1 to ensure it runs first; other consumers e.g. extensions, could therefore inject their own template instead
	 * of this one when using the default priority of 10.
	 *
	 * @param array $templates Templates that match the pages_template_hierarchy.
	 */
	public function page_template_hierarchy( $templates ) {
		if ( $this->is_active_template() ) {
			array_unshift( $templates, self::SLUG );
			array_unshift( $templates, 'checkout' );
		}
		return $templates;
	}
}
