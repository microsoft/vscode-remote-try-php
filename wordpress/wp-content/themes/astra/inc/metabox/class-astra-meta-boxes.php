<?php
/**
 * Post Meta Box
 *
 * @package     Astra
 * @author      Astra
 * @copyright   Copyright (c) 2020, Astra
 * @link        https://wpastra.com/
 * @since       Astra 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Meta Boxes setup
 */
if ( ! class_exists( 'Astra_Meta_Boxes' ) ) {

	/**
	 * Meta Boxes setup
	 */
	class Astra_Meta_Boxes {

		/**
		 * Instance
		 *
		 * @var $instance
		 */
		private static $instance;

		/**
		 * Meta Option
		 *
		 * @var $meta_option
		 */
		private static $meta_option;

		/**
		 * Initiator
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
			/** @psalm-suppress InvalidGlobal */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			global $pagenow;
			/** @psalm-suppress InvalidGlobal */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			/**
			 * Set metabox options
			 *
			 * @see https://php.net/manual/en/filter.filters.sanitize.php
			 */
			self::post_meta_options();
			add_action( 'load-post.php', array( $this, 'init_metabox' ) );
			add_action( 'load-post-new.php', array( $this, 'init_metabox' ) );
			add_action( 'do_meta_boxes', array( $this, 'remove_metabox' ) );
			add_filter( 'register_post_type_args', array( $this, 'custom_fields_support' ), 10, 2 );

			add_action( 'init', array( $this, 'register_script' ) );
			add_action( 'init', array( $this, 'register_meta_settings' ) );

			if ( 'widgets.php' !== $pagenow && ! is_customize_preview() ) {
				add_action( 'enqueue_block_editor_assets', array( $this, 'load_scripts' ) );
			}
		}

		/**
		 * Register Post Meta options support.
		 *
		 * @since 3.7.6
		 * @param array|mixed $args the post type args.
		 * @param string      $post_type the post type.
		 */
		public function custom_fields_support( $args, $post_type ) {
			if ( is_array( $args ) && isset( $args['public'] ) && $args['public'] && isset( $args['supports'] ) && is_array( $args['supports'] ) && ! in_array( 'custom-fields', $args['supports'], true ) ) {
				$args['supports'][] = 'custom-fields';
			}

			return $args;
		}

		/**
		 * Check if layout is bb themer's layout
		 */
		public static function is_bb_themer_layout() {

			$is_layout = false;

			$post_type = get_post_type();
			$post_id   = get_the_ID();

			if ( 'fl-theme-layout' === $post_type && $post_id ) {

				$is_layout = true;
			}

			return $is_layout;
		}

		/**
		 *  Remove Metabox for beaver themer specific layouts
		 */
		public function remove_metabox() {

			$post_type = get_post_type();
			$post_id   = get_the_ID();

			if ( 'fl-theme-layout' === $post_type && $post_id ) {
					remove_meta_box( 'astra_settings_meta_box', 'fl-theme-layout', 'side' );
			}
		}

		/**
		 *  Init Metabox
		 */
		public function init_metabox() {
			self::post_meta_options();
			add_action( 'add_meta_boxes', array( $this, 'setup_meta_box' ) );
			add_action( 'save_post', array( $this, 'save_meta_box' ) );
		}

		/**
		 *  Setup Metabox
		 */
		public function setup_meta_box() {

			// Get all public posts.
			$post_types = get_post_types(
				array(
					'public' => true,
				)
			);

			$post_types['fl-theme-layout'] = 'fl-theme-layout';

			$metabox_name = sprintf(
				// Translators: %s is the theme name.
				__( '%s Settings', 'astra' ),
				astra_get_theme_name()
			);

			// Enable for all posts.
			foreach ( $post_types as $type ) {

				if ( 'attachment' !== $type ) {
					add_meta_box(
						'astra_settings_meta_box',              // Id.
						$metabox_name,                          // Title.
						array( $this, 'markup_meta_box' ),      // Callback.
						$type,                                  // Post_type.
						'side',                                 // Context.
						'default',                              // Priority.
						array(
							'__back_compat_meta_box' => true,
						)
					);
				}
			}
		}

		/**
		 * Get metabox options
		 */
		public static function get_meta_option() {
			return self::$meta_option;
		}

		/**
		 * Metabox Markup
		 *
		 * @param  object $post Post object.
		 * @return void
		 */
		public function markup_meta_box( $post ) {

			wp_nonce_field( basename( __FILE__ ), 'astra_settings_meta_box' );
			$stored = get_post_meta( $post->ID );

			if ( is_array( $stored ) ) {

				// Set stored and override defaults.
				foreach ( $stored as $key => $value ) {
					self::$meta_option[ $key ]['default'] = ( isset( $stored[ $key ][0] ) ) ? $stored[ $key ][0] : '';
				}
			}

			// Get defaults.
			$meta = self::get_meta_option();

			/**
			 * Get options
			 */
			$site_sidebar            = ( isset( $meta['site-sidebar-layout']['default'] ) ) ? $meta['site-sidebar-layout']['default'] : 'default';
			$site_content_style      = ( isset( $meta['site-content-style']['default'] ) ) ? $meta['site-content-style']['default'] : 'default';
			$site_sidebar_style      = ( isset( $meta['site-sidebar-style']['default'] ) ) ? $meta['site-sidebar-style']['default'] : 'default';
			$new_site_content_layout = ( isset( $meta['ast-site-content-layout']['default'] ) ) ? $meta['ast-site-content-layout']['default'] : '';
			$site_post_title         = ( isset( $meta['site-post-title']['default'] ) ) ? $meta['site-post-title']['default'] : '';
			$footer_bar              = ( isset( $meta['footer-sml-layout']['default'] ) ) ? $meta['footer-sml-layout']['default'] : '';
			$footer_widgets          = ( isset( $meta['footer-adv-display']['default'] ) ) ? $meta['footer-adv-display']['default'] : '';
			$above_header            = ( isset( $meta['ast-hfb-above-header-display']['default'] ) ) ? $meta['ast-hfb-above-header-display']['default'] : 'default';
			$primary_header          = ( isset( $meta['ast-main-header-display']['default'] ) ) ? $meta['ast-main-header-display']['default'] : '';
			$below_header            = ( isset( $meta['ast-hfb-below-header-display']['default'] ) ) ? $meta['ast-hfb-below-header-display']['default'] : 'default';
			$mobile_header           = ( isset( $meta['ast-hfb-mobile-header-display']['default'] ) ) ? $meta['ast-hfb-mobile-header-display']['default'] : 'default';
			$ast_featured_img        = ( isset( $meta['ast-featured-img']['default'] ) ) ? $meta['ast-featured-img']['default'] : '';
			$breadcrumbs_content     = ( isset( $meta['ast-breadcrumbs-content']['default'] ) ) ? $meta['ast-breadcrumbs-content']['default'] : '';
			$ast_banner_visibility   = ( isset( $meta['ast-banner-title-visibility']['default'] ) ) ? $meta['ast-banner-title-visibility']['default'] : '';
			$exclude_cpt             = isset( $post->post_type ) ? in_array(
				$post->post_type,
				array(
					'product',
					'download',
					'course',
					'lesson',
					'tutor_quiz',
					'tutor_assignments',
					'sfwd-assignment',
					'sfwd-essays',
					'sfwd-transactions',
					'sfwd-certificates',
					'sfwd-quiz',
					'sfwd-courses',
					'sfwd-lessons',
					'sfwd-topic',
					'groups',
				)
			) : '';
			$show_meta_field         = ! self::is_bb_themer_layout();
			$old_meta_layout         = isset( $meta['site-content-layout']['default'] ) ? $meta['site-content-layout']['default'] : '';
			$meta_key                = ( isset( $meta['astra-migrate-meta-layouts']['default'] ) ) ? $meta['astra-migrate-meta-layouts']['default'] : '';
			$migrated_user           = ( ! Astra_Dynamic_CSS::astra_fullwidth_sidebar_support() );
			do_action( 'astra_meta_box_markup_before', $meta );

			// Migrate old user existing container layout option to new layout options.
			if ( ! empty( $old_meta_layout ) && 'set' !== $meta_key && $migrated_user ) {
				$old_meta_content_layout = $meta['site-content-layout']['default'];
				switch ( $old_meta_content_layout ) {
					case 'plain-container':
						$new_site_content_layout = 'normal-width-container';
						$site_content_style      = 'unboxed';
						$site_sidebar_style      = 'unboxed';
						break;
					case 'boxed-container':
						$new_site_content_layout = 'normal-width-container';
						$site_content_style      = 'boxed';
						$site_sidebar_style      = 'boxed';
						break;
					case 'content-boxed-container':
						$new_site_content_layout = 'normal-width-container';
						$site_content_style      = 'boxed';
						$site_sidebar_style      = 'unboxed';
						break;
					case 'page-builder':
						$new_site_content_layout = 'full-width-container';
						$site_content_style      = 'unboxed';
						$site_sidebar_style      = 'unboxed';
						break;
					case 'narrow-container':
						$new_site_content_layout = 'narrow-width-container';
						$site_content_style      = 'unboxed';
						$site_sidebar_style      = 'unboxed';
						break;
					default:
						$new_site_content_layout = 'default';
						$site_content_style      = 'default';
						$site_sidebar_style      = 'default';
						break;
				}
			}

			/**
			 * Option: Content Layout.
			 */
			?>
			<div class="ast-site-content-layout-meta-wrap components-base-control__field">
				<p class="post-attributes-label-wrapper" >
					<strong> <?php esc_html_e( 'Container Layout', 'astra' ); ?> </strong>
				</p>
				<select name="ast-site-content-layout" id="ast-site-content-layout">
					<option value="default" <?php selected( $new_site_content_layout, 'default' ); ?> > <?php esc_html_e( 'Customizer Setting', 'astra' ); ?></option>
					<option value="normal-width-container" <?php selected( $new_site_content_layout, 'normal-width-container' ); ?> > <?php esc_html_e( 'Normal', 'astra' ); ?></option>
					<?php if ( ! $exclude_cpt ) { ?>
						<option value="narrow-width-container" <?php selected( $new_site_content_layout, 'narrow-width-container' ); ?> > <?php esc_html_e( 'Narrow', 'astra' ); ?></option>
						<?php } ?>
						<option value="full-width-container" <?php selected( $new_site_content_layout, 'full-width-container' ); ?> > <?php esc_html_e( 'Full Width', 'astra' ); ?></option>
					</select>
			</div>
			<?php
			/**
			 * Option: Content Style.
			 */
			?>
			<div class="site-content-style-meta-wrap components-base-control__field">
				<p class="post-attributes-label-wrapper" >
					<strong> <?php esc_html_e( 'Container Style', 'astra' ); ?> </strong>
				</p>
				<select name="site-content-style" id="site-content-style">
					<option value="default" <?php selected( $site_content_style, 'default' ); ?> > <?php esc_html_e( 'Customizer Setting', 'astra' ); ?></option>
					<option value="unboxed" <?php selected( $site_content_style, 'unboxed' ); ?> > <?php esc_html_e( 'Unboxed', 'astra' ); ?></option>
					<option value="boxed" <?php selected( $site_content_style, 'boxed' ); ?> > <?php esc_html_e( 'Boxed', 'astra' ); ?></option>
				</select>
			</div>
			<?php
			/**
			 * Option: Sidebar
			 */
			?>
			<div class="site-sidebar-layout-meta-wrap components-base-control__field">
				<p class="post-attributes-label-wrapper" >
					<strong> <?php esc_html_e( 'Sidebar Layout', 'astra' ); ?> </strong>
				</p>
				<select name="site-sidebar-layout" id="site-sidebar-layout">
					<option value="default" <?php selected( $site_sidebar, 'default' ); ?> > <?php esc_html_e( 'Customizer Setting', 'astra' ); ?></option>
					<option value="left-sidebar" <?php selected( $site_sidebar, 'left-sidebar' ); ?> > <?php esc_html_e( 'Left Sidebar', 'astra' ); ?></option>
					<option value="right-sidebar" <?php selected( $site_sidebar, 'right-sidebar' ); ?> > <?php esc_html_e( 'Right Sidebar', 'astra' ); ?></option>
					<option value="no-sidebar" <?php selected( $site_sidebar, 'no-sidebar' ); ?> > <?php esc_html_e( 'No Sidebar', 'astra' ); ?></option>
				</select>
			</div>
			<?php
			/**
			 * Option: Sidebar Style.
			 */
			?>
			<div class="site-sidebar-style-meta-wrap components-base-control__field">
				<p class="post-attributes-label-wrapper" >
					<strong> <?php esc_html_e( 'Sidebar Style', 'astra' ); ?> </strong>
				</p>
				<select name="site-sidebar-style" id="site-sidebar-style">
					<option value="default" <?php selected( $site_sidebar_style, 'default' ); ?> > <?php esc_html_e( 'Customizer Setting', 'astra' ); ?></option>
					<option value="unboxed" <?php selected( $site_sidebar_style, 'unboxed' ); ?> > <?php esc_html_e( 'Unboxed', 'astra' ); ?></option>
					<option value="boxed" <?php selected( $site_sidebar_style, 'boxed' ); ?> > <?php esc_html_e( 'Boxed', 'astra' ); ?></option>
				</select>
			</div>
			<?php
			/**
			 * Option: Disable Sections - Primary Header, Title, Footer Widgets, Footer Bar
			 */
			?>
			<div class="disable-section-meta-wrap components-base-control__field">
				<p class="post-attributes-label-wrapper">
					<strong> <?php esc_html_e( 'Disable Sections', 'astra' ); ?> </strong>
				</p>
				<div class="disable-section-meta">
					<?php do_action( 'astra_meta_box_markup_disable_sections_before', $meta ); ?>

					<?php
						/** @psalm-suppress DocblockTypeContradiction */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
					if ( $show_meta_field && true === Astra_Builder_Helper::$is_header_footer_builder_active && Astra_Builder_Helper::is_row_empty( 'above', 'header', 'desktop' ) ) :
						/** @psalm-suppress DocblockTypeContradiction */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
						?>
					<div class="ast-hfb-above-header-display-option-wrap">
						<input type="checkbox" id="ast-hfb-above-header-display" name="ast-hfb-above-header-display" value="disabled" <?php checked( $above_header, 'disabled' ); ?> />
						<label for="ast-hfb-above-header-display"><?php esc_html_e( 'Disable Above Header', 'astra' ); ?></label> <br />
					</div>
					<?php endif; ?>

					<?php
						/** @psalm-suppress DocblockTypeContradiction */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
					if ( $show_meta_field && Astra_Builder_Helper::is_row_empty( 'primary', 'header', 'desktop' ) ) :
						/** @psalm-suppress DocblockTypeContradiction */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
						?>
					<div class="ast-main-header-display-option-wrap">
						<label for="ast-main-header-display">
							<input type="checkbox" id="ast-main-header-display" name="ast-main-header-display" value="disabled" <?php checked( $primary_header, 'disabled' ); ?> />
						<?php esc_html_e( 'Disable Primary Header', 'astra' ); ?>
						</label>
					</div>
					<?php endif; ?>

					<?php
						/** @psalm-suppress DocblockTypeContradiction */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
					if ( $show_meta_field && true === Astra_Builder_Helper::$is_header_footer_builder_active && Astra_Builder_Helper::is_row_empty( 'below', 'header', 'desktop' ) ) :
						/** @psalm-suppress DocblockTypeContradiction */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
						?>
					<div class="ast-hfb-below-header-display-option-wrap">
						<input type="checkbox" id="ast-hfb-below-header-display" name="ast-hfb-below-header-display" value="disabled" <?php checked( $below_header, 'disabled' ); ?> />
						<label for="ast-hfb-below-header-display"><?php esc_html_e( 'Disable Below Header', 'astra' ); ?></label> <br />
					</div>
					<?php endif; ?>
					<?php
						/** @psalm-suppress DocblockTypeContradiction */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
					if ( $show_meta_field && true === Astra_Builder_Helper::$is_header_footer_builder_active && ( Astra_Builder_Helper::is_row_empty( 'primary', 'header', 'mobile' ) || Astra_Builder_Helper::is_row_empty( 'above', 'header', 'mobile' ) || Astra_Builder_Helper::is_row_empty( 'below', 'header', 'mobile' ) ) ) :
						/** @psalm-suppress DocblockTypeContradiction */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
						?>

					<div class="ast-hfb-mobile-header-display-option-wrap">
						<input type="checkbox" id="ast-hfb-mobile-header-display" name="ast-hfb-mobile-header-display" value="disabled" <?php checked( $mobile_header, 'disabled' ); ?> />
						<label for="ast-hfb-mobile-header-display"><?php esc_html_e( 'Disable Mobile Header', 'astra' ); ?></label> <br />
					</div>
					<?php endif; ?>

					<?php do_action( 'astra_meta_box_markup_disable_sections_after_primary_header', $meta ); ?>
					<?php if ( $show_meta_field ) { ?>
						<div class="site-post-title-option-wrap">
							<label for="site-post-title">
								<input type="checkbox" id="site-post-title" name="site-post-title" value="disabled" <?php checked( $site_post_title, 'disabled' ); ?> />
								<?php esc_html_e( 'Disable Title', 'astra' ); ?>
							</label>
						</div>
						<?php
						$ast_breadcrumbs_content = astra_get_option( 'ast-breadcrumbs-content' );
						if ( 'disabled' != $ast_breadcrumbs_content && 'none' !== astra_get_option( 'breadcrumb-position' ) ) {
							?>
					<div class="ast-breadcrumbs-content-option-wrap">
						<label for="ast-breadcrumbs-content">
							<input type="checkbox" id="ast-breadcrumbs-content" name="ast-breadcrumbs-content" value="disabled" <?php checked( $breadcrumbs_content, 'disabled' ); ?> />
							<?php esc_html_e( 'Disable Breadcrumb', 'astra' ); ?>
						</label>
					</div>
						<?php } ?>

						<div class="ast-featured-img-option-wrap">
							<label for="ast-featured-img">
								<input type="checkbox" id="ast-featured-img" name="ast-featured-img" value="disabled" <?php checked( $ast_featured_img, 'disabled' ); ?> />
								<?php esc_html_e( 'Disable Featured Image', 'astra' ); ?>
							</label>
						</div>

						<?php
							$post_type            = $post->post_type;
							$supported_post_types = Astra_Posts_Structure_Loader::get_supported_post_types();
						if ( ( in_array( $post_type, $supported_post_types ) && true === astra_get_option( 'ast-single-' . $post_type . '-title', ( class_exists( 'WooCommerce' ) && 'product' === $post_type ) ? false : true ) ) ) {
							if ( ! ( class_exists( 'WooCommerce' ) && absint( astra_get_post_id() ) === wc_get_page_id( 'shop' ) ) ) {
								?>
							<div class="ast-banner-title-visibility-option-wrap">
								<label for="ast-banner-title-visibility">
									<input type="checkbox" id="ast-banner-title-visibility" name="ast-banner-title-visibility" value="disabled" <?php checked( $ast_banner_visibility, 'disabled' ); ?> />
								<?php esc_html_e( 'Disable Banner Area', 'astra' ); ?>
								</label>
							</div>
								<?php
							}
						}
						?>

					<?php } ?>

					<?php
					$footer_adv_layout = astra_get_option( 'footer-adv' );

					if ( $show_meta_field && ( 'disabled' != $footer_adv_layout && ! Astra_Builder_Helper::$is_header_footer_builder_active ) ) {
						?>
					<div class="footer-adv-display-option-wrap">
						<label for="footer-adv-display">
							<input type="checkbox" id="footer-adv-display" name="footer-adv-display" value="disabled" <?php checked( $footer_widgets, 'disabled' ); ?> />
							<?php esc_html_e( 'Disable Footer Widgets', 'astra' ); ?>
						</label>
					</div>

						<?php
					}
					$footer_sml_layout = astra_get_option( 'footer-sml-layout' );
					if ( 'disabled' != $footer_sml_layout || Astra_Builder_Helper::$is_header_footer_builder_active ) {
						?>
					<div class="footer-sml-layout-option-wrap">
						<label for="footer-sml-layout">
							<input type="checkbox" id="footer-sml-layout" name="footer-sml-layout" value="disabled" <?php checked( $footer_bar, 'disabled' ); ?> />
							<?php esc_html_e( 'Disable Footer', 'astra' ); ?>
						</label>
					</div>
						<?php
					}
					?>
					<?php do_action( 'astra_meta_box_markup_disable_sections_after', $meta ); ?>
				</div>
			</div>
			<?php

			do_action( 'astra_meta_box_markup_after', $meta );
		}

		/**
		 * Metabox Save
		 *
		 * @param  number $post_id Post ID.
		 * @return void
		 */
		public function save_meta_box( $post_id ) {

			// Checks save status.
			$is_autosave = wp_is_post_autosave( $post_id );
			$is_revision = wp_is_post_revision( $post_id );

			$is_valid_nonce = ( isset( $_POST['astra_settings_meta_box'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['astra_settings_meta_box'] ) ), basename( __FILE__ ) ) ) ? true : false;

			// Exits script depending on save status.
			if ( $is_autosave || $is_revision || ! $is_valid_nonce ) {
				return;
			}

			/**
			 * Get meta options
			 */
			$post_meta = self::get_meta_option();

			foreach ( $post_meta as $key => $data ) {

				// Sanitize values.
				$sanitize_filter = ( isset( $data['sanitize'] ) ) ? $data['sanitize'] : 'FILTER_SANITIZE_STRING';

				switch ( $sanitize_filter ) {

					default:
					case 'FILTER_SANITIZE_STRING':
						/** @psalm-suppress PossiblyInvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
						$meta_value = ! empty( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
						/** @psalm-suppress PossiblyInvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
						break;

					case 'FILTER_SANITIZE_URL':
							$meta_value = filter_input( INPUT_POST, $key, FILTER_SANITIZE_URL );
						break;

					case 'FILTER_SANITIZE_NUMBER_INT':
							$meta_value = filter_input( INPUT_POST, $key, FILTER_SANITIZE_NUMBER_INT );
						break;

					case 'FILTER_DEFAULT':
							$meta_value = filter_input( INPUT_POST, $key, FILTER_DEFAULT ); // phpcs:ignore WordPressVIPMinimum.Security.PHPFilterFunctions.RestrictedFilter -- Default filter after all other cases, keeping this filter for backward compatibility of PRO options.
						break;
				}

				// Store values.
				if ( $meta_value ) {
					update_post_meta( $post_id, $key, $meta_value );

					// Update meta key (flag) as old user migration is already completed at this point.
					update_post_meta( $post_id, 'astra-migrate-meta-layouts', 'set' );
				} else {

					/** @psalm-suppress InvalidArgument */
					delete_post_meta( $post_id, $key );
				}
			}

		}



		/**
		 * Register Script for Meta options
		 */
		public function register_script() {
			$path = get_template_directory_uri() . '/inc/metabox/extend-metabox/build/index.js';
			wp_register_script(
				'astra-meta-settings',
				$path,
				array( 'wp-plugins', 'wp-edit-post', 'wp-i18n', 'wp-element' ),
				ASTRA_THEME_VERSION,
				true
			);
		}

		/**
		 * Enqueue Script for Meta settings.
		 *
		 * @return void
		 */
		public function load_scripts() {
			$post_id   = get_the_ID();
			$post_type = get_post_type();

			if ( defined( 'ASTRA_ADVANCED_HOOKS_POST_TYPE' ) && ASTRA_ADVANCED_HOOKS_POST_TYPE === $post_type ) {
				return;
			}

			$metabox_name = sprintf(
				// Translators: %s is the theme name.
				__( '%s Settings', 'astra' ),
				astra_get_theme_name()
			);

			$settings_title = $metabox_name;

			/* Directory and Extension */
			$file_prefix  = ( is_rtl() ) ? '-rtl' : '';
			$file_prefix .= ( true === SCRIPT_DEBUG ) ? '' : '.min';
			$dir_name     = ( true === SCRIPT_DEBUG ) ? 'unminified' : 'minified';
			$css_uri      = ASTRA_THEME_URI . '/inc/metabox/extend-metabox/css/' . $dir_name;

			wp_enqueue_style( 'astra-meta-box', $css_uri . '/metabox' . $file_prefix . '.css', array(), ASTRA_THEME_VERSION );

			wp_enqueue_script( 'astra-meta-settings' );
			wp_set_script_translations( 'astra-meta-settings', 'astra' );
			$astra_ext_extension_class_exists = class_exists( 'Astra_Ext_Extension' ) ? true : false;

			$ast_content_layout_sidebar = false;
			if ( $post_id ) {
				$page_for_posts = absint( get_option( 'page_for_posts' ) );
				if ( $post_id === $page_for_posts ) {
					$ast_content_layout_sidebar = true;
				}
			}

			$palette_css_var_prefix   = Astra_Global_Palette::get_css_variable_prefix();
			$apply_new_default_values = astra_button_default_padding_updated();
			$bg_updated_title         = sprintf(
				/* translators: 1: Post type, 2: Background string */
				'%1$s %2$s',
				ucfirst( strval( $post_type ) ),
				__( 'Background', 'astra' )
			);
			$page_bg_dynamic_title = ( $post_type ? $bg_updated_title : __( 'Page Background', 'astra' ) );
			$global_palette        = astra_get_option( 'global-color-palette' );

			/* Created a new array specifically designed for storing post types that don't require Astra's meta settings.*/
			$register_astra_metabox = ! in_array( $post_type, array( 'wp_block' ), true );

			wp_localize_script(
				'astra-meta-settings',
				'astMetaParams',
				array(
					'post_type'                      => $post_type,
					'title'                          => $settings_title,
					'sidebar_options'                => $this->get_sidebar_options(),
					'sidebar_title'                  => __( 'Sidebar', 'astra' ),
					'content_layout'                 => $this->get_content_layout_options(),
					'content_style'                  => $this->get_content_style_options(),
					'sidebar_style'                  => $this->get_sidebar_style_options(),
					'content_layout_title'           => __( 'Content Layout', 'astra' ),
					'disable_sections_title'         => __( 'Disable Sections', 'astra' ),
					'disable_sections'               => $this->get_disable_section_fields(),
					'isWhiteLabelled'                => astra_is_white_labelled(),
					'sticky_header_title'            => __( 'Sticky Header', 'astra' ),
					'sticky_header_options'          => $this->get_sticky_header_options(),
					'transparent_header_title'       => __( 'Transparent Header', 'astra' ),
					'page_header_title'              => __( 'Page Header', 'astra' ),
					'page_header_edit_link'          => esc_url( admin_url( 'edit.php?post_type=astra_adv_header' ) ),
					'header_options'                 => $this->get_header_enabled_options(),
					'headers_meta_options'           => $this->get_header_disable_meta_fields(),
					'page_header_options'            => $this->get_page_header_options(),
					'page_header_availability'       => $this->check_page_header_availability(),
					'is_bb_themer_layout'            => ! astra_check_is_bb_themer_layout(), // Show page header option only when bb is not activated.
					'is_addon_activated'             => defined( 'ASTRA_EXT_VER' ) ? true : false,
					'sticky_addon_enabled'           => ( $astra_ext_extension_class_exists && Astra_Ext_Extension::is_active( 'sticky-header' ) ) ? true : false,
					'register_astra_metabox'         => apply_filters( 'astra_settings_metabox_register', $register_astra_metabox ),
					'is_hide_contnet_layout_sidebar' => $ast_content_layout_sidebar,
					'upgrade_pro_link'               => ASTRA_PRO_CUSTOMIZER_UPGRADE_URL,
					'show_upgrade_notice'            => astra_showcase_upgrade_notices(),
					// Flag needed to check whether user is old or new, true for old user, false for new.
					'v4_1_6_migration'               => ( ! Astra_Dynamic_CSS::astra_fullwidth_sidebar_support() ),
					'color_addon_enabled'            => ( $astra_ext_extension_class_exists && Astra_Ext_Extension::is_active( 'colors-and-background' ) ) ? true : false,
					'site_page_bg_meta_default'      => array(
						'desktop' => array(
							'background-color'      => $apply_new_default_values ? 'var(--ast-global-color-4)' : '',
							'background-image'      => '',
							'background-repeat'     => 'repeat',
							'background-position'   => 'center center',
							'background-size'       => 'auto',
							'background-attachment' => 'scroll',
							'background-type'       => '',
							'background-media'      => '',
							'overlay-type'          => '',
							'overlay-color'         => '',
							'overlay-opacity'       => '',
							'overlay-gradient'      => '',
						),
						'tablet'  => array(
							'background-color'      => '',
							'background-image'      => '',
							'background-repeat'     => 'repeat',
							'background-position'   => 'center center',
							'background-size'       => 'auto',
							'background-attachment' => 'scroll',
							'background-type'       => '',
							'background-media'      => '',
							'overlay-type'          => '',
							'overlay-color'         => '',
							'overlay-opacity'       => '',
							'overlay-gradient'      => '',
						),
						'mobile'  => array(
							'background-color'      => '',
							'background-image'      => '',
							'background-repeat'     => 'repeat',
							'background-position'   => 'center center',
							'background-size'       => 'auto',
							'background-attachment' => 'scroll',
							'background-type'       => '',
							'background-media'      => '',
							'overlay-type'          => '',
							'overlay-color'         => '',
							'overlay-opacity'       => '',
							'overlay-gradient'      => '',
						),
					),
					'content_page_bg_meta_default'   => array(
						'desktop' => array(
							'background-color'      => 'var(' . $palette_css_var_prefix . '5)',
							'background-image'      => '',
							'background-repeat'     => 'repeat',
							'background-position'   => 'center center',
							'background-size'       => 'auto',
							'background-attachment' => 'scroll',
							'background-type'       => '',
							'background-media'      => '',
							'overlay-type'          => '',
							'overlay-color'         => '',
							'overlay-opacity'       => '',
							'overlay-gradient'      => '',
						),
						'tablet'  => array(
							'background-color'      => 'var(' . $palette_css_var_prefix . '5)',
							'background-image'      => '',
							'background-repeat'     => 'repeat',
							'background-position'   => 'center center',
							'background-size'       => 'auto',
							'background-attachment' => 'scroll',
							'background-type'       => '',
							'background-media'      => '',
							'overlay-type'          => '',
							'overlay-color'         => '',
							'overlay-opacity'       => '',
							'overlay-gradient'      => '',
						),
						'mobile'  => array(
							'background-color'      => 'var(' . $palette_css_var_prefix . '5)',
							'background-image'      => '',
							'background-repeat'     => 'repeat',
							'background-position'   => 'center center',
							'background-size'       => 'auto',
							'background-attachment' => 'scroll',
							'background-type'       => '',
							'background-media'      => '',
							'overlay-type'          => '',
							'overlay-color'         => '',
							'overlay-opacity'       => '',
							'overlay-gradient'      => '',
						),
					),
					'isWP_5_9'                       => astra_wp_version_compare( '5.8.99', '>=' ),
					'ast_page_bg_title'              => __( 'Surface Colors', 'astra' ),
					'page_bg_toggle_options'         => $this->get_page_bg_toggle_options(),
					'surface_color_help_text'        => __( 'Enabling this option will override global > colors > surface color options', 'astra' ),
					'page_bg_dynamic_title'          => $page_bg_dynamic_title,
					'global_color_palette'           => $global_palette,
				)
			);

			wp_enqueue_script( 'astra-metabox-cf-compatibility', ASTRA_THEME_URI . 'inc/assets/js/custom-fields-priority.js', array(), ASTRA_THEME_VERSION, false );
		}

		/**
		 * Returns an array of sidebar options.
		 *
		 * @return array The array of sidebar options.
		 */
		public function get_sidebar_options() {
			return array(
				'default'       => __( 'Customizer Setting', 'astra' ),
				'no-sidebar'    => __( 'No Sidebar', 'astra' ),
				'left-sidebar'  => __( 'Left Sidebar', 'astra' ),
				'right-sidebar' => __( 'Right Sidebar', 'astra' ),
			);
		}

		/**
		 * Returns an array of content layout options for the current post type.
		 *
		 * @return array The array of content layout options.
		 */
		public function get_content_layout_options() {
			$post_type   = get_post_type();
			$exclude_cpt = in_array(
				$post_type,
				array(
					'product',
					'download',
					'course',
					'lesson',
					'tutor_quiz',
					'tutor_assignments',
					'sfwd-assignment',
					'sfwd-essays',
					'sfwd-transactions',
					'sfwd-certificates',
					'sfwd-quiz',
					'sfwd-courses',
					'sfwd-lessons',
					'sfwd-topic',
					'groups',
				)
			);
			if ( astra_with_third_party() || $exclude_cpt ) {
				return array(
					'default'                => __( 'Customizer Setting', 'astra' ),
					'normal-width-container' => __( 'Normal', 'astra' ),
					'full-width-container'   => __( 'Full Width', 'astra' ),
				);
			}
			return array(
				'default'                => __( 'Customizer Setting', 'astra' ),
				'normal-width-container' => __( 'Normal', 'astra' ),
				'narrow-width-container' => __( 'Narrow', 'astra' ),
				'full-width-container'   => __( 'Full Width', 'astra' ),
			);
		}

		/**
		 * @return array The array of content layout options.
		 * @since 4.2.0
		 */
		public function get_content_style_options() {
			return array(
				'default' => __( 'Default', 'astra' ),
				'unboxed' => __( 'Unboxed', 'astra' ),
				'boxed'   => __( 'Boxed', 'astra' ),
			);
		}

		/**
		 * @return array The array of sidebar style options.
		 * @since 4.2.0
		 */
		public function get_sidebar_style_options() {
			return array(
				'default' => __( 'Default', 'astra' ),
				'unboxed' => __( 'Unboxed', 'astra' ),
				'boxed'   => __( 'Boxed', 'astra' ),
			);
		}

		/**
		 * Get header related sub-meta fields.
		 *
		 * @return array $astra_header_options All header dependent toggle based page elements.
		 */
		public function get_header_disable_meta_fields() {
			$astra_header_options = array();

			if ( Astra_Builder_Helper::is_row_empty( 'above', 'header', 'desktop' ) ) {
				$astra_header_options[] = array(
					'key'   => 'ast-hfb-above-header-display',
					'label' => __( 'Disable Above Header', 'astra' ),
				);
			}

			if ( Astra_Builder_Helper::is_row_empty( 'primary', 'header', 'desktop' ) ) {
				$astra_header_options[] = array(
					'key'   => 'ast-main-header-display',
					'label' => __( 'Disable Primary Header', 'astra' ),
				);
			}

			if ( Astra_Builder_Helper::is_row_empty( 'below', 'header', 'desktop' ) ) {
				$astra_header_options[] = array(
					'key'   => 'ast-hfb-below-header-display',
					'label' => __( 'Disable Below Header', 'astra' ),
				);
			}

			if (
				Astra_Builder_Helper::is_row_empty( 'above', 'header', 'mobile' ) ||
				Astra_Builder_Helper::is_row_empty( 'primary', 'header', 'mobile' ) ||
				Astra_Builder_Helper::is_row_empty( 'below', 'header', 'mobile' )
			) {
				$astra_header_options[] = array(
					'key'   => 'ast-hfb-mobile-header-display',
					'label' => __( 'Disable Mobile Header', 'astra' ),
				);
			}

			return $astra_header_options;
		}

		/**
		 * Get disable section fields.
		 *
		 * @return array $astra_page_meta_elements All toggle based page elements.
		 */
		public function get_disable_section_fields() {

			$astra_page_meta_elements = array(
				array(
					'key'   => 'ast-global-header-display',
					'label' => __( 'Disable Header', 'astra' ),
				),
				array(
					'key'   => 'footer-sml-layout',
					'label' => __( 'Disable Footer', 'astra' ),
				),
			);

			$post_type            = strval( get_post_type() );
			$supported_post_types = Astra_Posts_Structure_Loader::get_supported_post_types();
			if ( ( in_array( $post_type, $supported_post_types ) && true === astra_get_option( 'ast-single-' . $post_type . '-title', ( class_exists( 'WooCommerce' ) && 'product' === $post_type ) ? false : true ) ) ) {
				if ( ! ( class_exists( 'WooCommerce' ) && absint( astra_get_post_id() ) === wc_get_page_id( 'shop' ) ) ) {
					$astra_page_meta_elements[] = array(
						'key'   => 'ast-banner-title-visibility',
						'label' => __( 'Disable Banner Area', 'astra' ),
					);
				}
			}

			if ( 'none' !== astra_get_option( 'breadcrumb-position', 'none' ) ) {
				$astra_page_meta_elements[] = array(
					'key'   => 'ast-breadcrumbs-content',
					'label' => __( 'Disable Breadcrumb', 'astra' ),
				);
			}

			return $astra_page_meta_elements;
		}

		/**
		 * Get sticky header options.
		 */
		public function get_sticky_header_options() {
			$astra_sticky_header_options     = array();
			$sticky_above_header_condition   = false;
			$sticky_primary_header_condition = false;
			$sticky_below_header_condition   = false;

			/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			if ( defined( 'ASTRA_EXT_VER' ) && Astra_Ext_Extension::is_active( 'sticky-header' ) ) {
				/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
				$sticky_on_devices = astra_get_option( 'sticky-header-on-devices' );
				switch ( $sticky_on_devices ) {
					case 'desktop':
						$sticky_above_header_condition   = Astra_Builder_Helper::is_row_empty( 'above', 'header', 'desktop' );
						$sticky_primary_header_condition = Astra_Builder_Helper::is_row_empty( 'primary', 'header', 'desktop' );
						$sticky_below_header_condition   = Astra_Builder_Helper::is_row_empty( 'below', 'header', 'desktop' );
						break;
					case 'mobile':
						$sticky_above_header_condition   = Astra_Builder_Helper::is_row_empty( 'above', 'header', 'mobile' );
						$sticky_primary_header_condition = Astra_Builder_Helper::is_row_empty( 'primary', 'header', 'mobile' );
						$sticky_below_header_condition   = Astra_Builder_Helper::is_row_empty( 'below', 'header', 'mobile' );
						break;
					default:
						$sticky_above_header_condition   = ( Astra_Builder_Helper::is_row_empty( 'above', 'header', 'desktop' ) || Astra_Builder_Helper::is_row_empty( 'above', 'header', 'mobile' ) ) ? true : false;
						$sticky_primary_header_condition = ( Astra_Builder_Helper::is_row_empty( 'primary', 'header', 'desktop' ) || Astra_Builder_Helper::is_row_empty( 'primary', 'header', 'mobile' ) ) ? true : false;
						$sticky_below_header_condition   = ( Astra_Builder_Helper::is_row_empty( 'below', 'header', 'desktop' ) || Astra_Builder_Helper::is_row_empty( 'below', 'header', 'mobile' ) ) ? true : false;
						break;
				}
			}

			if ( $sticky_above_header_condition ) {
				$astra_sticky_header_options[] = array(
					'key'   => 'header-above-stick-meta',
					'label' => __( 'Stick Above Header', 'astra' ),
				);
			}

			if ( $sticky_primary_header_condition ) {
				$astra_sticky_header_options[] = array(
					'key'   => 'header-main-stick-meta',
					'label' => __( 'Stick Primary Header', 'astra' ),
				);
			}

			if ( $sticky_below_header_condition ) {
				$astra_sticky_header_options[] = array(
					'key'   => 'header-below-stick-meta',
					'label' => __( 'Stick Below Header', 'astra' ),
				);
			}

			return $astra_sticky_header_options;
		}

		/**
		 * Get all transparet and sticky header options.
		 */
		public function get_header_enabled_options() {
			return array(
				'default'  => __( 'Inherit', 'astra' ),
				'enabled'  => __( 'Enabled', 'astra' ),
				'disabled' => __( 'Disabled', 'astra' ),
			);
		}

		/**
		 * Get Page Background Toggle Options.
		 *
		 * @since 4.4.0
		 */
		public function get_page_bg_toggle_options() {
			return array(
				'default' => __( 'Inherit', 'astra' ),
				'enabled' => __( 'Enabled', 'astra' ),
			);
		}

		/**
		 * Checking the page headers are available and have some posts with it.
		 *
		 * @since 3.8.0
		 * @return bool true|false.
		 */
		public function check_page_header_availability() {
			if ( ! defined( 'ASTRA_EXT_VER' ) ) {
				return false;
			}

			if ( class_exists( 'Astra_Ext_Extension' ) && ! Astra_Ext_Extension::is_active( 'advanced-headers' ) ) {
				return false;
			}

			/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$page_headers = Astra_Target_Rules_Fields::get_post_selection( 'astra_adv_header' );
			/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			if ( empty( $page_headers ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Get page header Options.
		 */
		public function get_page_header_options() {
			if ( ! defined( 'ASTRA_EXT_VER' ) ) {
				return array();
			}

			/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$header_options = Astra_Target_Rules_Fields::get_post_selection( 'astra_adv_header' );
			/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			if ( empty( $header_options ) ) {
				$header_options = array(
					'' => __( 'No Page Headers Found', 'astra' ),
				);
			}

			return $header_options;
		}

		/**
		 * Register Post Meta options for react based fields.
		 *
		 * @since 3.7.4
		 */
		public function register_meta_settings() {
			$meta = self::get_meta_option();

			register_post_meta(
				'',
				'site-sidebar-layout',
				array(
					'show_in_rest'  => true,
					'single'        => true,
					'default'       => isset( $meta['site-sidebar-layout']['default'] ) ? $meta['site-sidebar-layout']['default'] : '',
					'type'          => 'string',
					'auth_callback' => '__return_true',
				)
			);
			register_post_meta(
				'',
				'site-content-layout',
				array(
					'show_in_rest'  => true,
					'single'        => true,
					'default'       => isset( $meta['site-content-layout']['default'] ) ? $meta['site-content-layout']['default'] : '',
					'type'          => 'string',
					'auth_callback' => '__return_true',
				)
			);
			register_post_meta(
				'',
				'ast-site-content-layout',
				array(
					'show_in_rest'  => true,
					'single'        => true,
					'default'       => isset( $meta['ast-site-content-layout']['default'] ) ? $meta['ast-site-content-layout']['default'] : '',
					'type'          => 'string',
					'auth_callback' => '__return_true',
				)
			);
			register_post_meta(
				'',
				'site-content-style',
				array(
					'show_in_rest'  => true,
					'single'        => true,
					'default'       => isset( $meta['site-content-style']['default'] ) ? $meta['site-content-style']['default'] : '',
					'type'          => 'string',
					'auth_callback' => '__return_true',
				)
			);
			register_post_meta(
				'',
				'site-sidebar-style',
				array(
					'show_in_rest'  => true,
					'single'        => true,
					'default'       => isset( $meta['site-sidebar-style']['default'] ) ? $meta['site-sidebar-style']['default'] : '',
					'type'          => 'string',
					'auth_callback' => '__return_true',
				)
			);
			register_post_meta(
				'',
				'ast-global-header-display',
				array(
					'show_in_rest'  => true,
					'single'        => true,
					'default'       => isset( $meta['ast-global-header-display']['default'] ) ? $meta['ast-global-header-display']['default'] : '',
					'type'          => 'string',
					'auth_callback' => '__return_true',
				)
			);
			register_post_meta(
				'',
				'ast-banner-title-visibility',
				array(
					'show_in_rest'  => true,
					'single'        => true,
					'default'       => isset( $meta['ast-banner-title-visibility']['default'] ) ? $meta['ast-banner-title-visibility']['default'] : '',
					'type'          => 'string',
					'auth_callback' => '__return_true',
				)
			);
			register_post_meta(
				'',
				'ast-main-header-display',
				array(
					'show_in_rest'  => true,
					'single'        => true,
					'default'       => isset( $meta['ast-main-header-display']['default'] ) ? $meta['ast-main-header-display']['default'] : '',
					'type'          => 'string',
					'auth_callback' => '__return_true',
				)
			);
			register_post_meta(
				'',
				'ast-hfb-above-header-display',
				array(
					'show_in_rest'  => true,
					'single'        => true,
					'default'       => isset( $meta['ast-hfb-above-header-display']['default'] ) ? $meta['ast-hfb-above-header-display']['default'] : '',
					'type'          => 'string',
					'auth_callback' => '__return_true',
				)
			);
			register_post_meta(
				'',
				'ast-hfb-below-header-display',
				array(
					'show_in_rest'  => true,
					'single'        => true,
					'default'       => isset( $meta['ast-hfb-below-header-display']['default'] ) ? $meta['ast-hfb-below-header-display']['default'] : '',
					'type'          => 'string',
					'auth_callback' => '__return_true',
				)
			);
			register_post_meta(
				'',
				'ast-hfb-mobile-header-display',
				array(
					'show_in_rest'  => true,
					'single'        => true,
					'default'       => isset( $meta['ast-hfb-mobile-header-display']['default'] ) ? $meta['ast-hfb-mobile-header-display']['default'] : '',
					'type'          => 'string',
					'auth_callback' => '__return_true',
				)
			);
			register_post_meta(
				'',
				'site-post-title',
				array(
					'show_in_rest'  => true,
					'single'        => true,
					'default'       => isset( $meta['site-post-title']['default'] ) ? $meta['site-post-title']['default'] : '',
					'type'          => 'string',
					'auth_callback' => '__return_true',
				)
			);
			register_post_meta(
				'',
				'ast-breadcrumbs-content',
				array(
					'show_in_rest'  => true,
					'single'        => true,
					'default'       => isset( $meta['ast-breadcrumbs-content']['default'] ) ? $meta['ast-breadcrumbs-content']['default'] : '',
					'type'          => 'string',
					'auth_callback' => '__return_true',
				)
			);
			register_post_meta(
				'',
				'ast-featured-img',
				array(
					'show_in_rest'  => true,
					'single'        => true,
					'default'       => isset( $meta['ast-featured-img']['default'] ) ? $meta['ast-featured-img']['default'] : '',
					'type'          => 'string',
					'auth_callback' => '__return_true',
				)
			);
			register_post_meta(
				'',
				'footer-sml-layout',
				array(
					'show_in_rest'  => true,
					'single'        => true,
					'default'       => isset( $meta['footer-sml-layout']['default'] ) ? $meta['footer-sml-layout']['default'] : '',
					'type'          => 'string',
					'auth_callback' => '__return_true',
				)
			);
			register_post_meta(
				'',
				'theme-transparent-header-meta',
				array(
					'show_in_rest'  => true,
					'single'        => true,
					'type'          => 'string',
					'auth_callback' => '__return_true',
				)
			);
			register_post_meta(
				'',
				'adv-header-id-meta',
				array(
					'show_in_rest'  => true,
					'single'        => true,
					'type'          => 'string',
					'auth_callback' => '__return_true',
				)
			);
			register_post_meta(
				'',
				'stick-header-meta',
				array(
					'show_in_rest'  => true,
					'single'        => true,
					'type'          => 'string',
					'auth_callback' => '__return_true',
				)
			);

			register_post_meta(
				'',
				'header-above-stick-meta',
				array(
					'show_in_rest'  => true,
					'single'        => true,
					'default'       => isset( $meta['header-above-stick-meta']['default'] ) ? $meta['header-above-stick-meta']['default'] : '',
					'type'          => 'string',
					'auth_callback' => '__return_true',
				)
			);

			register_post_meta(
				'',
				'header-main-stick-meta',
				array(
					'show_in_rest'  => true,
					'single'        => true,
					'default'       => isset( $meta['header-main-stick-meta']['default'] ) ? $meta['header-main-stick-meta']['default'] : '',
					'type'          => 'string',
					'auth_callback' => '__return_true',
				)
			);

			register_post_meta(
				'',
				'header-below-stick-meta',
				array(
					'show_in_rest'  => true,
					'single'        => true,
					'default'       => isset( $meta['header-below-stick-meta']['default'] ) ? $meta['header-below-stick-meta']['default'] : '',
					'type'          => 'string',
					'auth_callback' => '__return_true',
				)
			);

			register_post_meta(
				'',
				'astra-migrate-meta-layouts',
				array(
					'show_in_rest'  => true,
					'single'        => true,
					'default'       => isset( $meta['astra-migrate-meta-layouts']['default'] ) ? $meta['astra-migrate-meta-layouts']['default'] : '',
					'type'          => 'string',
					'auth_callback' => '__return_true',
				)
			);

			register_post_meta(
				'',
				'ast-page-background-enabled',
				array(
					'show_in_rest'  => true,
					'single'        => true,
					'default'       => 'default',
					'type'          => 'string',
					'auth_callback' => '__return_true',
				)
			);

			$apply_new_default_values = astra_button_default_padding_updated();
			register_post_meta(
				'',
				'ast-page-background-meta',
				array(
					'single'        => true,
					'type'          => 'object',
					'auth_callback' => '__return_true',
					'show_in_rest'  => array(
						'schema' => array(
							'type'       => 'object',
							'properties' => array(
								'desktop' => array(
									'type'       => 'object',
									'properties' => array(
										'background-color' => array(
											'type' => 'string',
										),
										'background-image' => array(
											'type' => 'string',
										),
										'background-repeat' => array(
											'type' => 'string',
										),
										'background-position' => array(
											'type' => 'string',
										),
										'background-size'  => array(
											'type' => 'string',
										),
										'background-attachment' => array(
											'type' => 'string',
										),
										'background-type'  => array(
											'type' => 'string',
										),
										'background-media' => array(
											'type' => 'string',
										),
										'overlay-type'     => array(
											'type' => 'string',
										),
										'overlay-color'    => array(
											'type' => 'string',
										),
										'overlay-opacity'  => array(
											'type' => 'string',
										),
										'overlay-gradient' => array(
											'type' => 'string',
										),
									),
								),
								'tablet'  => array(
									'type'       => 'object',
									'properties' => array(
										'background-color' => array(
											'type' => 'string',
										),
										'background-image' => array(
											'type' => 'string',
										),
										'background-repeat' => array(
											'type' => 'string',
										),
										'background-position' => array(
											'type' => 'string',
										),
										'background-size'  => array(
											'type' => 'string',
										),
										'background-attachment' => array(
											'type' => 'string',
										),
										'background-type'  => array(
											'type' => 'string',
										),
										'background-media' => array(
											'type' => 'string',
										),
										'overlay-type'     => array(
											'type' => 'string',
										),
										'overlay-color'    => array(
											'type' => 'string',
										),
										'overlay-opacity'  => array(
											'type' => 'string',
										),
										'overlay-gradient' => array(
											'type' => 'string',
										),
									),
								),
								'mobile'  => array(
									'type'       => 'object',
									'properties' => array(
										'background-color' => array(
											'type' => 'string',
										),
										'background-image' => array(
											'type' => 'string',
										),
										'background-repeat' => array(
											'type' => 'string',
										),
										'background-position' => array(
											'type' => 'string',
										),
										'background-size'  => array(
											'type' => 'string',
										),
										'background-attachment' => array(
											'type' => 'string',
										),
										'background-type'  => array(
											'type' => 'string',
										),
										'background-media' => array(
											'type' => 'string',
										),
										'overlay-type'     => array(
											'type' => 'string',
										),
										'overlay-color'    => array(
											'type' => 'string',
										),
										'overlay-opacity'  => array(
											'type' => 'string',
										),
										'overlay-gradient' => array(
											'type' => 'string',
										),
									),
								),
							),
						),
					),
					'default'       => array(
						'desktop' => array(
							'background-color'      => $apply_new_default_values ? 'var(--ast-global-color-4)' : '',
							'background-image'      => '',
							'background-repeat'     => 'repeat',
							'background-position'   => 'center center',
							'background-size'       => 'auto',
							'background-attachment' => 'scroll',
							'background-type'       => '',
							'background-media'      => '',
							'overlay-type'          => '',
							'overlay-color'         => '',
							'overlay-opacity'       => '',
							'overlay-gradient'      => '',
						),
						'tablet'  => array(
							'background-color'      => '',
							'background-image'      => '',
							'background-repeat'     => 'repeat',
							'background-position'   => 'center center',
							'background-size'       => 'auto',
							'background-attachment' => 'scroll',
							'background-type'       => '',
							'background-media'      => '',
							'overlay-type'          => '',
							'overlay-color'         => '',
							'overlay-opacity'       => '',
							'overlay-gradient'      => '',
						),
						'mobile'  => array(
							'background-color'      => '',
							'background-image'      => '',
							'background-repeat'     => 'repeat',
							'background-position'   => 'center center',
							'background-size'       => 'auto',
							'background-attachment' => 'scroll',
							'background-type'       => '',
							'background-media'      => '',
							'overlay-type'          => '',
							'overlay-color'         => '',
							'overlay-opacity'       => '',
							'overlay-gradient'      => '',
						),
					),
				)
			);

			$palette_css_var_prefix = Astra_Global_Palette::get_css_variable_prefix();
			register_post_meta(
				'',
				'ast-content-background-meta',
				array(
					'single'        => true,
					'type'          => 'object',
					'auth_callback' => '__return_true',
					'show_in_rest'  => array(
						'schema' => array(
							'type'       => 'object',
							'properties' => array(
								'desktop' => array(
									'type'       => 'object',
									'properties' => array(
										'background-color' => array(
											'type' => 'string',
										),
										'background-image' => array(
											'type' => 'string',
										),
										'background-repeat' => array(
											'type' => 'string',
										),
										'background-position' => array(
											'type' => 'string',
										),
										'background-size'  => array(
											'type' => 'string',
										),
										'background-attachment' => array(
											'type' => 'string',
										),
										'background-type'  => array(
											'type' => 'string',
										),
										'background-media' => array(
											'type' => 'string',
										),
										'overlay-type'     => array(
											'type' => 'string',
										),
										'overlay-color'    => array(
											'type' => 'string',
										),
										'overlay-opacity'  => array(
											'type' => 'string',
										),
										'overlay-gradient' => array(
											'type' => 'string',
										),
									),
								),
								'tablet'  => array(
									'type'       => 'object',
									'properties' => array(
										'background-color' => array(
											'type' => 'string',
										),
										'background-image' => array(
											'type' => 'string',
										),
										'background-repeat' => array(
											'type' => 'string',
										),
										'background-position' => array(
											'type' => 'string',
										),
										'background-size'  => array(
											'type' => 'string',
										),
										'background-attachment' => array(
											'type' => 'string',
										),
										'background-type'  => array(
											'type' => 'string',
										),
										'background-media' => array(
											'type' => 'string',
										),
										'overlay-type'     => array(
											'type' => 'string',
										),
										'overlay-color'    => array(
											'type' => 'string',
										),
										'overlay-opacity'  => array(
											'type' => 'string',
										),
										'overlay-gradient' => array(
											'type' => 'string',
										),
									),
								),
								'mobile'  => array(
									'type'       => 'object',
									'properties' => array(
										'background-color' => array(
											'type' => 'string',
										),
										'background-image' => array(
											'type' => 'string',
										),
										'background-repeat' => array(
											'type' => 'string',
										),
										'background-position' => array(
											'type' => 'string',
										),
										'background-size'  => array(
											'type' => 'string',
										),
										'background-attachment' => array(
											'type' => 'string',
										),
										'background-type'  => array(
											'type' => 'string',
										),
										'background-media' => array(
											'type' => 'string',
										),
										'overlay-type'     => array(
											'type' => 'string',
										),
										'overlay-color'    => array(
											'type' => 'string',
										),
										'overlay-opacity'  => array(
											'type' => 'string',
										),
										'overlay-gradient' => array(
											'type' => 'string',
										),
									),
								),
							),
						),
					),
					'default'       => array(
						'desktop' => array(
							'background-color'      => 'var(' . $palette_css_var_prefix . '5)',
							'background-image'      => '',
							'background-repeat'     => 'repeat',
							'background-position'   => 'center center',
							'background-size'       => 'auto',
							'background-attachment' => 'scroll',
							'background-type'       => '',
							'background-media'      => '',
							'overlay-type'          => '',
							'overlay-color'         => '',
							'overlay-opacity'       => '',
							'overlay-gradient'      => '',
						),
						'tablet'  => array(
							'background-color'      => 'var(' . $palette_css_var_prefix . '5)',
							'background-image'      => '',
							'background-repeat'     => 'repeat',
							'background-position'   => 'center center',
							'background-size'       => 'auto',
							'background-attachment' => 'scroll',
							'background-type'       => '',
							'background-media'      => '',
							'overlay-type'          => '',
							'overlay-color'         => '',
							'overlay-opacity'       => '',
							'overlay-gradient'      => '',
						),
						'mobile'  => array(
							'background-color'      => 'var(' . $palette_css_var_prefix . '5)',
							'background-image'      => '',
							'background-repeat'     => 'repeat',
							'background-position'   => 'center center',
							'background-size'       => 'auto',
							'background-attachment' => 'scroll',
							'background-type'       => '',
							'background-media'      => '',
							'overlay-type'          => '',
							'overlay-color'         => '',
							'overlay-opacity'       => '',
							'overlay-gradient'      => '',
						),
					),
				)
			);
		}

		/**
		 * Setup meta options for Astra meta settings.
		 *
		 * @since 3.7.8
		 */
		public static function post_meta_options() {
			$palette_css_var_prefix   = Astra_Global_Palette::get_css_variable_prefix();
			$apply_new_default_values = astra_button_default_padding_updated();
			self::$meta_option        = apply_filters(
				'astra_meta_box_options',
				array(
					'ast-global-header-display'     => array(
						'sanitize' => 'FILTER_SANITIZE_STRING',
					),
					'ast-banner-title-visibility'   => array(
						'sanitize' => 'FILTER_SANITIZE_STRING',
					),
					'ast-hfb-above-header-display'  => array(
						'sanitize' => 'FILTER_SANITIZE_STRING',
					),
					'ast-main-header-display'       => array(
						'sanitize' => 'FILTER_SANITIZE_STRING',
					),
					'ast-hfb-below-header-display'  => array(
						'sanitize' => 'FILTER_SANITIZE_STRING',
					),
					'ast-hfb-mobile-header-display' => array(
						'sanitize' => 'FILTER_SANITIZE_STRING',
					),
					'footer-sml-layout'             => array(
						'sanitize' => 'FILTER_SANITIZE_STRING',
					),
					'footer-adv-display'            => array(
						'sanitize' => 'FILTER_SANITIZE_STRING',
					),
					'site-post-title'               => array(
						'sanitize' => 'FILTER_SANITIZE_STRING',
					),
					'site-sidebar-layout'           => array(
						'default'  => 'default',
						'sanitize' => 'FILTER_SANITIZE_STRING',
					),
					'ast-site-content-layout'       => array(
						'default'  => '',
						'sanitize' => 'FILTER_DEFAULT',
					),
					'site-content-style'            => array(
						'default'  => 'default',
						'sanitize' => 'FILTER_DEFAULT',
					),
					'site-sidebar-style'            => array(
						'default'  => 'default',
						'sanitize' => 'FILTER_DEFAULT',
					),
					'astra-migrate-meta-layouts'    => array(
						'default'  => 'default',
						'sanitize' => 'FILTER_DEFAULT',
					),
					'ast-featured-img'              => array(
						'sanitize' => 'FILTER_SANITIZE_STRING',
					),
					'ast-breadcrumbs-content'       => array(
						'sanitize' => 'FILTER_SANITIZE_STRING',
					),
					'ast-page-background-enabled'   => array(
						'default'  => 'default',
						'sanitize' => 'FILTER_DEFAULT',
					),
					'ast-page-background-meta'      => array(
						'default'  => array(
							'desktop' => array(
								'background-color'      => $apply_new_default_values ? 'var(--ast-global-color-4)' : '',
								'background-image'      => '',
								'background-repeat'     => 'repeat',
								'background-position'   => 'center center',
								'background-size'       => 'auto',
								'background-attachment' => 'scroll',
								'background-type'       => '',
								'background-media'      => '',
								'overlay-type'          => '',
								'overlay-color'         => '',
								'overlay-opacity'       => '',
								'overlay-gradient'      => '',
							),
							'tablet'  => array(
								'background-color'      => '',
								'background-image'      => '',
								'background-repeat'     => 'repeat',
								'background-position'   => 'center center',
								'background-size'       => 'auto',
								'background-attachment' => 'scroll',
								'background-type'       => '',
								'background-media'      => '',
								'overlay-type'          => '',
								'overlay-color'         => '',
								'overlay-opacity'       => '',
								'overlay-gradient'      => '',
							),
							'mobile'  => array(
								'background-color'      => '',
								'background-image'      => '',
								'background-repeat'     => 'repeat',
								'background-position'   => 'center center',
								'background-size'       => 'auto',
								'background-attachment' => 'scroll',
								'background-type'       => '',
								'background-media'      => '',
								'overlay-type'          => '',
								'overlay-color'         => '',
								'overlay-opacity'       => '',
								'overlay-gradient'      => '',
							),
						),
						'sanitize' => 'FILTER_DEFAULT',
					),
					'ast-content-background-meta'   => array(
						'default'  => array(
							'desktop' => array(
								'background-color'      => 'var(' . $palette_css_var_prefix . '5)',
								'background-image'      => '',
								'background-repeat'     => 'repeat',
								'background-position'   => 'center center',
								'background-size'       => 'auto',
								'background-attachment' => 'scroll',
								'background-type'       => '',
								'background-media'      => '',
								'overlay-type'          => '',
								'overlay-color'         => '',
								'overlay-opacity'       => '',
								'overlay-gradient'      => '',
							),
							'tablet'  => array(
								'background-color'      => 'var(' . $palette_css_var_prefix . '5)',
								'background-image'      => '',
								'background-repeat'     => 'repeat',
								'background-position'   => 'center center',
								'background-size'       => 'auto',
								'background-attachment' => 'scroll',
								'background-type'       => '',
								'background-media'      => '',
								'overlay-type'          => '',
								'overlay-color'         => '',
								'overlay-opacity'       => '',
								'overlay-gradient'      => '',
							),
							'mobile'  => array(
								'background-color'      => 'var(' . $palette_css_var_prefix . '5)',
								'background-image'      => '',
								'background-repeat'     => 'repeat',
								'background-position'   => 'center center',
								'background-size'       => 'auto',
								'background-attachment' => 'scroll',
								'background-type'       => '',
								'background-media'      => '',
								'overlay-type'          => '',
								'overlay-color'         => '',
								'overlay-opacity'       => '',
								'overlay-gradient'      => '',
							),
						),
						'sanitize' => 'FILTER_DEFAULT',
					),
				)
			);
		}
	}
}

/**
 * Footer disable on archive pages.
 *
 * @param bool $display_footer for controling the header and footer enable/disable options.
 *
 * @since 3.9.4
 */
function astra_footer_bar_display_cb( $display_footer ) {
	if ( is_home() && ! is_front_page() ) {
		$page_for_posts = get_option( 'page_for_posts' );
		$display_footer = get_post_meta( $page_for_posts, 'footer-sml-layout', true );
	}
	return $display_footer;
}

add_filter( 'astra_footer_bar_display', 'astra_footer_bar_display_cb', 99, 1 );

/**
 * Kicking this off by calling 'get_instance()' method
 */
Astra_Meta_Boxes::get_instance();
