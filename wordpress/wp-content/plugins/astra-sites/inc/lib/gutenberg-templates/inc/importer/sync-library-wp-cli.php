<?php
/**
 * WP CLI
 *
 * 1. Run `wp ast-block-templates sync`       Info.
 *
 * @since 1.0.0
 *
 * @package ast-block-templates
 */

namespace Gutenberg_Templates\Inc\Importer;

use Gutenberg_Templates\Inc\Importer\Sync_Library;
use Gutenberg_Templates\Inc\Traits\Helper;
use Gutenberg_Templates\Inc\Traits\Instance;
use WP_CLI;
use WP_CLI_Command;

/**
 * Ast_Block Templates WP CLI
 */
class Sync_Library_WP_CLI {

	use Instance;

	/**
	 * Sync
	 *
	 *  Example: wp ast-block-templates sync
	 *
	 * @since 1.0.0
	 * @param  array $args       Arguments.
	 * @param  array $assoc_args Associated Arguments.
	 * @return void
	 */
	public function sync( $args = array(), $assoc_args = array() ) {

		// Start Sync.
		if ( Helper::instance()->ast_block_templates_doing_wp_cli() ) {
			WP_CLI::line( 'Sync Started' );
		}

		$force = isset( $assoc_args['force'] ) ? true : false;

		$result_data = Sync_Library::instance()->check_checksum_and_get_blocks_data();

		if ( empty( $result_data ) && Helper::instance()->ast_block_templates_doing_wp_cli() && ! $force ) {
			WP_CLI::line( 'Blocks are up to date.' );
			return;
		}
		Sync_Library::instance()->process_data_sync( $result_data );
		Sync_Library::instance()->update_latest_checksums( $result_data['checksum'] );
		Sync_Library::instance()->get_server_astra_customizer_css();

		// Start Sync.
		if ( Helper::instance()->ast_block_templates_doing_wp_cli() ) {
			WP_CLI::line( 'Sync Completed' );
		}
	}
}

/**
 * Add Command
 */
if ( Helper::instance()->ast_block_templates_doing_wp_cli() ) {
	WP_CLI::add_command( 'ast-block-templates', 'Gutenberg_Templates\Inc\Importer\Sync_Library_WP_CLI' );
}
