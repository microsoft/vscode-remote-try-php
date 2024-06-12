<?php
/**
 * UAGB - Lottie
 *
 * @package UAGB
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'UAGB_Lottie' ) ) {

	/**
	 * Class UAGB_Lottie.
	 *
	 * @since 1.20.0
	 */
	class UAGB_Lottie {

		/**
		 * Member Variable
		 *
		 * @since 1.20.0
		 * @var instance
		 */
		private static $instance;

		/**
		 *  Initiator
		 *
		 * @since 1.20.0
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @since 1.20.0
		 */
		public function __construct() {

			// Activation hook.
			add_action( 'init', array( $this, 'register_blocks' ) );
		}

		/**
		 * Registers the `uagb/lottie` block on server.
		 *
		 * @since 1.20.0
		 */
		public function register_blocks() {

			// Check if the register function exists.
			if ( ! function_exists( 'register_block_type' ) ) {
				return;
			}

			register_block_type(
				'uagb/lottie',
				array(
					'attributes'      => array(
						'block_id'         => array(
							'type' => 'string',
						),
						'align'            => array(
							'type'    => 'string',
							'default' => 'center',
						),
						'lottieURl'        => array(
							'type'    => 'string',
							'default' => '',
						),
						'lottieSource'     => array(
							'type'    => 'string',
							'default' => 'library',
						),
						'jsonLottie'       => array(
							'type' => 'object',
						),
						// Controls.
						'loop'             => array(
							'type'    => 'boolean',
							'default' => true,
						),
						'speed'            => array(
							'type'    => 'number',
							'default' => 1,
						),
						'reverse'          => array(
							'type'    => 'boolean',
							'default' => false,
						),
						'playOnHover'      => array(
							'type'    => 'boolean',
							'default' => false,
						),
						'playOn'           => array(
							'type'    => 'string',
							'default' => 'none',
						),
						// Style.
						'height'           => array(
							'type' => 'number',
						),
						'heightTablet'     => array(
							'type' => 'number',
						),
						'heightMob'        => array(
							'type' => 'number',
						),
						'width'            => array(
							'type' => 'number',
						),
						'widthTablet'      => array(
							'type' => 'number',
						),
						'widthMob'         => array(
							'type' => 'number',
						),
						'backgroundColor'  => array(
							'type'    => 'string',
							'default' => '',
						),
						'backgroundHColor' => array(
							'type'    => 'string',
							'default' => '',
						),
						'isPreview'        => array(
							'type'    => 'boolean',
							'default' => false,
						),
					),
					'render_callback' => array( $this, 'render_html' ),
				)
			);
		}

		/**
		 * Render Lottie HTML.
		 *
		 * @param array $attributes Array of block attributes.
		 *
		 * @since 1.20.0
		 */
		public function render_html( $attributes ) {

			$block_id = '';

			if ( isset( $attributes['block_id'] ) ) {
				$block_id = $attributes['block_id'];
			}

			$desktop_class = '';
			$tab_class     = '';
			$mob_class     = '';

			if ( array_key_exists( 'UAGHideDesktop', $attributes ) || array_key_exists( 'UAGHideTab', $attributes ) || array_key_exists( 'UAGHideMob', $attributes ) ) {

				$desktop_class = ( isset( $attributes['UAGHideDesktop'] ) ) ? 'uag-hide-desktop' : '';

				$tab_class = ( isset( $attributes['UAGHideTab'] ) ) ? 'uag-hide-tab' : '';

				$mob_class = ( isset( $attributes['UAGHideMob'] ) ) ? 'uag-hide-mob' : '';
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

			$main_classes = array(
				'uagb-block-' . $block_id,
				'uagb-lottie__outer-wrap',
				'uagb-lottie__' . $attributes['align'],
				$desktop_class,
				$tab_class,
				$mob_class,
				$zindex_extention_enabled ? 'uag-blocks-common-selector' : '',
			);

			ob_start();

			?>
				<div class = "wp-block-uagb-lottie">
					<div class = "<?php echo esc_attr( implode( ' ', $main_classes ) ); ?>" style="<?php echo esc_attr( implode( '', $zindex_wrap ) ); ?>">
					</div>
				</div>
			<?php
				return ob_get_clean();
		}
	}

	/**
	 *  Prepare if class 'UAGB_Lottie' exist.
	 *  Kicking this off by calling 'get_instance()' method
	 */
	UAGB_Lottie::get_instance();
}
