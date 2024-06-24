<?php
/**
 * Astra Updates
 *
 * Functions for updating data, used by the background updater.
 *
 * @package Astra
 * @version 2.1.3
 */

defined( 'ABSPATH' ) || exit;

/**
 * Clear Astra + Astra Pro assets cache.
 *
 * @since 3.6.1
 * @return void.
 */
function astra_clear_all_assets_cache() {
	if ( ! class_exists( 'Astra_Cache_Base' ) ) {
		return;
	}
	// Clear Astra theme asset cache.
	$astra_cache_base_instance = new Astra_Cache_Base( 'astra' );
	$astra_cache_base_instance->refresh_assets( 'astra' );

	// Clear Astra Addon's static and dynamic CSS asset cache.
	$astra_addon_cache_base_instance = new Astra_Cache_Base( 'astra-addon' );
	$astra_addon_cache_base_instance->refresh_assets( 'astra-addon' );
}

/**
 * 4.0.0 backward handling part.
 *
 * 1. Migrate existing setting & do required onboarding for new admin dashboard v4.0.0 app.
 * 2. Migrating Post Structure & Meta options in title area meta parts.
 *
 * @since 4.0.0
 * @return void
 */
function astra_theme_background_updater_4_0_0() {
	// Dynamic customizer migration starts here.
	$theme_options = get_option( 'astra-settings', array() );
	if ( ! isset( $theme_options['dynamic-blog-layouts'] ) && ! isset( $theme_options['theme-dynamic-customizer-support'] ) ) {
		$theme_options['dynamic-blog-layouts']             = false;
		$theme_options['theme-dynamic-customizer-support'] = true;

		$post_types = Astra_Posts_Structure_Loader::get_supported_post_types();

		// Archive summary box compatibility.
		$archive_title_font_size = array(
			'desktop'      => isset( $theme_options['font-size-archive-summary-title']['desktop'] ) ? $theme_options['font-size-archive-summary-title']['desktop'] : 40,
			'tablet'       => isset( $theme_options['font-size-archive-summary-title']['tablet'] ) ? $theme_options['font-size-archive-summary-title']['tablet'] : '',
			'mobile'       => isset( $theme_options['font-size-archive-summary-title']['mobile'] ) ? $theme_options['font-size-archive-summary-title']['mobile'] : '',
			'desktop-unit' => isset( $theme_options['font-size-archive-summary-title']['desktop-unit'] ) ? $theme_options['font-size-archive-summary-title']['desktop-unit'] : 'px',
			'tablet-unit'  => isset( $theme_options['font-size-archive-summary-title']['tablet-unit'] ) ? $theme_options['font-size-archive-summary-title']['tablet-unit'] : 'px',
			'mobile-unit'  => isset( $theme_options['font-size-archive-summary-title']['mobile-unit'] ) ? $theme_options['font-size-archive-summary-title']['mobile-unit'] : 'px',
		);
		$single_title_font_size  = array(
			'desktop'      => isset( $theme_options['font-size-entry-title']['desktop'] ) ? $theme_options['font-size-entry-title']['desktop'] : '',
			'tablet'       => isset( $theme_options['font-size-entry-title']['tablet'] ) ? $theme_options['font-size-entry-title']['tablet'] : '',
			'mobile'       => isset( $theme_options['font-size-entry-title']['mobile'] ) ? $theme_options['font-size-entry-title']['mobile'] : '',
			'desktop-unit' => isset( $theme_options['font-size-entry-title']['desktop-unit'] ) ? $theme_options['font-size-entry-title']['desktop-unit'] : 'px',
			'tablet-unit'  => isset( $theme_options['font-size-entry-title']['tablet-unit'] ) ? $theme_options['font-size-entry-title']['tablet-unit'] : 'px',
			'mobile-unit'  => isset( $theme_options['font-size-entry-title']['mobile-unit'] ) ? $theme_options['font-size-entry-title']['mobile-unit'] : 'px',
		);
		$archive_summary_box_bg  = array(
			'desktop' => array(
				'background-color'      => ! empty( $theme_options['archive-summary-box-bg-color'] ) ? $theme_options['archive-summary-box-bg-color'] : '',
				'background-image'      => '',
				'background-repeat'     => 'repeat',
				'background-position'   => 'center center',
				'background-size'       => 'auto',
				'background-attachment' => 'scroll',
				'background-type'       => '',
				'background-media'      => '',
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
			),
		);
		// Single post structure.
		foreach ( $post_types as $index => $post_type ) {
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$single_post_structure = isset( $theme_options['blog-single-post-structure'] ) ? $theme_options['blog-single-post-structure'] : array( 'single-image', 'single-title-meta' );
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$migrated_post_structure = array();

			if ( ! empty( $single_post_structure ) ) {
				/** @psalm-suppress PossiblyInvalidIterator */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
				foreach ( $single_post_structure as $key ) {
					/** @psalm-suppress PossiblyInvalidIterator */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
					if ( 'single-title-meta' === $key ) {
						$migrated_post_structure[] = 'ast-dynamic-single-' . esc_attr( $post_type ) . '-title';
						if ( 'post' === $post_type ) {
							$migrated_post_structure[] = 'ast-dynamic-single-' . esc_attr( $post_type ) . '-meta';
						}
					}
					if ( 'single-image' === $key ) {
						$migrated_post_structure[] = 'ast-dynamic-single-' . esc_attr( $post_type ) . '-image';
					}
				}

				$theme_options[ 'ast-dynamic-single-' . esc_attr( $post_type ) . '-structure' ] = $migrated_post_structure;
			}

			// Single post meta.
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$single_post_meta = isset( $theme_options['blog-single-meta'] ) ? $theme_options['blog-single-meta'] : array( 'comments', 'category', 'author' );
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$migrated_post_metadata = array();

			if ( ! empty( $single_post_meta ) ) {
				$tax_counter = 0;
				$tax_slug    = 'ast-dynamic-single-' . esc_attr( $post_type ) . '-taxonomy';
				/** @psalm-suppress PossiblyInvalidIterator */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
				foreach ( $single_post_meta as $key ) {
					/** @psalm-suppress PossiblyInvalidIterator */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
					switch ( $key ) {
						case 'author':
							$migrated_post_metadata[] = 'author';
							break;
						case 'date':
							$migrated_post_metadata[] = 'date';
							break;
						case 'comments':
							$migrated_post_metadata[] = 'comments';
							break;
						case 'category':
							if ( 'post' === $post_type ) {
								$migrated_post_metadata[]   = $tax_slug;
								$theme_options[ $tax_slug ] = 'category';

								$tax_counter = ++$tax_counter;
								$tax_slug    = 'ast-dynamic-single-' . esc_attr( $post_type ) . '-taxonomy-' . $tax_counter;
							}
							break;
						case 'tag':
							if ( 'post' === $post_type ) {
								$migrated_post_metadata[]   = $tax_slug;
								$theme_options[ $tax_slug ] = 'post_tag';

								$tax_counter = ++$tax_counter;
								$tax_slug    = 'ast-dynamic-single-' . esc_attr( $post_type ) . '-taxonomy-' . $tax_counter;
							}
							break;
						default:
							break;
					}
				}

				$theme_options[ 'ast-dynamic-single-' . esc_attr( $post_type ) . '-metadata' ] = $migrated_post_metadata;
			}

			// Archive layout compatibilities.
			$archive_banner_layout = ( class_exists( 'WooCommerce' ) && 'product' === $post_type ) ? false : true; // Setting WooCommerce archive option disabled as WC already added their header content on archive.
			$theme_options[ 'ast-archive-' . esc_attr( $post_type ) . '-title' ] = $archive_banner_layout;

			// Single layout compatibilities.
			$single_banner_layout = ( class_exists( 'WooCommerce' ) && 'product' === $post_type ) ? false : true; // Setting WC single option disabled as there is no any header set from default WooCommerce.
			$theme_options[ 'ast-single-' . esc_attr( $post_type ) . '-title' ] = $single_banner_layout;

			// BG color support.
			$theme_options[ 'ast-dynamic-archive-' . esc_attr( $post_type ) . '-banner-image-type' ] = ! empty( $theme_options['archive-summary-box-bg-color'] ) ? 'custom' : 'none';
			$theme_options[ 'ast-dynamic-archive-' . esc_attr( $post_type ) . '-banner-custom-bg' ]  = $archive_summary_box_bg;

			// Archive title font support.
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$theme_options[ 'ast-dynamic-archive-' . esc_attr( $post_type ) . '-title-font-family' ] = ! empty( $theme_options['font-family-archive-summary-title'] ) ? $theme_options['font-family-archive-summary-title'] : '';
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$theme_options[ 'ast-dynamic-archive-' . esc_attr( $post_type ) . '-title-font-size' ] = $archive_title_font_size;
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$theme_options[ 'ast-dynamic-archive-' . esc_attr( $post_type ) . '-title-font-weight' ] = ! empty( $theme_options['font-weight-archive-summary-title'] ) ? $theme_options['font-weight-archive-summary-title'] : '';
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$archive_dynamic_line_height = ! empty( $theme_options['line-height-archive-summary-title'] ) ? $theme_options['line-height-archive-summary-title'] : '';
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$archive_dynamic_text_transform = ! empty( $theme_options['text-transform-archive-summary-title'] ) ? $theme_options['text-transform-archive-summary-title'] : '';
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			$theme_options[ 'ast-dynamic-archive-' . esc_attr( $post_type ) . '-title-font-extras' ] = array(
				'line-height'         => $archive_dynamic_line_height,
				'line-height-unit'    => 'em',
				'letter-spacing'      => '',
				'letter-spacing-unit' => 'px',
				'text-transform'      => $archive_dynamic_text_transform,
				'text-decoration'     => '',
			);

			// Archive title colors support.
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$theme_options[ 'ast-dynamic-archive-' . esc_attr( $post_type ) . '-banner-title-color' ] = ! empty( $theme_options['archive-summary-box-title-color'] ) ? $theme_options['archive-summary-box-title-color'] : '';
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$theme_options[ 'ast-dynamic-archive-' . esc_attr( $post_type ) . '-banner-text-color' ] = ! empty( $theme_options['archive-summary-box-text-color'] ) ? $theme_options['archive-summary-box-text-color'] : '';
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			// Single title colors support.
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$theme_options[ 'ast-dynamic-single-' . esc_attr( $post_type ) . '-banner-title-color' ] = ! empty( $theme_options['entry-title-color'] ) ? $theme_options['entry-title-color'] : '';
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			// Single title font support.
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$theme_options[ 'ast-dynamic-single-' . esc_attr( $post_type ) . '-title-font-family' ] = ! empty( $theme_options['font-family-entry-title'] ) ? $theme_options['font-family-entry-title'] : '';
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$theme_options[ 'ast-dynamic-single-' . esc_attr( $post_type ) . '-title-font-size' ] = $single_title_font_size;
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$theme_options[ 'ast-dynamic-single-' . esc_attr( $post_type ) . '-title-font-weight' ] = ! empty( $theme_options['font-weight-entry-title'] ) ? $theme_options['font-weight-entry-title'] : '';
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$single_dynamic_line_height = ! empty( $theme_options['line-height-entry-title'] ) ? $theme_options['line-height-entry-title'] : '';
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$single_dynamic_text_transform = ! empty( $theme_options['text-transform-entry-title'] ) ? $theme_options['text-transform-entry-title'] : '';
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			$theme_options[ 'ast-dynamic-single-' . esc_attr( $post_type ) . '-title-font-extras' ] = array(
				'line-height'         => $single_dynamic_line_height,
				'line-height-unit'    => 'em',
				'letter-spacing'      => '',
				'letter-spacing-unit' => 'px',
				'text-transform'      => $single_dynamic_text_transform,
				'text-decoration'     => '',
			);
		}

		// Set page specific structure, as page only has featured image at top & title beneath to it, hardcoded writing it here.
		$theme_options['ast-dynamic-single-page-structure'] = array( 'ast-dynamic-single-page-image', 'ast-dynamic-single-page-title' );

		// EDD content layout & sidebar layout migration in new dynamic option.
		$theme_options['archive-download-content-layout'] = isset( $theme_options['edd-archive-product-layout'] ) ? $theme_options['edd-archive-product-layout'] : 'default';
		$theme_options['archive-download-sidebar-layout'] = isset( $theme_options['edd-sidebar-layout'] ) ? $theme_options['edd-sidebar-layout'] : 'no-sidebar';
		$theme_options['single-download-content-layout']  = isset( $theme_options['edd-single-product-layout'] ) ? $theme_options['edd-single-product-layout'] : 'default';
		$theme_options['single-download-sidebar-layout']  = isset( $theme_options['edd-single-product-sidebar-layout'] ) ? $theme_options['edd-single-product-sidebar-layout'] : 'default';

		update_option( 'astra-settings', $theme_options );
	}

	// Admin backward handling starts here.
	$admin_dashboard_settings = get_option( 'astra_admin_settings', array() );
	if ( ! isset( $admin_dashboard_settings['theme-setup-admin-migrated'] ) ) {

		if ( ! isset( $admin_dashboard_settings['self_hosted_gfonts'] ) ) {
			$admin_dashboard_settings['self_hosted_gfonts'] = isset( $theme_options['load-google-fonts-locally'] ) ? $theme_options['load-google-fonts-locally'] : false;
		}
		if ( ! isset( $admin_dashboard_settings['preload_local_fonts'] ) ) {
			$admin_dashboard_settings['preload_local_fonts'] = isset( $theme_options['preload-local-fonts'] ) ? $theme_options['preload-local-fonts'] : false;
		}

		// Consider admin part from theme side migrated.
		$admin_dashboard_settings['theme-setup-admin-migrated'] = true;
		update_option( 'astra_admin_settings', $admin_dashboard_settings );
	}

	// Check if existing user and disable smooth scroll-to-id.
	if ( ! isset( $theme_options['enable-scroll-to-id'] ) ) {
		$theme_options['enable-scroll-to-id'] = false;
		update_option( 'astra-settings', $theme_options );
	}

	// Check if existing user and disable scroll to top if disabled from pro addons list.
	$scroll_to_top_visibility = false;
	/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
	if ( defined( 'ASTRA_EXT_VER' ) && Astra_Ext_Extension::is_active( 'scroll-to-top' ) ) {
		/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		$scroll_to_top_visibility = true;
	}
	if ( ! isset( $theme_options['scroll-to-top-enable'] ) ) {
		$theme_options['scroll-to-top-enable'] = $scroll_to_top_visibility;
		update_option( 'astra-settings', $theme_options );
	}

	// Default colors & typography flag.
	if ( ! isset( $theme_options['update-default-color-typo'] ) ) {
		$theme_options['update-default-color-typo'] = false;
		update_option( 'astra-settings', $theme_options );
	}

	// Block editor experience improvements compatibility flag.
	if ( ! isset( $theme_options['v4-block-editor-compat'] ) ) {
		$theme_options['v4-block-editor-compat'] = false;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * 4.0.2 backward handling part.
 *
 * 1. Read Time option backwards handling for old users.
 *
 * @since 4.0.2
 * @return void
 */
function astra_theme_background_updater_4_0_2() {
	$theme_options = get_option( 'astra-settings', array() );
	if ( ! isset( $theme_options['v4-0-2-update-migration'] ) && isset( $theme_options['blog-single-meta'] ) && in_array( 'read-time', $theme_options['blog-single-meta'] ) ) {
		if ( isset( $theme_options['ast-dynamic-single-post-metadata'] ) && ! in_array( 'read-time', $theme_options['ast-dynamic-single-post-metadata'] ) ) {
			$theme_options['ast-dynamic-single-post-metadata'][] = 'read-time';
			$theme_options['v4-0-2-update-migration']            = true;
			update_option( 'astra-settings', $theme_options );
		}
	}
}

/**
 * Handle backward compatibility on version 4.1.0
 *
 * @since 4.1.0
 * @return void
 */
function astra_theme_background_updater_4_1_0() {

	$theme_options = get_option( 'astra-settings', array() );

	if ( ! isset( $theme_options['v4-1-0-update-migration'] ) ) {
		$theme_options['v4-1-0-update-migration'] = true;
		$current_payment_list                     = array();
		$old_payment_list                         = isset( $theme_options['single-product-payment-list']['items'] ) ? $theme_options['single-product-payment-list']['items'] : array();

		$visa_payment       = isset( $theme_options['single-product-payment-visa'] ) ? $theme_options['single-product-payment-visa'] : '';
		$mastercard_payment = isset( $theme_options['single-product-payment-mastercard'] ) ? $theme_options['single-product-payment-mastercard'] : '';
		$discover_payment   = isset( $theme_options['single-product-payment-discover'] ) ? $theme_options['single-product-payment-discover'] : '';
		$paypal_payment     = isset( $theme_options['single-product-payment-paypal'] ) ? $theme_options['single-product-payment-paypal'] : '';
		$apple_pay_payment  = isset( $theme_options['single-product-payment-apple-pay'] ) ? $theme_options['single-product-payment-apple-pay'] : '';

		false !== $visa_payment ? array_push(
			$current_payment_list,
			array(
				'id'      => 'item-100',
				'enabled' => true,
				'source'  => 'icon',
				'icon'    => 'cc-visa',
				'image'   => '',
				'label'   => __( 'Visa', 'astra' ),
			)
		) : '';

		false !== $mastercard_payment ? array_push(
			$current_payment_list,
			array(
				'id'      => 'item-101',
				'enabled' => true,
				'source'  => 'icon',
				'icon'    => 'cc-mastercard',
				'image'   => '',
				'label'   => __( 'Mastercard', 'astra' ),
			)
		) : '';

		false !== $mastercard_payment ? array_push(
			$current_payment_list,
			array(
				'id'      => 'item-102',
				'enabled' => true,
				'source'  => 'icon',
				'icon'    => 'cc-amex',
				'image'   => '',
				'label'   => __( 'Amex', 'astra' ),
			)
		) : '';

		false !== $discover_payment ? array_push(
			$current_payment_list,
			array(
				'id'      => 'item-103',
				'enabled' => true,
				'source'  => 'icon',
				'icon'    => 'cc-discover',
				'image'   => '',
				'label'   => __( 'Discover', 'astra' ),
			)
		) : '';

		$paypal_payment ? array_push(
			$current_payment_list,
			array(
				'id'      => 'item-104',
				'enabled' => true,
				'source'  => 'icon',
				'icon'    => 'cc-paypal',
				'image'   => '',
				'label'   => __( 'Paypal', 'astra' ),
			)
		) : '';

		$apple_pay_payment ? array_push(
			$current_payment_list,
			array(
				'id'      => 'item-105',
				'enabled' => true,
				'source'  => 'icon',
				'icon'    => 'cc-apple-pay',
				'image'   => '',
				'label'   => __( 'Apple Pay', 'astra' ),
			)
		) : '';

		if ( $current_payment_list ) {
			$theme_options['single-product-payment-list'] =
			array(
				'items' =>
					array_merge(
						$current_payment_list,
						$old_payment_list
					),
			);

			update_option( 'astra-settings', $theme_options );
		}

		if ( ! isset( $theme_options['woo_support_global_settings'] ) ) {
			$theme_options['woo_support_global_settings'] = true;
			update_option( 'astra-settings', $theme_options );
		}

		if ( isset( $theme_options['theme-dynamic-customizer-support'] ) ) {
			$post_types = Astra_Posts_Structure_Loader::get_supported_post_types();
			foreach ( $post_types as $index => $post_type ) {
				$theme_options[ 'ast-dynamic-single-' . esc_attr( $post_type ) . '-title-font-extras' ]['text-transform'] = '';
			}
			update_option( 'astra-settings', $theme_options );
		}
	}
}

/**
 * 4.1.4 backward handling cases.
 *
 * 1. Migrating users to combined color overlay option to new dedicated overlay options.
 *
 * @since 4.1.4
 * @return void
 */
function astra_theme_background_updater_4_1_4() {
	$theme_options = get_option( 'astra-settings', array() );
	if ( ! isset( $theme_options['v4-1-4-update-migration'] ) ) {
		$ast_bg_control_options = array(
			'off-canvas-background',
			'footer-adv-bg-obj',
			'footer-bg-obj',
		);

		foreach ( $ast_bg_control_options as $key => $bg_option ) {
			if ( isset( $theme_options[ $bg_option ] ) && ! isset( $theme_options[ $bg_option ]['overlay-type'] ) ) {
				$bg_type = isset( $theme_options[ $bg_option ]['background-type'] ) ? $theme_options[ $bg_option ]['background-type'] : '';

				$theme_options[ $bg_option ]['overlay-type']     = 'none';
				$theme_options[ $bg_option ]['overlay-color']    = '';
				$theme_options[ $bg_option ]['overlay-opacity']  = '';
				$theme_options[ $bg_option ]['overlay-gradient'] = '';

				if ( 'image' === $bg_type ) {
					$bg_img   = isset( $theme_options[ $bg_option ]['background-image'] ) ? $theme_options[ $bg_option ]['background-image'] : '';
					$bg_color = isset( $theme_options[ $bg_option ]['background-color'] ) ? $theme_options[ $bg_option ]['background-color'] : '';

					if ( '' !== $bg_img && '' !== $bg_color && ( ! is_numeric( strpos( $bg_color, 'linear-gradient' ) ) && ! is_numeric( strpos( $bg_color, 'radial-gradient' ) ) ) ) {
						$theme_options[ $bg_option ]['overlay-type']     = 'classic';
						$theme_options[ $bg_option ]['overlay-color']    = $bg_color;
						$theme_options[ $bg_option ]['overlay-opacity']  = '';
						$theme_options[ $bg_option ]['overlay-gradient'] = '';
					}
				}
			}
		}

		$ast_resp_bg_control_options = array(
			'hba-footer-bg-obj-responsive',
			'hbb-footer-bg-obj-responsive',
			'footer-bg-obj-responsive',
			'footer-menu-bg-obj-responsive',
			'hb-footer-bg-obj-responsive',
			'hba-header-bg-obj-responsive',
			'hbb-header-bg-obj-responsive',
			'hb-header-bg-obj-responsive',
			'header-mobile-menu-bg-obj-responsive',
			'site-layout-outside-bg-obj-responsive',
			'content-bg-obj-responsive',
		);

		$post_types = Astra_Posts_Structure_Loader::get_supported_post_types();
		foreach ( $post_types as $index => $post_type ) {
			$ast_resp_bg_control_options[] = 'ast-dynamic-archive-' . esc_attr( $post_type ) . '-banner-custom-bg';
			$ast_resp_bg_control_options[] = 'ast-dynamic-single-' . esc_attr( $post_type ) . '-banner-background';
		}

		$component_limit = defined( 'ASTRA_EXT_VER' ) ? Astra_Builder_Helper::$component_limit : Astra_Builder_Helper::$num_of_header_menu;
		for ( $index = 1; $index <= $component_limit; $index++ ) {
			$_prefix                       = 'menu' . $index;
			$ast_resp_bg_control_options[] = 'header-' . $_prefix . '-bg-obj-responsive';
		}

		foreach ( $ast_resp_bg_control_options as $key => $resp_bg_option ) {
			// Desktop version.
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			if ( isset( $theme_options[ $resp_bg_option ]['desktop'] ) && is_array( $theme_options[ $resp_bg_option ]['desktop'] ) && ! isset( $theme_options[ $resp_bg_option ]['desktop']['overlay-type'] ) ) {
				// @codingStandardsIgnoreStart
				$desk_bg_type = isset( $theme_options[ $resp_bg_option ]['desktop']['background-type'] ) ? $theme_options[ $resp_bg_option ]['desktop']['background-type'] : '';
				// @codingStandardsIgnoreEnd

				$theme_options[ $resp_bg_option ]['desktop']['overlay-type']     = '';
				$theme_options[ $resp_bg_option ]['desktop']['overlay-color']    = '';
				$theme_options[ $resp_bg_option ]['desktop']['overlay-opacity']  = '';
				$theme_options[ $resp_bg_option ]['desktop']['overlay-gradient'] = '';

				if ( 'image' === $desk_bg_type ) {
					$bg_img   = isset( $theme_options[ $resp_bg_option ]['desktop']['background-image'] ) ? $theme_options[ $resp_bg_option ]['desktop']['background-image'] : '';
					$bg_color = isset( $theme_options[ $resp_bg_option ]['desktop']['background-color'] ) ? $theme_options[ $resp_bg_option ]['desktop']['background-color'] : '';

					if ( '' !== $bg_img && '' !== $bg_color && ( ! is_numeric( strpos( $bg_color, 'linear-gradient' ) ) && ! is_numeric( strpos( $bg_color, 'radial-gradient' ) ) ) ) {
						$theme_options[ $resp_bg_option ]['desktop']['overlay-type']     = 'classic';
						$theme_options[ $resp_bg_option ]['desktop']['overlay-color']    = $bg_color;
						$theme_options[ $resp_bg_option ]['desktop']['overlay-opacity']  = '';
						$theme_options[ $resp_bg_option ]['desktop']['overlay-gradient'] = '';
					}
				}
			}

			// Tablet version.
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			if ( isset( $theme_options[ $resp_bg_option ]['tablet'] ) && is_array( $theme_options[ $resp_bg_option ]['tablet'] ) && ! isset( $theme_options[ $resp_bg_option ]['tablet']['overlay-type'] ) ) {
				// @codingStandardsIgnoreStart
				$tablet_bg_type = isset( $theme_options[ $resp_bg_option ]['tablet']['background-type'] ) ? $theme_options[ $resp_bg_option ]['tablet']['background-type'] : '';
				// @codingStandardsIgnoreEnd
				$theme_options[ $resp_bg_option ]['tablet']['overlay-type']     = '';
				$theme_options[ $resp_bg_option ]['tablet']['overlay-color']    = '';
				$theme_options[ $resp_bg_option ]['tablet']['overlay-opacity']  = '';
				$theme_options[ $resp_bg_option ]['tablet']['overlay-gradient'] = '';
				if ( 'image' === $tablet_bg_type ) {
					$bg_img   = isset( $theme_options[ $resp_bg_option ]['tablet']['background-image'] ) ? $theme_options[ $resp_bg_option ]['tablet']['background-image'] : '';
					$bg_color = isset( $theme_options[ $resp_bg_option ]['tablet']['background-color'] ) ? $theme_options[ $resp_bg_option ]['tablet']['background-color'] : '';
					if ( '' !== $bg_img && '' !== $bg_color && ( ! is_numeric( strpos( $bg_color, 'linear-gradient' ) ) && ! is_numeric( strpos( $bg_color, 'radial-gradient' ) ) ) ) {
						$theme_options[ $resp_bg_option ]['tablet']['overlay-type']     = 'classic';
						$theme_options[ $resp_bg_option ]['tablet']['overlay-color']    = $bg_color;
						$theme_options[ $resp_bg_option ]['tablet']['overlay-opacity']  = '';
						$theme_options[ $resp_bg_option ]['tablet']['overlay-gradient'] = '';
					}
				}
			}


			// Mobile version.
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			if ( isset( $theme_options[ $resp_bg_option ]['mobile'] ) && is_array( $theme_options[ $resp_bg_option ]['mobile'] ) && ! isset( $theme_options[ $resp_bg_option ]['mobile']['overlay-type'] ) ) {
				// @codingStandardsIgnoreStart
				$mobile_bg_type = isset( $theme_options[ $resp_bg_option ]['mobile']['background-type'] ) ? $theme_options[ $resp_bg_option ]['mobile']['background-type'] : '';
				// @codingStandardsIgnoreEnd
				$theme_options[ $resp_bg_option ]['mobile']['overlay-type']     = '';
				$theme_options[ $resp_bg_option ]['mobile']['overlay-color']    = '';
				$theme_options[ $resp_bg_option ]['mobile']['overlay-opacity']  = '';
				$theme_options[ $resp_bg_option ]['mobile']['overlay-gradient'] = '';

				if ( 'image' === $mobile_bg_type ) {
					$bg_img   = isset( $theme_options[ $resp_bg_option ]['mobile']['background-image'] ) ? $theme_options[ $resp_bg_option ]['mobile']['background-image'] : '';
					$bg_color = isset( $theme_options[ $resp_bg_option ]['mobile']['background-color'] ) ? $theme_options[ $resp_bg_option ]['mobile']['background-color'] : '';

					if ( '' !== $bg_img && '' !== $bg_color && ( ! is_numeric( strpos( $bg_color, 'linear-gradient' ) ) && ! is_numeric( strpos( $bg_color, 'radial-gradient' ) ) ) ) {
						$theme_options[ $resp_bg_option ]['mobile']['overlay-type']     = 'classic';
						$theme_options[ $resp_bg_option ]['mobile']['overlay-color']    = $bg_color;
						$theme_options[ $resp_bg_option ]['mobile']['overlay-opacity']  = '';
						$theme_options[ $resp_bg_option ]['mobile']['overlay-gradient'] = '';
					}
				}
			}
		}

		$theme_options['v4-1-4-update-migration'] = true;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Handle backward compatibility on version 4.1.6
 *
 * @since 4.1.6
 * @return void
 */
function astra_theme_background_updater_4_1_6() {
	$theme_options = get_option( 'astra-settings', array() );
	if ( ! isset( $theme_options['list-block-vertical-spacing'] ) ) {
		$theme_options['list-block-vertical-spacing'] = false;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Set flag to avoid direct reflections on live site & to maintain backward compatibility for existing users.
 *
 * @since 4.1.7
 * @return void
 */
function astra_theme_background_updater_4_1_7() {
	$theme_options = get_option( 'astra-settings', array() );

	if ( ! isset( $theme_options['add-hr-styling-css'] ) ) {
		$theme_options['add-hr-styling-css'] = false;
		update_option( 'astra-settings', $theme_options );
	}

	if ( ! isset( $theme_options['astra-site-svg-logo-equal-height'] ) ) {
		$theme_options['astra-site-svg-logo-equal-height'] = false;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Migrating users to new container layout options
 *
 * @since 4.2.0
 * @return void
 */
function astra_theme_background_updater_4_2_0() {
	$theme_options = get_option( 'astra-settings', array() );
	if ( ! isset( $theme_options['v4-2-0-update-migration'] ) ) {

		$post_types          = Astra_Posts_Structure_Loader::get_supported_post_types();
		$theme_options       = get_option( 'astra-settings' );
		$blog_types          = array( 'single', 'archive' );
		$third_party_layouts = array( 'woocommerce', 'edd', 'lifterlms', 'lifterlms-course-lesson', 'learndash' );

		// Global.
		if ( isset( $theme_options['site-content-layout'] ) ) {
			$theme_options = astra_apply_layout_migration( 'site-content-layout', 'ast-site-content-layout', 'site-content-style', 'site-sidebar-style', $theme_options );
		}

		// Single, archive.
		foreach ( $blog_types as $index => $blog_type ) {
			foreach ( $post_types as $index => $post_type ) {
				$old_layout    = $blog_type . '-' . esc_attr( $post_type ) . '-content-layout';
				$new_layout    = $blog_type . '-' . esc_attr( $post_type ) . '-ast-content-layout';
				$content_style = $blog_type . '-' . esc_attr( $post_type ) . '-content-style';
				$sidebar_style = $blog_type . '-' . esc_attr( $post_type ) . '-sidebar-style';

				if ( isset( $theme_options[ $old_layout ] ) ) {
					$theme_options = astra_apply_layout_migration( $old_layout, $new_layout, $content_style, $sidebar_style, $theme_options );
				}
			}
		}

		// Third party existing layout migrations to new layout options.
		foreach ( $third_party_layouts as $index => $layout ) {
			$old_layout    = $layout . '-content-layout';
			$new_layout    = $layout . '-ast-content-layout';
			$content_style = $layout . '-content-style';
			$sidebar_style = $layout . '-sidebar-style';
			if ( isset( $theme_options[ $old_layout ] ) ) {
				if ( 'lifterlms' === $layout ) {

					// Lifterlms course/lesson sidebar style migration case.
					$theme_options = astra_apply_layout_migration( $old_layout, $new_layout, $content_style, 'lifterlms-course-lesson-sidebar-style', $theme_options );
				}
				$theme_options = astra_apply_layout_migration( $old_layout, $new_layout, $content_style, $sidebar_style, $theme_options );
			}
		}

		if ( ! isset( $theme_options['fullwidth_sidebar_support'] ) ) {
			$theme_options['fullwidth_sidebar_support'] = false;
		}

		$theme_options['v4-2-0-update-migration'] = true;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Handle migration from old to new layouts.
 *
 * Migration cases for old users, old layouts -> new layouts.
 *
 * @since 4.2.0
 * @param mixed $old_layout old_layout.
 * @param mixed $new_layout new_layout.
 * @param mixed $content_style content_style.
 * @param mixed $sidebar_style sidebar_style.
 * @param array $theme_options theme_options.
 * @return array $theme_options The updated theme options.
 */
function astra_apply_layout_migration( $old_layout, $new_layout, $content_style, $sidebar_style, $theme_options ) {
	switch ( astra_get_option( $old_layout ) ) {
		case 'boxed-container':
			$theme_options[ $new_layout ]    = 'normal-width-container';
			$theme_options[ $content_style ] = 'boxed';
			$theme_options[ $sidebar_style ] = 'boxed';
			break;
		case 'content-boxed-container':
			$theme_options[ $new_layout ]    = 'normal-width-container';
			$theme_options[ $content_style ] = 'boxed';
			$theme_options[ $sidebar_style ] = 'unboxed';
			break;
		case 'plain-container':
			$theme_options[ $new_layout ]    = 'normal-width-container';
			$theme_options[ $content_style ] = 'unboxed';
			$theme_options[ $sidebar_style ] = 'unboxed';
			break;
		case 'page-builder':
			$theme_options[ $new_layout ]    = 'full-width-container';
			$theme_options[ $content_style ] = 'unboxed';
			$theme_options[ $sidebar_style ] = 'unboxed';
			break;
		case 'narrow-container':
			$theme_options[ $new_layout ]    = 'narrow-width-container';
			$theme_options[ $content_style ] = 'unboxed';
			$theme_options[ $sidebar_style ] = 'unboxed';
			break;
		default:
			$theme_options[ $new_layout ]    = 'default';
			$theme_options[ $content_style ] = 'default';
			$theme_options[ $sidebar_style ] = 'default';
			break;
	}
	return $theme_options;
}

/**
 * Handle backward compatibility on version 4.2.2
 *
 * @since 4.2.2
 * @return void
 */
function astra_theme_background_updater_4_2_2() {
	$theme_options = get_option( 'astra-settings', array() );
	if ( ! isset( $theme_options['v4-2-2-core-form-btns-styling'] ) ) {
		$theme_options['v4-2-2-core-form-btns-styling'] = false;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Handle backward compatibility on version 4.6.0
 *
 * @since 4.4.0
 * @return void
 */
function astra_theme_background_updater_4_4_0() {
	$theme_options = get_option( 'astra-settings', array() );

	if ( ! isset( $theme_options['v4-4-0-backward-option'] ) ) {
		$theme_options['v4-4-0-backward-option'] = false;

		// Migrate primary button outline styles to secondary buttons.
		if ( isset( $theme_options['font-family-button'] ) ) {
			$theme_options['secondary-font-family-button'] = $theme_options['font-family-button'];
		}
		if ( isset( $theme_options['font-size-button'] ) ) {
			$theme_options['secondary-font-size-button'] = $theme_options['font-size-button'];
		}
		if ( isset( $theme_options['font-weight-button'] ) ) {
			$theme_options['secondary-font-weight-button'] = $theme_options['font-weight-button'];
		}
		if ( isset( $theme_options['font-extras-button'] ) ) {
			$theme_options['secondary-font-extras-button'] = $theme_options['font-extras-button'];
		}
		if ( isset( $theme_options['button-bg-color'] ) ) {
			$theme_options['secondary-button-bg-color'] = $theme_options['button-bg-color'];
		}
		if ( isset( $theme_options['button-bg-h-color'] ) ) {
			$theme_options['secondary-button-bg-h-color'] = $theme_options['button-bg-h-color'];
		}
		if ( isset( $theme_options['theme-button-border-group-border-color'] ) ) {
			$theme_options['secondary-theme-button-border-group-border-color'] = $theme_options['theme-button-border-group-border-color'];
		}
		if ( isset( $theme_options['theme-button-border-group-border-h-color'] ) ) {
			$theme_options['secondary-theme-button-border-group-border-h-color'] = $theme_options['theme-button-border-group-border-h-color'];
		}
		if ( isset( $theme_options['button-radius-fields'] ) ) {
			$theme_options['secondary-button-radius-fields'] = $theme_options['button-radius-fields'];
		}

		// Single - Article Featured Image visibility migration.
		$post_types = Astra_Posts_Structure_Loader::get_supported_post_types();
		foreach ( $post_types as $index => $post_type ) {
			$theme_options[ 'ast-dynamic-single-' . esc_attr( $post_type ) . '-article-featured-image-position-layout-1' ] = 'none';
			$theme_options[ 'ast-dynamic-single-' . esc_attr( $post_type ) . '-article-featured-image-position-layout-2' ] = 'none';
			$theme_options[ 'ast-dynamic-single-' . esc_attr( $post_type ) . '-article-featured-image-ratio-type' ]        = 'default';
		}

		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Handle backward compatibility on version 4.5.0.
 *
 * @since 4.5.0
 * @return void
 */
function astra_theme_background_updater_4_5_0() {
	$theme_options = get_option( 'astra-settings', array() );

	if ( ! isset( $theme_options['v4-5-0-backward-option'] ) ) {
		$theme_options['v4-5-0-backward-option'] = false;

		$palette_options = get_option( 'astra-color-palettes', Astra_Global_Palette::get_default_color_palette() );
		if ( ! isset( $palette_options['presets'] ) ) {
			$palette_options['presets'] = astra_get_palette_presets();
			update_option( 'astra-color-palettes', $palette_options );
		}

		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Handle backward compatibility on version 4.5.2.
 *
 * @since 4.5.2
 * @return void
 */
function astra_theme_background_updater_4_5_2() {
	$theme_options = get_option( 'astra-settings', array() );

	if ( ! isset( $theme_options['scndry-btn-default-padding'] ) ) {
		$theme_options['scndry-btn-default-padding'] = false;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Handle backward compatibility on version 4.6.0
 *
 * @since 4.6.0
 * @return void
 */
function astra_theme_background_updater_4_6_0() {
	$theme_options = get_option( 'astra-settings', array() );
	if ( ! isset( $theme_options['v4-6-0-backward-option'] ) ) {
		$theme_options['v4-6-0-backward-option'] = false;
		/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		$blog_post_structure = isset( $theme_options['blog-post-structure'] ) ? $theme_options['blog-post-structure'] : array( 'image', 'title-meta' );
		/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		$migrated_post_structure = array();

		if ( ! empty( $blog_post_structure ) ) {
			/** @psalm-suppress PossiblyInvalidIterator */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			foreach ( $blog_post_structure as $key ) {
				/** @psalm-suppress PossiblyInvalidIterator */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
				if ( 'title-meta' === $key ) {
					$migrated_post_structure[] = 'title';
					$migrated_post_structure[] = 'title-meta';
				}
				if ( 'image' === $key ) {
					$migrated_post_structure[] = 'image';
				}
			}

			$migrated_post_structure[] = 'excerpt';
			$migrated_post_structure[] = 'read-more';

			$theme_options['blog-post-structure'] = $migrated_post_structure;
		}

		if ( defined( 'ASTRA_EXT_VER' ) ) {
			$theme_options['ast-sub-section-author-box-border-width']  = isset( $theme_options['author-box-border-width'] ) ? $theme_options['author-box-border-width'] : array(
				'top'    => '',
				'right'  => '',
				'bottom' => '',
				'left'   => '',
			);
			$theme_options['ast-sub-section-author-box-border-radius'] = isset( $theme_options['author-box-border-radius'] ) ? $theme_options['author-box-border-radius'] : array(
				'top'    => '',
				'right'  => '',
				'bottom' => '',
				'left'   => '',
			);
			$theme_options['ast-sub-section-author-box-border-color']  = isset( $theme_options['author-box-border-color'] ) ? $theme_options['author-box-border-color'] : '';

			if ( isset( $theme_options['single-post-inside-spacing'] ) ) {
				$theme_options['ast-sub-section-author-box-padding'] = $theme_options['single-post-inside-spacing'];
			}
			if ( isset( $theme_options['font-family-post-meta'] ) ) {
				$theme_options['font-family-post-read-more'] = $theme_options['font-family-post-meta'];
			}
			if ( isset( $theme_options['font-extras-post-meta'] ) ) {
				$theme_options['font-extras-post-read-more'] = $theme_options['font-extras-post-meta'];
			}
		}

		if ( isset( $theme_options['single-post-inside-spacing'] ) ) {
			$theme_options['ast-sub-section-related-posts-padding'] = $theme_options['single-post-inside-spacing'];
		}

		$theme_options['single-content-images-shadow'] = false;
		$theme_options['ast-font-style-update']        = false;
		update_option( 'astra-settings', $theme_options );
	}
	$docs_legacy_data = get_option( 'astra_docs_data', array() );
	if ( ! empty( $docs_legacy_data ) ) {
		delete_option( 'astra_docs_data' );
	}
}

/**
 * Handle backward compatibility on version 4.6.2.
 *
 * @since 4.6.2
 * @return void
 */
function astra_theme_background_updater_4_6_2() {
	$theme_options = get_option( 'astra-settings', array() );

	// Unset "featured image" for pages structure.
	if ( ! isset( $theme_options['v4-6-2-backward-option'] ) ) {
		$theme_options['v4-6-2-backward-option'] = false;

		$page_banner_layout      = isset( $theme_options['ast-dynamic-single-page-layout'] ) ? $theme_options['ast-dynamic-single-page-layout'] : 'layout-1';
		$page_structure          = isset( $theme_options['ast-dynamic-single-page-structure'] ) ? $theme_options['ast-dynamic-single-page-structure'] : array( 'ast-dynamic-single-page-image', 'ast-dynamic-single-page-title' );
		$layout_1_image_position = isset( $theme_options['ast-dynamic-single-page-article-featured-image-position-layout-1'] ) ? $theme_options['ast-dynamic-single-page-article-featured-image-position-layout-1'] : 'behind';

		$migrated_page_structure = array();

		if ( 'layout-1' === $page_banner_layout && 'none' === $layout_1_image_position && ! empty( $page_structure ) ) {
			foreach ( $page_structure as $key ) {
				if ( 'ast-dynamic-single-page-image' !== $key ) {
					$migrated_page_structure[] = $key;
				}
			}
			$theme_options['ast-dynamic-single-page-structure'] = $migrated_page_structure;
		}

		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Handle backward compatibility on version 4.6.4.
 *
 * @since 4.6.4
 * @return void
 */
function astra_theme_background_updater_4_6_4() {
	$theme_options = get_option( 'astra-settings', array() );

	if ( ! isset( $theme_options['btn-stylings-upgrade'] ) ) {
		$theme_options['btn-stylings-upgrade'] = false;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Handle backward compatibility for Elementor Pro heading's margin.
 *
 * @since 4.6.5
 * @return void
 */
function astra_theme_background_updater_4_6_5() {
	$theme_options = get_option( 'astra-settings', array() );
	if ( ! isset( $theme_options['elementor-headings-style'] ) ) {
		$theme_options['elementor-headings-style'] = defined( 'ELEMENTOR_PRO_VERSION' ) ? true : false;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Handle backward compatibility for Elementor Loop block post div container padding.
 *
 * @since 4.6.6
 * @return void
 */
function astra_theme_background_updater_4_6_6() {
	$theme_options = get_option( 'astra-settings', array() );
	if ( ! isset( $theme_options['elementor-container-padding-style'] ) ) {
		$theme_options['elementor-container-padding-style'] = defined( 'ELEMENTOR_PRO_VERSION' ) ? true : false;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Handle backward compatibility for Starter template library preview line height cases.
 *
 * @since 4.6.11
 * @return void
 */

function astra_theme_background_updater_4_6_11() {
	$theme_options = get_option( 'astra-settings', array() );

	if ( isset( $theme_options['global-headings-line-height-update'] ) ) {
		return;
	}

	$headers_fonts = array(
		'h1' => '1.4',
		'h2' => '1.3',
		'h3' => '1.3',
		'h4' => '1.2',
		'h5' => '1.2',
		'h6' => '1.25',
	);

	foreach ( $headers_fonts as $header_tag => $header_font_value ) {

		if ( empty( $theme_options[ 'font-extras-' . $header_tag ]['line-height'] ) ) {
			$theme_options[ 'font-extras-' . $header_tag ]['line-height'] = $header_font_value;
			if ( empty( $theme_options[ 'font-extras-' . $header_tag ]['line-height-unit'] ) ) {
				$theme_options[ 'font-extras-' . $header_tag ]['line-height-unit'] = 'em';
			}
		}
	}

	$theme_options['global-headings-line-height-update'] = true;

	update_option( 'astra-settings', $theme_options );

}

/**
 * Handle backward compatibility for heading `clear:both` css in single posts and pages.
 *
 * @since 4.6.12
 * @return void
 */
function astra_theme_background_updater_4_6_12() {
	$theme_options = get_option( 'astra-settings', array() );

	if ( ! isset( $theme_options['single_posts_pages_heading_clear_none'] ) ) {
		$theme_options['single_posts_pages_heading_clear_none'] = false;
		update_option( 'astra-settings', $theme_options );
	}

	if ( ! isset( $theme_options['elementor-btn-styling'] ) ) {
		$theme_options['elementor-btn-styling'] = defined( 'ELEMENTOR_VERSION' ) ? true : false;
		update_option( 'astra-settings', $theme_options );
	}

	if ( ! isset( $theme_options['remove_single_posts_navigation_mobile_device_padding'] ) ) {
		$theme_options['remove_single_posts_navigation_mobile_device_padding'] = true;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Handle backward compatibility for following pointers.
 *
 * 1. unit less line-height support.
 * 2. H5 font size case.
 *
 * @since 4.6.14
 * @return void
 */
function astra_theme_background_updater_4_6_14() {
	$theme_options = get_option( 'astra-settings', array() );

	if ( ! isset( $theme_options['enable-4-6-14-compatibility'] ) ) {
		$theme_options['enable-4-6-14-compatibility'] = false;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Handle backward compatibility for following cases.
 *
 * 1. Making edd default option enable by default.
 * 2. Handle backward compatibility for Heading font size fix.
 *
 * @since 4.7.0
 * @return void
 */
function astra_theme_background_updater_4_7_0() {
	$theme_options = get_option( 'astra-settings', array() );

	if ( class_exists( 'Easy_Digital_Downloads' ) && ! isset( $theme_options['can-update-edd-featured-image-default'] ) ) {
		$theme_options['can-update-edd-featured-image-default'] = false;
		update_option( 'astra-settings', $theme_options );
	}

	if ( ! isset( $theme_options['heading-widget-font-size'] ) ) {
		$theme_options['heading-widget-font-size'] = false;
		update_option( 'astra-settings', $theme_options );
	}
}


/**
 * Handle backward compatibility for version 4.7.1
 *
 * @since 4.7.1
 * @return void
 */
function astra_theme_background_updater_4_7_1() {
	$theme_options = get_option( 'astra-settings', array() );

	// Setting same background color for above and below transparent headers as on transparent primary header.
	if ( isset( $theme_options['transparent-header-bg-color-responsive'] ) ) {
		if ( ! isset( $theme_options['hba-transparent-header-bg-color-responsive'] ) ) {
			$theme_options['hba-transparent-header-bg-color-responsive'] = $theme_options['transparent-header-bg-color-responsive'];
		}
		if ( ! isset( $theme_options['hbb-transparent-header-bg-color-responsive'] ) ) {
			$theme_options['hbb-transparent-header-bg-color-responsive'] = $theme_options['transparent-header-bg-color-responsive'];
		}
		update_option( 'astra-settings', $theme_options );
	}
}
