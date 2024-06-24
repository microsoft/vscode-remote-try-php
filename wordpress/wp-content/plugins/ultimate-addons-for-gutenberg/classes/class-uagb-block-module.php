<?php
/**
 * UAGB Block Module.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'UAGB_Block_Module' ) ) {

	/**
	 * Class doc
	 */
	class UAGB_Block_Module {

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
		public static $block_attributes = null;

		/**
		 * Block Assets
		 *
		 * @var block_assets
		 */
		public static $block_assets = null;

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
		 * Constructor
		 */
		public function __construct() {
			add_filter( 'uag_register_block_static_dependencies', array( __CLASS__, 'uag_register_block_static_dependencies' ) );
		}

		/**
		 * Add Blocks Static Assets.
		 *
		 * @since 2.0.0
		 *
		 * @param array $block_assets Block Assets.
		 * @return array
		 */
		public static function uag_register_block_static_dependencies( $block_assets ) {

			$blocks = self::get_blocks_info();

			foreach ( $blocks as $block ) {
				if ( ! isset( $block['static_dependencies'] ) ) {
					continue;
				}

				foreach ( $block['static_dependencies'] as $key => $static_dependencies ) {
					if ( ! isset( $static_dependencies['src'] ) ) {
						continue;
					}
					$block_assets[ $key ] = $static_dependencies;
				}
			}

			return $block_assets;
		}
		
		/**
		 * Get frontend CSS.
		 *
		 * @since 2.0.0
		 *
		 * @param string $slug Block slug.
		 * @param array  $attr Block attributes.
		 * @param string $id   Block id.
		 * @param bool   $is_gbs Is Global Block Style.
		 * @return array
		 */
		public static function get_frontend_css( $slug, $attr, $id, $is_gbs = false ) {
			return self::get_frontend_assets( $slug, $attr, esc_attr( $id ), 'css', $is_gbs );
		}

		/**
		 * Get frontend JS.
		 *
		 * @since 2.0.0
		 *
		 * @param string $slug Block slug.
		 * @param array  $attr Block attributes.
		 * @param string $id   Block id.
		 * @return array
		 */
		public static function get_frontend_js( $slug, $attr, $id ) {
			return self::get_frontend_assets( $slug, $attr, esc_attr( $id ), 'js' );
		}

		/**
		 * Filter GBS Placeholder Attributes.
		 *
		 * @param array $attributes Block attributes.
		 * @since 2.9.0
		 * @return array $attributes Block attributes by removing 0.001020304.
		 */
		public static function gbs_filter_placeholder_attributes( $attributes ) {
			if ( ! empty( $attributes ) && is_array( $attributes ) ) {
				foreach ( $attributes as $key => $attribute ) {
					// Replace 0.001020304 with empty string.
					if ( 0.001020304 === $attribute ) {
						$attributes[ $key ] = '';
					}
				}
				return $attributes;
			}
			return array();
		}
		
		/**
		 * Get frontend Assets.
		 *
		 * @since 2.0.0
		 *
		 * @param string $slug Block slug.
		 * @param array  $attr Block attributes.
		 * @param string $id   Block id.
		 * @param string $type Asset Type.
		 * @param bool   $is_gbs Is Global Block Style.
		 * @return array
		 */
		public static function get_frontend_assets( $slug, $attr, $id, $type = 'css', $is_gbs = false ) {

			$attr = self::gbs_filter_placeholder_attributes( $attr ); // Filter out GBS Placeholders if any added.

			$assets = array();

			if ( 'js' === $type ) {
				$assets = '';
			}

			$blocks_info = self::get_blocks_info();

			if ( ! isset( $blocks_info[ 'uagb/' . $slug ] ) || ! isset( $blocks_info[ 'uagb/' . $slug ]['dynamic_assets'] ) ) {
				return $assets;
			}

			$blocks = array(
				$slug => $blocks_info[ 'uagb/' . $slug ]['dynamic_assets'],
			);

			if ( isset( $blocks[ $slug ] ) ) {

				$main_dir = UAGB_DIR;

				if ( isset( $blocks[ $slug ]['plugin-dir'] ) ) {
					$main_dir = $blocks[ $slug ]['plugin-dir'];
				}

				$block_dir = $main_dir . 'includes/blocks/' . $blocks[ $slug ]['dir'];

				$assets_file = realpath( $block_dir . '/frontend.' . $type . '.php' );

				if ( is_string( $assets_file ) && file_exists( $assets_file ) ) {

					
					// Set default attributes.
					$attr_file = realpath( $block_dir . '/attributes.php' );
					
					if ( is_string( $attr_file ) && file_exists( $attr_file ) ) {
						
						$default_attr = include $attr_file;
						
						$attr = self::get_fallback_values( $default_attr, $attr );
						
						if ( ! empty( $attr['globalBlockStyleId'] ) && $is_gbs ) {
							$gbs_class = UAGB_Helper::get_gbs_selector( $attr['globalBlockStyleId'] );
						}
					}

					// Get Assets.
					$assets = include $assets_file;
				}
			}

			return $assets;

		}

		/**
		 * Get Widget List.
		 *
		 * @since 2.0.0
		 *
		 * @return array The Widget List.
		 */
		public static function get_blocks_info() {

			return uagb_block()->get_blocks();
		}

		/**
		 * Get Block Assets.
		 *
		 * @since 1.13.4
		 *
		 * @return array The Asset List.
		 */
		public static function get_block_dependencies() {

			$blocks = UAGB_Admin_Helper::get_block_options();

			if ( null === self::$block_assets && defined( 'UAGB_URL' ) ) {
				self::$block_assets = array(
					// Lib.
					'uagb-imagesloaded'          => array(
						'src'  => UAGB_URL . 'assets/js/imagesloaded.min.js',
						'dep'  => array( 'jquery' ),
						'type' => 'js',
					),
					'uagb-slick-js'              => array(
						'src'  => UAGB_URL . 'assets/js/slick.min.js',
						'dep'  => array( 'jquery' ),
						'type' => 'js',
					),
					'uagb-slick-css'             => array(
						'src'  => UAGB_URL . 'assets/css/slick.min.css',
						'dep'  => array(),
						'type' => 'css',
					),
					'uagb-masonry'               => array(
						'src'  => UAGB_URL . 'assets/js/isotope.min.js',
						'dep'  => array( 'jquery' ),
						'type' => 'js',
					),
					'uagb-cookie-lib'            => array(
						'src'        => UAGB_URL . 'assets/js/js_cookie.min.js',
						'dep'        => array( 'jquery' ),
						'skipEditor' => true,
						'type'       => 'js',
					),
					'uagb-bodymovin-js'          => array(
						'src'        => UAGB_URL . 'assets/js/uagb-bodymovin.min.js',
						'dep'        => array(),
						'skipEditor' => true,
						'type'       => 'js',
					),
					'uagb-countUp-js'            => array(
						'src'  => UAGB_URL . 'assets/js/countUp.min.js',
						'dep'  => array(),
						'type' => 'js',
					),
					'uagb-swiper-js'             => array(
						'src'        => UAGB_URL . 'assets/js/swiper-bundle.min.js',
						'dep'        => array(),
						'skipEditor' => true,
						'type'       => 'js',
					),
					'uagb-swiper-css'            => array(
						'src'  => UAGB_URL . 'assets/css/swiper-bundle.min.css',
						'dep'  => array(),
						'type' => 'css',
					),
					'uagb-aos-js'                => array(
						'src'  => UAGB_URL . 'assets/js/aos.min.js',
						'dep'  => array(),
						'type' => 'js',
					),
					'uagb-aos-css'               => array(
						'src'  => UAGB_URL . 'assets/css/aos.min.css',
						'dep'  => array(),
						'type' => 'css',
					),
					'uagb-block-positioning-js'  => array(
						'src'  => UAGB_URL . 'assets/js/spectra-block-positioning.min.js',
						'dep'  => array(),
						'type' => 'js',
					),
					'uagb-block-positioning-css' => array(
						'src'  => UAGB_URL . 'assets/css/spectra-block-positioning.min.css',
						'dep'  => array(),
						'type' => 'css',
					),
				);
			}

			return apply_filters( 'uag_register_block_static_dependencies', self::$block_assets );
		}

		/**
		 * Returns attributes array with default value wherever required.
		 *
		 * @param array $default_attr default attribute value array from attributes.php.
		 * @param array $attr saved attributes data from database.
		 * @return array
		 * @since 2.3.2
		 */
		public static function get_fallback_values( $default_attr, $attr ) {
			foreach ( $default_attr as $key => $value ) {
				// sets default value if key is not available in database.
				if ( ! isset( $attr[ $key ] ) ) {
					$attr[ $key ] = $value;
				}
			}

			return $attr;
		}
	}
}

/**
 *  Prepare if class 'UAGB_Block_Module' exist.
 *  Kicking this off by calling 'get_instance()' method
 */
UAGB_Block_Module::get_instance();
