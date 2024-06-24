<?php
/**
 * Ajax Base.
 *
 * @package uag
 */

namespace UagAdmin\Ajax;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use UagAdmin\Ajax\Ajax_Errors;

/**
 * Class Ajax_Base.
 */
abstract class Ajax_Base {

	/**
	 * Ajax action prefix.
	 *
	 * @var string
	 */
	private $prefix = 'uag';

	/**
	 * Erros class instance.
	 *
	 * @var object
	 */
	public $errors = null;

	/**
	 * Constructor
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$this->errors = Ajax_Errors::get_instance();
	}

	/**
	 * Register ajax events.
	 *
	 * @param array $ajax_events Ajax events.
	 */
	public function init_ajax_events( $ajax_events ) {

		if ( ! empty( $ajax_events ) ) {

			foreach ( $ajax_events as $ajax_event ) {
				add_action( 'wp_ajax_' . $this->prefix . '_' . $ajax_event, array( $this, $ajax_event ) );

				$this->localize_ajax_action_nonce( $ajax_event );
			}
		}
	}

	/**
	 * Localize nonce for ajax call.
	 *
	 * @param string $action Action name.
	 * @return void
	 */
	public function localize_ajax_action_nonce( $action ) {

		if ( current_user_can( 'manage_options' ) ) {

			add_filter(
				'uag_react_admin_localize',
				function( $localize ) use ( $action ) {

					$localize[ $action . '_nonce' ] = wp_create_nonce( $this->prefix . '_' . $action );
					return $localize;
				}
			);

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
