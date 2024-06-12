<?php
/**
 * Class AttributeManager
 *
 * @package Automattic\WooCommerce\Pinterest\Product\Attributes
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Pinterest\Product\Attributes;

use Automattic\WooCommerce\Pinterest\Exception\InvalidClass;
use Automattic\WooCommerce\Pinterest\Exception\InvalidValue;
use Automattic\WooCommerce\Pinterest\Exception\ValidateInterface;
use Automattic\WooCommerce\Pinterest\PluginHelper;
use WC_Product;

defined( 'ABSPATH' ) || exit;

/**
 * Class AttributeManager
 */
class AttributeManager {

	use PluginHelper;
	use ValidateInterface;

	/**
	 * The single instance of the class.
	 *
	 * @var AttributeManager
	 */
	protected static $instance = null;

	protected const ATTRIBUTES = array(
		Condition::class,
		GoogleCategory::class,
	);

	/**
	 * Attribute types mapped to product types.
	 *
	 * @var array
	 */
	protected $attribute_types_map;

	/**
	 * Load single instance of class.
	 *
	 * @return AttributeManager Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Update product attribute.
	 *
	 * @param WC_Product         $product   WooCommerce product.
	 * @param AttributeInterface $attribute Attribute to update.
	 *
	 * @throws InvalidValue If the attribute is invalid for the given product.
	 */
	public function update( WC_Product $product, AttributeInterface $attribute ) {
		$this->validate( $product, $attribute::get_id() );

		if ( null === $attribute->get_value() || '' === $attribute->get_value() ) {
			$this->delete( $product, $attribute::get_id() );
			return;
		}

		$value = $attribute->get_value();
		if ( in_array( $attribute::get_value_type(), array( 'bool', 'boolean' ), true ) ) {
			$value = wc_bool_to_string( $value );
		}

		$product->update_meta_data( $this->prefix_meta_key( $attribute::get_id() ), $value );
		$product->save_meta_data();
	}

	/**
	 * Get a product attribute.
	 *
	 * @param WC_Product $product      WooCommerce product.
	 * @param string     $attribute_id Attribute ID.
	 *
	 * @return AttributeInterface|null
	 *
	 * @throws InvalidValue If the attribute ID is invalid for the given product.
	 */
	public function get( WC_Product $product, string $attribute_id ): ?AttributeInterface {
		$this->validate( $product, $attribute_id );

		$value = null;
		if ( $this->exists( $product, $attribute_id ) ) {
			$value = $product->get_meta( $this->prefix_meta_key( $attribute_id ), true );
		}

		if ( null === $value || '' === $value ) {
			return null;
		}

		$attribute_class = $this->get_attribute_types_for_product( $product )[ $attribute_id ];
		return new $attribute_class( $value );
	}

	/**
	 * Return attribute value.
	 *
	 * @param WC_Product $product      WooCommerce product.
	 * @param string     $attribute_id Attribute ID.
	 *
	 * @return mixed|null
	 */
	public function get_value( WC_Product $product, string $attribute_id ) {
		$attribute = $this->get( $product, $attribute_id );

		return $attribute instanceof AttributeInterface ? $attribute->get_value() : null;
	}

	/**
	 * Return all attributes for the given product
	 *
	 * @param WC_Product $product WooCommerce product.
	 *
	 * @return AttributeInterface[]
	 */
	public function get_all( WC_Product $product ): array {
		$all_attributes = array();
		foreach ( array_keys( $this->get_attribute_types_for_product( $product ) ) as $attribute_id ) {
			$attribute = $this->get( $product, $attribute_id );
			if ( null !== $attribute ) {
				$all_attributes[ $attribute_id ] = $attribute;
			}
		}

		return $all_attributes;
	}

	/**
	 * Return all attribute values for the given product
	 *
	 * @param WC_Product $product WooCommerce Product.
	 *
	 * @return array of attribute values
	 */
	public function get_all_values( WC_Product $product ): array {
		$all_attributes = array();
		foreach ( array_keys( $this->get_attribute_types_for_product( $product ) ) as $attribute_id ) {
			$attribute = $this->get_value( $product, $attribute_id );
			if ( null !== $attribute ) {
				$all_attributes[ $attribute_id ] = $attribute;
			}
		}

		return $all_attributes;
	}

	/**
	 * Delete attribute data for a product.
	 *
	 * @param WC_Product $product      WooCommerce Product.
	 * @param string     $attribute_id Attribute ID.
	 *
	 * @throws InvalidValue If the attribute ID is invalid for the given product.
	 */
	public function delete( WC_Product $product, string $attribute_id ) {
		$this->validate( $product, $attribute_id );

		$product->delete_meta_data( $this->prefix_meta_key( $attribute_id ) );
		$product->save_meta_data();
	}

	/**
	 * Whether the attribute exists and has been set for the product.
	 *
	 * @param WC_Product $product      WooCommerce Product.
	 * @param string     $attribute_id Attribute ID.
	 *
	 * @return bool
	 */
	public function exists( WC_Product $product, string $attribute_id ): bool {
		return $product->meta_exists( $this->prefix_meta_key( $attribute_id ) );
	}

	/**
	 * Returns an array of attribute types for the given product
	 *
	 * @param WC_Product $product WooCommerce product.
	 *
	 * @return string[] of attribute classes mapped to attribute IDs
	 */
	public function get_attribute_types_for_product( WC_Product $product ): array {
		return $this->get_attribute_types_for_product_types( array( $product->get_type() ) );
	}

	/**
	 * Returns an array of attribute types for the given product types
	 *
	 * @param string[] $product_types Array of WooCommerce product types.
	 *
	 * @return string[] of attribute classes mapped to attribute IDs
	 */
	public function get_attribute_types_for_product_types( array $product_types ): array {
		// Flip the product types array to have them as array keys.
		$product_types_keys = array_flip( $product_types );

		// Intersect the product types with our stored attributes map to get arrays of attributes matching the given product types.
		$match_attributes = array_intersect_key( $this->get_attribute_types_map(), $product_types_keys );

		// Re-index the attributes map array to avoid string ($product_type) array keys.
		$match_attributes = array_values( $match_attributes );

		if ( empty( $match_attributes ) ) {
			return array();
		}

		// Merge all of the attribute arrays from the map (there might be duplicates) and return the results.
		return array_merge( ...$match_attributes );
	}

	/**
	 * Returns all available attribute IDs.
	 *
	 * @return array
	 *
	 * @since 1.3.0
	 */
	public static function get_available_attribute_ids(): array {
		$attributes = array();
		foreach ( self::get_available_attribute_types() as $attribute_type ) {
			if ( method_exists( $attribute_type, 'get_id' ) ) {
				$attribute_id                = call_user_func( array( $attribute_type, 'get_id' ) );
				$attributes[ $attribute_id ] = $attribute_id;
			}
		}

		return $attributes;
	}

	/**
	 * Return an array of all available attribute class names.
	 *
	 * @return string[] Attribute class names
	 */
	public static function get_available_attribute_types(): array {
		/**
		 * Filters the list of available product attributes.
		 *
		 * @param string[] $attributes Array of attribute class names (FQN)
		 * phpcs:disable WooCommerce.Commenting.CommentHooks.MissingSinceComment
		 */
		return apply_filters( 'wc_pinterest_product_attribute_types', self::ATTRIBUTES );
	}

	/**
	 * Returns an array of attribute types for all product types
	 *
	 * @return string[][] of attribute classes mapped to product types
	 */
	protected function get_attribute_types_map(): array {
		if ( ! isset( $this->attribute_types_map ) ) {
			$this->map_attribute_types();
		}

		return $this->attribute_types_map;
	}

	/**
	 * Validate product attribute.
	 *
	 * @param WC_Product $product      WooCommerce product.
	 * @param string     $attribute_id Attribute ID.
	 *
	 * @throws InvalidValue If the attribute type is invalid for the given product.
	 */
	protected function validate( WC_Product $product, string $attribute_id ) {
		$attribute_types = $this->get_attribute_types_for_product( $product );
		if ( ! isset( $attribute_types[ $attribute_id ] ) ) {
			/**
			 * Displays an error when an attribute is not supported for a product type.
			 */
			do_action(
				'wc_pinterest_error',
				sprintf( 'Attribute "%s" is not supported for a "%s" product (ID: %s).', $attribute_id, $product->get_type(), $product->get_id() ),
				__METHOD__
			);

			throw InvalidValue::not_in_allowed_list( 'attribute_id', array_keys( $attribute_types ) );
		}
	}

	/**
	 * Map attribute types.
	 *
	 * @throws InvalidClass If any of the given attribute classes do not implement the AttributeInterface.
	 */
	protected function map_attribute_types(): void {
		$this->attribute_types_map = array();
		foreach ( self::get_available_attribute_types() as $attribute_type ) {
			$this->validate_interface( $attribute_type, AttributeInterface::class );

			$attribute_id     = call_user_func( array( $attribute_type, 'get_id' ) );
			$applicable_types = call_user_func( array( $attribute_type, 'get_applicable_product_types' ) );

			/**
			 * Filters the list of applicable product types for each attribute.
			 *
			 * @param string[] $applicable_types Array of WooCommerce product types
			 * @param string   $attribute_type   Attribute class name (FQN)
			 * phpcs:disable WooCommerce.Commenting.CommentHooks.MissingSinceComment
			 */
			$applicable_types = apply_filters( "wc_pinterest_attribute_applicable_product_types_{$attribute_id}", $applicable_types, $attribute_type );

			foreach ( $applicable_types as $product_type ) {
				$this->attribute_types_map[ $product_type ]                  = $this->attribute_types_map[ $product_type ] ?? array();
				$this->attribute_types_map[ $product_type ][ $attribute_id ] = $attribute_type;
			}
		}
	}
}
