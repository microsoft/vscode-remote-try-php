<?php
/**
 * Spectra - Icon
 *
 * @since 2.12.5
 * @package UAGB
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Spectra_Icon' ) ) {

	/**
	 * Class Spectra_Icon.
	 * 
	 * @since 2.12.5
	 */
	final class Spectra_Icon {

		/**
		 * Member Variable
		 *
		 * @since 2.12.5
		 * @var Spectra_Icon
		 */
		private static $instance;

		/**
		 *  Initiator
		 *
		 * @since 2.12.5
		 * @return Spectra_Icon
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 * 
		 * @since 2.12.5
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'register_icon' ) );
			
		}

		/**
		 * Registers the `icon` block on server.
		 *
		 * @since 2.12.5
		 * @return void
		 */
		public function register_icon() {
			// Check if the register function exists.
			if ( ! function_exists( 'register_block_type' ) ) {
				return;
			}

			$icon_border_attributes = array();
			$icon_border_attributes = UAGB_Block_Helper::uag_generate_php_border_attribute( 'icon' ); // @phpstan-ignore-line
				
			register_block_type(
				'uagb/icon',
				array(
					'attributes'      => array_merge(
						array(
							'icon'           => array(
								'type'    => 'string',
								'default' => 'circle-check',
							),
							// Size.
							'iconSize'       => array(
								'type'    => 'number',
								'default' => 40,
							),
							'iconSizeTablet' => array(
								'type' => 'number',
							),
							'iconSizeMobile' => array(
								'type' => 'number',
							),
							'iconSizeUnit'   => array(
								'type'    => 'string',
								'default' => 'px',
							),
						),
						// Alignment.
						array(
							'align'       => array(
								'type'    => 'string',
								'default' => 'center',
							),
							'alignTablet' => array(
								'type'    => 'string',
								'default' => '',
							),
							'alignMobile' => array(
								'type'    => 'string',
								'default' => '',
							),
						),
						// Color.
						array(
							'iconColor'                    => array(
								'type'    => 'string',
								'default' => '#333',
							),
							'iconBorderColor'              => array(
								'type'    => 'string',
								'default' => '',
							),
							'iconBackgroundColorType'      => array(
								'type'    => 'string',
								'default' => 'classic',
							),
							'iconBackgroundColor'          => array(
								'type'    => 'string',
								'default' => '',
							),
							'iconBackgroundGradientColor'  => array(
								'type'    => 'string',
								'default' => 'linear-gradient(90deg, rgb(155, 81, 224) 0%, rgb(6, 147, 227) 100%)',
							),
							'iconHoverColor'               => array(
								'type'    => 'string',
								'default' => '',
							),
							'iconHoverBackgroundColorType' => array(
								'type'    => 'string',
								'default' => 'classic',
							),
							'iconHoverBackgroundColor'     => array(
								'type' => 'string',
							),
							'iconHoverBackgroundGradientColor' => array(
								'type'    => 'string',
								'default' => 'linear-gradient(90deg, rgb(155, 81, 224) 0%, rgb(6, 147, 227) 100%)',
							),
						),
						// Rotation.
						array(
							'rotation'     => array(
								'type'    => 'number',
								'default' => 0,
							),
							'rotationUnit' => array(
								'type'    => 'string',
								'default' => 'deg',
							),
							'block_id'     => array(
								'type' => 'string',
							),
						),
						// Link related attributes.
						array(
							'link'                  => array(
								'type'    => 'string',
								'default' => '',
							),
							'target'                => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'disableLink'           => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'iconAccessabilityMode' => array(
								'type'    => 'string',
								'default' => 'svg',
							),
							'iconAccessabilityDesc' => array(
								'type'    => 'string',
								'default' => '',
							),
						),
						// Padding.
						array(
							'iconTopPadding'          => array(
								'type'    => 'number',
								'default' => 5,
							),
							'iconRightPadding'        => array(
								'type'    => 'number',
								'default' => 5,
							),
							'iconLeftPadding'         => array(
								'type'    => 'number',
								'default' => 5,
							),
							'iconBottomPadding'       => array(
								'type'    => 'number',
								'default' => 5,
							),
							'iconTopTabletPadding'    => array(
								'type' => 'number',
							),
							'iconRightTabletPadding'  => array(
								'type' => 'number',
							),
							'iconLeftTabletPadding'   => array(
								'type' => 'number',
							),
							'iconBottomTabletPadding' => array(
								'type' => 'number',
							),
							'iconTopMobilePadding'    => array(
								'type' => 'number',
							),
							'iconRightMobilePadding'  => array(
								'type' => 'number',
							),
							'iconLeftMobilePadding'   => array(
								'type' => 'number',
							),
							'iconBottomMobilePadding' => array(
								'type' => 'number',
							),
							'iconPaddingUnit'         => array(
								'type'    => 'string',
								'default' => 'px',
							),
							'iconTabletPaddingUnit'   => array(
								'type'    => 'string',
								'default' => 'px',
							),
							'iconMobilePaddingUnit'   => array(
								'type'    => 'string',
								'default' => 'px',
							),
							'iconPaddingLink'         => array(
								'type'    => 'boolean',
								'default' => false,
							),
						),
						// Margin.
						array(
							'iconTopMargin'              => array(
								'type' => 'number',
							),
							'iconRightMargin'            => array(
								'type' => 'number',
							),
							'iconLeftMargin'             => array(
								'type' => 'number',
							),
							'iconBottomMargin'           => array(
								'type' => 'number',
							),
							'iconTopTabletMargin'        => array(
								'type' => 'number',
							),
							'iconRightTabletMargin'      => array(
								'type' => 'number',
							),
							'iconLeftTabletMargin'       => array(
								'type' => 'number',
							),
							'iconBottomTabletMargin'     => array(
								'type' => 'number',
							),
							'iconTopMobileMargin'        => array(
								'type' => 'number',
							),
							'iconRightMobileMargin'      => array(
								'type' => 'number',
							),
							'iconLeftMobileMargin'       => array(
								'type' => 'number',
							),
							'iconBottomMobileMargin'     => array(
								'type' => 'number',
							),
							'iconMarginUnit'             => array(
								'type'    => 'string',
								'default' => 'px',
							),
							'iconTabletMarginUnit'       => array(
								'type'    => 'string',
								'default' => 'px',
							),
							'iconMobileMarginUnit'       => array(
								'type'    => 'string',
								'default' => 'px',
							),
							'iconMarginLink'             => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'isPreview'                  => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'iconBorderStyle'            => array(
								'type'    => 'string',
								'default' => 'default',
							),
							'useSeparateBoxShadows'      => array(
								'type'    => 'boolean',
								'default' => true,
							),
							'iconShadowColor'            => array(
								'type'    => 'string',
								'default' => '#00000070',
							),
							'iconShadowHOffset'          => array(
								'type'    => 'number',
								'default' => 0,
							),
							'iconShadowVOffset'          => array(
								'type'    => 'number',
								'default' => 0,
							),
							'iconShadowBlur'             => array(
								'type'    => 'number',
								'default' => 0,
							),
							'iconBoxShadowColor'         => array(
								'type'    => 'string',
								'default' => '#00000070',
							),
							'iconBoxShadowHOffset'       => array(
								'type'    => 'number',
								'default' => 0,
							),
							'iconBoxShadowVOffset'       => array(
								'type'    => 'number',
								'default' => 0,
							),
							'iconBoxShadowBlur'          => array(
								'type' => 'number',
							),
							'iconBoxShadowSpread'        => array(
								'type' => 'number',
							),
							'iconBoxShadowPosition'      => array(
								'type'    => 'string',
								'default' => 'outset',
							),
							'iconShadowColorHover'       => array(
								'type'    => 'string',
								'default' => '#00000070',
							),
							'iconShadowHOffsetHover'     => array(
								'type'    => 'number',
								'default' => 0,
							),
							'iconShadowVOffsetHover'     => array(
								'type'    => 'number',
								'default' => 0,
							),
							'iconShadowBlurHover'        => array(
								'type'    => 'number',
								'default' => 0,
							),
							'iconBoxShadowColorHover'    => array(
								'type' => 'string',
							),
							'iconBoxShadowHOffsetHover'  => array(
								'type'    => 'number',
								'default' => 0,
							),
							'iconBoxShadowVOffsetHover'  => array(
								'type'    => 'number',
								'default' => 0,
							),
							'iconBoxShadowBlurHover'     => array(
								'type' => 'number',
							),
							'iconBoxShadowSpreadHover'   => array(
								'type' => 'number',
							),
							'iconBoxShadowPositionHover' => array(
								'type'    => 'string',
								'default' => 'outset',
							),
						),
						// Responsive Borders.
						$icon_border_attributes
					),
					'render_callback' => array( $this, 'render_uagb_icon' ),
				)
			);
	  
			
		}

		/**
		 * Check if a URL has a protocol (http/https).
		 *
		 * @since 2.12.5
		 * 
		 * @param string $url The URL to check.
		 * @return bool Whether the URL has a protocol.
		 */
		public static function get_protocol( $url ) {
			$urlParts = wp_parse_url( $url );

			if ( is_array( $urlParts ) ) {
				return isset( $urlParts['scheme'] );
			}
			return false;
		}

		/**
		 * Prepend 'http://' to a URL if it doesn't have a protocol.
		 *
		 * @since 2.12.5
		 * 
		 * @param string $url The URL to prepend 'http://' to.
		 * @return string The modified URL.
		 */
		public static function prepend_http( $url ) {
			return ( ! empty( $url ) && ! self::get_protocol( $url ) ) ? 'http://' . $url : $url;
		}


		/**
		 * Renders the icon block.
		 *
		 * @param array $attributes Array of block attributes.
		 *
		 * @since 2.12.5
		 * @return string|false
		 */
		public function render_uagb_icon( $attributes ) {
			
			$block_id               = 'uagb-block-' . $attributes['block_id'];
			$iconBottomMargin       = isset( $attributes['iconBottomMargin'] ) ? $attributes['iconBottomMargin'] : '';
			$iconLeftMargin         = isset( $attributes['iconLeftMargin'] ) ? $attributes['iconLeftMargin'] : '';
			$iconRightMargin        = isset( $attributes['iconRightMargin'] ) ? $attributes['iconRightMargin'] : '';
			$iconTopMargin          = isset( $attributes['iconTopMargin'] ) ? $attributes['iconTopMargin'] : '';
			$iconBottomTabletMargin = isset( $attributes['iconBottomTabletMargin'] ) ? $attributes['iconBottomTabletMargin'] : '';
			$iconLeftTabletMargin   = isset( $attributes['iconLeftTabletMargin'] ) ? $attributes['iconLeftTabletMargin'] : '';
			$iconRightTabletMargin  = isset( $attributes['iconRightTabletMargin'] ) ? $attributes['iconRightTabletMargin'] : '';
			$iconTopTabletMargin    = isset( $attributes['iconTopTabletMargin'] ) ? $attributes['iconTopTabletMargin'] : '';
			$iconBottomMobileMargin = isset( $attributes['iconBottomMobileMargin'] ) ? $attributes['iconBottomMobileMargin'] : '';
			$iconLeftMobileMargin   = isset( $attributes['iconLeftMobileMargin'] ) ? $attributes['iconLeftMobileMargin'] : '';
			$iconRightMobileMargin  = isset( $attributes['iconRightMobileMargin'] ) ? $attributes['iconRightMobileMargin'] : '';
			$iconTopMobileMargin    = isset( $attributes['iconTopMobileMargin'] ) ? $attributes['iconTopMobileMargin'] : '';
			$margin_variables       = array( $iconBottomMargin, $iconLeftMargin, $iconRightMargin, $iconTopMargin, $iconBottomTabletMargin, $iconLeftTabletMargin, $iconRightTabletMargin, $iconTopTabletMargin, $iconBottomMobileMargin, $iconLeftMobileMargin, $iconRightMobileMargin, $iconTopMobileMargin );

			$has_margin = false;
			foreach ( $margin_variables as $margin ) {
				if ( is_numeric( $margin ) ) {
					$has_margin = true;
					break;
				}
			}
			$margin_class = $has_margin ? 'wp-block-uagb-icon--has-margin' : '';
			$main_classes = array(
				'uagb-icon-wrapper',
				$block_id,
				( is_array( $attributes ) && isset( $attributes['className'] ) ) ? $attributes['className'] : '',
				$margin_class,
			);
	
			$iconSvg     = isset( $attributes['icon'] ) ? $attributes['icon'] : 'circle-check';
			$link        = isset( $attributes['link'] ) ? $attributes['link'] : '';
			$target      = isset( $attributes['target'] ) ? $attributes['target'] : false;
			$disableLink = isset( $attributes['disableLink'] ) ? $attributes['disableLink'] : false;
			$linkUrl     = $disableLink ? $link : '#';
			$targetVal   = $target ? '_blank' : '_self';

			ob_start();
			$iconHtml = UAGB_Helper::render_svg_html( $iconSvg );
			$iconHtml = ob_get_clean();
	  
			if ( $iconHtml ) {

				$role_attr        = ( 'image' === $attributes['iconAccessabilityMode'] ) ? 'img' : 'graphics-symbol';
				$aria_hidden_attr = ( 'presentation' === $attributes['iconAccessabilityMode'] ) ? 'true' : 'false';
				$aria_label_attr  = ( 'presentation' !== $attributes['iconAccessabilityMode'] ) ? ' aria-label="' . esc_attr( $attributes['iconAccessabilityDesc'] ) . '"' : '';
			
				$iconHtml = preg_replace(
					'/<svg(.*?)>/',
					'<svg$1 role="' . esc_attr( $role_attr ) . '" aria-hidden="' . $aria_hidden_attr . '"' . $aria_label_attr . '>',
					$iconHtml
				);
			}
			

			$aria_label_attr = ( 'presentation' !== $attributes['iconAccessabilityMode'] ) ? ' aria-label="' . esc_attr( implode( '', str_split( $attributes['icon'] ) ) ) . '"' : '';

			// Check and prepend the protocol if necessary.
			if ( '#' !== $linkUrl ) {
				$linkUrl = self::get_protocol( $linkUrl ) ? $linkUrl : self::prepend_http( $linkUrl );
			}
			
			if ( $iconHtml && $disableLink && $linkUrl ) {
				// Wrap the SVG content with an anchor tag.
				$iconHtml = preg_replace(
					'/<svg(.*?)>(.*?)<\/svg>/s',
					'<a rel="noopener noreferrer" href="' . esc_url( $linkUrl ) . '" target="' . esc_attr( $targetVal ) . '"><svg$1>$2</svg></a>',
					$iconHtml
				);
				
			}

			ob_start();
			?>      
			<div class="<?php echo esc_attr( implode( ' ', $main_classes ) ); ?>"	>
				<?php if ( $has_margin ) : ?>
				<div class='uagb-icon-margin-wrapper'>
				<?php endif; ?>
					<span class="uagb-svg-wrapper"<?php echo esc_attr( $aria_label_attr ); ?>>		
						<?php echo $iconHtml; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</span>
				<?php if ( $has_margin ) : ?>
				</div>
				<?php endif; ?>
			</div>
			<?php
			return ob_get_clean();

		}
	}

		
	/**
	 *  Prepare if class 'Spectra_Icon' exist.
	 *  Kicking this off by calling 'get_instance()' method
	 */
	Spectra_Icon::get_instance();
}
