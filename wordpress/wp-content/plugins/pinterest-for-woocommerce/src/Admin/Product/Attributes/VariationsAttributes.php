<?php
/**
 * Class VariationsAttributes
 *
 * @package Automattic\WooCommerce\Pinterest\Admin\Product\Attributes
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Pinterest\Admin\Product\Attributes;

use Automattic\WooCommerce\Pinterest\Admin\Admin;
use Automattic\WooCommerce\Pinterest\Admin\Input\Form;
use Automattic\WooCommerce\Pinterest\Product\Attributes\AttributeManager;
use WC_Product_Variation;
use WP_Post;

defined( 'ABSPATH' ) || exit;

/**
 * Class VariationsAttributes
 */
class VariationsAttributes {

	/**
	 * Admin class.
	 *
	 * @var Admin
	 */
	protected $admin;

	/**
	 * Attibute Manager.
	 *
	 * @var AttributeManager
	 */
	protected $attribute_manager;

	/**
	 * VariationsAttributes constructor.
	 *
	 * @param Admin $admin Admin class.
	 */
	public function __construct( Admin $admin ) {
		$this->admin             = $admin;
		$this->attribute_manager = AttributeManager::instance();
	}
	/**
	 * Register a service.
	 */
	public function register(): void {

		if ( ! array_key_exists( 'variable', AttributesTab::get_applicable_product_types() ) ) {
			return;
		}

		// Priority 90 to render after regular variation attributes.
		add_action(
			'woocommerce_product_after_variable_attributes',
			function ( int $variation_index, array $variation_data, WP_Post $variation ) {
				$this->render_attributes_form( $variation_index, $variation );
			},
			90,
			3
		);
		add_action(
			'woocommerce_save_product_variation',
			function ( int $variation_id, int $variation_index ) {
				$this->handle_save_variation( $variation_id, $variation_index );
			},
			10,
			2
		);
	}

	/**
	 * Render the attributes form for variations.
	 *
	 * @param int     $variation_index Position in the loop.
	 * @param WP_Post $variation       Post data.
	 */
	private function render_attributes_form( int $variation_index, WP_Post $variation ) {
		/**
		 * Product variation.
		 *
		 * @var WC_Product_Variation $product
		 */
		$product = wc_get_product( $variation->ID );

		$data = $this->get_form( $product, $variation_index )->get_view_data();

		// Do not render the form if it doesn't contain any child attributes.
		$attributes = reset( $data['children'] );
		if ( empty( $data['children'] ) || empty( $attributes['children'] ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $this->admin->get_view( 'attributes/variations-form', $data );
	}

	/**
	 * Handle form submission and update the product attributes.
	 *
	 * @param int $variation_id    Variation ID.
	 * @param int $variation_index Variation index.
	 */
	private function handle_save_variation( int $variation_id, int $variation_index ) {
		/**
		 * Product variation.
		 *
		 * @var WC_Product_Variation $variation
		 */
		$variation = wc_get_product( $variation_id );

		$form           = $this->get_form( $variation, $variation_index );
		$form_view_data = $form->get_view_data();

		// phpcs:disable WordPress.Security.NonceVerification
		if ( empty( $_POST[ $form_view_data['name'] ] ) ) {
			return;
		}
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$submitted_data = (array) wc_clean( wp_unslash( $_POST[ $form_view_data['name'] ] ) );
		// phpcs:enable WordPress.Security.NonceVerification

		$form->submit( $submitted_data );
		$form_data = $form->get_data();

		if ( ! empty( $form_data[ $variation_index ] ) ) {
			$this->update_data( $variation, $form_data[ $variation_index ] );
		}
	}

	/**
	 * Get form.
	 *
	 * @param WC_Product_Variation $variation       Product variation.
	 * @param int                  $variation_index Variation index.
	 *
	 * @return Form
	 */
	protected function get_form( WC_Product_Variation $variation, int $variation_index ): Form {
		$attribute_types = $this->attribute_manager->get_attribute_types_for_product( $variation );
		$attribute_form  = new AttributesForm( $attribute_types );
		$attribute_form->set_name( (string) $variation_index );

		$form = new Form();
		$form->set_name( 'variation_attributes' )
			->add( $attribute_form )
			->set_data(
				array(
					(string) $variation_index => $this->attribute_manager->get_all_values( $variation ),
				)
			);

		return $form;
	}

	/**
	 * Update data.
	 *
	 * @param WC_Product_Variation $variation Product variation.
	 * @param array                $data      Product data.
	 *
	 * @return void
	 */
	protected function update_data( WC_Product_Variation $variation, array $data ): void {
		foreach ( $this->attribute_manager->get_attribute_types_for_product( $variation ) as $attribute_id => $attribute_type ) {
			if ( isset( $data[ $attribute_id ] ) ) {
				$this->attribute_manager->update( $variation, new $attribute_type( $data[ $attribute_id ] ) );
			}
		}
	}

}
