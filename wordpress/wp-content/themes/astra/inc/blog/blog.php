<?php
/**
 * Blog Helper Functions
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Adds custom classes to the array of body classes.
 */
if ( ! function_exists( 'astra_blog_body_classes' ) ) {

	/**
	 * Adds custom classes to the array of body classes.
	 *
	 * @since 1.0
	 * @param array $classes Classes for the body element.
	 * @return array
	 */
	function astra_blog_body_classes( $classes ) {

		// Adds a class of group-blog to blogs with more than 1 published author.
		if ( is_multi_author() ) {
			$classes[] = 'group-blog';
		}

		return $classes;
	}
}

add_filter( 'body_class', 'astra_blog_body_classes' );

/**
 * Adds custom classes to the array of post grid classes.
 */
if ( ! function_exists( 'astra_post_class_blog_grid' ) ) {

	/**
	 * Adds custom classes to the array of post grid classes.
	 *
	 * @since 1.0
	 * @param array $classes Classes for the post element.
	 * @return array
	 */
	function astra_post_class_blog_grid( $classes ) {
		$blog_layout = astra_get_blog_layout();
		if ( is_archive() || is_home() || is_search() ) {
			$classes[] = astra_attr( 'ast-blog-col' );
			$classes[] = 'ast-article-post';

			/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			if ( ! ( defined( 'ASTRA_EXT_VER' ) && Astra_Ext_Extension::is_active( 'blog-pro' ) ) ) {
				/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
				if ( 'blog-layout-4' === $blog_layout ) {
					$classes[] = 'remove-featured-img-padding';
				}
			}
		}

		return $classes;
	}
}

add_filter( 'post_class', 'astra_post_class_blog_grid' );

/**
 * Add Body Classes
 *
 * @param array $classes Blog Layout Class Array.
 * @since 4.6.0
 * @return array
 */
function astra_add_blog_layout_class( $classes ) {
	if ( ! is_singular() ) {
		$classes[] = 'ast-article-inner';
	}
	return $classes;
}

add_filter( 'astra_blog_layout_class', 'astra_add_blog_layout_class' );

/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
if ( ! function_exists( 'astra_blog_get_post_meta' ) ) {

	/**
	 * Prints HTML with meta information for the current post-date/time and author.
	 *
	 * @since 1.0
	 * @return mixed            Markup.
	 */
	function astra_blog_get_post_meta() {

		$enable_meta       = apply_filters( 'astra_blog_post_meta_enabled', '__return_true' );
		$post_meta         = astra_get_option( 'blog-meta' );
		$current_post_type = get_post_type();

		if ( is_array( $post_meta ) && $enable_meta ) {

			$output_str = astra_get_post_meta( $post_meta, '/', 'blog' );

			if ( ! empty( $output_str ) ) {
				echo apply_filters( 'astra_blog_post_meta', '<div class="entry-meta">' . $output_str . '</div>', $output_str ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}
	}
}

/**
 * Featured post meta.
 */
if ( ! function_exists( 'astra_blog_post_get_featured_item' ) ) {

	/**
	 * To featured image / gallery / audio / video etc. As per the post format.
	 *
	 * @since 1.0
	 * @return mixed
	 */
	function astra_blog_post_get_featured_item() {

		$post_featured_data = '';
		$post_format        = get_post_format();

		if ( has_post_thumbnail() ) {

			$post_featured_data  = '<a href="' . esc_url( get_permalink() ) . '" >';
			$post_featured_data .= get_the_post_thumbnail();
			$post_featured_data .= '</a>';

		} else {

			switch ( $post_format ) {
				case 'image':
					break;

				case 'video':
					$post_featured_data = astra_get_video_from_post( get_the_ID() );
					break;

				case 'gallery':
					$post_featured_data = get_post_gallery( get_the_ID(), false );
					if ( isset( $post_featured_data['ids'] ) ) {
						$img_ids = explode( ',', $post_featured_data['ids'] );

						$image_alt = get_post_meta( $img_ids[0], '_wp_attachment_image_alt', true );
						$image_url = wp_get_attachment_url( $img_ids[0] );

						if ( isset( $img_ids[0] ) ) {
							$post_featured_data  = '<a href="' . esc_url( get_permalink() ) . '" >';
							$post_featured_data .= '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $image_alt ) . '" >';
							$post_featured_data .= '</a>';
						}
					}
					break;

				case 'audio':
					$post_featured_data = do_shortcode( astra_get_audios_from_post( get_the_ID() ) );
					break;
			}
		}

		echo $post_featured_data; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

add_action( 'astra_blog_post_featured_format', 'astra_blog_post_get_featured_item' );


/**
 * Blog Post Thumbnail / Title & Meta Order
 */
if ( ! function_exists( 'astra_blog_post_thumbnail_and_title_order' ) ) {

	/**
	 * Blog post Thubmnail, Title & Blog Meta order
	 *
	 * @since  1.0.8
	 * @param array $remove_elements Remove unwanted sections.
	 */
	function astra_blog_post_thumbnail_and_title_order( $remove_elements = array() ) {

		$blog_post_thumb_title_order = astra_get_option( 'blog-post-structure' );

		$remove_post_element = apply_filters( 'astra_remove_post_elements', $remove_elements );

		if ( isset( $blog_post_thumb_title_order ) && isset( $remove_post_element ) ) {
			foreach ( $remove_post_element as $single ) {
				$key = array_search( $single, $blog_post_thumb_title_order );
				if ( ( $key ) !== false ) {
					unset( $blog_post_thumb_title_order[ $key ] );
				}
			}
		}

		if ( is_singular() ) {
			return astra_banner_elements_order();
		}

		if ( is_array( $blog_post_thumb_title_order ) ) {
			// Append the custom class for second element for single post.
			foreach ( $blog_post_thumb_title_order as $post_thumb_title_order ) {

				switch ( $post_thumb_title_order ) {

					// Blog Post Featured Image.
					case 'image':
						do_action( 'astra_blog_archive_featured_image_before' );
						astra_get_blog_post_thumbnail( 'archive' );
						do_action( 'astra_blog_archive_featured_image_after' );
						break;

					// Blog Categories.
					case 'category':
						do_action( 'astra_blog_archive_category_before' );
						// @codingStandardsIgnoreStart
						/**
						 * @psalm-suppress InvalidArgument
						* @psalm-suppress TooManyArguments
						 */
						echo astra_post_categories( 'astra_blog_archive_category', 'blog-category-style', true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						// @codingStandardsIgnoreEnd
						do_action( 'astra_blog_archive_category_after' );
						break;

					// Blog Tags.
					case 'tag':
						do_action( 'astra_blog_archive_tag_before' );
						// @codingStandardsIgnoreStart
						/**
						 * @psalm-suppress InvalidArgument
						* @psalm-suppress TooManyArguments
						 */
						echo astra_post_tags( 'astra_blog_archive_tag', 'blog-tag-style', true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						// @codingStandardsIgnoreEnd
						do_action( 'astra_blog_archive_tag_after' );
						break;

					// Blog Post Title.
					case 'title':
						do_action( 'astra_blog_archive_title_before' );
						astra_get_blog_post_title();
						do_action( 'astra_blog_archive_title_after' );
						break;

					// Blog Post Title and Blog Post Meta.
					case 'title-meta':
						do_action( 'astra_blog_archive_title_meta_before' );
						astra_get_blog_post_title_meta();
						do_action( 'astra_blog_archive_title_meta_after' );
						break;
					// Blog Post excerpt.
					case 'excerpt':
						do_action( 'astra_blog_archive_excerpt_before' );
						astra_the_excerpt();
						do_action( 'astra_blog_archive_excerpt_after' );
						break;

					// Blog Post read more.
					case 'read-more':
						do_action( 'astra_blog_archive_read_more_before' );
						astra_post_link();
						do_action( 'astra_blog_archive_read_more_after' );
						break;

				}
			}
		}
	}
}

/**
 * Blog / Single Post Thumbnail
 */
if ( ! function_exists( 'astra_get_blog_post_thumbnail' ) ) {

	/**
	 * Blog post Thumbnail
	 *
	 * @param string $type Type of post.
	 * @since  1.0.8
	 */
	function astra_get_blog_post_thumbnail( $type = 'archive' ) {

		if ( 'archive' === $type ) {
			// Blog Post Featured Image.
			astra_get_post_thumbnail( '<div class="ast-blog-featured-section post-thumb ast-blog-single-element">', '</div>' );
		} elseif ( 'single' === $type ) {
			// Single Post Featured Image.
			astra_get_post_thumbnail();
		}
	}
}

/**
 * Blog Post Title & Meta Order
 */
if ( ! function_exists( 'astra_get_blog_post_title_meta' ) ) {

	/**
	 * Blog post Thumbnail
	 *
	 * @since  1.0.8
	 */
	function astra_get_blog_post_title_meta() {

		// Blog Post Title and Blog Post Meta.
		do_action( 'astra_archive_entry_header_before' );
		?>
		<header class="entry-header ast-blog-single-element ast-blog-meta-container">
			<?php

				do_action( 'astra_archive_post_meta_before' );

				astra_blog_get_post_meta();

				do_action( 'astra_archive_post_meta_after' );

			?>
		</header><!-- .entry-header -->
		<?php

		do_action( 'astra_archive_entry_header_after' );
	}
}

/**
 * Blog post title
 *
 * @since  4.6.0
 */
function astra_get_blog_post_title() {

	do_action( 'astra_archive_post_title_before' );

	/* translators: 1: Current post link, 2: Current post id */
	astra_the_post_title(
		sprintf(
			'<h2 class="entry-title ast-blog-single-element" %2$s><a href="%1$s" rel="bookmark">',
			esc_url( get_permalink() ),
			astra_attr(
				'article-title-blog',
				array(
					'class' => '',
				)
			)
		),
		'</a></h2>',
		get_the_id()
	);

	do_action( 'astra_archive_post_title_after' );

}

/**
 * Get audio files from post content
 */
if ( ! function_exists( 'astra_get_audios_from_post' ) ) {

	/**
	 * Get audio files from post content
	 *
	 * @param  number $post_id Post id.
	 * @return mixed          Iframe.
	 */
	function astra_get_audios_from_post( $post_id ) {

		// for audio post type - grab.
		$post    = get_post( $post_id );
		$content = do_shortcode( apply_filters( 'the_content', $post->post_content ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		$embeds  = apply_filters( 'astra_get_post_audio', get_media_embedded_in_content( $content ) );

		if ( empty( $embeds ) ) {
			return '';
		}

		// check what is the first embed containg video tag, youtube or vimeo.
		foreach ( $embeds as $embed ) {
			if ( strpos( $embed, 'audio' ) ) {
				return '<span class="ast-post-audio-wrapper">' . $embed . '</span>';
			}
		}
	}
}

/**
 * Get first image from post content
 */
if ( ! function_exists( 'astra_get_video_from_post' ) ) {

	/**
	 * Get first image from post content
	 *
	 * @since 1.0
	 * @param  number $post_id Post id.
	 * @return mixed
	 */
	function astra_get_video_from_post( $post_id ) {

		$post    = get_post( $post_id );
		$content = do_shortcode( apply_filters( 'the_content', $post->post_content ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		$embeds  = apply_filters( 'astra_get_post_audio', get_media_embedded_in_content( $content ) );

		if ( empty( $embeds ) ) {
			return '';
		}

		// check what is the first embed containg video tag, youtube or vimeo.
		foreach ( $embeds as $embed ) {
			if ( strpos( $embed, 'video' ) || strpos( $embed, 'youtube' ) || strpos( $embed, 'vimeo' ) ) {
				return $embed;
			}
		}
	}
}

/**
 * Get last word of string to get meta-key of custom post structure.
 *
 * @since 4.0.0
 * @param string $string from this get last word.
 * @return string $last_word result.
 */
function astra_get_last_meta_word( $string ) {
	$string    = explode( '-', $string );
	$last_word = array_pop( $string );
	return $last_word;
}

/**
 * Get the current archive description.
 *
 * @since 4.0.0
 * @param string $post_type post type.
 * @return string $description Description for archive.
 */
function astra_get_archive_description( $post_type ) {
	$description = '';

	if ( defined( 'SURECART_PLUGIN_FILE' ) && is_page() && get_the_ID() === absint( get_option( 'surecart_shop_page_id' ) ) ) {
		$description = astra_get_option( 'ast-dynamic-archive-sc_product-custom-description', '' );
		return $description;
	}

	if ( is_search() ) {
		if ( have_posts() ) {
			$description = astra_get_option( 'section-search-page-title-found-custom-description' );
		} else {
			$description = astra_get_option( 'section-search-page-title-not-found-custom-description' );
		}
		return $description;
	} else {
		$get_archive_description = get_the_archive_description();
		$get_author_meta         = trim( get_the_author_meta( 'description' ) );

		if ( ! empty( $get_archive_description ) ) {
			$description = get_the_archive_description();
		}
		if ( is_author() ) {
			if ( ! empty( $get_author_meta ) ) {
				$description = get_the_author_meta( 'description' );
			}
		}
		if ( empty( $description ) && ! have_posts() ) {
			$description = esc_html( astra_default_strings( 'string-content-nothing-found-message', false ) );
		}
	}
	if ( is_post_type_archive( $post_type ) ) {
		$description = astra_get_option( 'ast-dynamic-archive-' . $post_type . '-custom-description', '' );
	}
	if ( 'post' === $post_type && ( ( is_front_page() && is_home() ) || is_home() ) ) {
		$description = astra_get_option( 'ast-dynamic-archive-post-custom-description', '' );
	}
	return $description;
}

/**
 * Custom single post Title & Meta order display.
 *
 * @since 4.0.0
 * @param array $structure archive or single post structure.
 * @return mixed
 */
function astra_banner_elements_order( $structure = array() ) {

	if ( true === apply_filters( 'astra_remove_entry_header_content', false ) ) {
		return;
	}

	// If search page.
	$post_type = 'post';
	if ( ! is_search() ) {
		/** @psalm-suppress InvalidGlobal */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		global $post;
		/** @psalm-suppress InvalidGlobal */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		if ( is_null( $post ) ) {
			return;
		}
		$post_type = strval( $post->post_type );
	}

	// If 404 page.
	if ( is_404() ) {
		$post_type = '';
	}

	// If Blog / Latest Post page is active then looping required structural order.
	if ( ( ! is_front_page() && is_home() ) && false === astra_get_option( 'ast-dynamic-archive-post-banner-on-blog', false ) ) {
		return astra_blog_post_thumbnail_and_title_order();
	}

	$prefix      = 'archive';
	$structure   = empty( $structure ) ? astra_get_option( 'ast-dynamic-' . $prefix . '-' . $post_type . '-structure', array( 'ast-dynamic-' . $prefix . '-' . $post_type . '-title', 'ast-dynamic-' . $prefix . '-' . $post_type . '-description' ) ) : $structure;
	$layout_type = astra_get_option( 'ast-dynamic-' . $prefix . '-' . $post_type . '-layout', 'layout-1' );

	if ( is_singular() ) {
		$prefix    = 'single';
		$structure = astra_get_option( 'ast-dynamic-' . $prefix . '-' . $post_type . '-structure', array( 'ast-dynamic-' . $prefix . '-' . $post_type . '-title', 'ast-dynamic-' . $prefix . '-' . $post_type . '-meta' ) );
		if ( 'page' === $post_type ) {
			$structure = astra_get_option( 'ast-dynamic-single-page-structure', array( 'ast-dynamic-single-page-image', 'ast-dynamic-single-page-title' ) );
		}
		$layout_type = astra_get_option( 'ast-dynamic-' . $prefix . '-' . $post_type . '-layout', 'layout-1' );
	}

	do_action( 'astra_single_post_banner_before' );
	$post_type = apply_filters( 'astra_banner_elements_post_type', $post_type );
	$prefix    = apply_filters( 'astra_banner_elements_prefix', $prefix );

	foreach ( apply_filters( 'astra_banner_elements_structure', $structure ) as $metaval ) {
		$meta_key = $prefix . '-' . astra_get_last_meta_word( $metaval );
		switch ( $meta_key ) {
			case 'single-breadcrumb':
				do_action( 'astra_single_post_banner_breadcrumb_before' );
				echo astra_get_breadcrumb(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				do_action( 'astra_single_post_banner_breadcrumb_after' );
				break;

			case 'single-title':
				do_action( 'astra_single_post_banner_title_before' );
				if ( 'page' === $post_type ) {
					astra_the_title(
						'<h1 class="entry-title" ' . astra_attr(
							'article-title-content-page',
							array(
								'class' => '',
							)
						) . '>',
						'</h1>'
					);
				} else {
					astra_the_title(
						'<h1 class="entry-title" ' . astra_attr(
							'article-title-blog-single',
							array(
								'class' => '',
							)
						) . '>',
						'</h1>'
					);
				}
				do_action( 'astra_single_post_banner_title_after' );
				break;

			case 'single-excerpt':
				do_action( 'astra_single_post_banner_excerpt_before' );
				/** @psalm-suppress PossiblyUndefinedVariable */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
				echo ! empty( $post ) && ! empty( $post->ID ) ? '<p>' . get_the_excerpt( $post->ID ) . '</p>' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				/** @psalm-suppress PossiblyUndefinedVariable */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
				do_action( 'astra_single_post_banner_excerpt_after' );
				break;

			case 'single-meta':
				do_action( 'astra_single_post_banner_meta_before' );
				$post_meta = astra_get_option( 'ast-dynamic-single-' . $post_type . '-metadata', array( 'comments', 'author', 'date' ) );
				$output    = '';
				if ( ! empty( $post_meta ) ) {
					$output_str = astra_get_post_meta( $post_meta, '/', 'single-post' );
					if ( ! empty( $output_str ) ) {
						$output = apply_filters( 'astra_single_post_meta', '<div class="entry-meta">' . $output_str . '</div>' ); // WPCS: XSS OK.
					}
				}
				echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				do_action( 'astra_single_post_banner_meta_after' );
				break;

			case 'single-image':
				if ( 'disabled' === astra_get_option_meta( 'ast-featured-img' ) ) {
					break;
				}

				$featured_background = astra_get_option( 'ast-dynamic-single-' . $post_type . '-featured-as-background', false );

				if ( 'layout-1' === $layout_type ) {
					$article_featured_image_position = astra_get_option( 'ast-dynamic-single-' . $post_type . '-article-featured-image-position-layout-1', 'behind' );
				} else {
					$article_featured_image_position = astra_get_option( 'ast-dynamic-single-' . $post_type . '-article-featured-image-position-layout-2', 'none' );
				}

				if ( 'none' !== $article_featured_image_position ) {
					break;
				}

				if ( ( 'layout-2' === $layout_type && false === $featured_background ) || 'layout-1' === $layout_type ) {
					do_action( 'astra_blog_single_featured_image_before' );
					astra_get_blog_post_thumbnail( 'single' );
					do_action( 'astra_blog_single_featured_image_after' );
				}
				break;

			case 'single-taxonomy':
			case 'single-str-taxonomy':
				do_action( 'astra_single_post_banner_taxonomies_before' );
				echo astra_get_dynamic_taxonomy( 'ast-dynamic-single-' . $post_type . '-structural-taxonomy', 1, astra_get_option( 'ast-dynamic-single-' . $post_type . '-metadata-separator' ), astra_get_option( 'ast-dynamic-single-' . $post_type . '-structural-taxonomy-style', '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				do_action( 'astra_single_post_banner_taxonomies_after' );
				break;

			case 'archive-title':
				do_action( 'astra_blog_archive_title_before' );
				add_filter( 'get_the_archive_title_prefix', '__return_empty_string' );
				if ( 'layout-1' === $layout_type ) {
					astra_the_post_title( '<h1 class="page-title ast-archive-title">', '</h1>', 0, true );
				} else {
					astra_the_post_title( '<h1>', '</h1>', 0, true );
					do_action( 'astra_after_archive_title' );
				}
				remove_filter( 'get_the_archive_title_prefix', '__return_empty_string' );
				do_action( 'astra_blog_archive_title_after' );
				break;

			case 'archive-breadcrumb':
				if ( ! is_author() ) {
					do_action( 'astra_blog_archive_breadcrumb_before' );
					echo astra_get_breadcrumb(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					do_action( 'astra_blog_archive_breadcrumb_after' );
				}
				break;

			case 'archive-description':
				do_action( 'astra_blog_archive_description_before' );
				echo wp_kses_post( wpautop( astra_get_archive_description( $post_type ) ) );
				do_action( 'astra_blog_archive_description_after' );
				break;
		}
	}

	do_action( 'astra_single_post_banner_after' );
}

/**
 * Blog Post Per Page
 *
 * @since 4.6.0
 * @param WP_Query $query Query.
 */
function astra_blog_post_per_page( $query ) {

	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( ! ( is_home() || is_archive() || is_search() ) ) {
		return;
	}

	if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
		return;
	}

	$exclusions = apply_filters(
		'astra_blog_post_per_page_exclusions',
		array(
			'bbpress_single',
			'courses_archive',
			'product',
		)
	);

	if ( in_array( $query->get( 'post_type' ), $exclusions, true ) ) {
		return;
	}

	$limit = apply_filters( 'astra_blog_post_per_page', astra_get_blog_posts_per_page() );
	$query->set( 'posts_per_page', $limit );
}

add_action( 'parse_tax_query', 'astra_blog_post_per_page' );

/**
 * Add Blog Layout Class
 *
 * @param array $classes Body Class Array.
 * @since 4.6.0
 * @return array
 */
function astra_primary_class_blog_layout( $classes ) {

	if ( is_archive() && function_exists( 'is_bbpress' ) &&
		( get_post_type() === 'forum' || get_post_type() === 'topic' || get_post_type() === 'reply' || get_query_var( 'post_type' ) === 'forum' || bbp_is_topic_tag() || bbp_is_topic_tag_edit() || is_bbpress() )
	) {
		return $classes;
	}

	// Apply grid class to archive page.
	if ( ( is_home() ) || is_archive() || is_search() ) {

		$blog_layout = astra_get_blog_layout();

		/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		if ( ! ( defined( 'ASTRA_EXT_VER' ) && Astra_Ext_Extension::is_active( 'blog-pro' ) ) ) {
			/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			// If a old pro user has used blog-layout-1 to 3 and disabled astra addon then moved layout to 'blog-layout-4'.
			if ( 'blog-layout-1' == $blog_layout || 'blog-layout-2' === $blog_layout || 'blog-layout-3' === $blog_layout ) {
				$blog_layout = 'blog-layout-4';
			}

			if ( 'blog-layout-4' == $blog_layout || 'blog-layout-5' === $blog_layout || 'blog-layout-6' === $blog_layout ) {
				$classes[] = 'ast-grid-3';

			}
		}

		if ( 'blog-layout-4' == $blog_layout || 'blog-layout-5' === $blog_layout || 'blog-layout-6' === $blog_layout ) {
			$classes[] = 'ast-' . esc_attr( $blog_layout ) . '-grid';

		}

		$classes = apply_filters( 'astra_primary_class_blog_grid', $classes );
	}

	return $classes;
}

add_filter( 'astra_primary_class', 'astra_primary_class_blog_layout' );



/**
 * Blog Layout Customization
 *
 * @since 4.6.0
 * @return void
 */
function astra_blog_layout_customization() {
	$blog_layout       = astra_get_blog_layout();
	$blog_layout_slugs = array( 'blog-layout-4', 'blog-layout-5', 'blog-layout-6' );

	if ( in_array( $blog_layout, $blog_layout_slugs ) ) {
		remove_action( 'astra_entry_content_blog', 'astra_entry_content_blog_template' );
		add_action( 'astra_entry_content_blog', 'astra_blog_layout_template' );
	}
}

add_action( 'wp_head', 'astra_blog_layout_customization' );

/**
 * Blog Layout Template Markup
 *
 * @since 4.6.0
 * @return void
 */
function astra_blog_layout_template() {
	get_template_part( 'template-parts/blog/' . esc_attr( astra_get_blog_layout() ) );
}

/**
 * Blog Custom excerpt length.
 *
 * @since 4.6.0
 * @param int $length Length.
 * @return int
 */
function astra_custom_excerpt_length( $length ) {
	$blog_layout = astra_get_blog_layout();
	return 'blog-layout-4' === $blog_layout ? 20 : $length;
}
add_filter( 'excerpt_length', 'astra_custom_excerpt_length', 1 );

/**
 * Remove link from featured image for layout 6
 *
 * @since 4.6.0
 * @param string $content Content.
 * @return mixed
 */
function astra_remove_link_from_featured_image( $content = '' ) {
	$blog_layout = astra_get_blog_layout();

	if ( is_archive() || is_home() || is_search() ) {
		if ( 'blog-layout-6' === $blog_layout ) {
			add_filter( 'astra_blog_post_featured_image_link_after', '__return_false' );
			add_filter( 'astra_blog_post_featured_image_link_before', '__return_false' );
		}
	}
	return $content;

}
add_filter( 'wp', 'astra_remove_link_from_featured_image' );
