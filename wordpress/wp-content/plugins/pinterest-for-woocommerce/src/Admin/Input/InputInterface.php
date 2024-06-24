<?php
/**
 * Interface InputInterface
 *
 * @package Automattic\WooCommerce\Pinterest\Admin\Input
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Pinterest\Admin\Input;

defined( 'ABSPATH' ) || exit;

/**
 * Interface InputInterface
 */
interface InputInterface extends FormInterface {

	/**
	 * Get input ID.
	 *
	 * @return string|null
	 */
	public function get_id(): ?string;

	/**
	 * Set input ID.
	 *
	 * @param string|null $id Input ID.
	 *
	 * @return InputInterface
	 */
	public function set_id( ?string $id ): InputInterface;

	/**
	 * Get type.
	 *
	 * @return string
	 */
	public function get_type(): string;

	/**
	 * Get label.
	 *
	 * @return string|null
	 */
	public function get_label(): ?string;

	/**
	 * Set label.
	 *
	 * @param string|null $label Input label.
	 *
	 * @return InputInterface
	 */
	public function set_label( ?string $label ): InputInterface;

	/**
	 * Get description.
	 *
	 * @return string|null
	 */
	public function get_description(): ?string;

	/**
	 * Set description.
	 *
	 * @param string|null $description Input description.
	 *
	 * @return InputInterface
	 */
	public function set_description( ?string $description ): InputInterface;

	/**
	 * Get value.
	 *
	 * @return mixed
	 */
	public function get_value();

	/**
	 * Set value.
	 *
	 * @param mixed $value Input value.
	 *
	 * @return InputInterface
	 */
	public function set_value( $value ): InputInterface;

	/**
	 * Return the id used for the input's view.
	 *
	 * @return string
	 */
	public function get_view_id(): string;
}
