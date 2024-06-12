<?php
/**
 * AI content generator and replacer file.
 *
 * @package {{package}}
 * @since {{since}}
 */

namespace Gutenberg_Templates\Inc\Importer;

use Gutenberg_Templates\Inc\Traits\Instance;
use Gutenberg_Templates\Inc\Traits\Helper;
use Gutenberg_Templates\Inc\Importer\Importer_Helper;
use Gutenberg_Templates\Inc\Importer\Images;

/**
 * Block Editor Blocks Replacer
 *
 * @since {{since}}
 */
class BlockEditor {

	use Instance;

	/**
	 * Constructor
	 *
	 * @since {{since}}
	 */
	public function __construct() {}

	/**
	 * Old Images
	 *
	 * @var array<int> $old_images Old images.
	 */
	public static $old_images = array();

	/**
	 * Parses images and other content in the Spectra Container block.
	 *
	 * @since {{since}}
	 * @param array<mixed> $block Block.
	 * @return array<mixed> $block Block.
	 */
	public function parse_spectra_container( $block ) {
		
		if (
			! isset( $block['attrs']['backgroundImageDesktop'] ) ||
			empty( $block['attrs']['backgroundImageDesktop'] ) ||
			Importer_Helper::is_skipable( $block['attrs']['backgroundImageDesktop']['url'] )
		) {
			return $block;
		}

		$image = Images::instance()->get_image( Images::$image_index );
		if ( empty( $image ) || ! is_array( $image ) || is_bool( $image ) ) {
			return $block;
		}

		$image = Images::instance()->download_image( $image );

		if ( is_wp_error( $image ) ) {
			Helper::instance()->ast_block_templates_log( 'Replacing Image problem : ' . $block['attrs']['backgroundImageDesktop']['url'] . ' Warning: ' . wp_json_encode( $image ) );
			return $block;
		}

		$attachment = wp_prepare_attachment_for_js( absint( $image ) );
		if ( ! is_array( $attachment ) ) {
			return $block;
		}

		self::$old_images[] = $block['attrs']['backgroundImageDesktop']['id'];

		Helper::instance()->ast_block_templates_log( 'Replacing Image from ' . $block['attrs']['backgroundImageDesktop']['url'] . 'to "' . $attachment['url'] . '" for ' . $block['blockName'] . '" with index "' . Images::$image_index . '"' );
		$block['attrs']['backgroundImageDesktop'] = $attachment;
		Images::$image_index++;

		return $block;
	}

	/**
	 * Parses images and other content in the Spectra Info Box block.
	 *
	 * @since {{since}}
	 * @param array<mixed> $block Block.
	 * @return array<mixed> $block Block.
	 */
	public function parse_spectra_infobox( $block ) {
		
		if (
			! isset( $block['attrs']['iconImage'] ) ||
			empty( $block['attrs']['iconImage'] ) ||
			Importer_Helper::is_skipable( $block['attrs']['iconImage']['url'] )
		) {
			return $block;
		}

		$image = Images::instance()->get_image( Images::$image_index );
		if ( empty( $image ) || ! is_array( $image ) || is_bool( $image ) ) {
			return $block;
		}

		$image = Images::instance()->download_image( $image );

		if ( is_wp_error( $image ) ) {
			Helper::instance()->ast_block_templates_log( 'Replacing Image problem : ' . $block['attrs']['iconImage']['url'] . ' Warning: ' . wp_json_encode( $image ) );
			return $block;
		}

		$attachment = wp_prepare_attachment_for_js( absint( $image ) );

		if ( ! is_array( $attachment ) ) {
			return $block;
		}

		self::$old_images[] = $block['attrs']['iconImage']['id'];

		if ( ! empty( $block['attrs']['iconImage']['url'] ) ) {
			Helper::instance()->ast_block_templates_log( 'Replacing Image from ' . $block['attrs']['iconImage']['url'] . ' to "' . $attachment['url'] . '" for ' . $block['blockName'] . '" with index "' . Images::$image_index . '"' );
			$block['innerHTML'] = str_replace( $block['attrs']['iconImage']['url'], $attachment['url'], $block['innerHTML'] );
		}

		foreach ( $block['innerContent'] as $key => &$inner_content ) {

			if ( is_string( $block['innerContent'][ $key ] ) && '' === trim( $block['innerContent'][ $key ] ) ) {
				continue;
			}
			$block['innerContent'][ $key ] = str_replace( $block['attrs']['iconImage']['url'], $attachment['url'], $block['innerContent'][ $key ] );
		}
		$block['attrs']['iconImage'] = $attachment;
		Images::$image_index++;

		return $block;
	}

	/**
	 * Parses images and other content in the Spectra Image block.
	 *
	 * @since {{since}}
	 * @param array<mixed> $block Block.
	 * @return array<mixed> $block Block.
	 */
	public function parse_spectra_image( $block ) {
		
		if (
			! isset( $block['attrs']['url'] ) ||
			Importer_Helper::is_skipable( $block['attrs']['url'] )
		) {
			return $block;
		}

		$image = Images::instance()->get_image( Images::$image_index );
		if ( empty( $image ) || ! is_array( $image ) ) {
			return $block;
		}

		$image = Images::instance()->download_image( $image );

		if ( is_wp_error( $image ) ) {
			Helper::instance()->ast_block_templates_log( 'Replacing Image problem : ' . $block['attrs']['url'] . ' Warning: ' . wp_json_encode( $image ) );
			return $block;
		}

		$attachment = wp_prepare_attachment_for_js( absint( $image ) );
		if ( ! is_array( $attachment ) ) {
			return $block;
		}

		self::$old_images[] = $block['attrs']['id'];
		Helper::instance()->ast_block_templates_log( 'Replacing Image from ' . $block['attrs']['url'] . ' to "' . $attachment['url'] . '" for ' . $block['blockName'] . '" with index "' . Images::$image_index . '"' );
		$block['innerHTML'] = str_replace( $block['attrs']['url'], $attachment['url'], $block['innerHTML'] );
		$block['innerHTML'] = str_replace( $block['attrs']['id'], $attachment['id'], $block['innerHTML'] );

		$tablet_size_slug = ! empty( $block['attrs']['sizeSlugTablet'] ) ? $block['attrs']['sizeSlugTablet'] : '';
		$mobile_size_slug = ! empty( $block['attrs']['sizeSlugMobile'] ) ? $block['attrs']['sizeSlugMobile'] : '';
		$is_attachemnts   = is_array( $attachment['sizes'] ) && ! empty( $attachment['sizes'] );

		if ( ! empty( $block['attrs']['urlTablet'] ) && ! empty( $tablet_size_slug ) && ! empty( $attachment['sizes'][ $tablet_size_slug ]['url'] ) ) {
			$block['innerHTML'] = str_replace( $block['attrs']['urlTablet'], $attachment['sizes'][ $tablet_size_slug ]['url'], $block['innerHTML'] );
		}
		if ( ! empty( $block['attrs']['urlMobile'] && ! empty( $mobile_size_slug ) ) && ! empty( $attachment['sizes'][ $mobile_size_slug ]['url'] ) ) {
			$block['innerHTML'] = str_replace( $block['attrs']['urlMobile'], $attachment['sizes'][ $mobile_size_slug ]['url'], $block['innerHTML'] );
		}

		foreach ( $block['innerContent'] as $key => &$inner_content ) {

			if ( is_string( $block['innerContent'][ $key ] ) && '' === trim( $block['innerContent'][ $key ] ) ) {
				continue;
			}
			$block['innerContent'][ $key ] = str_replace( $block['attrs']['url'], $attachment['url'], $block['innerContent'][ $key ] );
			$block['innerContent'][ $key ] = str_replace( $block['attrs']['id'], $attachment['id'], $block['innerContent'][ $key ] );

			if ( $is_attachemnts ) {
				if ( ! empty( $block['attrs']['urlTablet'] ) ) {
					$block['innerContent'][ $key ] = str_replace( $block['attrs']['urlTablet'], $attachment['url'], $block['innerContent'][ $key ] );
				}
				if ( ! empty( $block['attrs']['urlMobile'] ) ) {
					$block['innerContent'][ $key ] = str_replace( $block['attrs']['urlMobile'], $attachment['url'], $block['innerContent'][ $key ] );
				}
			}
		}

		if ( $is_attachemnts ) {
			if ( ! empty( $block['attrs']['urlTablet'] ) ) {
				$block['attrs']['urlTablet'] = $attachment['url'];
			}

			if ( ! empty( $block['attrs']['urlMobile'] ) ) {
				$block['attrs']['urlMobile'] = $attachment['url'];
			}
		}

		$block['attrs']['url'] = $attachment['url'];
		$block['attrs']['id']  = $attachment['id'];
		Images::$image_index++;

		return $block;
	}

	/**
	 * Parses images and other content in the Spectra Info Box block.
	 *
	 * @since {{since}}
	 * @param array<mixed> $block Block.
	 * @return array<mixed> $block Block.
	 */
	public function parse_spectra_gallery( $block ) {
		$images      = $block['attrs']['mediaGallery'];
		$gallery_ids = array();
		foreach ( $images as $key => &$image ) {

			if (
				! isset( $image ) ||
				empty( $image ) ||
				Importer_Helper::is_skipable( $image['url'] )
			) {
				continue;
			}
			
			$new_image = Images::instance()->get_image( Images::$image_index );
			if ( empty( $new_image ) || ! is_array( $new_image ) || is_bool( $new_image ) ) {
				continue;
			}

			$new_image = Images::instance()->download_image( $new_image );

			if ( is_wp_error( $new_image ) ) {
				Helper::instance()->ast_block_templates_log( 'Replacing Image problem : ' . $image['url'] . ' Warning: ' . wp_json_encode( $new_image ) );
				continue;
			}

			$attachment = wp_prepare_attachment_for_js( absint( $new_image ) );

			if ( ! is_array( $attachment ) ) {
				continue;
			}

			$gallery_ids[] = $attachment['id'];

			self::$old_images[] = $image['id'];
			Helper::instance()->ast_block_templates_log( 'Replacing Image from ' . $image['url'] . ' to "' . $attachment['url'] . '" for ' . $block['blockName'] . '" with index "' . Images::$image_index . '"' );
			$image['url']     = ! empty( $attachment['url'] ) ? $attachment['url'] : $image['url'];
			$image['sizes']   = ! empty( $attachment['sizes'] ) ? $attachment['sizes'] : $image['sizes'];
			$image['mime']    = ! empty( $attachment['mime'] ) ? $attachment['mime'] : $image['mime'];
			$image['type']    = ! empty( $attachment['type'] ) ? $attachment['type'] : $image['type'];
			$image['subtype'] = ! empty( $attachment['subtype'] ) ? $attachment['subtype'] : $image['subtype'];
			$image['id']      = ! empty( $attachment['id'] ) ? $attachment['id'] : $image['id'];
			$image['alt']     = ! empty( $attachment['alt'] ) ? $attachment['alt'] : $image['alt'];
			$image['link']    = ! empty( $attachment['link'] ) ? $attachment['link'] : $image['link'];
			Images::$image_index++;
		}
		$block['attrs']['mediaGallery'] = $images;
		$block['attrs']['mediaIDs']     = $gallery_ids;

		return $block;
	}

	/**
	 * Parses Google Map for the Spectra Google Map block.
	 *
	 * @since {{since}}
	 * @param array<mixed> $block Block.
	 * @return array<mixed> $block Block.
	 */
	public function parse_spectra_google_map( $block ) {

		$address = Importer_Helper::get_business_details( 'business_address' );
		if ( empty( $address ) ) {
			return $block;
		}

		Helper::instance()->ast_block_templates_log( 'Replacing Google Map from ' . $block['attrs']['address'] . ' to "' . $address );
		$block['attrs']['address'] = $address;

		return $block;
	}

	/**
	 * Parses images and other content in the Spectra Info Box block.
	 *
	 * @since {{since}}
	 * @param \WP_Post $post Post.
	 * @return void
	 */
	public function parse_featured_image( $post ) {
		$thumb_id = get_post_thumbnail_id( $post );
		if ( false === $thumb_id ) {
			return;
		}
		$thumb = wp_prepare_attachment_for_js( $thumb_id );
		
		if (
			! isset( $thumb['url'] ) ||
			Importer_Helper::is_skipable( $thumb['url'] )
		) {
			return;
		}

		$image = Images::instance()->get_image( Images::$image_index );
		if ( empty( $image ) || ! is_array( $image ) || is_bool( $image ) ) {
			return;
		}

		$image = Images::instance()->download_image( $image );

		if ( is_wp_error( $image ) ) {
			Helper::instance()->ast_block_templates_log( 'Replacing Image problem : ' . $thumb['url'] . ' Warning: ' . wp_json_encode( $image ) );
			return;
		}

		$attachment = wp_prepare_attachment_for_js( absint( $image ) );
		if ( ! is_array( $attachment ) ) {
			return;
		}

		self::$old_images[] = $thumb['id'];
		Helper::instance()->ast_block_templates_log( 'Replacing Image from ' . $thumb['url'] . ' to "' . $attachment['url'] . '" with index "' . Images::$image_index . '"' );

		set_post_thumbnail( $post, $attachment['id'] );

		Images::$image_index++;
	}

	/**
	 * Parses address and contct in the block.
	 *
	 * @since {{since}}
	 * @param string $key key to replace.
	 * @param string $ai_content ai content string.
	 *
	 * @return string
	 */
	public function replace_contact_details( $key, $ai_content ) {

		$business_details = Importer_Helper::get_business_details();

		$social_profiles = $business_details['social_profiles'];

		if ( ! is_array( $social_profiles ) ) {
			return $ai_content;
		}
		
		$social_icons = array_combine( array_column( $social_profiles, 'type' ), array_column( $social_profiles, 'url' ) );

		switch ( $key ) {
			case '2360 Hood Avenue, San Diego, CA, 92123':
				$ai_content = $business_details['business_address'];
				break;

			case '202-555-0188':
				$ai_content = $business_details['business_phone'];
				break;

			case 'contact@example.com':
				$ai_content = $business_details['business_email'];
				break;

			case '#facebook':
			case '#twitter':
			case '#linkenin':
			case '#instagram':
			case '#youtube':
				$ai_content = $social_icons[ str_replace( '#', '', $key ) ];
				break;
		}

		return $ai_content;

	}

	/**
	 * Parses Spectra form block.
	 *
	 * @since {{since}}
	 * @param array<mixed> $block Block.
	 * @return array<mixed> $block Block.
	 */
	public function parse_spectra_form( $block ) {

		$business_email = Importer_Helper::get_business_details( 'business_email' );

		if ( ! empty( $business_email ) ) {
			$block['attrs']['afterSubmitToEmail'] = $business_email;
		}
		return $block;

	}

	/**
	 * Parse social icon list.
	 *
	 * @since {{since}}
	 * @param array<mixed> $block Block.
	 * @return array<mixed> $block Block.
	 */
	public function parse_spectra_social_icons( $block ) {

		$social_profile = Importer_Helper::get_business_details( 'social_profiles' );
		if ( empty( $social_profile ) ) {
			return $block;
		}

		$social_icons = array_combine( array_column( $social_profile, 'id' ), array_column( $social_profile, 'url' ) );
		$inner_blocks = $block['innerBlocks'];

		if ( is_array( $inner_blocks ) ) {

			$social_icons_list = array_map(
				function( $item ) {
					return $item['attrs']['icon'];
				},
				$inner_blocks
			);

			// Check if icon-list contains social icons by checking facebook icon in list.
			if ( ! in_array( 'facebook', $social_icons_list, true ) ) {
				return $block;
			}

			foreach ( $inner_blocks as $index => &$inner_block ) {

				if ( 'uagb/icon-list-child' !== $inner_block['blockName'] ) {
					continue;
				}

				$icon = $inner_block['attrs']['icon'];

				if ( empty( $icon ) ) {
					continue;
				}

				if ( in_array( $icon, array_keys( $social_icons ), true ) ) {
					$block['innerBlocks'][ $index ]['attrs']['link'] = $social_icons[ $icon ];
				}

				if ( ! in_array( $icon, array_keys( $social_icons ), true ) ) {
					unset( $block['innerBlocks'][ $index ] );
				}
			}

			$block['innerBlocks'] = array_values( $block['innerBlocks'] );
		}
		return $block;
	}
}
