<?php
/**
 * Spectra AI Block.
 *
 * @package Gutenberg_Templates
 */

namespace Gutenberg_Templates\Inc\Block;

/**
 * Spectra_AI_Block
 *
 * @since 2.0.16
 */
class Spectra_AI_Block {
	/**
	 * Instance
	 *
	 * @access private
	 * @var object Class Instance.
	 * @since 2.0.16
	 */
	private static $instance = null;

	/**
	 * Constructor
	 *
	 * @since 2.0.16
	 */
	public function __construct() {
		global $wp_version;
		$hook = version_compare( $wp_version, '5.8-alpha', '<' ) ? 'block_editor_preload_paths' : 'block_editor_rest_api_preload_paths';

		add_action( 'init', array( $this, 'register_block_type' ) );
		add_filter( $hook, array( $this, 'update_new_post' ), 10, 2 );
	}

	/**
	 * Get Instance
	 *
	 * @since 2.0.16
	 *
	 * @return object Class object.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Register Block Type
	 *
	 * @since 2.0.16
	 *
	 * @return void
	 */
	public function register_block_type() {
		register_block_type( AST_BLOCK_TEMPLATES_DIR . '/block/dist' );
	}

	/**
	 * Add the block on new page creation.
	 *
	 * @since 2.0.16
	 *
	 * @param array<string, string> $paths   Array of preload paths.
	 * @param mixed                 $context Context of the request.
	 *
	 * @return array<string, string>
	 */
	public static function update_new_post( $paths, $context ) {
		if ( ! is_object( $context ) || ! property_exists( $context, 'post' ) ) {
			return $paths;
		}

		$post = $context->post;

		if ( ! is_object( $post ) || ! property_exists( $post, 'ID' ) || ! property_exists( $post, 'post_content' ) ) {
			return $paths;
		}

		if ( ! is_string( $post->post_content ) ) {
			return $paths;
		}

		$blocks = preg_match( '/<!-- wp:(.*) \/?-->/', $post->post_content );

		if ( ! $blocks ) {
			$block  = '<!-- wp:gutenberg-templates/spectra-ai -->';
			$block .= self::remove_broken_p_tags( $post->post_content );
			$block .= '<!-- /wp:gutenberg-templates/spectra-ai -->';

			$post->post_content = $block;

			wp_update_post(
				array(
					'ID'           => $post->ID,
					'post_content' => $block,
				)
			);
		}

		return $paths;
	}


	/**
	 * Remove broken p tags.
	 *
	 * @since 2.0.16
	 *
	 * @param string $content Post content.
	 *
	 * @return string
	 */
	public static function remove_broken_p_tags( $content ) {

		if ( ! is_string( $content ) ) {
			return '';
		}

		// Convert microsoft special characters.
		$replace = array(
			'‘' => "'",
			'’' => "'",
			'”' => '"',
			'“' => '"',
			'–' => '-',
			'—' => '-',
			'…' => '&#8230;',
		);

		// Remove empty tags.
		$content = preg_replace( '@<([^>]+)\s*>\s*<\/\1\s*>@m', '', $content );

		// Remove all <p> tags.
		$content = preg_replace( '/<\/?p[^>]*\>/i', '', $content );

		// Replace special characters.
		foreach ( $replace as $k => $v ) {
			$content = str_replace( $k, $v, $content );
		}

		// Balance tags.
		$content = force_balance_tags( $content );

		return $content;
	}
}

/**
 * Kicking this off by calling 'get_instance()' method.
 */
Spectra_AI_Block::get_instance();
