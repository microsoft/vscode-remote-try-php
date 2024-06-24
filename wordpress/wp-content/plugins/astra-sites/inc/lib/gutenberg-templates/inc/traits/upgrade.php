<?php
/**
 * Trait.
 *
 * @package {{package}}
 * @since 0.0.1
 */

namespace Gutenberg_Templates\Inc\Traits;

use Gutenberg_Templates\Inc\Traits\Instance;

/**
 * Trait Instance.
 */
class Upgrade {

	use Instance;

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'wp_ajax_ast_skip_zip_ai_onboarding', array( $this, 'skip_spectra_pro_onboarding' ) );
	}

	/**
	 * Activate Plugin
	 */
	public function skip_spectra_pro_onboarding() {

		if ( ! current_user_can( 'manage_ast_block_templates' ) ) {
			wp_send_json_error( __( 'You are not allowed to perform this action', 'ast-block-templates' ) );
		}

		// Verify Nonce.
		check_ajax_referer( 'skip-spectra-pro-onboarding-nonce', 'security' );

		update_option( 'ast_skip_zip_ai_onboarding', 'yes' );

		wp_send_json_success(
			array(
				'success' => 'true',
			)
		);
	}

}

