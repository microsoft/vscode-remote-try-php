<?php
/**
 * Class AttributesForm
 *
 * @package Automattic\WooCommerce\Pinterest\Admin\Product\Attributes
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Pinterest\Admin\Product\Attributes;

use Automattic\WooCommerce\Pinterest\Admin\Input\Form;
use Automattic\WooCommerce\Pinterest\Admin\Input\FormException;
use Automattic\WooCommerce\Pinterest\Admin\Input\InputInterface;
use Automattic\WooCommerce\Pinterest\Admin\Input\Select;
use Automattic\WooCommerce\Pinterest\Admin\Input\SelectWithTextInput;
use Automattic\WooCommerce\Pinterest\Exception\InvalidValue;
use Automattic\WooCommerce\Pinterest\Exception\ValidateInterface;
use Automattic\WooCommerce\Pinterest\Product\Attributes\AttributeInterface;
use Automattic\WooCommerce\Pinterest\Product\Attributes\WithValueOptionsInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Class AttributesForm
 */
class AttributesForm extends Form {

	use ValidateInterface;

	/**
	 * Attribute types.
	 *
	 * @var string[]
	 */
	protected $attribute_types = array();

	/**
	 * AttributesForm constructor.
	 *
	 * @param string[] $attribute_types Attribute Types.
	 * @param array    $data            Attribute data.
	 */
	public function __construct( array $attribute_types, array $data = array() ) {
		foreach ( $attribute_types as $attribute_type ) {
			$this->add_attribute( $attribute_type );
		}

		parent::__construct( $data );
	}

	/**
	 * Return the data used for the input's view.
	 *
	 * @return array
	 */
	public function get_view_data(): array {
		$view_data = parent::get_view_data();

		// Add classes to hide/display attributes based on product type.
		foreach ( $view_data['children'] as $index => $input ) {
			if ( ! isset( $this->attribute_types[ $index ] ) ) {
				continue;
			}

			$attribute_id     = $index;
			$attribute_type   = $this->attribute_types[ $index ];
			$applicable_types = call_user_func( array( $attribute_type, 'get_applicable_product_types' ) );

			/**
			 * This filter is documented in AttributeManager::map_attribute_types
			 *
			 * @see AttributeManager::map_attribute_types
			 * phpcs:disable WooCommerce.Commenting.CommentHooks.MissingSinceComment
			 */
			$applicable_types = apply_filters( "wc_pinterest_attribute_applicable_product_types_{$attribute_id}", $applicable_types, $attribute_type );

			/**
			 * Filters the list of product types to hide the attribute for.
			 * phpcs:disable WooCommerce.Commenting.CommentHooks.MissingSinceComment
			 */
			$hidden_types = apply_filters( "wc_pinterest_attribute_hidden_product_types_{$attribute_id}", array() );

			$visible_types = array_diff( $applicable_types, $hidden_types );

			$input['pinterest_wrapper_class'] = $input['pinterest_wrapper_class'] ?? '';

			if ( ! empty( $visible_types ) ) {
				$input['pinterest_wrapper_class'] .= ' show_if_' . join( ' show_if_', $visible_types );
			}

			if ( ! empty( $hidden_types ) ) {
				$input['pinterest_wrapper_class'] .= ' hide_if_' . join( ' hide_if_', $hidden_types );
			}

			$view_data['children'][ $index ] = $input;
		}

		return $view_data;
	}

	/**
	 * Initialize input.
	 *
	 * @param InputInterface     $input     Input interface.
	 * @param AttributeInterface $attribute Attribute interface.
	 *
	 * @return InputInterface
	 */
	protected function init_input( InputInterface $input, AttributeInterface $attribute ) {
		$input->set_id( $attribute::get_id() )
			->set_name( $attribute::get_id() );

		$value_options = array();
		if ( $attribute instanceof WithValueOptionsInterface ) {
			$value_options = $attribute::get_value_options();
		}
		/**
		 * Filters the list of value options for the given attribute.
		 */
		$value_options = apply_filters( "wc_pinterest_product_attribute_value_options_{$attribute::get_id()}", $value_options );

		if ( ! empty( $value_options ) ) {
			if ( ! $input instanceof Select && ! $input instanceof SelectWithTextInput ) {
				$new_input = new SelectWithTextInput();
				$new_input->set_label( $input->get_label() )
					->set_description( $input->get_description() );

				return $this->init_input( $new_input, $attribute );
			}

			// Add a 'default' value option.
			$value_options = array( '' => __( 'Default', 'pinterest-for-woocommerce' ) ) + $value_options;

			$input->set_options( $value_options );
		}

		return $input;
	}

	/**
	 * Add an attribute to the form
	 *
	 * @param string      $attribute_type An attribute class extending AttributeInterface.
	 * @param string|null $input_type     An input class extending InputInterface to use for attribute input.
	 *
	 * @return AttributesForm
	 *
	 * @throws InvalidValue  If the attribute type is invalid or an invalid input type is specified for the attribute.
	 * @throws FormException If form is already submitted.
	 */
	public function add_attribute( string $attribute_type, ?string $input_type = null ): AttributesForm {
		$this->validate_interface( $attribute_type, AttributeInterface::class );

		// use the attribute's default input type if none provided.
		if ( empty( $input_type ) ) {
			$input_type = call_user_func( array( $attribute_type, 'get_input_type' ) );
		}

		$this->validate_interface( $input_type, InputInterface::class );

		$attribute_input = $this->init_input( new $input_type(), new $attribute_type() );
		$this->add( $attribute_input );

		$attribute_id                           = call_user_func( array( $attribute_type, 'get_id' ) );
		$this->attribute_types[ $attribute_id ] = $attribute_type;

		return $this;
	}

	/**
	 * Remove an attribute from the form
	 *
	 * @param string $attribute_type An attribute class extending AttributeInterface.
	 *
	 * @return AttributesForm
	 *
	 * @throws InvalidValue  If the attribute type is invalid or an invalid input type is specified for the attribute.
	 * @throws FormException If form is already submitted.
	 */
	public function remove_attribute( string $attribute_type ): AttributesForm {
		$this->validate_interface( $attribute_type, AttributeInterface::class );

		$attribute_id = call_user_func( array( $attribute_type, 'get_id' ) );
		unset( $this->attribute_types[ $attribute_id ] );
		$this->remove( $attribute_id );

		return $this;
	}

	/**
	 * Sets the input type for the given attribute.
	 *
	 * @param string $attribute_type Attribute type.
	 * @param string $input_type     Input type.
	 *
	 * @return $this
	 *
	 * @throws FormException If form is already submitted.
	 */
	public function set_attribute_input( string $attribute_type, string $input_type ): AttributesForm {
		if ( $this->is_submitted ) {
			throw FormException::cannot_modify_submitted();
		}

		$this->validate_interface( $attribute_type, AttributeInterface::class );
		$this->validate_interface( $input_type, InputInterface::class );

		$attribute_id = call_user_func( array( $attribute_type, 'get_id' ) );
		if ( $this->has( $attribute_id ) ) {
			$this->children[ $attribute_id ] = $this->init_input( new $input_type(), new $attribute_type() );
		}

		return $this;
	}
}
