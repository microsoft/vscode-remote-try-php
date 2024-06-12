<?php
/**
 * Ai Builder Importer
 *
 * @since  1.0.0
 * @package Ai Builder
 */

namespace AiBuilder\Inc\Classes\Importer;

use AiBuilder\Inc\Traits\Instance;
use STImporter\Importer\ST_Importer_File_System;

/**
 * Ai_Builder
 */
class Ai_Builder_Importer {

	use Instance;

	/**
	 * API Domain name
	 *
	 * @var (String) URL
	 */
	public $api_domain;

	/**
	 * API URL which is used to get the response from.
	 *
	 * @since  1.0.0
	 * @var (String) URL
	 */
	public $api_url;

	/**
	 * Search API URL which is used to get the response from.
	 *
	 * @since  2.0.0
	 * @var (String) URL
	 */
	public $search_analytics_url;

	/**
	 * Import Analytics API URL
	 *
	 * @since  3.1.4
	 * @var (String) URL
	 */
	public $import_analytics_url;

	/**
	 * Constructor.
	 *
	 * @since  1.0.0
	 */
	private function __construct() {
		$this->set_api_url();
		$this->includes();

		add_action( 'astra_sites_batch_process_complete', array( $this, 'clear_related_cache' ) );
		add_action( 'astra_sites_batch_process_complete', array( $this, 'delete_related_transient' ) );
		add_action( 'init', array( $this, 'permalink_update_after_import' ) );
	}

	/**
	 * Clear Cache.
	 *
	 * @since  1.0.9
	 */
	public function clear_related_cache() {

		// Clear 'Astra Addon' cache.
		if ( is_callable( 'Astra_Minify::refresh_assets' ) ) {
			\Astra_Minify::refresh_assets();
		}

		Ai_Builder_Utils::third_party_cache_plugins_clear_cache();

		$this->update_latest_checksums();

		// Flush permalinks.
		flush_rewrite_rules(); //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rewrite_rules_flush_rewrite_rules -- This function is called only after import is completed
	}

	/**
	 * Update Latest Checksums
	 *
	 * Store latest checksum after batch complete.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function update_latest_checksums() {
		$latest_checksums = get_site_option( 'astra-sites-last-export-checksums-latest', '' );
		update_site_option( 'astra-sites-last-export-checksums', $latest_checksums );
	}

	/**
	 * Delete related transients
	 *
	 * @since 3.1.3
	 */
	public function delete_related_transient() {
		delete_transient( 'astra_sites_batch_process_started' );
		ST_Importer_File_System::get_instance()->delete_demo_content();
		delete_option( 'ast_ai_import_current_url' );
		delete_option( 'astra_sites_ai_import_started' );
	}

	/**
	 * Include files.
	 *
	 * @since  1.0.0
	 */
	public function includes() {

		require_once AI_BUILDER_DIR . 'inc/classes/importer/class-ai-builder-error-handler.php';
		require_once AI_BUILDER_DIR . 'inc/classes/importer/class-ai-builder-importer-utils.php';
		require_once AI_BUILDER_DIR . 'inc/classes/importer/class-ai-builder-options-import.php';
		require_once AI_BUILDER_DIR . 'inc/classes/importer/class-ai-builder-fse-importer.php';
	}

	/**
	 * Get the API URL.
	 *
	 * @since  1.0.0
	 */
	public static function get_api_domain() {
		return defined( 'STARTER_TEMPLATES_REMOTE_URL' ) ? STARTER_TEMPLATES_REMOTE_URL : apply_filters( 'astra_sites_api_domain', 'https://websitedemos.net/' );
	}

	/**
	 * Setter for $api_url
	 *
	 * @since  1.0.0
	 */
	public function set_api_url() {
		$this->api_domain = trailingslashit( self::get_api_domain() );
		$this->api_url    = apply_filters( 'astra_sites_api_url', $this->api_domain . 'wp-json/wp/v2/' );

		$this->search_analytics_url = apply_filters( 'astra_sites_search_api_url', $this->api_domain . 'wp-json/analytics/v2/search/' );
		$this->import_analytics_url = apply_filters( 'astra_sites_import_analytics_api_url', $this->api_domain . 'wp-json/analytics/v2/import/' );
	}

	/**
	 * Flush Rewrite rules
	 *
	 * @since  1.0.36
	 * @return void
	 */
	public function permalink_update_after_import() {
		if ( 'no' === get_option( 'astra-site-permalink-update-status', '' ) ) {
			// Flush the rewrite rules to apply the changes.
			flush_rewrite_rules();
			delete_option( 'astra-site-permalink-update-status' );
		}
	}
}

/**
 * Kicking this off by calling 'get_instance()' method
 */
Ai_Builder_Importer::Instance();
