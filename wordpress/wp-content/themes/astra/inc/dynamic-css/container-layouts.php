<?php
/**
 * Container Layout - Dynamic CSS
 *
 * @package astra
 * @since 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Init fire to add required compatibility for divi page bulder.
 *
 * Case: In customizer-defaults update case Astra v3.8.3 we introduced some padding for stretched layout, that should not load for page builder layouts, that is why this compatibility added here.
 *
 * @param int $post_id Current post ID.
 *
 * @since 3.8.3
 */
function astra_check_any_page_builder_is_active( $post_id ) {
	$post = get_post( $post_id );

	if ( class_exists( '\Elementor\Plugin' ) ) {
		/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		$document = Elementor\Plugin::$instance->documents->get( $post_id ); // phpcs:ignore PHPCompatibility.LanguageConstructs.NewLanguageConstructs.t_ns_separatorFound
		if ( $document ) {
			$deprecated_handle = $document->is_built_with_elementor();
		} else {
			$deprecated_handle = false;
		}
		if ( ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '1.5.0', '<' ) && 'builder' === Elementor\Plugin::$instance->db->get_edit_mode( $post_id ) ) || $deprecated_handle ) { // phpcs:ignore PHPCompatibility.LanguageConstructs.NewLanguageConstructs.t_ns_separatorFound
			/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			return true;
		}
	}

	if ( defined( 'TVE_VERSION' ) && get_post_meta( $post_id, 'tcb_editor_enabled', true ) ) {
		return true;
	}

	if ( class_exists( 'FLBuilderModel' ) && apply_filters( 'fl_builder_do_render_content', true, FLBuilderModel::get_post_id() ) && get_post_meta( $post_id, '_fl_builder_enabled', true ) ) { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		return true;
	}

	$vc_active = get_post_meta( $post_id, '_wpb_vc_js_status', true );
	if ( class_exists( 'Vc_Manager' ) && ( 'true' == $vc_active || has_shortcode( $post->post_content, 'vc_row' ) ) ) {
		return true;
	}

	if ( function_exists( 'et_pb_is_pagebuilder_used' ) && et_pb_is_pagebuilder_used( $post_id ) ) {
		return true;
	}

	if ( class_exists( 'Brizy_Editor_Post' ) && class_exists( 'Brizy_Editor' ) ) {

		$brizy_post_types = Brizy_Editor::get()->supported_post_types();
		$post_type        = get_post_type( $post_id );

		if ( in_array( $post_type, $brizy_post_types ) ) {

			if ( Brizy_Editor_Post::get( $post_id )->uses_editor() ) {
				return true;
			}
		}
	}

	return false;
}

/**
 * Container Layout - Dynamic CSS.
 *
 * @since 3.3.0
 */
function astra_container_layout_css() {
	$container_layout = astra_get_content_layout();

	$page_container_css = '
		.ast-single-post-featured-section + article {
			margin-top: 2em;
		}
		.site-content .ast-single-post-featured-section img {
			width: 100%;
			overflow: hidden;
			object-fit: cover;
		}
	';

	$tab_one_max_breakpoint = astra_get_tablet_breakpoint( '', 1 );
	/** @psalm-suppress InvalidOperand */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
	$tab_one_max_breakpoint = '@media (min-width: ' . $tab_one_max_breakpoint . 'px)';
	/** @psalm-suppress InvalidOperand */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

	$current_post_type = strval( get_post_type() );
	$layout_type       = astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-layout', 'layout-1' );
	$image_position    = astra_get_option( 'ast-dynamic-single-' . $current_post_type . '-article-featured-image-position-layout-1', 'behind' );

	if ( 'layout-1' === $layout_type && 'behind' === $image_position ) {
		$page_container_css .= '
			.ast-separate-container .site-content .ast-single-post-featured-section + article {
				margin-top: -80px;
				z-index: 9;
				position: relative;
				border-radius: 4px;
			}
			' . $tab_one_max_breakpoint . ' {
				.ast-no-sidebar .site-content .ast-article-image-container--wide {
					margin-left: -120px;
					margin-right: -120px;
					max-width: unset;
					width: unset;
				}
				.ast-left-sidebar .site-content .ast-article-image-container--wide, .ast-right-sidebar .site-content .ast-article-image-container--wide {
					margin-left: -10px;
					margin-right: -10px;
				}
				.site-content .ast-article-image-container--full {
					margin-left: calc( -50vw + 50%);
					margin-right: calc( -50vw + 50%);
					max-width: 100vw;
					width: 100vw;
				}
				.ast-left-sidebar .site-content .ast-article-image-container--full,
				.ast-right-sidebar .site-content .ast-article-image-container--full {
					margin-left: -10px;
					margin-right: -10px;
					max-width: inherit;
					width: auto;
				}
			}
		';
	}

	$customizer_default_update = astra_check_is_structural_setup();
	$page_title_header_padding = ( true === $customizer_default_update ) ? '2em' : '4em';
	// Transparent Header.
	$display_title = get_post_meta( absint( astra_get_post_id() ), 'site-post-title', true );
	/** @psalm-suppress InvalidCast */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
	$tablet_breakpoint = (string) astra_get_tablet_breakpoint();
	/** @psalm-suppress InvalidCast */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

	$page_container_css .= '
		.site > .ast-single-related-posts-container {
			margin-top: 0;
		}
		@media (min-width: ' . strval( astra_get_tablet_breakpoint( '', 1 ) ) . 'px) {
			.ast-desktop .ast-container--narrow {
				max-width: var(--ast-narrow-container-width);
				margin: 0 auto;
			}
		}
	';

	if ( 'page-builder' === $container_layout ) {

		$page_container_css .= '
        .ast-page-builder-template .hentry {
            margin: 0;
          }
          .ast-page-builder-template .site-content > .ast-container {
            max-width: 100%;
            padding: 0;
          }
          .ast-page-builder-template .site .site-content #primary {
            padding: 0;
            margin: 0;
          }
          .ast-page-builder-template .no-results {
            text-align: center;
            margin: 4em auto;
          }
          .ast-page-builder-template .ast-pagination {
            padding: 2em;
          }

          .ast-page-builder-template .entry-header.ast-no-title.ast-no-thumbnail {
            margin-top: 0;
          }
          .ast-page-builder-template .entry-header.ast-header-without-markup {
            margin-top: 0;
            margin-bottom: 0;
          }

          .ast-page-builder-template .entry-header.ast-no-title.ast-no-meta {
            margin-bottom: 0;
          }
          .ast-page-builder-template.single .post-navigation {
            padding-bottom: 2em;
          }
          .ast-page-builder-template.single-post .site-content > .ast-container {
            max-width: 100%;
          }';

		$astra_blog_improvements  = Astra_Dynamic_CSS::astra_4_6_0_compatibility();
		$post_navigation_selector = $astra_blog_improvements ? ', .ast-page-builder-template .post-navigation' : '';
		if ( true === $customizer_default_update ) {
			$page_container_css .= '
				.ast-page-builder-template .entry-header {
					margin-top: ' . esc_attr( $page_title_header_padding ) . ';
					margin-left: auto;
					margin-right: auto;
				}
			';
			/** @psalm-suppress InvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			if ( 'disabled' !== $display_title && true === apply_filters( 'astra_stretched_layout_with_spacing', true ) && false === astra_check_any_page_builder_is_active( astra_get_post_id() ) ) {
				/** @psalm-suppress InvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
				$page_container_css .= '
					.ast-single-post.ast-page-builder-template .site-main > article, .woocommerce.ast-page-builder-template .site-main' . $post_navigation_selector . ' {
						padding-top: 2em;
						padding-left: 20px;
						padding-right: 20px;
					}
				';
			}
		} else {
			$page_container_css .= '
				.ast-page-builder-template .entry-header {
					margin-top: ' . esc_attr( $page_title_header_padding ) . ';
					margin-left: auto;
					margin-right: auto;
					padding-left: 20px;
					padding-right: 20px;
				}
				.single.ast-page-builder-template .entry-header {
					padding-left: 20px;
					padding-right: 20px;
				}
			';
		}

		$page_container_css .= '
			.ast-page-builder-template .ast-archive-description {
				margin: ' . esc_attr( $page_title_header_padding ) . ' auto 0;
				padding-left: 20px;
				padding-right: 20px;
			}
		';

		if ( true === $customizer_default_update ) {
			$page_container_css .= '
				.ast-page-builder-template .ast-row {
					margin-left: 0;
					margin-right: 0;
				}
				.single.ast-page-builder-template .entry-header + .entry-content,
				.single.ast-page-builder-template .ast-single-entry-banner + .site-content article .entry-content {
					margin-bottom: 2em;
				}
				@media(min-width: ' . $tablet_breakpoint . 'px) {
					.ast-page-builder-template.archive.ast-right-sidebar .ast-row article, .ast-page-builder-template.archive.ast-left-sidebar .ast-row article {
						padding-left: 0;
						padding-right: 0;
					}
				}
			';
		}

		/** @psalm-suppress InvalidScalarArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		if ( false === astra_get_option( 'improve-gb-editor-ui', true ) ) {
			/** @psalm-suppress InvalidScalarArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$page_container_css .= '.ast-page-builder-template.ast-no-sidebar .entry-content .alignwide {
                margin-left: 0;
                margin-right: 0;
            }';
		}

		return Astra_Enqueue_Scripts::trim_css( $page_container_css );
	}
	return $page_container_css;
}
