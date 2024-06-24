<?php
/**
 * UAGB Block.
 *
 * @since 2.1.0
 *
 * @package uagb
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'UAGB_Block' ) ) {

	/**
	 * Class doc
	 */
	class UAGB_Block {

		/**
		 * Member Variable
		 *
		 * @var instance
		 */
		private static $instance;

		/**
		 * Block Attributes
		 *
		 * @var block_attributes
		 */
		private static $blocks = null;

		/**
		 *  Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Register a Block.
		 *
		 * @since 2.1.0
		 * @param string $block_file Block File Path.
		 */
		public function register( $block_file ) {

			$block_slug = '';
			$block_data = array();

			include $block_file;
			
			if ( ! empty( $block_slug ) && ! empty( $block_data ) ) {
				self::$blocks[ $block_slug ] = apply_filters( "spectra_{$block_slug}_blockdata", $block_data );
			}
		}

		/**
		 * Register all UAG Lite Blocks.
		 *
		 * @since 2.1.0
		 */
		public function register_blocks() {

			self::$blocks = array();

			$block_files = glob( UAGB_DIR . 'includes/blocks/*/block.php' );

			foreach ( $block_files as $block_file ) {
				$this->register( $block_file );
			}

			do_action( 'uag_register_block', $this );
		}

		/**
		 * Gives all Blocks.
		 *
		 * @since 2.1.0
		 */
		public function get_blocks() {

			if ( null === self::$blocks ) {

				$this->register_blocks();
			}

			return self::$blocks;
		}
	}

	/**
	 * Gives UAGB_Block object
	 *
	 * @since 2.1.0
	 *
	 * @return object
	 */
	function uagb_block() {
		return UAGB_Block::get_instance();
	}
}
