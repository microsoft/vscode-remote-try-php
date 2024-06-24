<?php
/**
 * UAGB Post.
 *
 * @package UAGB
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'UAGB_Image' ) ) {

	/**
	 * Class UAGB_Image.
	 */
	class UAGB_Image {


		/**
		 * Member Variable
		 *
		 * @since 2.0.0
		 * @var instance
		 */
		private static $instance;


		/**
		 *  Initiator
		 *
		 * @since 2.0.0
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'register_blocks' ) );
		}

		/**
		 * Register the Image block on server.
		 *
		 * @since 2.0.0
		 */
		public function register_blocks() {
			// Check if the register function exists.
			if ( ! function_exists( 'register_block_type' ) ) {
				return;
			}

			register_block_type(
				'uagb/image',
				array(
					'supports' => array(
						'color' => array(
							'__experimentalDuotone' => 'img',
							'text'                  => false,
							'background'            => false,
						),
					),
				)
			);
		}
	}

	/**
	 *  Prepare if class 'UAGB_Image' exist.
	 *  Kicking this off by calling 'get_instance()' method
	 */
	UAGB_Image::get_instance();
}
