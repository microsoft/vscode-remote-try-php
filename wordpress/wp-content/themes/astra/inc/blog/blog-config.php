<?php
/**
 * Blog Config File
 * Common Functions for Blog and Single Blog
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Prepare markup for taxonomies.
 *
 * @param string $control_tax Taxonomy subcontrol name.
 * @param int    $loop_count Meta loop counter to decide separator appearance.
 * @param string $separator Separator.
 * @param string $badge_style For taxonomies as badge styles.
 * @param string $html_tag HTML tag.
 *
 * @return string $output Taxonomy output.
 */
function astra_get_dynamic_taxonomy( $control_tax, $loop_count, $separator, $badge_style = '', $html_tag = 'p' ) {

	$tax_type = astra_get_option( $control_tax );
	$post_id  = get_the_ID();

	if ( ! $post_id ) {
		return '';
	}

	$terms = get_the_terms( $post_id, $tax_type );
	/** @psalm-suppress RedundantCondition */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
	if ( $terms && ! empty( $terms ) && ! is_wp_error( $terms ) ) {
		/** @psalm-suppress RedundantCondition */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

		$term_links = array();

		/** @psalm-suppress PossibleRawObjectIteration */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		foreach ( $terms as $term ) {
			/** @psalm-suppress PossibleRawObjectIteration */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			$term_link = get_term_link( $term->slug, $tax_type );

			// If there was an error, continue to the next term.
			if ( is_wp_error( $term_link ) ) {
				continue;
			}

			$tax_badge_selector = '';
			if ( '' !== $badge_style ) {
				$tax_badge_selector = 'badge' === $badge_style ? 'ast-button ast-badge-tax' : 'ast-underline-text';
			}

			/** @psalm-suppress PossiblyInvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$term_links[] = '<a href="' . esc_url( $term_link ) . '" class="' . esc_attr( $tax_badge_selector ) . '">' . esc_html( $term->name ) . '</a>';
			/** @psalm-suppress PossiblyInvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		}

		$join_separator = 'badge' === $badge_style ? ' ' : ', ';
		$all_terms      = join( $join_separator, $term_links );
		$output_str     = '<' . esc_attr( $html_tag ) . ' class="ast-terms-link">' . $all_terms . '</' . esc_attr( $html_tag ) . '>';

		return ( 1 != $loop_count ) ? ' ' . $separator . ' ' . $output_str : $output_str;
	}

	return '';
}

/**
 * Function to get Author ID.
 *
 * @since 4.6.0
 * @return mixed $author_id Author ID.
 */
function astra_get_author_id() {
	global $post;
	if ( isset( $post->post_author ) ) {
		$author_id = $post->post_author;
	} elseif ( is_callable( 'get_the_author_meta' ) ) {
		$author_id = get_the_author_meta( 'ID' );
	} else {
		$author_id = 1;
	}
	return $author_id;
}

/**
 * Function to get Author Avatar.
 *
 * @since 4.6.0
 * @param string $get_for Get for.
 * @return mixed $avatar Author Avatar.
 */
function astra_author_avatar( $get_for = 'single-post' ) {
	$avatar = '';
	if ( is_singular() ) {
		if ( 'single-post' === $get_for && astra_get_option( 'ast-dynamic-single-' . strval( get_post_type() ) . '-author-avatar', false ) ) {
			$avatar_image_size = astra_get_option( 'ast-dynamic-single-' . strval( get_post_type() ) . '-author-avatar-size', 30 );
			$avatar            = '<span class="ast-author-avatar">' . strval( get_avatar( astra_get_author_id(), $avatar_image_size ) ) . '</span>';
		} elseif ( 'related-post' === $get_for && astra_get_option( 'related-posts-author-avatar', false ) ) {
			$avatar_image_size = astra_get_option( 'related-posts-author-avatar-size', 30 );
			$avatar            = '<span class="ast-author-avatar">' . strval( get_avatar( astra_get_author_id(), $avatar_image_size ) ) . '</span>';
		} else {
			$avatar = '';
		}
	}
	return $avatar;
}

/**
 * Common Functions for Blog and Single Blog
 *
 * @return  post meta
 */
if ( ! function_exists( 'astra_get_post_meta' ) ) {

	/**
	 * Post meta
	 *
	 * @param  array  $post_meta Post meta.
	 * @param  string $separator Separator.
	 * @param  string $render_by Render by Single|Related Posts|Blog.
	 * @return string            post meta markup.
	 */
	function astra_get_post_meta( $post_meta, $separator = '/', $render_by = '' ) {

		$output_str = '';
		$loop_count = 1;

		if ( is_singular() ) {
			if ( 'single-post' === $render_by ) {
				$separator = 'none' === astra_get_option( 'ast-dynamic-single-' . strval( get_post_type() ) . '-metadata-separator', '/' ) ? '&nbsp' : astra_get_option( 'ast-dynamic-single-' . strval( get_post_type() ) . '-metadata-separator', '/' );
			} elseif ( 'related-posts' === $render_by ) {
				$separator = 'none' === $separator ? '&nbsp' : $separator;
			}
		} else {
			$divider_type = astra_get_option( 'blog-post-meta-divider-type' );
			if ( 'none' !== $divider_type ) {
				$separator = $divider_type;
			} else {
				$separator = '&nbsp';
			}
		}

		$separator = apply_filters( 'astra_post_meta_separator', $separator );

		foreach ( $post_meta as $meta_value ) {

			switch ( $meta_value ) {

				case 'author':
					$output_str .= ( 1 != $loop_count && '' != $output_str ) ? ' ' . $separator . ' ' : '';
					/** @psalm-suppress InvalidOperand */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
					$astra_post_author_html = '' . astra_post_author();
					/** @psalm-suppress InvalidOperand */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
					if ( is_singular() ) {
						if ( 'single-post' === $render_by ) {
							$author_prefix_label = astra_get_option( 'ast-dynamic-single-' . strval( get_post_type() ) . '-author-prefix-label', astra_default_strings( 'string-blog-meta-author-by', false ) );
							$output_str         .= astra_author_avatar() . esc_html( $author_prefix_label ) . $astra_post_author_html;
						} elseif ( 'related-posts' === $render_by ) {
							$author_prefix_label = astra_get_option( 'related-posts-author-prefix-label', astra_default_strings( 'string-blog-meta-author-by', false ) );
							$output_str         .= astra_author_avatar( 'related-post' ) . esc_html( $author_prefix_label ) . $astra_post_author_html;
						} else {
							$output_str .= esc_html( astra_default_strings( 'string-blog-meta-author-by', false ) ) . $astra_post_author_html;
						}
					} else {
						/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
						if ( defined( 'ASTRA_EXT_VER' ) && Astra_Ext_Extension::is_active( 'blog-pro' ) ) {
							$author_avatar = astra_get_option( 'blog-meta-author-avatar' );
							if ( $author_avatar ) {
								$get_author_id = get_the_author_meta( 'ID' );
								/** @psalm-suppress ArgumentTypeCoercion */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
								$get_author_gravatar = get_avatar_url( $get_author_id, array( 'size' => astra_get_option( 'blog-meta-author-avatar-size', 25 ) ) );
									/** @psalm-suppress PossiblyFalseOperand */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
								$output_str .= '<img class=' . esc_attr( 'ast-author-image' ) . ' src="' . $get_author_gravatar . '" alt="' . get_the_title() . '" />';
									/** @psalm-suppress PossiblyFalseOperand */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
							}
						}
						$output_str .= esc_html( astra_get_option( 'blog-meta-author-avatar-prefix-label' ) ) . $astra_post_author_html;
					}
					break;

				case 'date':
					$output_str .= ( 1 != $loop_count && '' != $output_str ) ? ' ' . $separator . ' ' : '';
					$get_for     = 'related-posts' === $render_by ? 'related-post' : 'single-post';
					$output_str .= astra_post_date( $get_for );
					break;

				case 'category':
					$category = astra_post_categories( 'post_categories', 'blog-meta-category-style', false );
					if ( '' != $category ) {
						$output_str .= ( 1 != $loop_count && '' != $output_str ) ? ' ' . $separator . ' ' : '';
						$output_str .= $category;
					}
					break;

				case 'tag':
					$tags = astra_post_tags( 'post_tags', 'blog-meta-tag-style', false );
					if ( '' != $tags ) {
						$output_str .= ( 1 != $loop_count && '' != $output_str ) ? ' ' . $separator . ' ' : '';
						$output_str .= $tags;
					}
					break;

				case 'comments':
					$comment = astra_post_comments();
					if ( '' != $comment ) {
						$output_str .= ( 1 != $loop_count && '' != $output_str ) ? ' ' . $separator . ' ' : '';
						$output_str .= $comment;
					}
					break;
				default:
					$output_str = apply_filters( 'astra_meta_case_' . $meta_value, $output_str, $loop_count, $separator );

			}

			if ( strpos( $meta_value, '-taxonomy' ) !== false ) {
				$output_str .= astra_get_dynamic_taxonomy( $meta_value, $loop_count, $separator, astra_get_option( $meta_value . '-style', '' ), 'span' );
			}

			$loop_count ++;
		}

		return $output_str;
	}
}

/**
 * Get post format as per new configurations set in customizer.
 *
 * @param string $get_for Get for.
 * @return string HTML markup for date span.
 * @since 4.1.0
 */
function astra_get_dynamic_post_format( $get_for = 'single-post' ) {
	$is_singular = is_singular() ? true : false;

	if ( 'related-post' === $get_for ) {
		$date_format_option = astra_get_option( 'related-posts-date-format', '' );
		$date_type          = astra_get_option( 'related-posts-meta-date-type', 'published' );
		$date_format        = apply_filters( 'astra_related_post_date_format', ( '' === $date_format_option ) ? get_option( 'date_format' ) : $date_format_option );
	} else {
		$post_type          = strval( get_post_type() );
		$date_format_option = $is_singular ? astra_get_option( 'ast-dynamic-single-' . esc_attr( $post_type ) . '-date-format', '' ) : astra_get_option( 'blog-meta-date-format', '' );
		$date_type          = $is_singular ? astra_get_option( 'ast-dynamic-single-' . esc_attr( $post_type ) . '-meta-date-type', 'published' ) : astra_get_option( 'blog-meta-date-type', 'published' );
		$date_format        = apply_filters( 'astra_post_date_format', ( '' === $date_format_option ) ? get_option( 'date_format' ) : $date_format_option );
	}

	$published_date = strval( get_the_date( $date_format ) );
	$modified_date  = strval( get_the_modified_date( $date_format ) );

	if ( 'updated' === $date_type ) {
		$class    = 'updated';
		$itemprop = 'dateModified';
		$date     = sprintf(
			esc_html( '%s' ),
			$modified_date
		);
	} else {
		$class    = 'published';
		$itemprop = 'datePublished';
		$date     = sprintf(
			esc_html( '%s' ),
			$published_date
		);
	}

	return sprintf( '<span class="%1$s" itemprop="%2$s"> %3$s </span>', $class, $itemprop, $date );
}

	/**
	 * Get category List.
	 *
	 * @since 4.6.0
	 * @param  string $filter_name Filter name.
	 * @param  string $style_type_slug Style slug.
	 * @param  bool   $post_meta Post meta.
	 * @return mixed Markup.
	 */
function astra_get_category_list( $filter_name, $style_type_slug, $post_meta ) {
	$style_type_class = '';
	$separator        = ', ';
	$categories_list  = '';

	$style_type       = astra_get_option( $style_type_slug );
	$separator        = 'badge' === $style_type ? ' ' : $separator;
	$style_type_class = ' ' . $style_type;
	/* translators: used between list items, there is a space after the comma */
	$get_category_html = get_the_category_list( apply_filters( 'astra_' . $filter_name, $separator ) );
	if ( $get_category_html ) {
		if ( 'badge' === $style_type ) {
			/** @psalm-suppress PossiblyInvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$categories_list = str_replace( '<a', '<a class="ast-button"', $get_category_html );
		} else {
			$categories_list = $get_category_html;
		}
	}

	$post_tax_class = $post_meta ? 'ast-blog-single-element ' : '';

	if ( $categories_list ) {
		return '<span class="' . $post_tax_class . 'ast-taxonomy-container cat-links' . $style_type_class . '">' . $categories_list . '</span>';
	} else {
		return '';
	}
}

	/**
	 * Get tag List.
	 *
	 * @since 4.6.0
	 * @param  string $filter_name Filter name.
	 * @param string $style_type_slug style type slug.
	 * @param  bool   $post_meta Post meta.
	 * @return mixed Markup.
	 */
function astra_get_tag_list( $filter_name, $style_type_slug, $post_meta ) {
	$style_type_class = '';
	$separator        = ', ';
	$tags_list        = '';

	$style_type       = astra_get_option( $style_type_slug );
	$separator        = 'badge' === $style_type ? ' ' : $separator;
	$style_type_class = ' ' . $style_type;

	/* translators: used between list items, there is a space after the comma */
	$tags_list_html = get_the_tag_list( '', apply_filters( 'astra_' . $filter_name, $separator ) );

	if ( $tags_list_html ) {
		if ( 'badge' === $style_type ) {
			/** @psalm-suppress PossiblyInvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$tags_list = str_replace( '<a', '<a class="ast-button"', $tags_list_html );
		} else {
			$tags_list = $tags_list_html;
		}
	}

	$post_tax_class = $post_meta ? 'ast-blog-single-element ' : '';

	if ( $tags_list ) {
		return '<span class="' . $post_tax_class . 'ast-taxonomy-container tags-links' . $style_type_class . '">' . $tags_list . '</span>';
	} else {
		return '';
	}
}

/**
 * Function to get Date of Post
 *
 * @since 1.0.0
 * @return html
 */
if ( ! function_exists( 'astra_post_date' ) ) {

	/**
	 * Function to get Date of Post
	 *
	 * @param string $get_for Get for single/related post/etc.
	 * @return string Markup.
	 */
	function astra_post_date( $get_for = 'single-post' ) {
		$output  = '';
		$output .= '<span class="posted-on">';
		$output .= astra_get_dynamic_post_format( $get_for );
		$output .= '</span>';
		return apply_filters( 'astra_post_date', $output );
	}
}

/**
 * Function to get Author name.
 *
 * @return mixed $author_name Author name.
 * @since 4.0.0
 */
function astra_post_author_name() {
	$author_name    = '';
	$get_the_author = get_the_author();
	if ( empty( $get_the_author ) ) {
		/** @psalm-suppress InvalidGlobal */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		global $post;
		/** @psalm-suppress InvalidGlobal */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		if ( is_object( $post ) && isset( $post->post_author ) ) {
			$user_id = $post->post_author;
			/** @psalm-suppress InvalidGlobal */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			global $authordata;
				/** @psalm-suppress InvalidGlobal */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$author_data = '';
			if ( ! $authordata ) {
				$author_data = get_userdata( $user_id );
			}

			$author_name = esc_attr( ! empty( $author_data ) ? $author_data->display_name : '' );
		}
	} else {
		$author_name = esc_attr( get_the_author() );
	}

	return $author_name;
}

/**
 * Function to get Author of Post
 *
 * @since 1.0.0
 * @return html
 */
if ( ! function_exists( 'astra_post_author' ) ) {

	/**
	 * Function to get Author of Post
	 *
	 * @param  string $output_filter Filter string.
	 * @return html                Markup.
	 */
	function astra_post_author( $output_filter = '' ) {

		$author_id = astra_get_author_id();

		ob_start();

		echo '<span ';
			echo astra_attr(
				'post-meta-author',
				array(
					'class' => 'posted-by vcard author',
				)
			);
		echo '>';
			// Translators: Author Name. ?>
			<a title="<?php printf( esc_attr__( 'View all posts by %1$s', 'astra' ), esc_attr( strval( get_the_author() ) ) ); ?>"
				href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>" rel="author"
				<?php
					echo astra_attr(
						'author-url',
						array(
							'class' => 'url fn n',
						)
					);
				?>
				>
				<span
				<?php
					echo astra_attr(
						'author-name',
						array(
							'class' => 'author-name',
						)
					);
				?>
				>
				<?php
					/** @psalm-suppress PossiblyNullArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
					echo wp_kses_post( astra_post_author_name() );
				?>
			</span>
			</a>
		</span>

		<?php

		$output = ob_get_clean();

		return apply_filters( 'astra_post_author', $output, $output_filter );
	}
}

/**
 * Function to get Read More Link of Post
 *
 * @since 1.0.0
 * @return html
 */
if ( ! function_exists( 'astra_post_link' ) ) {

	/**
	 * Function to get Read More Link of Post
	 *
	 * @param  string $output_filter Filter string.
	 * @return html                Markup.
	 */
	function astra_post_link( $output_filter = '' ) {

		$enabled = apply_filters( 'astra_post_link_enabled', '__return_true' );
		if ( ( is_admin() && ! wp_doing_ajax() ) || ! $enabled ) {
			return $output_filter;
		}

		$more_label        = Astra_Dynamic_CSS::astra_4_6_0_compatibility() ? esc_html__( 'Read Post »', 'astra' ) : esc_html__( 'Read More »', 'astra' );
		$read_more_text    = apply_filters( 'astra_post_read_more', $more_label );
		$read_more_classes = apply_filters( 'astra_post_read_more_class', array() );

		$post_link = sprintf(
			esc_html( '%s' ),
			'<a class="' . esc_attr( implode( ' ', $read_more_classes ) ) . '" href="' . esc_url( get_permalink() ) . '"> ' . the_title( '<span class="screen-reader-text">', '</span>', false ) . ' ' . $read_more_text . '</a>'
		);
		$output    = '<p class="ast-blog-single-element ast-read-more-container read-more"> ' . $post_link . '</p>';

		echo wp_kses_post( apply_filters( 'astra_post_link', $output, $output_filter ) );
	}
}

/**
 * Function to get Number of Comments of Post
 *
 * @since 1.0.0
 * @return html
 */
if ( ! function_exists( 'astra_post_comments' ) ) {

	/**
	 * Function to get Number of Comments of Post
	 *
	 * @param  string $output_filter Output filter.
	 * @return html                Markup.
	 */
	function astra_post_comments( $output_filter = '' ) {

		$output = '';

		ob_start();
		if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			?>
			<span class="comments-link">
				<?php
				/**
				 * Get Comment Link
				 *
				 * @see astra_default_strings()
				 */
				comments_popup_link( astra_default_strings( 'string-blog-meta-leave-a-comment', false ), astra_default_strings( 'string-blog-meta-one-comment', false ), astra_default_strings( 'string-blog-meta-multiple-comment', false ) );
				?>
			</span>

			<?php
		}

		$output = ob_get_clean();

		return apply_filters( 'astra_post_comments', $output, $output_filter );
	}
}

/**
 * Function to get Tags applied of Post
 *
 * @since 1.0.0
 * @return mixed
 */
if ( ! function_exists( 'astra_post_tags' ) ) {

	/**
	 * Function to get Tags applied of Post
	 *
	 * @param  string $filter_name Filter name.
	 * @param  string $style_type Style type slug.
	 * @param  bool   $post_meta Post meta.
	 * @return mixed Markup.
	 */
	function astra_post_tags( $filter_name, $style_type, $post_meta ) {
		return apply_filters( 'astra_' . $filter_name, astra_get_tag_list( $filter_name . '_separator', $style_type, $post_meta ) );
	}
}

/**
 * Function to get Categories of Post
 *
 * @since 1.0.0
 * @return mixed
 */
if ( ! function_exists( 'astra_post_categories' ) ) {

	/**
	 * Function to get Categories applied of Post
	 *
	 * @param  string $filter_name Filter name.
	 * @param  string $style_type Style type slug.
	 * @param  bool   $post_meta Post meta.
	 * @return mixed Markup.
	 */
	function astra_post_categories( $filter_name, $style_type, $post_meta ) {
		return apply_filters( 'astra_' . $filter_name, astra_get_category_list( $filter_name . '_separator', $style_type, $post_meta ) );
	}
}
/**
 * Display classes for primary div
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'astra_blog_layout_class' ) ) {

	/**
	 * Layout class
	 *
	 * @param  string $class Class.
	 */
	function astra_blog_layout_class( $class = '' ) {

		// Separates classes with a single space, collates classes for body element.
		echo 'class="' . esc_attr( join( ' ', astra_get_blog_layout_class( $class ) ) ) . '"';
	}
}

/**
 * Retrieve the classes for the body element as an array.
 *
 * @since 1.0.0
 * @param string|array $class One or more classes to add to the class list.
 * @return array Array of classes.
 */
if ( ! function_exists( 'astra_get_blog_layout_class' ) ) {

	/**
	 * Retrieve the classes for the body element as an array.
	 *
	 * @param string $class Class.
	 */
	function astra_get_blog_layout_class( $class = '' ) {

		// array of class names.
		$classes = array();

		$post_format = get_post_format();
		if ( $post_format ) {
			$post_format = 'standard';
		}

		$classes[] = 'ast-post-format-' . $post_format;

		if ( ! has_post_thumbnail() || ! wp_get_attachment_image_src( get_post_thumbnail_id() ) ) {
			switch ( $post_format ) {

				case 'aside':
								$classes[] = 'ast-no-thumb';
					break;

				case 'image':
								$has_image = astra_get_first_image_from_post();
					if ( empty( $has_image ) || is_single() ) {
						$classes[] = 'ast-no-thumb';
					}
					break;

				case 'video':
								$post_featured_data = astra_get_video_from_post( get_the_ID() );
					if ( empty( $post_featured_data ) ) {
						$classes[] = 'ast-no-thumb';
					}
					break;

				case 'quote':
								$classes[] = 'ast-no-thumb';
					break;

				case 'link':
								$classes[] = 'ast-no-thumb';
					break;

				case 'gallery':
								$post_featured_data = get_post_gallery();
					if ( empty( $post_featured_data ) || is_single() ) {
						$classes[] = 'ast-no-thumb';
					}
					break;

				case 'audio':
								$has_audio = astra_get_audios_from_post( get_the_ID() );
					if ( empty( $has_audio ) || is_single() ) {
						$classes[] = 'ast-no-thumb';
					} else {
						$classes[] = 'ast-embeded-audio';
					}
					break;

				case 'standard':
				default:
					if ( ! has_post_thumbnail() || ! wp_get_attachment_image_src( get_post_thumbnail_id() ) ) {
						$classes[] = 'ast-no-thumb';
					}
					break;
			}
		}

		if ( ! in_array( 'ast-no-thumb', $classes ) && ! in_array( 'image', astra_get_option( 'blog-post-structure', array() ) ) ) {
			$classes[] = 'ast-no-thumb';
		}

		if ( ! empty( $class ) ) {
			if ( ! is_array( $class ) ) {
				$class = preg_split( '#\s+#', $class );
			}
			$classes = array_merge( $classes, $class );
		} else {
			// Ensure that we always coerce class to being an array.
			$class = array();
		}

		/**
		 * Filter primary div class names
		 */
		$classes = apply_filters( 'astra_blog_layout_class', $classes, $class );

		$classes = array_map( 'sanitize_html_class', $classes );

		return array_unique( $classes );
	}
}

/**
 * Function to get Content Read More Link of Post
 *
 * @since 1.2.7
 * @return mixed
 */
if ( ! function_exists( 'astra_the_content_more_link' ) ) {

	/**
	 * Filters the Read More link text.
	 *
	 * @param  string $more_link_element Read More link element.
	 * @param  string $more_link_text Read More text.
	 * @return mixed                Markup.
	 */
	function astra_the_content_more_link( $more_link_element = '', $more_link_text = '' ) {

		$enabled = apply_filters( 'astra_the_content_more_link_enabled', '__return_true' );
		if ( ( is_admin() && ! wp_doing_ajax() ) || ! $enabled ) {
			return $more_link_element;
		}

		$more_link_text    = apply_filters( 'astra_the_content_more_string', __( 'Read More &raquo;', 'astra' ) );
		$read_more_classes = apply_filters( 'astra_the_content_more_link_class', array() );

		$post_link = sprintf(
			esc_html( '%s' ),
			'<a class="' . esc_attr( implode( ' ', $read_more_classes ) ) . '" href="' . esc_url( get_permalink() ) . '"> ' . the_title( '<span class="screen-reader-text">', '</span>', false ) . $more_link_text . '</a>'
		);

		$more_link_element = ' &hellip;<p class="ast-the-content-more-link"> ' . $post_link . '</p>';

		return apply_filters( 'astra_the_content_more_link', $more_link_element, $more_link_text );
	}
}
add_filter( 'the_content_more_link', 'astra_the_content_more_link', 10, 2 );
