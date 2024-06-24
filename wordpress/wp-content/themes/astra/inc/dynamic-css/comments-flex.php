<?php
/**
 * Comments - Dynamic CSS
 *
 * @package astra-builder
 * @since 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_filter( 'astra_dynamic_theme_css', 'astra_comments_css', 11 );

/**
 * Comments - Dynamic CSS
 *
 * @param  string $dynamic_css          Astra Dynamic CSS.
 * @return String Generated dynamic CSS for Pagination.
 *
 * @since 3.2.0
 */
function astra_comments_css( $dynamic_css ) {

	if ( astra_check_current_post_comment_enabled() || 0 < get_comments_number() ) {

		$body_font_size              = astra_get_option( 'font-size-body' );
		$theme_color                 = astra_get_option( 'theme-color' );
		$link_color                  = astra_get_option( 'link-color', $theme_color );
		$is_site_rtl                 = is_rtl();
		$reply_title_space_threshold = Astra_Dynamic_CSS::astra_4_4_0_compatibility() ? 1.3 : 1.66666;

		if ( is_array( $body_font_size ) ) {
			$body_font_size_desktop = ( isset( $body_font_size['desktop'] ) && '' != $body_font_size['desktop'] ) ? $body_font_size['desktop'] : 15;
		} else {
			$body_font_size_desktop = ( '' != $body_font_size ) ? $body_font_size : 15;
		}

		$desktop_comment_global = array(
			'.comment-reply-title'                         => array(
				'font-size' => astra_get_font_css_value( (int) $body_font_size_desktop * $reply_title_space_threshold ),
			),
			// Single Post Meta.
			'.ast-comment-meta'                            => array(
				'line-height' => '1.666666667',
				'color'       => esc_attr( $link_color ),
				'font-size'   => astra_get_font_css_value( (int) $body_font_size_desktop * 0.8571428571 ),
			),
			'.ast-comment-list #cancel-comment-reply-link' => array(
				'font-size' => astra_responsive_font( $body_font_size, 'desktop' ),
			),
		);
		$dynamic_css .= astra_parse_css( $desktop_comment_global );

		$update_customizer_strctural_defaults = ( true === astra_check_is_structural_setup() );
		$padding_comment_title                = $update_customizer_strctural_defaults ? '1em 0 0' : '2em 0';
		$padding_ast_comment                  = $update_customizer_strctural_defaults ? '0' : '1em 0';
		$padding_ast_comment_list             = $update_customizer_strctural_defaults ? '0' : '0.5em';
		$border_color                         = astra_get_option( 'border-color' );
		$blog_improvements                    = Astra_Dynamic_CSS::astra_4_6_0_compatibility();
		$comments_title_css                   = $blog_improvements ? 'font-weight: 600; padding-bottom: 1em;' : 'font-weight: normal;';

		$single_post_comment_css = '.comments-title {
            padding: ' . esc_attr( $padding_comment_title ) . ';
          }

          .comments-title {
            word-wrap: break-word;
			' . $comments_title_css . '
          }

          .ast-comment-list {
            margin: 0;
            word-wrap: break-word;
            padding-bottom: ' . esc_attr( $padding_ast_comment_list ) . ';
            list-style: none;
          }
          .ast-comment-list li {
            list-style: none;
          }

          .ast-comment-list .ast-comment-edit-reply-wrap {
            -js-display: flex;
            display: flex;
            justify-content: flex-end;
          }

          .ast-comment-list .comment-awaiting-moderation {
            margin-bottom: 0;
          }

          .ast-comment {
            padding: ' . esc_attr( $padding_ast_comment ) . ' ;
          }
          .ast-comment-info img {
            border-radius: 50%;
          }
          .ast-comment-cite-wrap cite {
            font-style: normal;
          }

          .comment-reply-title {
            font-weight: ' . esc_attr( Astra_Dynamic_CSS::astra_4_4_0_compatibility() ? '600' : 'normal' ) . ';
            line-height: 1.65;
          }

          .ast-comment-meta {
            margin-bottom: 0.5em;
          }

          .comments-area .comment-form-comment {
            width: 100%;
            border: none;
            margin: 0;
            padding: 0;
          }
          .comments-area .comment-notes,
          .comments-area .comment-textarea,
          .comments-area .form-allowed-tags {
            margin-bottom: 1.5em;
          }
          .comments-area .form-submit {
            margin-bottom: 0;
          }
          .comments-area textarea#comment,
          .comments-area .ast-comment-formwrap input[type="text"] {
            width: 100%;
            border-radius: 0;
            vertical-align: middle;
            margin-bottom: 10px;
          }
          .comments-area .no-comments {
            margin-top: 0.5em;
            margin-bottom: 0.5em;
          }
          .comments-area p.logged-in-as {
            margin-bottom: 1em;
          }
          .ast-separate-container .ast-comment-list {
            padding-bottom: 0;
          }

          .ast-separate-container .ast-comment-list li.depth-1 .children li, .ast-narrow-container .ast-comment-list li.depth-1 .children li {
            padding-bottom: 0;
            padding-top: 0;
            margin-bottom: 0;
          }

          .ast-separate-container .ast-comment-list .comment-respond {
            padding-top: 0;
            padding-bottom: 1em;
            background-color: transparent;
          }

		  .ast-comment-list .comment .comment-respond {
			padding-bottom: 2em;
			border-bottom: none;
		  }

          .ast-separate-container .ast-comment-list .bypostauthor, .ast-narrow-container .ast-comment-list .bypostauthor {
            padding: 2em;
            margin-bottom: 1em;
          }

          .ast-separate-container .ast-comment-list .bypostauthor li, .ast-narrow-container .ast-comment-list .bypostauthor li {
            background: transparent;
            margin-bottom: 0;
            padding: 0 0 0 2em;
          }

          .comment-content a {
            word-wrap: break-word;
          }

          .comment-form-legend {
            margin-bottom: unset;
            padding: 0 0.5em;
          }';

		if ( Astra_Dynamic_CSS::astra_4_6_0_compatibility() ) {
			$single_post_comment_css .= '
				.comment-reply-title {
					padding-top: 0;
					margin-bottom: 1em;
				}
				.ast-comment {
					padding-top: 2.5em;
					padding-bottom: 2.5em;
					border-top: 1px solid var(--ast-single-post-border, var(--ast-border-color));
				}
				.ast-separate-container .ast-comment-list .comment + .comment,
				.ast-narrow-container .ast-comment-list .comment + .comment {
					padding-top: 0;
					padding-bottom: 0;
				}
			';

			$is_boxed       = astra_is_content_style_boxed();
			$content_layout = astra_get_content_layout();

			$post_with_boxed_layout = ( 'plain-container' === $content_layout || 'narrow-container' === $content_layout ) && $is_boxed ? true : false;
			if ( $post_with_boxed_layout && 'inside' !== astra_get_option( 'comments-box-placement', '' ) ) {
				$single_post_comment_css .= '
					.ast-separate-container .ast-comment-list li.depth-1, .ast-narrow-container .ast-comment-list li.depth-1 {
						padding-left: 2.5em;
						padding-right: 2.5em;
					}
				';
			}
		} else {
			$single_post_comment_css .= '
				.ast-separate-container .ast-comment-list .pingback p {
					margin-bottom: 0;
				}
				.ast-separate-container .ast-comment-list li.depth-1, .ast-narrow-container .ast-comment-list li.depth-1 {
					padding: 3em;
				}
				.ast-comment-list > .comment:last-child .ast-comment {
					border: none;
				}
				.ast-separate-container .ast-comment-list .comment .comment-respond,
				.ast-narrow-container .ast-comment-list .comment .comment-respond {
					padding-bottom: 0;
				}
				.ast-separate-container .comment .comment-respond {
					margin-top: 2em;
				}
				.ast-separate-container .ast-comment-list li.depth-1 .ast-comment,
				.ast-separate-container .ast-comment-list li.depth-2 .ast-comment {
					border-bottom: 0;
				}
			';
		}

		if ( false === $update_customizer_strctural_defaults ) {
			$single_post_comment_css .= '.ast-separate-container .ast-comment-list li.depth-1 {
					padding: 4em 6.67em;
					margin-bottom: 2em;
				}
				@media (max-width: 1200px) {
					.ast-separate-container .ast-comment-list li.depth-1 {
						padding: 3em 3.34em;
					}
				}
				.ast-separate-container .comment-respond {
					background-color: #fff;
					padding: 4em 6.67em;
					border-bottom: 0;
				}
				@media (max-width: 1200px) {
					.ast-separate-container .comment-respond {
						padding: 3em 2.34em;
					}
				}
				.ast-separate-container .comments-title {
					background-color: #fff;
					padding: 1.2em 3.99em 0;
				}
			';
		} else {
			$single_post_comment_css .= '
				.ast-plain-container .ast-comment, .ast-page-builder-template .ast-comment {
					padding: 2em 0;
				}
				.page.ast-page-builder-template .comments-area {
					margin-top: 2em;
				}
			';
		}

		$content_layout   = astra_get_content_layout();
		$is_boxed         = astra_is_content_style_boxed();
		$is_sidebar_boxed = astra_is_sidebar_style_boxed();
		$content_layout   = astra_apply_boxed_layouts( $content_layout, $is_boxed, $is_sidebar_boxed );
		if ( 'page-builder' == $content_layout || 'plain-container' == $content_layout ) {
			$single_post_comment_css .= '
				.ast-page-builder-template .comment-respond {
					border-top: none;
					padding-bottom: 2em;
				}
			';
			if ( ! Astra_Dynamic_CSS::astra_4_6_0_compatibility() ) {
				$single_post_comment_css .= '
					.ast-plain-container .comment-reply-title {
						padding-top: 1em;
					}
				';
			}
		}

		if ( $is_site_rtl ) {
			$single_post_comment_css .= '
            .ast-comment-list .children {
                margin-right: 2em;
            }

            @media (max-width: 992px) {
                .ast-comment-list .children {
                    margin-right: 1em;
                }
            }

            .ast-comment-list #cancel-comment-reply-link {
                white-space: nowrap;
                font-size: 13px;
                font-weight: normal;
                margin-right: 1em;
            }

            .ast-comment-meta {
                justify-content: left;
                padding: 0 3.4em 1.333em;
            }

            .ast-comment-time .timendate,
                .ast-comment-time .reply {
                margin-left: 0.5em;
            }
            .comments-area #wp-comment-cookies-consent {
                margin-left: 10px;
            }
            .ast-page-builder-template .comments-area {
                padding-right: 20px;
                padding-left: 20px;
                margin-top: 0;
                margin-bottom: 2em;
            }
            .ast-separate-container .ast-comment-list .bypostauthor .bypostauthor {
                background: transparent;
                margin-bottom: 0;
                padding-left: 0;
                padding-bottom: 0;
                padding-top: 0;
            }';
		} else {
			$single_post_comment_css .= '
            .ast-comment-list .children {
                margin-left: 2em;
            }

            @media (max-width: 992px) {
                .ast-comment-list .children {
                    margin-left: 1em;
                }
            }

            .ast-comment-list #cancel-comment-reply-link {
                white-space: nowrap;
                font-size: 13px;
				font-weight: normal;
                margin-left: 1em;
            }

            .ast-comment-info {
                display: flex;
                position: relative;
            }
            .ast-comment-meta {
                justify-content: right;
                padding: 0 3.4em 1.60em;
            }
            .comments-area #wp-comment-cookies-consent {
                margin-right: 10px;
            }
            .ast-page-builder-template .comments-area {
                padding-left: 20px;
                padding-right: 20px;
                margin-top: 0;
                margin-bottom: 2em;
            }
            .ast-separate-container .ast-comment-list .bypostauthor .bypostauthor {
                background: transparent;
                margin-bottom: 0;
                padding-right: 0;
                padding-bottom: 0;
                padding-top: 0;
            }';
		}

		$dynamic_css .= Astra_Enqueue_Scripts::trim_css( $single_post_comment_css );

		$static_layout_css_min_comment = array(
			'.ast-separate-container .ast-comment-list li .comment-respond' => array(
				'padding-left'  => '2.66666em',
				'padding-right' => '2.66666em',
			),
		);

		$dynamic_css                 .= astra_parse_css( $static_layout_css_min_comment, astra_get_tablet_breakpoint( '', '1' ) );
		$global_button_comment_mobile = array(
			'.ast-separate-container .ast-comment-list li.depth-1' => array(
				'padding'       => Astra_Dynamic_CSS::astra_4_6_0_compatibility() ? '' : '1.5em 1em',
				'margin-bottom' => Astra_Dynamic_CSS::astra_4_4_0_compatibility() ? '0' : '1.5em',
			),
			'.ast-separate-container .ast-comment-list .bypostauthor' => array(
				'padding' => '.5em',
			),
			'.ast-separate-container .comment-respond'     => array(
				'padding' => Astra_Dynamic_CSS::astra_4_6_0_compatibility() ? '' : '1.5em 1em',
			),
			// Single Post Meta.
			'.ast-comment-meta'                            => array(
				'font-size' => ! empty( $body_font_size['mobile'] ) ? astra_get_font_css_value( (int) $body_font_size['mobile'] * 0.8571428571, 'px', 'mobile' ) : '',
			),
			'.comment-reply-title'                         => array(
				'font-size' => ! empty( $body_font_size['mobile'] ) ? astra_get_font_css_value( (int) $body_font_size['mobile'] * $reply_title_space_threshold, 'px', 'mobile' ) : '',
			),
			'.ast-comment-list #cancel-comment-reply-link' => array(
				'font-size' => astra_responsive_font( $body_font_size, 'mobile' ),
			),
			'.ast-separate-container .ast-comment-list .bypostauthor li' => array(
				'padding' => '0 0 0 .5em',
			),
		);

		if ( $is_site_rtl ) {
			$global_button_comment_mobile['.ast-comment-list .children'] = array(
				'margin-right' => '0.66666em',
			);
		} else {
			$global_button_comment_mobile['.ast-comment-list .children'] = array(
				'margin-left' => '0.66666em',
			);
		}

		$dynamic_css .= astra_parse_css( $global_button_comment_mobile, '', astra_get_mobile_breakpoint() );

		$global_button_comment_tablet = array(
			'.ast-comment-avatar-wrap img'                 => array(
				'max-width' => '2.5em',
			),
			'.comments-area'                               => array(
				'margin-top' => '1.5em',
			),
			'.ast-comment-meta'                            => array(
				'padding'   => '0 1.8888em 1.3333em',
				'font-size' => ! empty( $body_font_size['tablet'] ) ? astra_get_font_css_value( (int) $body_font_size['tablet'] * 0.8571428571, 'px', 'tablet' ) : '',
			),
			'.ast-separate-container .ast-comment-list li.depth-1' => array(
				'padding' => Astra_Dynamic_CSS::astra_4_6_0_compatibility() ? '' : '1.5em 2.14em',
			),
			'.ast-separate-container .comment-respond'     => array(
				'padding' => Astra_Dynamic_CSS::astra_4_6_0_compatibility() ? '' : '2em 2.14em',
			),
			'.comment-reply-title'                         => array(
				'font-size' => ! empty( $body_font_size['tablet'] ) ? astra_get_font_css_value( (int) $body_font_size['tablet'] * $reply_title_space_threshold, 'px', 'tablet' ) : '',
			),
			'.ast-comment-list #cancel-comment-reply-link' => array(
				'font-size' => astra_responsive_font( $body_font_size, 'tablet' ),
			),
			'.ast-comment-meta'                            => array(
				'padding' => '0 1.8888em 1.3333em',
			),
		);

		if ( false === $update_customizer_strctural_defaults ) {
			$global_button_comment_tablet['.ast-separate-container .comments-title'] = array(
				'padding' => '1.43em 1.48em',
			);
		}

		if ( $is_site_rtl ) {
			$global_button_comment_tablet['.ast-comment-avatar-wrap'] = array(
				'margin-left' => '0.5em',
			);
		} else {
			$global_button_comment_tablet['.ast-comment-avatar-wrap'] = array(
				'margin-right' => '0.5em',
			);
		}

		if ( Astra_Dynamic_CSS::astra_4_6_0_compatibility() ) {
			$dynamic_css .= '
				.ast-comment-cite-wrap cite {
					font-weight: 600;
					font-size: 1.2em;
				}
				.ast-comment-info img {
					box-shadow: 0 0 5px 0 rgba(0,0,0,.15);
					border: 1px solid var(--ast-single-post-border, var(--ast-border-color));
				}
				.ast-comment-info {
					margin-bottom: 1em;
				}
				.logged-in span.ast-reply-link {
					margin-right: 16px;
				}
				a.comment-edit-link, a.comment-reply-link {
					font-size: 13px;
					transition: all 0.2s;
				}
				header.ast-comment-meta {
					text-transform: inherit;
				}
				.ast-page-builder-template .ast-comment-list .children {
					margin-top: 0em;
				}
				.ast-page-builder-template .ast-comment-meta {
					padding: 0 22px;
				}
				.ast-comment-content.comment p {
					margin-bottom: 16px;
				}
				.ast-comment-list .ast-comment-edit-reply-wrap {
					justify-content: flex-start;
					align-items: center;
				}
				.comment-awaiting-moderation {
					margin-top: 20px;
				}
				.entry-content ul li, .entry-content ol li {
					margin-bottom: 10px;
				}
				.comment-respond {
					padding-top: 2em;
					padding-bottom: 2em;
				}
				.ast-comment-list + .comment-respond {
					border-top: 1px solid var(--ast-single-post-border, var(--ast-border-color));
					padding-bottom: 0;
				}
				.comment .comment-reply-title {
					display: flex;
					align-items: center;
					justify-content: space-between;
				}
				@media(min-width: ' . strval( astra_get_mobile_breakpoint( '', 1 ) ) . 'px) {
					header.ast-comment-meta {
						display: flex;
						width: 100%;
						margin-bottom: 0;
						padding-bottom: 0;
						align-items: center;
					}
					a.comment-reply-link {
						padding: 1px 10px;
						display: block;
						border-radius: 3px;
						border: none;
					}
					.ast-separate-container .ast-comment-list li.depth-1, .ast-narrow-container .ast-comment-list li.depth-1 {
						margin-bottom: 0;
					}
					.ast-comment-time {
						display: flex;
						margin-left: auto;
						font-weight: 500;
					}
					section.ast-comment-content.comment {
						padding-left: 50px;
					}
					.ast-comment .comment-reply-link:hover {
						background: ' . astra_get_option( 'theme-color' ) . ';
						color: #fff;
					}
					.ast-comment .comment-edit-link:hover {
						text-decoration: underline;
					}
					svg.ast-reply-icon {
						fill: currentColor;
						margin-right: 5px;
						padding-top: 2px;
						transition: none;
					}
					.comment-reply-link:hover .ast-reply-icon {
						fill: #fff;
					}
				}
				@media(min-width: ' . strval( astra_get_tablet_breakpoint() ) . 'px) {
					.ast-comment-cite-wrap {
						margin-left: -7px;
					}
					section.ast-comment-content.comment {
						padding-left: 70px;
					}
				}
			';
			if ( 'above' === astra_get_option( 'comment-form-position' ) ) {
				$dynamic_css .= '
					.comment-respond {
						border-bottom: 1px solid var(--ast-single-post-border, var(--ast-border-color));
					}
				';
			}
			$comments_section_placement = astra_get_option( 'comments-box-placement', '' );
			if ( 'inside' === $comments_section_placement ) {
				$dynamic_css .= '
					.site-content article .comment-respond {
						padding-top: 1.5em;
					}
				';
			}
		} else {
			$dynamic_css .= '
				.ast-comment-time .timendate{
					margin-right: 0.5em;
				}
				.ast-separate-container .comment-reply-title {
					padding-top: 0;
				}
				.ast-comment-list .ast-edit-link {
					flex: 1;
				}
				.comments-area {
					border-top: 1px solid var(--ast-global-color-6);
					margin-top: 2em;
				}
				.ast-separate-container .comments-area {
					border-top: 0;
				}
			';
		}

		return $dynamic_css .= astra_parse_css( $global_button_comment_tablet, '', astra_get_tablet_breakpoint() );
	}
	return $dynamic_css;
}
