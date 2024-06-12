<?php
/**
 * Single Blog Helper Functions
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Adds custom classes to the array of body classes.
 */
if ( ! function_exists( 'astra_single_body_class' ) ) {

	/**
	 * Adds custom classes to the array of body classes.
	 *
	 * @since 1.0.0
	 * @param array $classes Classes for the body element.
	 * @return array
	 */
	function astra_single_body_class( $classes ) {

		// Blog layout.
		if ( is_single() ) {
			$classes[] = 'ast-blog-single-style-1';

			if ( 'post' != get_post_type() ) {
				$classes[] = 'ast-custom-post-type';
			}
		}

		if ( is_singular() ) {
			$classes[] = 'ast-single-post';
		}

		return $classes;
	}
}

add_filter( 'body_class', 'astra_single_body_class' );

/**
 * Adds custom classes to the array of body classes.
 */
if ( ! function_exists( 'astra_single_post_class' ) ) {

	/**
	 * Adds custom classes to the array of body classes.
	 *
	 * @since 1.0.0
	 * @param array $classes Classes for the body element.
	 * @return array
	 */
	function astra_single_post_class( $classes ) {

		// Blog layout.
		if ( is_singular() ) {

			if ( ! in_array( 'ast-related-post', $classes ) ) {
				$classes[] = 'ast-article-single';
			}

			// Remove hentry from page.
			if ( 'page' == get_post_type() ) {
				$classes = array_diff( $classes, array( 'hentry' ) );
			}
		}

		return $classes;
	}
}

add_filter( 'post_class', 'astra_single_post_class' );

/**
 * Template for comments and pingbacks.
 */
if ( ! function_exists( 'astra_theme_comment' ) ) {

	/**
	 * Template for comments and pingbacks.
	 *
	 * To override this walker in a child theme without modifying the comments template
	 * simply create your own astra_theme_comment(), and that function will be used instead.
	 *
	 * Used as a callback by wp_list_comments() for displaying the comments.
	 *
	 * @param  string $comment Comment.
	 * @param  array  $args    Comment arguments.
	 * @param  number $depth   Depth.
	 * @return mixed          Comment markup.
	 */
	function astra_theme_comment( $comment, $args, $depth ) {
		switch ( $comment->comment_type ) {

			case 'pingback':
			case 'trackback':
				// Display trackbacks differently than normal comments.
				?>
				<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
					<p><?php esc_html_e( 'Pingback:', 'astra' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', 'astra' ), '<span class="edit-link">', '</span>' ); ?></p>
				</li>
				<?php
				break;

			default:
				// Proceed with normal comments.
				global $post;
				$entry_content_class = Astra_Dynamic_CSS::astra_4_6_0_compatibility() ? ' entry-content' : '';
				?>
				<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">

					<article id="comment-<?php comment_ID(); ?>" class="ast-comment">
					<div class= 'ast-comment-info'>
						<div class='ast-comment-avatar-wrap'><?php echo get_avatar( $comment, 50 ); ?></div><!-- Remove 1px Space
						-->
								<?php
								astra_markup_open( 'ast-comment-data-wrap' );
								astra_markup_open( 'ast-comment-meta-wrap' );
								echo '<header ';
								echo astra_attr(
									'commen-meta-author',
									array(
										'class' => 'ast-comment-meta ast-row ast-comment-author vcard capitalize',
									)
								);
								echo '>';

									printf(
										astra_markup_open(
											'ast-comment-cite-wrap',
											array(
												'open'  => '<div %s>',
												'class' => 'ast-comment-cite-wrap',
											)
										) . '<cite><b class="fn">%1$s</b> %2$s</cite></div>',
										get_comment_author_link(),
										// If current post author is also comment author, make it known visually.
										( $comment->user_id === $post->post_author ) ? '<span class="ast-highlight-text ast-cmt-post-author"></span>' : ''
									);

								if ( apply_filters( 'astra_single_post_comment_time_enabled', true ) ) {
									printf(
										esc_attr(
											astra_markup_open(
												'ast-comment-time',
												array(
													'open' => '<div %s>',
													'class' => 'ast-comment-time',
												)
											)
										) . '<span  class="timendate"><a href="%1$s"><time datetime="%2$s">%3$s</time></a></span></div>',
										esc_url( get_comment_link( $comment->comment_ID ) ),
										esc_attr( get_comment_time( 'c' ) ),
										/* translators: 1: date, 2: time */
										esc_html( sprintf( __( '%1$s at %2$s', 'astra' ), get_comment_date(), get_comment_time() ) )
									);
								}

								?>
								<?php astra_markup_close( 'ast-comment-meta-wrap' ); ?>
								</header> <!-- .ast-comment-meta -->
							</div>
							<section class="ast-comment-content comment <?php echo esc_attr( $entry_content_class ); ?>">
								<?php comment_text(); ?>
								<div class="ast-comment-edit-reply-wrap">
									<?php
									if ( Astra_Dynamic_CSS::astra_4_6_0_compatibility() ) {
										comment_reply_link(
											array_merge(
												$args,
												array(
													'reply_text' => astra_default_strings( 'string-comment-reply-link', false ),
													'add_below' => 'comment',
													'depth'  => $depth,
													'max_depth' => $args['max_depth'],
													'before' => '<span class="ast-reply-link">',
													'after'  => '</span>',
												)
											)
										);
										edit_comment_link( astra_default_strings( 'string-comment-edit-link', false ), '<span class="ast-edit-link">', '</span>' );
									} else {
										edit_comment_link( astra_default_strings( 'string-comment-edit-link', false ), '<span class="ast-edit-link">', '</span>' );
										comment_reply_link(
											array_merge(
												$args,
												array(
													'reply_text' => astra_default_strings( 'string-comment-reply-link', false ),
													'add_below' => 'comment',
													'depth'  => $depth,
													'max_depth' => $args['max_depth'],
													'before' => '<span class="ast-reply-link">',
													'after'  => '</span>',
												)
											)
										);
									}
									?>
								</div>
								<?php if ( '0' == $comment->comment_approved ) : ?>
									<p class="ast-highlight-text comment-awaiting-moderation"><?php echo esc_html( astra_default_strings( 'string-comment-awaiting-moderation', false ) ); ?></p>
								<?php endif; ?>
							</section> <!-- .ast-comment-content -->
							<?php astra_markup_close( 'ast-comment-data-wrap' ); ?>
					</article><!-- #comment-## -->

				<?php
				break;
		}
	}
}

/**
 * Adjacent navigation post link attributes.
 *
 * @param string         $output   The adjacent post link.
 * @param string         $format   Link anchor format.
 * @param string         $link     Link permalink format.
 * @param WP_Post|string $post     The adjacent post. Empty string if no corresponding post exists.
 * @param string         $adjacent Whether the post is previous or next.
 *
 * @return string       Link of post URL.
 * @since 4.6.0
 */
function astra_adjacent_post_links_title( $output, $format, $link, $post, $adjacent ) {
	/** @psalm-suppress PossiblyInvalidPropertyFetch */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
	if ( ! empty( $post->post_title ) ) {
		/** @psalm-suppress PossiblyInvalidPropertyFetch */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		$output = str_replace( 'href="', 'title="' . esc_attr( $post->post_title ) . '"' . 'href="', $output );
	}
	return $output;
}

/**
 * Get Post Navigation
 */
if ( ! function_exists( 'astra_single_post_navigation_markup' ) ) {

	/**
	 * Get Post Navigation
	 *
	 * Checks post navigation, if exists return as button.
	 *
	 * @return mixed Post Navigation Buttons
	 */
	function astra_single_post_navigation_markup() {

		$single_post_navigation_enabled = apply_filters( 'astra_single_post_navigation_enabled', true );

		if ( is_single() && $single_post_navigation_enabled ) {

			$post_obj = get_post_type_object( get_post_type() );
			/** @psalm-suppress PossiblyNullPropertyFetch */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$post_singular_name = ! empty( $post_obj->labels->singular_name ) ? $post_obj->labels->singular_name : '';
			/** @psalm-suppress PossiblyNullPropertyFetch */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			$prev_text = Astra_Dynamic_CSS::astra_4_6_0_compatibility() ? '<span class="ast-post-nav">' . Astra_Builder_UI_Controller::fetch_svg_icon( 'long-arrow-alt-left' ) . ' ' . astra_default_strings( 'string-previous-text', false ) . '</span> <p> %title </p>' : sprintf(
				astra_default_strings( 'string-single-navigation-previous', false ),
				$post_singular_name
			);
			$next_text = Astra_Dynamic_CSS::astra_4_6_0_compatibility() ? '<span class="ast-post-nav">' . astra_default_strings( 'string-next-text', false ) . ' ' . Astra_Builder_UI_Controller::fetch_svg_icon( 'long-arrow-alt-right' ) . '</span> <p> %title </p>' : sprintf(
				astra_default_strings( 'string-single-navigation-next', false ),
				$post_singular_name
			);

			add_filter( 'previous_post_link', 'astra_adjacent_post_links_title', 10, 5 );
			add_filter( 'next_post_link', 'astra_adjacent_post_links_title', 10, 5 );

			/**
			 * Filter the post pagination markup
			 */
			the_post_navigation(
				apply_filters(
					'astra_single_post_navigation',
					array(
						'next_text'          => $next_text,
						'prev_text'          => $prev_text,
						'screen_reader_text' => __( 'Post navigation', 'astra' ),
					)
				)
			);

			remove_filter( 'previous_post_link', 'astra_adjacent_post_links_title', 10 );
			remove_filter( 'next_post_link', 'astra_adjacent_post_links_title', 10 );
		}
	}
}

add_action( 'astra_entry_after', 'astra_single_post_navigation_markup' );
