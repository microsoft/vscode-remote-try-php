<?php
/**
 * Class Form
 *
 * @package Automattic\WooCommerce\Pinterest\Admin\Input
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Pinterest\Admin\Input;

use Automattic\WooCommerce\Pinterest\Exception\ValidateInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Class Form
 */
class Form implements FormInterface {

	use ValidateInterface;

	/**
	 * Form name.
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * Form data.
	 *
	 * @var mixed
	 */
	protected $data;

	/**
	 * Children.
	 *
	 * @var FormInterface[]
	 */
	protected $children = array();

	/**
	 * Parent form.
	 *
	 * @var FormInterface
	 */
	protected $parent;

	/**
	 * Is form submitted.
	 *
	 * @var bool
	 */
	protected $is_submitted = false;

	/**
	 * Form constructor.
	 *
	 * @param mixed $data Form data.
	 */
	public function __construct( $data = null ) {
		$this->set_data( $data );
	}

	/**
	 * Get form name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Set form name.
	 *
	 * @param string $name Form name.
	 *
	 * @return FormInterface
	 */
	public function set_name( string $name ): FormInterface {
		$this->name = $name;

		return $this;
	}

	/**
	 * Get form children.
	 *
	 * @return FormInterface[]
	 */
	public function get_children(): array {
		return $this->children;
	}

	/**
	 * Add a child form.
	 *
	 * @param FormInterface $form Form.
	 *
	 * @return FormInterface
	 *
	 * @throws FormException If form is already submitted.
	 */
	public function add( FormInterface $form ): FormInterface {
		if ( $this->is_submitted ) {
			throw FormException::cannot_modify_submitted();
		}

		$this->children[ $form->get_name() ] = $form;
		$form->set_parent( $this );

		return $this;
	}

	/**
	 * Remove a child with the given name from the form's children.
	 *
	 * @param string $name Form name.
	 *
	 * @return FormInterface
	 *
	 * @throws FormException If form is already submitted.
	 */
	public function remove( string $name ): FormInterface {
		if ( $this->is_submitted ) {
			throw FormException::cannot_modify_submitted();
		}

		if ( $this->has( $name ) ) {
			$this->children[ $name ]->set_parent( null );
			unset( $this->children[ $name ] );
		}

		return $this;
	}

	/**
	 * Whether the form contains a child with the given name.
	 *
	 * @param string $name Form name.
	 *
	 * @return bool
	 */
	public function has( string $name ): bool {
		return isset( $this->children[ $name ] );
	}

	/**
	 * Set parent form.
	 *
	 * @param FormInterface|null $form Form.
	 *
	 * @return void
	 */
	public function set_parent( ?FormInterface $form ): void {
		$this->parent = $form;
	}

	/**
	 * Get parent form.
	 *
	 * @return FormInterface|null
	 */
	public function get_parent(): ?FormInterface {
		return $this->parent;
	}

	/**
	 * Return the form's data.
	 *
	 * @return mixed
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * Set the form's data.
	 *
	 * @param mixed $data Form data.
	 *
	 * @return void
	 */
	public function set_data( $data ): void {
		if ( is_array( $data ) && ! empty( $this->children ) ) {
			$this->data = $this->map_children_data( $data );
		} else {
			if ( is_string( $data ) ) {
				$data = trim( $data );
			}
			$this->data = $data;
		}
	}

	/**
	 * Maps the data to each child and returns the mapped data.
	 *
	 * @param array $data Form data.
	 *
	 * @return array
	 */
	protected function map_children_data( array $data ): array {
		$children_data = array();
		foreach ( $data as $key => $datum ) {
			if ( isset( $this->children[ $key ] ) ) {
				$this->children[ $key ]->set_data( $datum );
				$children_data[ $key ] = $this->children[ $key ]->get_data();
			}
		}

		return $children_data;
	}

	/**
	 * Submit the form.
	 *
	 * @param array $submitted_data Submitted form data.
	 */
	public function submit( array $submitted_data = array() ): void {
		if ( ! $this->is_submitted ) {
			$this->is_submitted = true;
			$this->set_data( $submitted_data );
		}
	}

	/**
	 * Return the data used for the form's view.
	 *
	 * @return array
	 */
	public function get_view_data(): array {
		$view_data = array(
			'name'     => $this->get_view_name(),
			'is_root'  => $this->is_root(),
			'children' => array(),
		);

		foreach ( $this->get_children() as $index => $form ) {
			$view_data['children'][ $index ] = $form->get_view_data();
		}

		return $view_data;
	}

	/**
	 * Return the name used for the form's view.
	 *
	 * @return string
	 */
	public function get_view_name(): string {
		return $this->is_root() ? sprintf( 'pinterest_%s', $this->get_name() ) : sprintf( '%s[%s]', $this->get_parent()->get_view_name(), $this->get_name() );
	}

	/**
	 * Whether this is the root form (i.e. has no parents).
	 *
	 * @return bool
	 */
	public function is_root(): bool {
		return null === $this->parent;
	}

	/**
	 * Whether the form has been already submitted.
	 *
	 * @return bool
	 */
	public function is_submitted(): bool {
		return $this->is_submitted;
	}
}
