<?php
/**
 * Astra Builder UI Controller.
 *
 * @package astra-builder
 */

// No direct access, please.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Astra_Builder_UI_Controller' ) ) {

	/**
	 * Class Astra_Builder_UI_Controller.
	 */
	final class Astra_Builder_UI_Controller {

		/**
		 * Astra SVGs.
		 *
		 * @var mixed ast_svgs
		 */
		private static $ast_svgs = null;

		/**
		 * Get an SVG Icon
		 *
		 * @param string $icon the icon name.
		 * @param bool   $base if the baseline class should be added.
		 */
		public static function fetch_svg_icon( $icon = '', $base = true ) {
			$output = '<span class="ahfb-svg-iconset ast-inline-flex' . ( $base ? ' svg-baseline' : '' ) . '">';

			/** @psalm-suppress DocblockTypeContradiction */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			if ( ! self::$ast_svgs ) {
				ob_start();
				include_once ASTRA_THEME_DIR . 'assets/svg/svgs.json'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
				self::$ast_svgs = json_decode( ob_get_clean(), true );
				self::$ast_svgs = apply_filters( 'astra_svg_icons', self::$ast_svgs );
			}

			$output .= isset( self::$ast_svgs[ $icon ] ) ? self::$ast_svgs[ $icon ] : '';
			$output .= '</span>';

			return $output;
		}

		/**
		 * Prepare Social Icon HTML.
		 *
		 * @param string $index The Index of the social icon.
		 * @param string $builder_type the type of the builder.
		 */
		public static function render_social_icon( $index, $builder_type = 'header' ) {
			$items        = astra_get_option( $builder_type . '-social-icons-' . $index );
			$items        = isset( $items['items'] ) ? $items['items'] : array();
			$show_label   = astra_get_option( $builder_type . '-social-' . $index . '-label-toggle' );
			$color_type   = astra_get_option( $builder_type . '-social-' . $index . '-color-type' );
			$social_stack = astra_get_option( $builder_type . '-social-' . $index . '-stack', 'none' );

			echo '<div class="ast-' . esc_attr( $builder_type ) . '-social-' . esc_attr( $index ) . '-wrap ast-' . esc_attr( $builder_type ) . '-social-wrap">';

			if ( is_customize_preview() ) {
				self::render_customizer_edit_button();
			}

			echo '<div class="' . esc_attr( $builder_type ) . '-social-inner-wrap element-social-inner-wrap social-show-label-' . ( $show_label ? 'true' : 'false' ) . ' ast-social-color-type-' . esc_attr( $color_type ) . ' ast-social-stack-' . esc_attr( $social_stack ) . ' ast-social-element-style-filled">';

			if ( is_array( $items ) && ! empty( $items ) ) {

				foreach ( $items as $item ) {
					if ( $item['enabled'] ) {

						$link = $item['url'];

						switch ( $item['id'] ) {

							case 'phone':
								$link = 'tel:' . $item['url'];
								break;

							case 'email':
								$link = 'mailto:' . $item['url'];
								break;

							case 'whatsapp':
								$link = 'https://api.whatsapp.com/send?phone=' . $item['url'];
								break;
						}

						echo '<a href="' . esc_url( $link ) . '"' . esc_attr( $item['label'] ? ' aria-label=' . $item['label'] . '' : ' aria-label=' . $item['id'] . '' ) . ' ' . ( 'phone' === $item['id'] || 'email' === $item['id'] ? '' : 'target="_blank" rel="noopener noreferrer" ' ) . 'style="--color: ' . esc_attr( ! empty( $item['color'] ) ? $item['color'] : '#3a3a3a' ) . '; --background-color: ' . esc_attr( ! empty( $item['background'] ) ? $item['background'] : 'transparent' ) . ';" class="ast-builder-social-element ast-inline-flex ast-' . esc_attr( $item['id'] ) . ' ' . esc_attr( $builder_type ) . '-social-item">';
						echo self::fetch_svg_icon( $item['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

						if ( $show_label ) {
							echo '<span class="social-item-label">' . esc_html( $item['label'] ) . '</span>';
						}

						echo '</a>';
					}
				}
			}
			echo apply_filters( 'astra_social_icons_after', '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '</div>';
			echo '</div>';
		}

		/**
		 * Prepare HTML Markup.
		 *
		 * @param string $index Key of the HTML Control.
		 */
		public static function render_html_markup( $index = 'header-html-1' ) {

			$theme_author = astra_get_theme_author_details();

			$content = astra_get_option( $index );
			if ( $content || is_customize_preview() ) {
				$link_style = '';
				echo '<div class="ast-header-html inner-link-style-' . esc_attr( $link_style ) . '">';
				if ( is_customize_preview() ) {
					self::render_customizer_edit_button();
				}
				echo '<div class="ast-builder-html-element">';
				$content = str_replace( '[copyright]', '&copy;', $content );
				$content = str_replace( '[current_year]', gmdate( 'Y' ), $content );
				$content = str_replace( '[site_title]', get_bloginfo( 'name' ), $content );
				$content = str_replace( '[theme_author]', '<a href=" ' . esc_url( $theme_author['theme_author_url'] ) . '" rel="nofollow noopener" target="_blank">' . $theme_author['theme_name'] . '</a>', $content );
				echo do_shortcode( wp_kses_post( wpautop( $content ) ) );
				echo '</div>';
				echo '</div>';
			}
		}

		/**
		 * Prepare Edit icon inside customizer.
		 *
		 * @param string $class custom class.
		 * @since 3.9.4
		 */
		public static function render_customizer_edit_button( $class = '' ) { ?>
			<div class="customize-partial-edit-shortcut <?php echo esc_attr( $class ); ?>" data-id="ahfb">
				<button aria-label="<?php esc_attr_e( 'Click to edit this element.', 'astra' ); ?>"
						title="<?php esc_attr_e( 'Click to edit this element.', 'astra' ); ?>"
						class="customize-partial-edit-shortcut-button item-customizer-focus">
					<?php echo self::fetch_svg_icon( 'edit' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
			</div>
			<?php
		}

		/**
		 * Prepare Special Edit navigatory trigger for Builder Grid Rows in customizer.
		 *
		 * @param string $type Header / Footer row type.
		 * @param string $row_position Above / Primary / Below.
		 *
		 * @since 3.0.0
		 */
		public static function render_grid_row_customizer_edit_button( $type, $row_position ) {

			switch ( $row_position ) {
				case 'primary':
					/* translators: %s: icon term */
					$row_label = sprintf( __( 'Primary %s', 'astra' ), $type );
					break;
				case 'above':
					/* translators: %s: icon term */
					$row_label = sprintf( __( 'Above %s', 'astra' ), $type );
					break;
				case 'below':
					/* translators: %s: icon term */
					$row_label = sprintf( __( 'Below %s', 'astra' ), $type );
					break;
				default:
					$row_label = $type;
					break;
			}

			?>
			<div class="customize-partial-edit-shortcut row-editor-shortcut" data-id="ahfb">
				<button aria-label="<?php esc_attr_e( 'Click to edit this element.', 'astra' ); ?>"	title="<?php esc_attr_e( 'Click to edit this Row.', 'astra' ); ?>" class="item-customizer-focus">
					<?php echo self::fetch_svg_icon( 'edit' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
			</div>
			<?php
		}

		/**
		 * Prepare Edit navigatory trigger for Banner Section in customizer.
		 *
		 * @since 3.9.0
		 */
		public static function render_banner_customizer_edit_button() {
			?>
				<div class="customize-partial-edit-shortcut banner-editor-shortcut" data-id="ahfb">
					<button aria-label="<?php esc_attr_e( 'Click to edit this element.', 'astra' ); ?>"	title="<?php esc_attr_e( 'Click to edit this Row.', 'astra' ); ?>" class="item-customizer-focus">
						<?php echo self::fetch_svg_icon( 'edit' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
				</div>
			<?php
		}

		/**
		 * Render Trigger Markup.
		 *
		 * @since 3.0.0
		 */
		public static function render_mobile_trigger() {

			$icon             = astra_get_option( 'header-trigger-icon' );
			$mobile_label     = astra_get_option( 'mobile-header-menu-label' );
			$toggle_btn_style = astra_get_option( 'mobile-header-toggle-btn-style' );
			$aria_controls    = '';
			if ( false === Astra_Builder_Helper::$is_header_footer_builder_active ) {
				$aria_controls = 'aria-controls="primary-menu"';
			}
			?>
			<div class="ast-button-wrap">
				<button type="button" class="menu-toggle main-header-menu-toggle ast-mobile-menu-trigger-<?php echo esc_attr( $toggle_btn_style ); ?>" <?php echo apply_filters( 'astra_nav_toggle_data_attrs', '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <?php echo esc_attr( $aria_controls ); ?> aria-expanded="false">
					<span class="screen-reader-text">Main Menu</span>
					<span class="mobile-menu-toggle-icon">
						<?php
							echo self::fetch_svg_icon( $icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							echo self::fetch_svg_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</span>
					<?php
					if ( isset( $mobile_label ) && ! empty( $mobile_label ) ) {
						?>

						<span class="mobile-menu-wrap">
							<span class="mobile-menu"><?php echo esc_html( $mobile_label ); ?></span>
						</span>
						<?php
					}
					?>
				</button>
			</div>
			<?php
		}

		/**
		 * Prepare Button HTML.
		 *
		 * @param string $index The Index of the button.
		 * @param string $builder_type the type of the builder.
		 */
		public static function render_button( $index, $builder_type = 'header' ) {
			if ( is_customize_preview() ) {
				self::render_customizer_edit_button();
			}

			$button_size = astra_get_option( $builder_type . '-button' . $index . '-size' );

			echo '<div class="ast-builder-button-wrap ast-builder-button-size-' . $button_size . '">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo astra_get_custom_button( $builder_type . '-button' . $index . '-text', $builder_type . '-button' . $index . '-link-option', 'header-button' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Site Identity.
		 *
		 * @param  string $device   Device name.
		 */
		public static function render_site_identity( $device ) {
			?>
				<?php
				if ( is_customize_preview() ) {
					self::render_customizer_edit_button();
				}
				?>
				<div
				<?php
					echo astra_attr(
						'site-identity',
						array(
							'class' => 'site-branding ast-site-identity',
						)
					);
				?>
				>
					<?php astra_logo( $device ); ?>
				</div>
			<!-- .site-branding -->
			<?php
		}

		/**
		 * Render Mobile Cart Flyout Markup.
		 *
		 * @since 3.1.0
		 */
		public static function render_mobile_cart_flyout_markup() {
			$flyout_cart_width              = astra_get_option( 'woo-slide-in-cart-width' );
			$flyout_cart_width_desktop      = ( isset( $flyout_cart_width['desktop'] ) ) ? $flyout_cart_width['desktop'] : '';
			$flyout_cart_width_desktop_unit = ( isset( $flyout_cart_width['desktop-unit'] ) ) ? $flyout_cart_width['desktop-unit'] : '';
			$flyout_cart_unit_breakpoint    = 'px' === $flyout_cart_width_desktop_unit ? 500 : 50;
			$is_width_long                  = $flyout_cart_width_desktop && $flyout_cart_width_desktop > $flyout_cart_unit_breakpoint ? 'ast-large-view' : '';
			?>
			<div class="astra-mobile-cart-overlay"></div>
			<div id="astra-mobile-cart-drawer" class="astra-cart-drawer">
				<div class="astra-cart-drawer-header">
					<button type="button" class="astra-cart-drawer-close" aria-label="<?php echo esc_attr__( 'Close Cart Drawer', 'astra' ); ?>">
							<?php echo self::fetch_svg_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
					<div class="astra-cart-drawer-title">
					<?php
						echo apply_filters( 'astra_header_cart_flyout_shopping_cart_text', __( 'Shopping Cart', 'astra' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
					</div>
				</div>
				<div class="astra-cart-drawer-content <?php echo esc_attr( $is_width_long ); ?>">
					<?php
					if ( class_exists( 'Astra_Woocommerce' ) ) {
						the_widget( 'WC_Widget_Cart', 'title=' );
					}
					if ( class_exists( 'Easy_Digital_Downloads' ) ) {
						the_widget( 'edd_cart_widget', 'title=' );
					}
					?>
				</div>
			</div>
			<?php
		}
		/**
		 * Account HTML.
		 */
		public static function render_account() {

			$is_logged_in = is_user_logged_in();

			$link_href        = '';
			$new_tab          = '';
			$link_rel         = '';
			$account_link     = '';
			$link_url         = '';
			$logout_preview   = astra_get_option( 'header-account-logout-preview' );
			$is_customizer    = is_customize_preview();
			$logged_out_style = astra_get_option( 'header-account-logout-style' );

			if ( ! $is_logged_in && 'none' === $logged_out_style ) {
				return;
			}

			$icon_skin = ( '' !== astra_get_option( 'header-account-icon-type' ) ) ? astra_get_option( 'header-account-icon-type' ) : 'account-1';

			?>

			<div class="ast-header-account-wrap" tabindex="0">
				<?php
				if ( $is_customizer ) {
					self::render_customizer_edit_button();
				}

				/** @psalm-suppress RedundantConditionGivenDocblockType */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
				if ( $is_logged_in && ( ( ( ( ! $logout_preview ) || ( 'none' === $logged_out_style && $logout_preview ) ) && $is_customizer ) || ( ! $is_customizer ) ) ) {

					$account_type = astra_get_option( 'header-account-type' );

					$login_profile_type = astra_get_option( 'header-account-login-style' );

					$extend_text_profile_type = astra_get_option( 'header-account-login-style-extend-text-profile-type' );

					$action_type = astra_get_option( 'header-account-action-type' );
					$link_type   = astra_get_option( 'header-account-link-type' );

					$account_link = astra_get_option( 'header-account-login-link' );

					$logged_in_text = astra_get_option( 'header-account-logged-in-text' );

					if ( 'default' !== $account_type && 'default' === $link_type && defined( 'ASTRA_EXT_VER' ) ) {
						$new_tab = 'target=_self';
						if ( 'woocommerce' === $account_type && class_exists( 'WooCommerce' ) ) {

							$woocommerce_link = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );

							$link_url = ( $woocommerce_link ) ? $woocommerce_link : '';

						} elseif ( 'lifterlms' === $account_type && class_exists( 'LifterLMS' ) ) {

							$lifterlms_link = get_permalink( llms_get_page_id( 'myaccount' ) );

							$link_url = ( $lifterlms_link ) ? $lifterlms_link : '';
						}
					} elseif ( '' !== $account_link && '' !== $account_link['url'] ) {

						$link_url = $account_link['url'];

						$new_tab = ( $account_link['new_tab'] ? 'target=_blank' : 'target=_self' );

						$link_rel = ( ! empty( $account_link['link_rel'] ) ? 'rel=' . esc_attr( $account_link['link_rel'] ) : '' );
					}

					$link_href = ( '' !== $link_url ) ? 'href=' . esc_url( $link_url ) : '';

					$link_classes = array(
						'ast-header-account-link',
						'ast-account-action-' . $action_type,
					);

					if ( 'text' !== $login_profile_type ) {
						$link_classes[] = 'ast-header-account-type-' . $login_profile_type;
					} else {
						if ( 'default' === $extend_text_profile_type ) {
							$link_classes[] = 'ast-header-account-type-' . $login_profile_type;
						} else {
							// Make sure, we set the common class as before so that we can adapt to existing CSS styles.
							$link_classes[] = 'ast-header-account-type-' . $extend_text_profile_type;
							$link_classes[] = 'ast-header-account-type-extend-text-profile-type';
						}
					}

					?>
					<div class="ast-header-account-inner-wrap">
						<a class="<?php echo esc_attr( implode( ' ', $link_classes ) ); ?>" role="link" aria-label="<?php esc_attr_e( 'Account icon link', 'astra' ); ?>" <?php echo esc_attr( $link_href . ' ' . $new_tab . ' ' . $link_rel ); ?> >

							<?php
							if ( 'avatar' === $login_profile_type ) {

								echo get_avatar( get_current_user_id() );

							} elseif ( 'icon' === $login_profile_type ) {
								echo self::fetch_svg_icon( $icon_skin ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							} elseif ( 'text' === $login_profile_type ) {

								if ( 'avatar' === $extend_text_profile_type ) {
									echo get_avatar( get_current_user_id() );
								} elseif ( 'icon' === $extend_text_profile_type ) {
									echo self::fetch_svg_icon( $icon_skin ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								}

								?>
								<span class="ast-header-account-text"><?php echo esc_html( $logged_in_text ); ?></span>
							<?php } ?>
						</a>
						<?php
						if ( defined( 'ASTRA_EXT_VER' ) && 'menu' === $action_type ) {
							Astra_Header_Account_Component::account_menu_markup();
						}
						?>
					</div>
				<?php } elseif ( ( 'none' !== $logged_out_style ) && ( ( ! $is_logged_in ) || ( $is_logged_in && $logout_preview && $is_customizer ) ) ) { ?>

					<?php
					$action_type     = astra_get_option( 'header-account-logout-action' );
					$logged_out_text = astra_get_option( 'header-account-logged-out-text' );
					$login_link      = astra_get_option( 'header-account-logout-link' );

					$extend_text_profile_type = astra_get_option( 'header-account-logout-style-extend-text-profile-type' );

					$logged_out_style_class = array(
						'ast-header-account-link',
						'ast-account-action-' . $action_type,
					);

					if ( 'text' !== $logged_out_style ) {
						$logged_out_style_class[] = 'ast-header-account-type-' . $logged_out_style;
					} else {
						if ( 'default' === $extend_text_profile_type ) {
							$logged_out_style_class[] = 'ast-header-account-type-' . $logged_out_style;
						} else {
							// Make sure, we set the common class as before so that we can adapt to existing CSS styles.
							$logged_out_style_class[] = 'ast-header-account-type-' . $extend_text_profile_type;
							$logged_out_style_class[] = 'ast-header-account-type-extend-text-profile-type';
						}
					}

					if ( '' !== $login_link && '' !== $login_link['url'] ) {

						$current_url   = home_url( add_query_arg( array(), $GLOBALS['wp']->request ) );
						$default_login = wp_login_url();

						if ( $default_login === $login_link['url'] ) {
							$login_link['url'] = wp_login_url( $current_url );
						}

						$link_url = $login_link['url'];
						$new_tab  = ( $login_link['new_tab'] ? 'target=_blank' : 'target=_self' );

						$link_rel = ( ! empty( $login_link['link_rel'] ) ? 'rel=' . esc_attr( $login_link['link_rel'] ) : '' );
					}

					$link_href = 'href=' . esc_url( $link_url ) . '';
					?>
					<a class="<?php echo esc_attr( implode( ' ', $logged_out_style_class ) ); ?>" aria-label="<?php esc_attr_e( 'Account icon link', 'astra' ); ?>" <?php echo esc_attr( $link_href . ' ' . $new_tab . ' ' . $link_rel ); ?> >
						<?php if ( 'icon' === $logged_out_style ) { ?>
							<?php echo self::fetch_svg_icon( $icon_skin ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php 
						} elseif ( 'text' === $logged_out_style ) {
							if ( 'icon' === $extend_text_profile_type ) {
								echo self::fetch_svg_icon( $icon_skin ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
							?>
							<span class="ast-header-account-text"><?php echo esc_html( $logged_out_text ); ?></span>
						<?php } ?>
					</a>

					<?php
					/**
					 * The login popup form is moved to footer from here @since 4.6.12
					 *
					 * @see Astra Addon -> Astra_Addon_Header_Account_Markup::login_popup_form_markup
					 */
					?>
				<?php } ?>

			</div>

			<?php
		}

	}
}

?>
