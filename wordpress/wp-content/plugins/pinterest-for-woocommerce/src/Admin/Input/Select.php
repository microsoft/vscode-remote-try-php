<?php
/**
 * Class Select
 *
 * @package Automattic\WooCommerce\Pinterest\Admin\Input
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Pinterest\Admin\Input;

defined( 'ABSPATH' ) || exit;

/**
 * Class Select
 */
class Select extends Input {
	/**
	 * Input options.
	 *
	 * @var array
	 */
	protected $options = array();

	/**
	 * Select constructor.
	 */
	public function __construct() {
		parent::__construct( 'select' );
	}

	/**
	 * Get options.
	 *
	 * @return array
	 */
	public function get_options(): array {
		return $this->options;
	}

	/**
	 * Set options.
	 *
	 * @param array $options List of options.
	 *
	 * @return $this
	 */
	public function set_options( array $options ): Select {
		$this->options = $options;

		return $this;
	}

	/**
	 * Return the data used for the input's view.
	 *
	 * @return array
	 */
	public function get_view_data(): array {
		$view_data            = parent::get_view_data();
		$view_data['options'] = $this->get_options();

		// Add custom class.
		$view_data['class'] = 'select short';

		return $view_data;
	}
}
