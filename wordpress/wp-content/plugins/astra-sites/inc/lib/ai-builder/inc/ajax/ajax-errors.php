<?php
/**
 * Ai Builder Ajax Errors.
 *
 * @package Ai Builder
 */

namespace AiBuilder\Inc\Ajax;

use AiBuilder\Inc\Traits\Instance;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AjaxErrors
 */
class AjaxErrors {

	use Instance;

	/**
	 * Errors
	 *
	 * @access private
	 * @var array Errors strings.
	 * @since 1.0.0
	 */
	private static $errors = array();

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		self::$errors = array(
			'permission' => __( 'Sorry, you are not allowed to do this operation.', 'ai-builder', 'astra-sites' ),
			'nonce'      => __( 'Nonce validation failed', 'ai-builder', 'astra-sites' ),
			'default'    => __( 'Sorry, something went wrong.', 'ai-builder', 'astra-sites' ),
		);
	}

	/**
	 * Get error message.
	 *
	 * @param string $type Message type.
	 * @return string
	 */
	public function get_error_msg( $type ) {

		if ( ! isset( self::$errors[ $type ] ) ) {
			$type = 'default';
		}

		return self::$errors[ $type ];
	}
}

AjaxErrors::Instance();
