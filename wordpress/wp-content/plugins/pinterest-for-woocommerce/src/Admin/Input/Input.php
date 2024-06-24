<?php
/**
 * Class Input
 *
 * @package Automattic\WooCommerce\Pinterest\Admin\Input
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Pinterest\Admin\Input;

defined( 'ABSPATH' ) || exit;

/**
 * Class Input
 */
class Input extends Form implements InputInterface {

	/**
	 * Input ID.
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * Input type.
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * Input label.
	 *
	 * @var string
	 */
	protected $label;

	/**
	 * Input description.
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * Input value.
	 *
	 * @var mixed
	 */
	protected $value;

	/**
	 * Input constructor.
	 *
	 * @param string $type Input type.
	 */
	public function __construct( string $type ) {
		$this->type = $type;
		parent::__construct();
	}

	/**
	 * Get ID.
	 *
	 * @return string|null
	 */
	public function get_id(): ?string {
		return $this->id;
	}

	/**
	 * Get type.
	 *
	 * @return string
	 */
	public function get_type(): string {
		return $this->type;
	}

	/**
	 * Get label.
	 *
	 * @return string|null
	 */
	public function get_label(): ?string {
		return $this->label;
	}

	/**
	 * Get description.
	 *
	 * @return string|null
	 */
	public function get_description(): ?string {
		return $this->description;
	}

	/**
	 * Get value.
	 *
	 * @return mixed
	 */
	public function get_value() {
		return $this->get_data();
	}

	/**
	 * Set ID.
	 *
	 * @param string|null $id Input ID.
	 *
	 * @return InputInterface
	 */
	public function set_id( ?string $id ): InputInterface {
		$this->id = $id;

		return $this;
	}

	/**
	 * Set label.
	 *
	 * @param string|null $label Input label.
	 *
	 * @return InputInterface
	 */
	public function set_label( ?string $label ): InputInterface {
		$this->label = $label;

		return $this;
	}

	/**
	 * Set description.
	 *
	 * @param string|null $description Input description.
	 *
	 * @return InputInterface
	 */
	public function set_description( ?string $description ): InputInterface {
		$this->description = $description;

		return $this;
	}

	/**
	 * Set value.
	 *
	 * @param mixed $value Input value.
	 *
	 * @return InputInterface
	 */
	public function set_value( $value ): InputInterface {
		$this->set_data( $value );

		return $this;
	}

	/**
	 * Return the data used for the input's view.
	 *
	 * @return array
	 */
	public function get_view_data(): array {
		$view_data = array(
			'id'          => $this->get_view_id(),
			'type'        => $this->get_type(),
			'label'       => $this->get_label(),
			'value'       => $this->get_value(),
			'description' => $this->get_description(),
			'desc_tip'    => true,
		);

		return array_merge( parent::get_view_data(), $view_data );
	}

	/**
	 * Return the id used for the input's view.
	 *
	 * @return string
	 */
	public function get_view_id(): string {
		$parent = $this->get_parent();
		if ( $parent instanceof InputInterface ) {
			return sprintf( '%s_%s', $parent->get_view_id(), $this->get_id() );
		} elseif ( $parent instanceof FormInterface ) {
			return sprintf( '%s_%s', $parent->get_view_name(), $this->get_id() );
		}

		return sprintf( 'pinterest_%s', $this->get_name() );
	}
}
