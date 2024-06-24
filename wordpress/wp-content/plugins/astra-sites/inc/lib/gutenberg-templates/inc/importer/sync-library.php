<?php
/**
 * Sync Library
 *
 * @package Ast Block Templates
 * @since 1.0.0
 */

namespace Gutenberg_Templates\Inc\Importer;

use Gutenberg_Templates\Inc\Traits\Instance;
use Gutenberg_Templates\Inc\Traits\Helper;
use Gutenberg_Templates\Inc\Importer\Plugin;

/**
 * Sync Library
 *
 * @since 1.0.0
 */
class Sync_Library {

	use Instance;

	/**
	 * Catch the latest checksums
	 *
	 * @since 1.1.0
	 * @access public
	 * @var string Last checksums.
	 */
	public $last_export_checksums;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'wp_ajax_ast-block-templates-check-sync-library-status', array( $this, 'sync_via_ajax' ) );
		add_action( 'wp_ajax_ast-block-templates-import-blocks', array( $this, 'ajax_import_blocks' ) );
		add_action( 'sync_blocks', array( $this, 'sync_blocks' ) );
		add_action( 'wp_ajax_ast-block-templates-get-sites-request-count', array( $this, 'ajax_sites_requests_count' ) );
		add_action( 'wp_ajax_ast-block-templates-import-sites', array( $this, 'ajax_import_sites' ) );
	}

	/**
	 * Get Custimizer CSS.
	 *
	 * @return void
	 */
	public function get_server_astra_customizer_css() {

		Helper::instance()->ast_block_templates_log( 'BLOCK: Getting Server Custimizer CSS' );

		$api_args = array(
			'timeout' => 50,
		);

		$query_args = array();
		$api_url = add_query_arg( $query_args, AST_BLOCK_TEMPLATES_LIBRARY_URL . 'wp-json/astra-blocks/v2/get-customizer-css' );

		Helper::instance()->ast_block_templates_log( 'BLOCK: ' . $api_url );

		$response = wp_safe_remote_get( $api_url, $api_args );

		if ( ! is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) === 200 ) {
			$res_data = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( isset( $res_data['data']['customizer_css'] ) ) {
				Helper::instance()->update_json_file( 'ast-block-templates-customizer-css', $res_data['data']['customizer_css'] );
				do_action( 'ast_block_templates_customizer_css', $res_data['data']['customizer_css'] );
			}
		}
	}

	/**
	 * Get Spectra Common CSS.
	 *
	 * @return string
	 */
	public function get_server_spectra_common_css() {

		Helper::instance()->ast_block_templates_log( 'BLOCK: Getting Spectra Common CSS' );

		$common_css_content = get_option( 'ast-block-templates-spectra-common-styles', '' );

		if ( ! empty( $common_css_content ) ) {
			return $common_css_content;
		}

		$api_args = array(
			'timeout' => 50,
		);

		$query_args = array();
		$api_url = add_query_arg( $query_args, AST_BLOCK_TEMPLATES_LIBRARY_URL . 'wp-json/astra-blocks/v2/spectra-common-styles' );

		Helper::instance()->ast_block_templates_log( 'BLOCK: ' . $api_url );

		$response = wp_safe_remote_get( $api_url, $api_args );

		if ( ! is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) === 200 ) {
			$res_data = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( isset( $res_data['data']['spectra-common-styles'] ) ) {
				update_option( 'ast-block-templates-spectra-common-styles', $res_data['data']['spectra-common-styles'] );
				return $res_data['data']['spectra-common-styles'];
			}
		}

		return '';
	}

	/**
	 * Start Importer
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function setup_templates() {
		$is_fresh_site = get_option( 'ast_block_templates_fresh_site', 'yes' );

		if ( 'yes' === $is_fresh_site ) {
			$this->set_default_assets();
			update_option( 'ast_block_templates_fresh_site', 'no' );
		}

		$this->process_sync();
	}

	/**
	 * Set default assets
	 *
	 * @since 1.0.2
	 */
	public function set_default_assets() {

		$list_files = $this->get_default_assets();
		$files    = array();

		foreach ( $list_files as $key => $file_name ) {

			$content = '';

			$file_data = array(
				'file_name' => $file_name . '.json',
				'file_content' => $content,
				'file_base' => AST_BLOCK_TEMPLATES_JSON_DIR,
			);
			
			array_push( $files, $file_data );
		}

		Helper::instance()->create_files( $files );
	}

	/**
	 * Process Import
	 *
	 * @since 1.0.6
	 *
	 * @return void
	 */
	public function process_sync() {

		if ( apply_filters( 'ast_block_templates_disable_auto_sync', false ) ) {
			return;
		}

		// Check if last sync and this sync has a gap of 24 hours.
		$last_check_time = (int) get_option( 'ast-block-templates-last-export-checksums-time', 0 );
		if ( ( time() - $last_check_time ) < 86400 ) {
			return;
		}

		update_option( 'ast_blocks_sync_in_progress', 'yes', 'no' );
		$this->sync_blocks();
	}

	/**
	 * Sync Blocks
	 * Sync blocks from library.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function sync_blocks() {
		// Check checksum difference, if found get blocks categories, count and blocks.
		$result_data = $this->check_checksum_and_get_blocks_data();

		if ( empty( $result_data ) ) {
			Helper::instance()->ast_block_templates_log( 'Blocks are up to date.' );
			update_option( 'ast-block-templates-last-export-checksums-time', time() );
			update_option( 'ast_blocks_sync_in_progress', 'no', 'no' );
			return;
		}
		$this->process_data_sync( $result_data );
		$this->update_latest_checksums( $result_data['checksum'] );
		$this->get_server_astra_customizer_css();
	}

	/**
	 * Handle Sync API Response
	 *
	 * @since 2.0.0
	 * @return array
	 */
	public function check_checksum_and_get_blocks_data() {

		$old_last_export_checksums = Helper::instance()->get_last_exported_checksum();

		$api_args = array(
			'timeout' => 100,
		);

		$query_args = array(
			'blocks_category'    => array(
				'per_page'   => 100,
				'_fields'    => 'id,count,name,slug,parent',
				'hide_empty' => true,
			),
			'blocks_pages' => array(
				'page_builder' => 'gutenberg',
				'wireframe'    => 'yes',
				'per_page' => 30,
			),
			'last_export_checksums' => urlencode( $old_last_export_checksums ),
		);

		$api_url = add_query_arg( $query_args, AST_BLOCK_TEMPLATES_LIBRARY_URL . 'wp-json/astra-sites/v2/checksum/' );

		$response = wp_safe_remote_get( $api_url, $api_args );

		if ( ! is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) === 200 ) {
			$result = json_decode( wp_remote_retrieve_body( $response ), true );
			$result_data = isset( $result['data'] ) ? $result['data'] : '';
			return $result_data;
		}
		return array();
	}

	/**
	 * Process Data Sync
	 *
	 * @since 2.0.0
	 * @param  array $data Data to process.
	 * @return void
	 */
	public function process_data_sync( $data ) {
		Helper::instance()->ast_block_templates_log( 'Sync process for Gutenberg Blocks has started.' );

		if ( isset( $data['categories'] ) && ! empty( $data['categories'] ) ) {
			Helper::instance()->ast_block_templates_log( 'CATEGORY: Storing in ast-block-templates-categories.json' );
			Helper::instance()->update_json_file( 'ast-block-templates-categories', $data['categories'] );
			do_action( 'ast_block_templates_sync_categories', $data['categories'] );
		}

		if ( isset( $data['count']['pages'] ) && ! empty( $data['count']['pages'] ) ) {
			Helper::instance()->ast_block_templates_log( 'BLOCK: Requests count ' . $data['count']['pages'] );
			Helper::instance()->update_json_file( 'ast-block-templates-block-requests', $data['count']['pages'] );
			do_action( 'ast_block_templates_sync_blocks_requests', $data['count']['pages'] );
		}

		for ( $i = 1; $i <= $data['count']['pages']; $i++ ) {
			$this->import_blocks( $i );
		}

		$sites = $this->get_total_sites_count();

		for ( $i = 1; $i <= $sites; $i++ ) {
			$this->import_sites( $i );
		}

		Helper::instance()->ast_block_templates_log( 'Sync process for Gutenberg Blocks is done.' );
	}

	/**
	 * Json Files Names.
	 *
	 * @since 1.0.1
	 * @return array
	 */
	public function get_default_assets() {
		return array(
			'ast-block-templates-categories',
			'ast-block-templates-blocks-1',
			'ast-block-templates-blocks-2',
			'ast-block-templates-blocks-3',
			'ast-block-templates-blocks-4',
			'ast-block-templates-blocks-5',
			'ast-block-templates-blocks-6',
			'ast-block-templates-blocks-7',
			'ast-block-templates-block-requests',
			'ast-block-templates-sites-1',
			'ast-block-templates-sites-2',
			'ast-block-templates-sites-3',
			'ast-block-templates-sites-4',
			'ast-block-templates-sites-5',
			'ast-block-templates-sites-6',
			'ast-block-templates-sites-7',
			'ast-block-templates-site-requests',
			'ast-block-templates-last-export-checksums',
			'ast-block-templates-customizer-css',
		);
	}

	/**
	 * Update Library
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function sync_via_ajax() {

		if ( ! Helper::instance()->ast_block_templates_doing_wp_cli() ) {

			if ( ! current_user_can( 'manage_ast_block_templates' ) ) {
				wp_send_json_error( __( 'You are not allowed to perform this action', 'astra-sites' ) );
			}
			// Verify Nonce.
			check_ajax_referer( 'ast-block-templates-ajax-nonce', '_ajax_nonce' );
		}

		$result_data = $this->check_checksum_and_get_blocks_data();

		if ( empty( $result_data ) ) {
			Helper::instance()->ast_block_templates_log( 'Blocks are up to date.' );
			wp_send_json_success(
				array(
					'message' => 'Complete',
					'status'  => true,
					'data'    => 'updated',
				)
			);
		}
		$this->get_server_astra_customizer_css();
		if ( isset( $result_data['categories'] ) && ! empty( $result_data['categories'] ) ) {
		
			Helper::instance()->ast_block_templates_log( 'CATEGORY: Storing in option ast-block-templates-categories.json' );
			Helper::instance()->update_json_file( 'ast-block-templates-categories', $result_data['categories'] );
			do_action( 'ast_block_templates_sync_categories', $result_data['categories'] );
		}
		if ( isset( $result_data['count']['pages'] ) && ! empty( $result_data['count']['pages'] ) ) {
			Helper::instance()->ast_block_templates_log( 'BLOCK: Requests count ' . $result_data['count']['pages'] );
			Helper::instance()->update_json_file( 'ast-block-templates-block-requests', $result_data['count']['pages'] );
			do_action( 'ast_block_templates_sync_blocks_requests', $result_data['count']['pages'] );
		}
		$this->update_latest_checksums( $result_data['checksum'] );
		wp_send_json_success(
			array(
				'message' => 'in-progress',
				'status'  => true,
				'data'    => $result_data['count'],
			)
		);
	}

	/**
	 * Get Last Exported Checksum Status
	 *
	 * @since 1.0.0
	 * @return string Checksums Status.
	 */
	public function get_last_export_checksums() {

		$old_last_export_checksums = Helper::instance()->get_last_exported_checksum();

		$new_last_export_checksums = $this->set_last_export_checksums();

		$checksums_status = 'no';

		if ( empty( $old_last_export_checksums ) ) {
			$checksums_status = 'yes';
		}

		if ( $new_last_export_checksums !== $old_last_export_checksums ) {
			$checksums_status = 'yes';
		}

		return apply_filters( 'ast_block_templates_checksums_status', $checksums_status );
	}

	/**
	 * Set Last Exported Checksum
	 *
	 * @since 1.0.0
	 * @return string Checksums Status.
	 */
	public function set_last_export_checksums() {

		if ( ! empty( $this->last_export_checksums ) ) {
			return $this->last_export_checksums;
		}

		$api_args = array(
			'timeout' => 60,
		);

		$query_args = array();

		$api_url = add_query_arg( $query_args, AST_BLOCK_TEMPLATES_LIBRARY_URL . 'wp-json/astra-sites/v1/get-last-export-checksums/' );

		$response = wp_safe_remote_get( $api_url, $api_args );

		if ( ! is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) === 200 ) {
			$result = json_decode( wp_remote_retrieve_body( $response ), true );

			// Set last export checksums.
			if ( ! empty( $result['last_export_checksums'] ) ) {
				update_option( 'ast-block-templates-last-export-checksums-latest', $result['last_export_checksums'], 'no' );

				$this->last_export_checksums = $result['last_export_checksums'];
			}
		}

		return $this->last_export_checksums;
	}

	/**
	 * Update Latest Checksums
	 *
	 * Store latest checksum after batch complete.
	 *
	 * @param string $new_checksum New Checksum.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function update_latest_checksums( $new_checksum ) {
		Helper::instance()->update_json_file( 'ast-block-templates-last-export-checksums', $new_checksum );
		update_option( 'ast-block-templates-last-export-checksums-time', time(), 'no' );
		update_option( 'ast_blocks_sync_in_progress', 'no', 'no' );
		do_action( 'ast_block_templates_sync_export_checksum', $new_checksum );
	}

	/**
	 * Import Categories
	 *
	 * @since 1.0.3
	 * @return void
	 */
	public function ajax_import_categories() {

		if ( ! current_user_can( 'manage_ast_block_templates' ) ) {
			wp_send_json_error( __( 'You are not allowed to perform this action', 'astra-sites' ) );
		}
		// Verify Nonce.
		check_ajax_referer( 'ast-block-templates-ajax-nonce', '_ajax_nonce' );

		$categories = $this->import_categories();
		wp_send_json_success(
			array(
				'message' => 'Success imported categories',
				'status'  => true,
				'data'    => $categories,
			)
		);

	}

	/**
	 * Import Blocks
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ajax_import_blocks() {

		if ( ! current_user_can( 'manage_ast_block_templates' ) ) {
			wp_send_json_error( __( 'You are not allowed to perform this action', 'astra-sites' ) );
		}
		// Verify Nonce.
		check_ajax_referer( 'ast-block-templates-ajax-nonce', '_ajax_nonce' );

		$page_no = isset( $_POST['page_no'] ) ? absint( $_POST['page_no'] ) : '';
		if ( $page_no ) {
			$sites_and_pages = $this->import_blocks( $page_no );

			$data = array(
				'message' => 'Success imported sites for page ' . $page_no,
				'status'  => true,
				'data'    => array(),
			);

			if ( isset( $_POST['total'] ) && $_POST['total'] === $_POST['page_no'] ) {
				$data['data']['allBlocks'] = Plugin::instance()->get_all_blocks();
				$data['data']['categories'] = Helper::instance()->get_block_template_category();
			}

			wp_send_json_success(
				$data
			);
		}

		wp_send_json_error(
			array(
				'message' => 'Failed imported blocks for page ' . $page_no,
				'status'  => false,
				'data'    => '',
			)
		);
	}

	/**
	 * Blocks Requests Count
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ajax_blocks_requests_count() {

		if ( ! current_user_can( 'manage_ast_block_templates' ) ) {
			wp_send_json_error( __( 'You are not allowed to perform this action', 'astra-sites' ) );
		}
		// Verify Nonce.
		check_ajax_referer( 'ast-block-templates-ajax-nonce', '_ajax_nonce' );

		// Get count.
		$total_requests = $this->get_total_blocks_requests();
		$this->get_server_astra_customizer_css();
		if ( $total_requests ) {
			wp_send_json_success(
				array(
					'message' => 'Success',
					'status'  => true,
					'data'    => $total_requests,
				)
			);
		}

		wp_send_json_success(
			array(
				'message' => 'Failed',
				'status'  => false,
				'data'    => $total_requests,
			)
		);
	}

	/**
	 * Get Blocks Total Requests
	 *
	 * @return integer
	 */
	public function get_total_blocks_requests() {

		Helper::instance()->ast_block_templates_log( 'BLOCK: Getting Total Blocks' );

		$api_args = array(
			'timeout' => 60,
		);

		$query_args = apply_filters(
			'ast_block_templates_get_blocks_count_args',
			array(
				'page_builder' => 'gutenberg',
				'wireframe'    => 'yes',
				'per_page' => 30,
			)
		);

		$api_url = add_query_arg( $query_args, AST_BLOCK_TEMPLATES_LIBRARY_URL . 'wp-json/astra-blocks/v2/get-blocks-count/' );

		Helper::instance()->ast_block_templates_log( 'BLOCK: ' . $api_url );

		$response = wp_safe_remote_get( $api_url, $api_args );

		if ( ! is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) === 200 ) {
			$total_requests = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( isset( $total_requests['pages'] ) ) {
				Helper::instance()->ast_block_templates_log( 'BLOCK: Requests count ' . $total_requests['pages'] );
				Helper::instance()->update_json_file( 'ast-block-templates-block-requests', $total_requests['pages'] );
				do_action( 'ast_block_templates_sync_blocks_requests', $total_requests['pages'] );
				return $total_requests['pages'];
			}
		}

	}

	/**
	 * Import Categories
	 *
	 * @since 1.0.3
	 * @return void
	 */
	public function import_categories() {

		Helper::instance()->ast_block_templates_log( 'CATEGORY:Importing categories..' );
		$api_args = array(
			'timeout' => 30,
		);

		$query_args = apply_filters(
			'ast_block_templates_get_category_args',
			array(
				'per_page'   => 100,
				'_fields'    => 'id,count,name,slug,parent',
				'hide_empty' => true,
			)
		);

		$api_url = add_query_arg( $query_args, AST_BLOCK_TEMPLATES_LIBRARY_URL . 'wp-json/wp/v2/blocks-category/' );

		$response = wp_safe_remote_get( $api_url, $api_args );

		if ( ! is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) === 200 ) {
			$all_categories = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( isset( $all_categories['code'] ) ) {
				$message = isset( $all_categories['message'] ) ? $all_categories['message'] : '';
				if ( ! empty( $message ) ) {
					Helper::instance()->ast_block_templates_log( 'CATEGORY:HTTP Request Error: ' . $message );
				} else {
					Helper::instance()->ast_block_templates_log( 'CATEGORY:HTTP Request Error!' );
				}
			} else {

				Helper::instance()->ast_block_templates_log( 'CATEGORY:Storing in file ast-block-templates-categories' );
				Helper::instance()->update_json_file( 'ast-block-templates-categories', $all_categories );

				do_action( 'ast_block_templates_sync_categories', $all_categories );

				if ( Helper::instance()->ast_block_templates_doing_wp_cli() ) {
					Helper::instance()->ast_block_templates_log( 'CATEGORY:Generating ast-block-templates-categories.json file' );
				}
			}
		} else {
			Helper::instance()->ast_block_templates_log( 'CATEGORY:API Error: ' . $response->get_error_message() );
		}

		Helper::instance()->ast_block_templates_log( 'CATEGORY:Completed category import.' );
	}

	/**
	 * Import Blocks
	 *
	 * @since 1.0.0
	 * @param  integer $page Page number.
	 * @return void
	 */
	public function import_blocks( $page = 1 ) {

		Helper::instance()->ast_block_templates_log( 'BLOCK: Importing request ' . $page . ' ..' );
		$api_args   = array(
			'timeout' => 30,
		);
		$all_blocks = array();

		$query_args = apply_filters(
			'ast_block_templates_blocks_args',
			array(
				'page_builder' => 'gutenberg',
				'per_page'     => 30,
				'page'         => $page,
				'wireframe'    => 'yes',
			)
		);

		$api_url = add_query_arg( $query_args, AST_BLOCK_TEMPLATES_LIBRARY_URL . 'wp-json/astra-blocks/v2/blocks/' );

		Helper::instance()->ast_block_templates_log( 'BLOCK: ' . $api_url );

		$response = wp_safe_remote_get( $api_url, $api_args );

		if ( ! is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) === 200 ) {
			$all_blocks = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( isset( $all_blocks['code'] ) ) {
				$message = isset( $all_blocks['message'] ) ? $all_blocks['message'] : '';
				if ( ! empty( $message ) ) {
					Helper::instance()->ast_block_templates_log( 'BLOCK: HTTP Request Error: ' . $message );
				} else {
					Helper::instance()->ast_block_templates_log( 'BLOCK: HTTP Request Error!' );
				}
			} else {
				$file_name = 'ast-block-templates-blocks-' . $page;
				Helper::instance()->ast_block_templates_log( 'BLOCK: Storing in file ' . $file_name );

				Helper::instance()->update_json_file( $file_name, $all_blocks );

				if ( Helper::instance()->ast_block_templates_doing_wp_cli() ) {
					do_action( 'ast_block_templates_sync_blocks', $page, $all_blocks );
					Helper::instance()->ast_block_templates_log( 'BLOCK: Genearting ' . $file_name . '.json file' );
				}
			}
		} else {
			Helper::instance()->ast_block_templates_log( 'BLOCK: API Error: ' . $response->get_error_message() );
		}

		Helper::instance()->ast_block_templates_log( 'BLOCK: Completed request ' . $page );
	}

	/**
	 * Get Sites Total Requests
	 *
	 * @return integer
	 */
	public function get_total_sites_count() {

		Helper::instance()->ast_block_templates_log( 'SITE: Getting Total Sites' );

		$api_args = array(
			'timeout' => 60,
		);

		$query_args = apply_filters(
			'ast_block_templates_get_total_pages_args',
			array(
				'page_builder' => 'gutenberg',
				'per_page' => 30,
			)
		);

		$api_url = esc_url_raw( add_query_arg( $query_args, AST_BLOCK_TEMPLATES_LIBRARY_URL . 'wp-json/astra-sites/v1/get-total-pages/' ) );

		Helper::instance()->ast_block_templates_log( 'SITE: ' . $api_url );

		$response = wp_safe_remote_get( $api_url, $api_args );

		if ( ! is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) === 200 ) {
			$total_requests = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( isset( $total_requests['pages'] ) ) {
				Helper::instance()->ast_block_templates_log( 'SITE: Request count ' . $total_requests['pages'] );
				Helper::instance()->update_json_file( 'ast-block-templates-site-requests', $total_requests['pages'] );

				do_action( 'ast_block_templates_sync_get_total_pages', $total_requests['pages'] );
				return $total_requests['pages'];
			}
		}
		// Return a default value if conditions are not met.
		return 0;
	}

	/**
	 * Import Sites
	 *
	 * @since 1.0.0
	 * @param  integer $page Page number.
	 * @return void
	 */
	public function import_sites( $page = 1 ) {

		Helper::instance()->ast_block_templates_log( 'SITE: Importing request ' . $page . ' ..' );
		$api_args   = array(
			'timeout' => 30,
		);
		$all_blocks = array();

		$query_args = apply_filters(
			'ast_block_templates_get_sites_and_pages_args',
			array(
				'per_page'     => 30,
				'page'         => $page,
				'page-builder' => 'gutenberg',
			)
		);

		$api_url = esc_url_raw( add_query_arg( $query_args, AST_BLOCK_TEMPLATES_LIBRARY_URL . 'wp-json/astra-sites/v1/sites-and-pages/' ) );

		Helper::instance()->ast_block_templates_log( 'SITE: ' . $api_url );

		$response = wp_safe_remote_get( $api_url, $api_args );

		if ( ! is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) === 200 ) {
			$all_blocks = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( isset( $all_blocks['code'] ) ) {
				$message = isset( $all_blocks['message'] ) ? $all_blocks['message'] : '';
				if ( ! empty( $message ) ) {
					Helper::instance()->ast_block_templates_log( 'SITE: HTTP Request Error: ' . $message );
				} else {
					Helper::instance()->ast_block_templates_log( 'SITE: HTTP Request Error!' );
				}
			} else {

				$file_name = 'ast-block-templates-sites-' . $page;
				Helper::instance()->ast_block_templates_log( 'SITE: Storing in file ' . $file_name );
				Helper::instance()->update_json_file( $file_name, $all_blocks );

				do_action( 'ast_block_templates_sync_sites', $page, $all_blocks );

				if ( Helper::instance()->ast_block_templates_doing_wp_cli() ) {
					Helper::instance()->ast_block_templates_log( 'SITE: Generating ' . $file_name . '.json file' );
				}
			}
		} else {
			Helper::instance()->ast_block_templates_log( 'SITE: API Error: ' . $response->get_error_message() );
		}

		Helper::instance()->ast_block_templates_log( 'SITE: Completed request ' . $page );
	}

	/**
	 * Blocks Requests Count
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function ajax_sites_requests_count() {

		if ( ! current_user_can( 'manage_ast_block_templates' ) ) {
			wp_send_json_error( __( 'You are not allowed to perform this action', 'astra-sites' ) );
		}
		// Verify Nonce.
		check_ajax_referer( 'ast-block-templates-ajax-nonce', '_ajax_nonce' );

		// Get count.
		$total_requests = $this->get_total_sites_count();
		if ( $total_requests ) {
			wp_send_json_success(
				array(
					'message' => 'Success',
					'status'  => true,
					'data'    => $total_requests,
				)
			);
		}

		wp_send_json_success(
			array(
				'message' => 'Failed',
				'status'  => false,
				'data'    => $total_requests,
			)
		);
	}

	/** 
	 * Import Sites
	 *
	 * @since 2.1.0
	 * @return void
	 */
	public function ajax_import_sites() {

		if ( ! current_user_can( 'manage_ast_block_templates' ) ) {
			wp_send_json_error( __( 'You are not allowed to perform this action', 'astra-sites' ) );
		}
		// Verify Nonce.
		check_ajax_referer( 'ast-block-templates-ajax-nonce', '_ajax_nonce' );

		$page_no = isset( $_POST['page_no'] ) ? absint( $_POST['page_no'] ) : '';
		if ( $page_no ) {
			$this->import_sites( $page_no );
			$data = array(
				'message' => 'Success imported sites for page ' . $page_no,
				'status'  => true,
				'data'    => array(),
			);

			if ( isset( $_POST['total'] ) && $_POST['total'] === $_POST['page_no'] ) {
				$data['data']['allBlocks'] = Plugin::instance()->get_all_blocks();
				$data['data']['categories'] = Helper::instance()->get_block_template_category();
				$data['data']['allSites'] = Plugin::instance()->get_all_sites();
			}

			wp_send_json_success(
				$data
			);
		}

		wp_send_json_error(
			array(
				'message' => 'Failed imported sites for page ' . $page_no,
				'status'  => false,
				'data'    => '',
			)
		);
	}

}
