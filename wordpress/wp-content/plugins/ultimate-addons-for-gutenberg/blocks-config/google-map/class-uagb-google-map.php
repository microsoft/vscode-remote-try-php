<?php
/**
 * UAGB Google Map.
 *
 * @package UAGB
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'UAGB_Google_Map' ) ) {

	/**
	 * Class UAGB_Google_Map.
	 */
	class UAGB_Google_Map {


		/**
		 * Member Variable
		 *
		 * @since 2.6.4
		 * @var instance
		 */
		private static $instance;

		/**
		 *  Initiator
		 *
		 * @since 2.6.4
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
		 * Registers the `core/latest-posts` block on server.
		 *
		 * @since 2.6.4
		 */
		public function register_blocks() {
			// Check if the register function exists.
			if ( ! function_exists( 'register_block_type' ) ) {
				return;
			}

			register_block_type(
				'uagb/google-map',
				array(
					'attributes'      => array(
						'block_id'            => array(
							'type' => 'string',
						),
						'address'             => array(
							'type'    => 'string',
							'default' => 'Brainstorm Force',
						),
						'height'              => array(
							'type'    => 'number',
							'default' => 300,
						),
						'heightTablet'        => array(
							'type'    => 'number',
							'default' => 300,
						),
						'heightMobile'        => array(
							'type'    => 'number',
							'default' => 300,
						),
						'zoom'                => array(
							'type'    => 'number',
							'default' => 12,
						),
						'language'            => array(
							'type'    => 'string',
							'default' => 'en',
						),
						'isPreview'           => array(
							'type'    => 'boolean',
							'default' => false,
						),
						'enableSatelliteView' => array(
							'type'    => 'boolean',
							'default' => false,
						),
					),
					'render_callback' => array( $this, 'google_map_callback' ),
				)
			);
		}

		/**
		 * Renders the Google Map block on server.
		 *
		 * @param array $attributes Array of block attributes.
		 *
		 * @since 2.6.4
		 */
		public function google_map_callback( $attributes ) {
			$desktop_class = '';
			$tab_class     = '';
			$mob_class     = '';

			/**
			 * Added filter to attributes to support Dynamic Content.
			 *
			 * @since 2.7.0
			 * @hooked Pro -> DynamicContent->uagb_google_map_block_attributes
			 * */
			$attributes = apply_filters( 'uagb_google_map_block_attributes', $attributes );
			
			if ( array_key_exists( 'UAGHideDesktop', $attributes ) || array_key_exists( 'UAGHideTab', $attributes ) || array_key_exists( 'UAGHideMob', $attributes ) ) {
				$desktop_class = ( isset( $attributes['UAGHideDesktop'] ) ) ? 'uag-hide-desktop' : '';
				$tab_class     = ( isset( $attributes['UAGHideTab'] ) ) ? 'uag-hide-tab' : '';
				$mob_class     = ( isset( $attributes['UAGHideMob'] ) ) ? 'uag-hide-mob' : '';
			}
			
			$zindex_desktop           = '';
			$zindex_tablet            = '';
			$zindex_mobile            = '';
			$zindex_wrap              = array();
			$zindex_extention_enabled = ( isset( $attributes['zIndex'] ) || isset( $attributes['zIndexTablet'] ) || isset( $attributes['zIndexMobile'] ) );
			
			if ( $zindex_extention_enabled ) {
				$zindex_desktop = ( isset( $attributes['zIndex'] ) ) ? '--z-index-desktop:' . $attributes['zIndex'] . ';' : false;
				$zindex_tablet  = ( isset( $attributes['zIndexTablet'] ) ) ? '--z-index-tablet:' . $attributes['zIndexTablet'] . ';' : false;
				$zindex_mobile  = ( isset( $attributes['zIndexMobile'] ) ) ? '--z-index-mobile:' . $attributes['zIndexMobile'] . ';' : false;
				
				if ( $zindex_desktop ) {
					array_push( $zindex_wrap, $zindex_desktop );
				}
				
				if ( $zindex_tablet ) {
					array_push( $zindex_wrap, $zindex_tablet );
				}
				
				if ( $zindex_mobile ) {
					array_push( $zindex_wrap, $zindex_mobile );
				}
			}
			
			$block_id     = 'uagb-block-' . $attributes['block_id'];
			$main_classes = array(
				'wp-block-uagb-google-map',
				'uagb-google-map__wrap',
				$block_id,
				$desktop_class,
				$tab_class,
				$mob_class,
				$zindex_extention_enabled ? 'uag-blocks-common-selector' : '',
				( is_array( $attributes ) && isset( $attributes['className'] ) ) ? $attributes['className'] : '',
			);

			$address  = ! empty( $attributes['address'] ) ? rawurlencode( $attributes['address'] ) : rawurlencode( 'Brainstorm Force' );
			$zoom     = ! empty( $attributes['zoom'] ) ? $attributes['zoom'] : 12;
			$language = ! empty( $attributes['language'] ) ? $attributes['language'] : 'en';
			$height   = ! empty( $attributes['height'] ) ? $attributes['height'] : 300;
			$map_type = 'm';

			if ( is_array( $attributes ) && isset( $attributes['enableSatelliteView'] ) ) {
				$map_type = $attributes['enableSatelliteView'] ? 'k' : 'm';
			}


			$updated_url = add_query_arg(
				array(
					'q'      => $address,
					'z'      => $zoom,
					'hl'     => $language,
					't'      => $map_type,
					'output' => 'embed',
					'iwloc'  => 'near',
				),
				'https://maps.google.com/maps' 
			);
			ob_start();
			?>
			<div 
			class="<?php echo esc_attr( implode( ' ', $main_classes ) ); ?>"
			style="<?php echo esc_attr( implode( '', $zindex_wrap ) ); ?>" >
				<embed
					class="uagb-google-map__iframe"
					title="<?php _e( 'Google Map for ', 'ultimate-addons-for-gutenberg' ) . $address; ?>"
					src="<?php echo esc_url_raw( $updated_url ); ?>"
					width="640"
					height="<?php echo floatval( $height ); ?>"
					loading="lazy"
				></embed>
			</div>
			<?php
			return ob_get_clean();
		}
	}

	/**
	 *  Prepare if class 'UAGB_Google_Map' exist.
	 *  Kicking this off by calling 'get_instance()' method
	 */
	UAGB_Google_Map::get_instance();
}
