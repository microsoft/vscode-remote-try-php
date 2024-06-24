<?php
/**
 * FSE importer class.
 *
 * @since  1.0.0
 * @package Astra Addon
 */

namespace AiBuilder\Inc\Classes\Importer;

use AiBuilder\Inc\Traits\Instance;

/**
 * Customizer Site options importer class.
 *
 * @since  1.0.0
 */
class Ai_Builder_Fse_Importer {

	use Instance;

	/**
	 * FSE logo attributes
	 *
	 * @since 3.3.0
	 * @var (array) fse_logo_attributes
	 */
	public static $fse_logo_attributes = [];

	/**
	 * Set FSE site related data.
	 *
	 * @since 3.3.0
	 * @return void
	 */
	public static function set_fse_site_data() {

		check_ajax_referer( 'astra-sites-set-ai-site-data', 'security' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(
				array(
					'success' => false,
					'message' => __( 'You are not authorized to perform this action.', 'ai-builder', 'astra-sites' ),
				)
			);
		}

		$param = isset( $_POST['param'] ) ? sanitize_text_field( $_POST['param'] ) : '';

		if ( empty( $param ) ) {
			wp_send_json_error(
				array(
					'error' => __( 'Received empty parameters.', 'ai-builder', 'astra-sites' ),
				)
			);
		}

		switch ( $param ) {

			case 'site-logo' === $param:
					$logo       = isset( $_POST['logo'] ) ? absint( $_POST['logo'] ) : 0;
					$logo_width = isset( $_POST['logo-width'] ) ? sanitize_text_field( $_POST['logo-width'] ) : '';

				if ( ! empty( $logo ) || ! empty( $logo_width ) ) {
					self::$fse_logo_attributes = array(
						'logo_width' => $logo_width,
						'logo'       => $logo,
					);
					self::update_fse_site_logo( 'header' );
					self::update_fse_site_logo( 'footer' );
				}
				break;

			case 'site-colors' === $param:
					$palette       = isset( $_POST['palette'] ) ? (array) json_decode( stripslashes( $_POST['palette'] ) ) : array();
					$colors_passed = isset( $palette['colors'] ) ? (array) $palette['colors'] : array();
				if ( ! empty( $colors_passed ) ) {
					$colors_array   = Swt\get_theme_custom_styles();
					$colors_content = $colors_array['post_content'];
					if ( $colors_content && isset( $colors_content['settings']['color']['palette']['theme'] ) ) {
						$theme_colors = $colors_content['settings']['color']['palette']['theme'];
						$set_colors   = array();
						foreach ( $theme_colors as $key => $single ) {
							$single['color'] = $colors_passed[ $key ];
							$set_colors[]    = $single;

						}
						$colors_content['settings']['color']['palette']['theme'] = $set_colors;

					}

					$update_colors = array(
						'ID'           => $colors_array['ID'],
						'post_content' => wp_json_encode( $colors_content ),
					);

						// Update the post into the database.
					wp_update_post( $update_colors );
				}

				break;

			case 'site-typography' === $param:
					$typography_passed                              = isset( $_POST['typography'] ) ? (array) json_decode( stripslashes( $_POST['typography'] ) ) : '';
					$typography_passed['body-font-family-slug']     = isset( $typography_passed['body-font-family-slug'] ) ? $typography_passed['body-font-family-slug'] : 'inter';
					$typography_passed['headings-font-family-slug'] = isset( $typography_passed['headings-font-family-slug'] ) ? $typography_passed['headings-font-family-slug'] : 'inter';
				if ( ! empty( $typography_passed ) ) {
					if ( is_callable( 'UAGB_FSE_Fonts_Compatibility::get_instance' ) ) {
						$fse_fonts_comp_instance = new \UAGB_FSE_Fonts_Compatibility();
						$fse_fonts_comp_instance->get_font_family_for_starter_template( array( ucfirst( $typography_passed['body-font-family'] ), ucfirst( $typography_passed['headings-font-family'] ) ) );
					}
					$typography_array   = Swt\get_theme_custom_styles();
					$typography_content = $typography_array['post_content'];
					if ( $typography_content && isset( $typography_content['styles']['typography'] ) ) {
						$typography_content['styles']['typography']['fontFamily'] = 'var:preset|font-family|' . $typography_passed['body-font-family-slug'];
					}

					if ( $typography_content && isset( $typography_content['styles']['elements']['link']['typography'] ) ) {
						$typography_content['styles']['elements']['link']['typography']['fontFamily'] = 'var:preset|font-family|' . $typography_passed['body-font-family-slug'];
					}

					if ( $typography_content && isset( $typography_content['styles']['elements']['heading']['typography'] ) ) {
						$typography_content['styles']['elements']['heading']['typography']['fontFamily'] = 'var:preset|font-family|' . $typography_passed['headings-font-family-slug'];
					}

					if ( $typography_content && isset( $typography_content['styles']['elements']['button']['typography'] ) ) {
						$typography_content['styles']['elements']['button']['typography']['fontFamily'] = 'var:preset|font-family|' . $typography_passed['body-font-family-slug'];
					}

					if ( $typography_content && isset( $typography_content['styles']['elements']['h1']['typography'] ) ) {
						$typography_content['styles']['elements']['h1']['typography']['fontFamily'] = 'var:preset|font-family|' . $typography_passed['headings-font-family-slug'];
					}

					if ( $typography_content && isset( $typography_content['styles']['elements']['h2']['typography'] ) ) {
						$typography_content['styles']['elements']['h2']['typography']['fontFamily'] = 'var:preset|font-family|' . $typography_passed['headings-font-family-slug'];
					}

					if ( $typography_content && isset( $typography_content['styles']['elements']['h3']['typography'] ) ) {
						$typography_content['styles']['elements']['h3']['typography']['fontFamily'] = 'var:preset|font-family|' . $typography_passed['headings-font-family-slug'];
					}

					if ( $typography_content && isset( $typography_content['styles']['elements']['h4']['typography'] ) ) {
						$typography_content['styles']['elements']['h4']['typography']['fontFamily'] = 'var:preset|font-family|' . $typography_passed['headings-font-family-slug'];
					}

					if ( $typography_content && isset( $typography_content['styles']['elements']['h5']['typography'] ) ) {
						$typography_content['styles']['elements']['h5']['typography']['fontFamily'] = 'var:preset|font-family|' . $typography_passed['headings-font-family-slug'];
					}

					if ( $typography_content && isset( $typography_content['styles']['elements']['h6']['typography'] ) ) {
						$typography_content['styles']['elements']['h6']['typography']['fontFamily'] = 'var:preset|font-family|' . $typography_passed['headings-font-family-slug'];
					}

					$update_typography = array(
						'ID'           => $typography_array['ID'],
						'post_content' => wp_json_encode( $typography_content ),
					);

						// Update the post into the database.
					wp_update_post( $update_typography );
				}

				break;
		}

		wp_send_json_success();
	}

	/**
	 * Set FSE site related data.
	 *
	 * @since 3.3.0
	 * @param string $post_name post name.
	 * @return void
	 */
	public static function update_fse_site_logo( $post_name ) {
		$args = array(
			'orderby'     => 'post_type',
			'post_status' => 'publish',
			'post_type'   => array( 'wp_template_part' ),
			'name'        => $post_name,
		);

		$fse_posts    = get_posts( $args );
		$post_content = '';

		if ( isset( $fse_posts[0] ) && isset( $fse_posts[0]->post_content ) ) {
			$post_content = stripslashes( $fse_posts[0]->post_content );
		}

		// Define the regex pattern to match the logo code with 'site-logo-img' class.
		$regex_pattern = '/<img\s[^>]*src=([\'\"]??)([^\' >]*?)\\1[^>]*>/i';
		$regex_src     = '/src=[\'"]([^\'"]+)[\'"]/i';
		$regex_width   = '/width=[\'"]([^\'"]+)[\'"]/i';

		// Search for the logo code using regex.
		preg_match( $regex_pattern, $post_content, $matches );

		// Check if a match is found.
		if ( ! empty( $matches ) ) {
			$logo_code = $matches[0]; // The matched logo code.
			if ( strpos( $logo_code, 'width=' ) === false ) {
				// Width attribute is not present, so add it.
				$width_add = 'width="' . self::$fse_logo_attributes['logo_width'] . '" />';
				$logo_code = str_replace( '/>', $width_add, $logo_code );
			}

			// Extract the src attribute using regex.
			preg_match( $regex_src, $logo_code, $matches_src );
			if ( ! empty( $matches_src ) ) {
				$src_attribute = $matches_src[1]; // The value of the src attribute.
				$attachment    = wp_prepare_attachment_for_js( absint( self::$fse_logo_attributes['logo'] ) );
				if ( is_wp_error( $attachment ) ) {
					return;
				}
				$post_content = str_replace( $src_attribute, $attachment['url'], $post_content );
			}

			// Extract the width attribute using regex.
			preg_match( $regex_width, $logo_code, $matches_width );
			if ( ! empty( $matches_width ) ) {
				$width_attribute = $matches_width[1]; // The value of the width attribute.
				$post_content    = str_replace( $width_attribute, self::$fse_logo_attributes['logo_width'], $post_content );
			}
			$update_post = array(
				'ID'           => $fse_posts[0]->ID,
				'post_content' => $post_content,
			);

			// Update the post into the database.
			wp_update_post( $update_post );
		}

	}

}
Ai_Builder_Fse_Importer::Instance();
