<?php
/**
 * UAGB Block Positioning.
 *
 * @since 2.8.0
 * @package uagb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


if ( ! class_exists( 'UAGB_Block_Positioning' ) ) {

	/**
	 * Class UAGB_Block_Positioning.
	 * 
	 * @since 2.8.0
	 */
	class UAGB_Block_Positioning {

		/**
		 * The instance of this class, or null if it has not been created yet.
		 *
		 * @since 2.8.0
		 * @var object|null instance
		 */
		private static $instance = null;

		/**
		 * The Initiator.
		 *
		 * @since 2.8.0
		 * @return object  An instance of this class.
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * The Constructor.
		 * 
		 * @since 2.8.0
		 * @return void
		 */
		public function __construct() {
			add_filter( 'uagb_render_block', array( $this, 'add_positioning_classes' ), 10, 2 );
		}

		/**
		 * Add the required positioning classes if needed.
		 *
		 * @param string $block_content  The block content.
		 * @param array  $block          The block data.
		 * @since 2.8.0
		 * @return string                The block content after updation.
		 */
		public function add_positioning_classes( $block_content, $block ) {
			if ( empty( $block['blockName'] ) ) {
				return $block_content;
			}
			
			// Check $block_content is string or not.
			if ( ! is_string( $block_content ) || false === strpos( $block['blockName'], 'uagb' ) ) {
				return $block_content;
			}
			
			// Filter image block content.
			if ( 'uagb/image' === $block['blockName'] ) {
				$block_content = $this->image_block_content_filters( $block_content, $block );
			}

			// Return early if this doesn't need any positioning classes.
			if (
				'uagb/container' !== $block['blockName']
				|| empty( $block['attrs']['UAGPosition'] )
			) {
				return $block_content;
			}

			// Create the class to prepend to this block's class list.
			$prepended_classes = 'uagb-position__sticky';
			
			// Once all the additional classes have been added, add the start of the block selector.
			$prepended_classes .= ' wp-block-uagb-';

			// Replace the closest opening block selector with the prepended classes.
			$updated_content = preg_replace( '/wp-block-uagb-/', $prepended_classes, $block_content, 1 );

			// If an error was encountered, null would have been passed. Keep the content as it is when this happens.
			if ( $updated_content ) {
				$block_content = $updated_content;
			}

			return $block_content;
		}

		/**
		 * This function is used to filter image block content.
		 *
		 * @param string $block_content Image block content.
		 * @param array  $block Image block data.
		 * @since 2.10.2
		 * @return string
		 */
		public function image_block_content_filters( $block_content, $block ) {
			// Remove srcset attribute from image.
			if ( empty( $block['attrs']['id'] ) && ! empty( $block['attrs']['url'] ) && strpos( $block_content, 'srcset' ) ) {
				$remove_srcset_from_content = preg_replace( '/srcset="([^"]*)"/', '', $block_content );
				if ( $remove_srcset_from_content ) {
					$block_content = $remove_srcset_from_content;
				}
				
				return $block_content;
			}

			/**
			 * For migrating http and https.
			 */
			if ( empty( $block['attrs']['id'] ) || empty( $block['attrs']['url'] ) ) {
				return $block_content;
			}

			// Check url protocol.
			$current_url_protocol   = wp_parse_url( get_site_url(), PHP_URL_SCHEME );
			$attribute_url_protocol = wp_parse_url( $block['attrs']['url'], PHP_URL_SCHEME );

			if ( ! is_string( $current_url_protocol ) || ! is_string( $attribute_url_protocol ) || $current_url_protocol === $attribute_url_protocol ) {
				return $block_content;
			}

			foreach ( array( 'url', 'urlMobile', 'urlTablet' ) as $replace_attributes_url ) {
				if ( empty( $block['attrs'][ $replace_attributes_url ] ) ) {
					continue;
				}

				if ( false === strpos( $block_content, $block['attrs'][ $replace_attributes_url ] ) ) {
					continue;
				}

				// Replace http with https with current url protocol.
				$migrated_urls = str_replace( $attribute_url_protocol, $current_url_protocol, $block['attrs'][ $replace_attributes_url ] );

				$block_content = str_replace( $block['attrs'][ $replace_attributes_url ], $migrated_urls, $block_content );
			}
			
			return $block_content;
		}
	}

	/**
	 *  Prepare if class 'UAGB_Block_Positioning' exist.
	 *  Kicking this off by calling 'get_instance()' method
	 */
	UAGB_Block_Positioning::get_instance();
}
