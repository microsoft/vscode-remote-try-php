<?php
/**
 * Pinterest for WooCommerce Rich Pins
 *
 * @package     Pinterest_For_WooCommerce/Classes/
 * @version     1.0.0
 */

namespace Automattic\WooCommerce\Pinterest;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class adding RichPins support.
 */
class RichPins {

	use PluginHelper;

	/**
	 * Output Pinterest Rich Pins metatags based on post_type and site setup.
	 *
	 * Rich Pins show metadata right on the Pin itself, giving Pinners a richer experience and increasing engagement.
	 * It works by displaying metadata from marked up pages on the website.
	 * Pinterest supports Open Graph and Schema.org markup for Rich Pins.
	 * Adding OpenGraph or Schema.org markup to the site lets Pinterest sync that information from the site to the Pins.
	 * Since WooCommerce is already adding Schema.org markup, this method inject OpenGraph metatag for Products and Posts based on site's setup.
	 *
	 * @since 1.0.0
	 */
	public static function maybe_inject_rich_pins_opengraph_tags() {

		if ( ! is_singular( 'product' ) && ! is_singular( 'post' ) ) {
			return;
		}

		$tags = array();

		/**
		 * Allow 3rd parties to disable Rich Pins content.
		 *
		 * @see https://developers.pinterest.com/docs/rich-pins/overview/?#opt-out
		 *
		 * @since 1.0.0
		 *
		 * @param bool $is_disable If true, the content won't be shown as a Rich Pin. Default: false.
		 */
		$disable_rich_pins = apply_filters( 'pinterest_for_woocommerce_disable_rich_pins', false );

		if ( ! $disable_rich_pins ) {

			$rich_pins_on_products = Pinterest_For_Woocommerce()::get_setting( 'rich_pins_on_products' );
			$rich_pins_on_posts    = Pinterest_For_Woocommerce()::get_setting( 'rich_pins_on_posts' );

			if ( $rich_pins_on_products || $rich_pins_on_posts ) {

				/**
				 * Rich Pins arguments.
				 * phpcs:disable WooCommerce.Commenting.CommentHooks.MissingSinceComment
				 */
				$args = apply_filters(
					'pinterest_for_woocommerce_richpins_args',
					array(
						'products' => array(
							'enabled'             => $rich_pins_on_products,
							'enable_description'  => true,
							'enable_availability' => true,
						),
						'posts'    => array(
							'enabled'             => $rich_pins_on_posts,
							'enable_publish_time' => true,
							'enable_author'       => true,
						),
					)
				);

				$tags = self::get_opengraph_tags( $args );
			}
		}

		if ( empty( $tags ) || $disable_rich_pins ) {
			echo '<meta name="pinterest-rich-pin" content="false" />';
			return;
		}

		foreach ( $tags as $key => $content ) {
			printf(
				'<meta property="%s" content="%s" />',
				esc_attr( $key ),
				esc_attr( $content )
			);
		}
	}


	/**
	 * Return OpenGraph tags that must be render in the header
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Rich Pins arguments.
	 *
	 * @return array
	 */
	protected static function get_opengraph_tags( $args ) {

		if ( empty( $args['products']['enabled'] ) && empty( $args['posts']['enabled'] ) ) {
			return array();
		}

		$tags = array(
			'og:url'       => esc_url( get_the_permalink() ),
			'og:site_name' => get_bloginfo( 'name' ),
			'og:type'      => is_singular( 'product' ) ? 'og:product' : 'article',
			'og:title'     => get_the_title(),
		);

		$image = self::get_post_featured_image_src( get_queried_object()->ID );
		if ( ! empty( $image ) ) {
			$tags['og:image'] = $image;
		}

		$tags = self::add_product_opengraph_tags( $tags, $args );
		$tags = self::add_post_opengraph_tags( $tags, $args );

		/**
		 * Filter OpenGraph tags.
		 *
		 * @since 1.0.0
		 *
		 * @param array $tags  OpenGraph tags.
		 * @param array $args Rich Pins arguments.
		 */
		return (array) apply_filters( 'pinterest_for_woocommerce_opengraph_tags', $tags, $args );
	}


	/**
	 * Return WC Product's OpenGraph tags
	 *
	 * @see Product's Rich Pins : https://developers.pinterest.com/docs/rich-pins/products/
	 *
	 * @since 1.0.0
	 *
	 * @param array $tags  Open Graph Tags.
	 * @param array $args Rich Pins arguments.
	 *
	 * @return array
	 */
	public static function add_product_opengraph_tags( $tags, $args ) {

		if ( ! is_singular( 'product' ) || empty( $args['products']['enabled'] ) ) {
			return $tags;
		}

		// get product object.
		$product_id = get_queried_object()->ID;
		$product    = wc_get_product( $product_id );

		if ( ! is_object( $product ) ) {
			return $tags;
		}

		// mandatory tags.
		$tags['product:price:currency'] = get_woocommerce_currency();
		$tags['product:price:amount']   = wc_get_price_to_display( $product );

		// tags enabled by setup.
		if ( $product->is_on_sale() ) {
			$tags['og:price:standard_amount'] = $product->get_regular_price();
		}

		if ( ! empty( $args['products']['enable_description'] ) ) {
			$description = $product->get_short_description();
			if ( empty( $description ) ) {
				$description = $product->get_description();
			}

			/**
			 * Filters whether the shortcodes should be applied for product descriptions on the rich pins or be stripped out.
			 *
			 * @param bool       $apply_shortcodes Shortcodes are applied if set to `true` and stripped out if set to `false`.
			 * @param WC_Product $product          WooCommerce product object.
			 * phpcs:disable WooCommerce.Commenting.CommentHooks.MissingSinceComment
			 */
			$apply_shortcodes = apply_filters( 'pinterest_for_woocommerce_rich_pins_product_description_apply_shortcodes', false, $product );

			$description = self::strip_tags_from_string( $description, $apply_shortcodes );

			$tags['og:description'] = $description;
		}

		if ( array_key_exists( 'enable_availability', $args['products'] ) ) {

			$status_map = array(
				'instock'     => 'instock',
				'outofstock'  => 'out of stock',
				'onbackorder' => 'backorder',
			);

			$stock_status = $product->get_stock_status();

			if ( array_key_exists( $stock_status, $status_map ) ) {
				$tags['og:availability'] = $status_map[ $stock_status ];
			}
		}

		return $tags;
	}


	/**
	 * Return WP Post's OpenGraph tags
	 *
	 * @see Article's Rich Pins: https://developers.pinterest.com/docs/rich-pins/articles/
	 *
	 * @since 1.0.0
	 *
	 * @param array $tags  OpenGraph tags.
	 * @param array $args Rich Pins arguments.
	 *
	 * @return array
	 */
	public static function add_post_opengraph_tags( $tags, $args ) {

		if ( ! is_singular( 'post' ) || empty( $args['posts']['enabled'] ) ) {
			return $tags;
		}

		$description = get_the_excerpt();

		/**
		 * Filters whether the shortcodes should be applied for product descriptions on the rich pins or be stripped out.
		 *
		 * @param bool $apply_shortcodes Shortcodes are applied if set to `true` and stripped out if set to `false`.
		 * @param int  The post id.
		 * phpcs:disable WooCommerce.Commenting.CommentHooks.MissingSinceComment
		 */
		$apply_shortcodes = apply_filters( 'pinterest_for_woocommerce_rich_pins_post_description_apply_shortcodes', false, get_the_ID() );

		$description = self::strip_tags_from_string( $description, $apply_shortcodes );

		// mandatory tags.
		$tags['og:description'] = $description;

		// tags enabled by setup.
		if ( ! empty( $args['posts']['enable_publish_time'] ) ) {
			$tags['article:published_time'] = get_the_date( 'c' );
		}

		$author = get_userdata( get_queried_object()->post_author );
		if ( ! empty( $args['posts']['enable_author'] ) && ! empty( $author->display_name ) ) {
			$tags['article:author'] = $author->display_name;
		}

		return $tags;
	}


	/**
	 * Return post featured image
	 *
	 * @since 1.0.0
	 *
	 * @param int    $post_id Post ID.
	 * @param string $size Image size.
	 * @return string
	 */
	protected static function get_post_featured_image_src( $post_id, $size = 'large' ) {

		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $size );
		if ( ! empty( $image[0] ) ) {
			return $image[0];
		}

		return '';
	}


	/**
	 * Get the formatted warning message for the potential conflicting rich pin tags.
	 *
	 * @since 1.2.7
	 *
	 * @return string The warning message.
	 */
	public static function get_third_party_conflict_warning_message() {

		$third_party_plugins = self::get_third_party_conflicting_plugins();

		if ( empty( $third_party_plugins ) ) {
			return '';
		}

		return sprintf(
			/* Translators: 1: Conflicting plugins, 2: Plugins Admin page opening tag, 3: Pinterest settings opening tag, 4: Closing anchor tag */
			esc_html__( 'The following installed plugin(s) can potentially cause problems with Rich Pins: %1$s. %2$sRemove conflicting plugins%4$s or %3$smanage Rich Pins settings%4$s.', 'pinterest-for-woocommerce' ),
			implode( ', ', $third_party_plugins ),
			sprintf( '<a href="%s" target="_blank">', esc_url( admin_url( 'plugins.php' ) ) ),
			sprintf( '<a href="%s" target="_blank">', esc_url( wc_admin_url( '&path=/pinterest/settings' ) ) ),
			'</a>',
		);

	}


	/**
	 * Detect if there are other plugins installed on the site that conflicts with Rich Pins feature.
	 *
	 * @since 1.2.7
	 *
	 * @return array The list of installed plugins.
	 */
	public static function get_third_party_conflicting_plugins() {

		$third_party_plugins = array();

		if ( class_exists( 'RankMath' ) ) {
			$third_party_plugins['rmseo'] = 'RankMath SEO';
		}

		return $third_party_plugins;
	}
}
