<?php
/**
 * AiBuilder Ajax Base.
 *
 * @package AiBuilder
 */

namespace AiBuilder\Inc\Ajax;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AiBuilder\Inc\Ajax\AjaxErrors;

/**
 * Class Admin_Menu.
 */
abstract class AjaxBase {

	/**
	 * Ajax action prefix.
	 *
	 * @var string
	 */
	private $prefix = 'astra-sites';

	/**
	 * Erros class instance.
	 *
	 * @var object
	 */
	public $errors = null;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->errors = AjaxErrors::Instance();
	}

	/**
	 * Register ajax events.
	 *
	 * @param array $ajax_events Ajax events.
	 */
	public function init_ajax_events( $ajax_events ) {

		if ( ! empty( $ajax_events ) ) {

			foreach ( $ajax_events as $ajax_event ) {
				add_action( 'wp_ajax_' . $this->prefix . '-' . $ajax_event, array( $this, $ajax_event ) );
			}
		}
	}

	/**
	 * Get ajax error message.
	 *
	 * @param string $type Message type.
	 * @return string
	 */
	public function get_error_msg( $type ) {

		return $this->errors->get_error_msg( $type );
	}
}
