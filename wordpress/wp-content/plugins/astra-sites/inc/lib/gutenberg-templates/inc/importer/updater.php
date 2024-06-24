<?php
/**
 * Update Compatibility
 *
 * @package ast-block-templates
 */

namespace Gutenberg_Templates\Inc\Importer;

use Gutenberg_Templates\Inc\Traits\Instance;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Updater class
 *
 * @since 2.0.0
 */
class Updater {

	use Instance;

	/**
	 *  Constructor
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'ast_block_templates_updated', array( $this, 'updated' ), 10, 2 );
	}

	/**
	 * Init
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function init() {
		// Get auto saved version number.
		$saved_version = get_option( 'ast-block-templates-version', false );

		// Update auto saved version number.
		if ( ! $saved_version ) {

			// Fresh install updation.
			$this->fresh_v2_install();

			// Update current version.
			update_option( 'ast-block-templates-version', AST_BLOCK_TEMPLATES_VER );
			return;
		}

		do_action( 'ast_block_templates_update_before' );

		// If equals then return.
		if ( version_compare( $saved_version, AST_BLOCK_TEMPLATES_VER, '=' ) ) {
			return;
		}

		do_action( 'ast_block_templates_updated', $saved_version, AST_BLOCK_TEMPLATES_VER );

		// Update auto saved version number.
		update_option( 'ast-block-templates-version', AST_BLOCK_TEMPLATES_VER );

		do_action( 'ast_block_templates_update_after' );
	}


	/**
	 * Update onboarding variables
	 * 
	 * @since 2.1.12
	 * @return void
	 */
	public function remove_deprecated_option() {

		$deprecated_option = get_option( 'ast-templates-business-details', false );

		if ( ! empty( $deprecated_option['business_name'] ) ) {

			// Update social media key meta.
			$social_profiles = isset( $deprecated_option['social_profiles'] ) ? $deprecated_option['social_profiles'] : array();

			if ( is_array( $social_profiles ) ) {
				foreach ( $social_profiles as $index => $profile ) {
					if ( isset( $profile['id'] ) ) {
						$social_profiles[ $index ]['type'] = $profile['id'];
					}
				}
			}
			
			$business_details = array(
				'business_name' => isset( $deprecated_option['business_name'] ) ? $deprecated_option['business_name'] : '',
				'business_description' => isset( $deprecated_option['business_description'] ) ? $deprecated_option['business_description'] : '',
				'business_category' => '',
				'images' => isset( $deprecated_option['images'] ) ? $deprecated_option['images'] : array(),
				'image_keyword' => isset( $deprecated_option['image_keywords'] ) ? $deprecated_option['image_keywords'] : array(),
				'business_address' => isset( $deprecated_option['business_address'] ) ? $deprecated_option['business_address'] : '',
				'business_phone' => isset( $deprecated_option['business_phone'] ) ? $deprecated_option['business_phone'] : '',
				'business_email' => isset( $deprecated_option['business_email'] ) ? $deprecated_option['business_email'] : '',
				'social_profiles' => $social_profiles,
			);

			// Merge with existing business details if available.
			$zip_user_details = get_option( 'zipwp_user_business_details', array() );
			$business_details = array_merge( $zip_user_details, $business_details );

			update_option( 'zipwp_user_business_details', $business_details );
			delete_option( 'ast-templates-business-details' );
		}
	}

	/**
	 * Fresh v2 install
	 * 
	 * @since 2.0.0
	 * @return void
	 */
	public function fresh_v2_install() {
		delete_option( 'ast-block-templates-last-export-checksums-time' );
		delete_option( 'ast_block_templates_fresh_site' );
	}

	/**
	 * Updated
	 * 
	 * @since 2.0.0
	 * @param string $old_version Old version number.
	 * @param string $new_version New version number.
	 * @return void
	 */
	public function updated( $old_version, $new_version ) {
		switch ( $new_version ) {
			case '2.0.0':
				// Do something for that version.
				break;
		}
		
		if ( version_compare( $old_version, '2.1.24', '<' ) ) {
			// Create new files.
			Sync_Library::instance()->set_default_assets();

			// Update new files.
			wp_schedule_single_event( time() + 1, 'sync_blocks' );
			
			// Delete older options data.
			$options = Sync_Library::instance()->get_default_assets();
			foreach ( $options as $option_name ) {
				delete_option( $option_name );
			}

			$this->remove_deprecated_option();
		}
	}
}
