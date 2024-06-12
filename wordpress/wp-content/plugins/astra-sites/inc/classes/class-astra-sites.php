<?php
/**
 * Astra Sites
 *
 * @since  1.0.0
 * @package Astra Sites
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Astra_Sites' ) ) :

	/**
	 * Astra_Sites
	 */
	class Astra_Sites {

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
		 * API URL which is used to get the response from Pixabay.
		 *
		 * @since  2.0.0
		 * @var (String) URL
		 */
		public $pixabay_url;

		/**
		 * API Key which is used to get the response from Pixabay.
		 *
		 * @since  2.0.0
		 * @var (String) URL
		 */
		public $pixabay_api_key;

		/**
		 * Instance of Astra_Sites
		 *
		 * @since  1.0.0
		 * @var (self) Astra_Sites
		 */
		private static $instance = null;

		/**
		 * Localization variable
		 *
		 * @since  2.0.0
		 * @var (Array) $local_vars
		 */
		public static $local_vars = array();

		/**
		 * Localization variable
		 *
		 * @since  2.0.0
		 * @var (Array) $wp_upload_url
		 */
		public $wp_upload_url = '';

		/**
		 * Ajax
		 *
		 * @since  2.6.20
		 * @var (Array) $ajax
		 */
		private $ajax = array();

		/**
		 * Instance of Astra_Sites.
		 *
		 * @since  1.0.0
		 *
		 * @return self Class object.
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @since  1.0.0
		 */
		private function __construct() {
			if ( ! class_exists( 'XMLReader' ) ) {
				add_action( 'admin_notices', array( $this, 'xml_reader_notice' ) );
				add_filter( 'ai_builder_load_library', '__return_false' );
				return;
			}

			$this->set_api_url();
			$this->includes();
			add_action( 'plugin_action_links_' . ASTRA_SITES_BASE, array( $this, 'action_links' ) );
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ), 99 );
			add_action( 'elementor/editor/footer', array( $this, 'insert_templates' ) );
			add_action( 'admin_footer', array( $this, 'insert_image_templates' ) );
			add_action( 'customize_controls_print_footer_scripts', array( $this, 'insert_image_templates' ) );
			add_action( 'wp_footer', array( $this, 'insert_image_templates_bb_and_brizy' ) );
			add_action( 'elementor/editor/footer', array( $this, 'register_widget_scripts' ), 99 );
			add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'popup_styles' ) );
			add_action( 'elementor/preview/enqueue_styles', array( $this, 'popup_styles' ) );
			add_action( 'astra_sites_after_plugin_activation', array( $this, 'disable_wp_forms_redirect' ) );
			add_action( 'astra_notice_before_markup', array( $this, 'notice_assets' ) );
			add_action( 'load-index.php', array( $this, 'admin_dashboard_notices' ) );
			add_action( 'admin_notices', array( $this, 'check_filesystem_access_notice' ) );

			// AJAX.
			$this->ajax = array(
				'astra-sites-create-template' => 'create_template',
				'astra-sites-create-image' => 'create_image',
				'astra-sites-search-images' => 'search_images',
				'astra-sites-getting-started-notice' => 'getting_started_notice',
				'astra-sites-favorite' => 'add_to_favorite',
				'astra-sites-api-request' => 'api_request',
				'astra-sites-elementor-api-request' => 'elementor_api_request',
				'astra-sites-elementor-flush-request' => 'elementor_flush_request',
				'astra-page-elementor-insert-page' => 'elementor_process_import_for_page',
				'astra-sites-update-subscription' => 'update_subscription',
				'astra-sites-update-analytics' => 'update_analytics',
				'astra-sites-generate-analytics-lead' => 'push_to_import_analytics',
			);

			foreach ( $this->ajax as $ajax_hook => $ajax_callback ) {
				add_action( 'wp_ajax_' . $ajax_hook, array( $this, $ajax_callback ) );
			}

			add_action( 'delete_attachment', array( $this, 'delete_astra_images' ) );
			add_filter( 'heartbeat_received', array( $this, 'search_push' ), 10, 2 );
			add_filter( 'status_header', array( $this, 'status_header' ), 10, 4 );
			add_filter( 'wp_php_error_message', array( $this, 'php_error_message' ), 10, 2 );
			add_filter( 'wp_import_post_data_processed', array( $this, 'wp_slash_after_xml_import' ), 99, 2 );
			
			add_filter( 'ast_block_templates_authorization_url_param', array( $this, 'add_auth_url_param' ) );
			add_action( 'admin_head', array( $this, 'add_custom_admin_css' ) );
			add_filter( 'zip_ai_modules', array( $this, 'enable_zip_ai_copilot' ), 20, 1 );
		}

		/**
		 * Display notice if XML Class Reader is not Available.
		 *
		 * @return void
		 */
		public function xml_reader_notice() {
			$plugin_name = defined( 'ASTRA_PRO_SITES_NAME' ) ? 'Premium Starter Templates' : 'Starter Templates';
			?>
			<div class="error">	
			<p>
			<?php
			/* Translators: %s Plugin Name. */ 
			echo esc_html( sprintf( __( '%s: XMLReader extension is missing! To import templates, please get in touch with your hosting provider to enable this extension.', 'astra-sites' ), $plugin_name ) );
			?>
			</p>
			</div>
			<?php
		}
		
		/**
		 * Set reset data
		 * Note: This function can be deleted after a few releases since we are performing the delete operation in chunks.
		 * 
		 * @return array<string, array>
		 */
		public function get_reset_data() {

			if ( wp_doing_ajax() ) {
				check_ajax_referer( 'astra-sites', '_ajax_nonce' );

				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( __( 'You are not allowed to perform this action', 'astra-sites' ) );
				}
			}

			Astra_Sites_Error_Handler::get_instance()->start_error_handler();

			$data = array(
				'reset_posts'    => astra_sites_get_reset_post_data(),
				'reset_wp_forms' => astra_sites_get_reset_form_data(),
				'reset_terms'    => astra_sites_get_reset_term_data(),
			);

			Astra_Sites_Error_Handler::get_instance()->stop_error_handler();

			if ( wp_doing_ajax() ) {
				wp_send_json_success( $data );
			}

			return $data;
		}

		/**
		 * Enable ZipAI Copilot.
		 *
		 * @since 3.5.0
		 *
		 * @param array $modules module array.
		 * @return boolean
		 */
		public function enable_zip_ai_copilot( $modules ) {

			if ( 'active' === $this->get_plugin_status( 'ultimate-addons-for-gutenberg/ultimate-addons-for-gutenberg.php' ) ) {
				return $modules;
			}

			// Ensure $modules is an array.
			$modules = is_array( $modules ) ? $modules : array();

			// Update AI Design Copilot module status.
			$modules['ai_design_copilot'] = array(
				'status' => 'enabled',
			);

			$modules['ai_assistant'] = array(
				'status' => 'enabled',
			);

			return $modules;
		}

		/** 
		 *  Set adding AI icon to WordPress menu.
		 * 
		 * @return void
		 */
		public function add_custom_admin_css() {
			$icon = ASTRA_SITES_URI . 'inc/assets/images/vector-ai.svg';
			?>
			<style type="text/css">
				a[href="themes.php?page=starter-templates"]::after {
					content: url("<?php echo esc_url( $icon ); ?>");
					position: absolute;
					margin-left: 5px;
					height: 18px;
					width: 18px;
				}	
				a[href="themes.php?page=ai-builder"] {
					display: none !important;
				}
			</style>
			<?php
		}

		/**
		 * Set plugin param for auth URL.
		 *
		 * @param array $url_param url parameters.
		 *
		 * @since  3.5.0
		 */
		public function add_auth_url_param( $url_param ) {

			$url_param['plugin'] = 'starter-templates';

			return $url_param;
		}

		/**
		 * Get plugin status
		 *
		 * @since 3.5.0
		 *
		 * @param  string $plugin_init_file Plguin init file.
		 * @return string
		 */
		public function get_plugin_status( $plugin_init_file ) {

			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			$installed_plugins = get_plugins();

			if ( ! isset( $installed_plugins[ $plugin_init_file ] ) ) {
				return 'not-installed';
			} elseif ( is_plugin_active( $plugin_init_file ) ) {
				return 'active';
			} else {
				return 'inactive';
			}
		}



		/**
		 * Add slashes while importing the XML with WordPress Importer v2.
		 *
		 * @param array $postdata Processed Post data.
		 * @param array $data Post data.
		 */
		public function wp_slash_after_xml_import( $postdata, $data ) {
			return wp_slash( $postdata );
		}

		/**
		 * Check is Starter Templates AJAX request.
		 *
		 * @since 2.7.0
		 * @return boolean
		 */
		public function is_starter_templates_request() {

			if ( isset( $_REQUEST['action'] ) && in_array( $_REQUEST['action'], array_keys( $this->ajax ), true ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Fetching GET parameter, no nonce associated with this action.
				return true;
			}

			return false;
		}

		/**
		 * Filters the message that the default PHP error template displays.
		 *
		 * @since 2.7.0
		 *
		 * @param string $message HTML error message to display.
		 * @param array  $error   Error information retrieved from `error_get_last()`.
		 * @return mixed
		 */
		public function php_error_message( $message, $error ) {

			if ( ! $this->is_starter_templates_request() ) {
				return $message;
			}

			if ( empty( $error ) ) {
				return $message;
			}

			$message = isset( $error['message'] ) ? $error['message'] : $message;

			return $message;
		}

		/**
		 * Filters an HTTP status header.
		 *
		 * @since 2.6.20
		 *
		 * @param string $status_header HTTP status header.
		 * @param int    $code          HTTP status code.
		 * @param string $description   Description for the status code.
		 * @param string $protocol      Server protocol.
		 *
		 * @return mixed
		 */
		public function status_header( $status_header, $code, $description, $protocol ) {

			if ( ! $this->is_starter_templates_request() ) {
				return $status_header;
			}

			$error = error_get_last();
			if ( empty( $error ) ) {
				return $status_header;
			}

			$message = isset( $error['message'] ) ? $error['message'] : $description;

			return "$protocol $code $message";
		}

		/**
		 * Update Analytics Optin/Optout
		 */
		public function update_analytics() {

			check_ajax_referer( 'astra-sites', '_ajax_nonce' );

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( 'You are not allowed to perform this action', 'astra-sites' );
			}

			$optin_answer = isset( $_POST['data'] ) ? sanitize_text_field( $_POST['data'] ) : 'no';
			$optin_answer = 'yes' === $optin_answer ? 'yes' : 'no';

			update_site_option( 'bsf_analytics_optin', $optin_answer );

			wp_send_json_success();
		}

		/**
		 * Update Subscription
		 */
		public function update_subscription() {

			check_ajax_referer( 'astra-sites', '_ajax_nonce' );

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( 'You are not allowed to perform this action', 'astra-sites' );
			}

			$arguments = isset( $_POST['data'] ) ? array_map( 'sanitize_text_field', json_decode( stripslashes( $_POST['data'] ), true ) ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Already sanitized using `array_map` and `sanitize_text_field`.

			// Page Builder mapping.
			$page_builder_mapping      = array(
				'Elementor'      => 1,
				'Beaver Builder' => 2,
				'Brizy'          => 3,
				'Gutenberg'      => 4,
			);
			$arguments['PAGE_BUILDER'] = isset( $page_builder_mapping[ $arguments['PAGE_BUILDER'] ] ) ? $page_builder_mapping[ $arguments['PAGE_BUILDER'] ] : '';

			$url = apply_filters( 'astra_sites_subscription_url', $this->api_domain . 'wp-json/starter-templates/v1/subscribe/' );

			$args = array(
				'timeout' => 30,
				'body'    => $arguments,
			);

			$response = wp_safe_remote_post( $url, $args );

			if ( ! is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) === 200 ) {
				$response = json_decode( wp_remote_retrieve_body( $response ), true );

				// Successfully subscribed.
				if ( isset( $response['success'] ) && $response['success'] ) {
					update_user_meta( get_current_user_ID(), 'astra-sites-subscribed', 'yes' );
				}
			}
			wp_send_json_success( $response );

		}

		/**
		 * Push Data to Search API.
		 *
		 * @since  2.0.0
		 * @param array<string, string> $response Response data object.
		 * @param array<string, string> $data Data object.
		 *
		 * @return array Search response.
		 */
		public function search_push( $response, $data ) {

			// If we didn't receive our data, don't send any back.
			if ( empty( $data['ast-sites-search-terms'] ) ) {
				return $response;
			}

			$args = array(
				'timeout'   => 3,
				'blocking'  => true,
				'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
				'body'      => array(
					'search' => $data['ast-sites-search-terms'],
					'builder' => isset( $data['ast-sites-builder'] ) ? $data['ast-sites-builder'] : 'gutenberg',
					'url'    => esc_url( site_url() ),
					'type'   => 'astra-sites',
				),
			);
			$result                             = wp_safe_remote_post( $this->search_analytics_url, $args );
			$response['ast-sites-search-terms'] = wp_remote_retrieve_body( $result );

			return $response;
		}

		/**
		 * Push Data to Import Analytics API.
		 *
		 * @since  3.1.4
		 */
		public function push_to_import_analytics() {

			check_ajax_referer( 'astra-sites', '_ajax_nonce' );

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( 'You are not allowed to perform this action', 'astra-sites' );
			}

			$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;

			if ( 0 === $id ) {
				wp_send_json_error(
					array(
						/* translators: %d is the Template ID. */
						'message' => sprintf( __( 'Invalid Template ID - %d', 'astra-sites' ), $id ),
						'code'    => 'Error',
					)
				);
			}

			$data = array(
				'id' => $id,
				'import_attempts' => isset( $_POST['try-again-count'] ) ? absint( $_POST['try-again-count'] ) : 0,
				'import_status' => isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : 'true',
				'type' => isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : 'astra-sites',
				'page_builder' => isset( $_POST['page-builder'] ) ? sanitize_text_field( $_POST['page-builder'] ) : 'gutenberg',
			);

			$result = Astra_Sites_Reporting::get_instance()->report( $data );

			if ( $result['status'] ) {
				delete_option( 'astra_sites_has_sent_error_report' );
				delete_option( 'astra_sites_cached_import_error' );
				wp_send_json_success( $result['data'] );
			}

			wp_send_json_error( $result['data'] );
		}

		/**
		 * Before Astra Image delete, remove from options.
		 *
		 * @since  2.0.0
		 * @param int $id ID to deleting image.
		 * @return void
		 */
		public function delete_astra_images( $id ) {

			if ( ! $id ) {
				return;
			}

			$saved_images     = get_option( 'astra-sites-saved-images', array() );
			$astra_image_flag = get_post_meta( $id, 'astra-images', true );
			$astra_image_flag = (int) $astra_image_flag;
			if (
				'' !== $astra_image_flag &&
				is_array( $saved_images ) &&
				! empty( $saved_images ) &&
				in_array( $astra_image_flag, $saved_images )
			) {
				$flag_arr = array( $astra_image_flag );
				$saved_images = array_diff( $saved_images, $flag_arr );
				update_option( 'astra-sites-saved-images', $saved_images, 'no' );
			}
		}

		/**
		 * Elementor Batch Process via AJAX
		 *
		 * @since 2.0.0
		 */
		public function elementor_process_import_for_page() {

			// Verify Nonce.
			check_ajax_referer( 'astra-sites', '_ajax_nonce' );

			if ( ! current_user_can( 'edit_posts' ) ) {
				wp_send_json_error( __( 'You are not allowed to perform this action', 'astra-sites' ) );
			}

			$type = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : '';
			$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : '';

			$demo_data = get_option( 'astra_sites_import_elementor_data_' . $id, array() );

			if ( 'astra-blocks' == $type ) {
				$api_url = trailingslashit( self::get_instance()->get_api_domain() ) . 'wp-json/wp/v2/' . $type . '/' . $id;
			} else {
				$api_url = $demo_data['astra-page-api-url'];
			}

			if ( ! astra_sites_is_valid_url( $api_url ) ) {
				wp_send_json_error( __( 'Invalid API URL.', 'astra-sites' ) );
			}

			$response = wp_safe_remote_get( $api_url );

			if ( is_wp_error( $response ) ) {
				wp_send_json_error( wp_remote_retrieve_body( $response ) );
			}

			$body = wp_remote_retrieve_body( $response );
			$data = json_decode( $body, true );
			if ( ! isset( $data['post-meta']['_elementor_data'] ) ) {
				wp_send_json_error( __( 'Invalid Post Meta', 'astra-sites' ) );
			}

			$meta    = json_decode( $data['post-meta']['_elementor_data'], true );
			$post_id = isset( $_POST['id'] ) ? absint( sanitize_key( $_POST['id'] ) ) : '';

			if ( empty( $post_id ) || empty( $meta ) ) {
				wp_send_json_error( __( 'Invalid Post ID or Elementor Meta', 'astra-sites' ) );
			}

			if ( isset( $data['astra-page-options-data'] ) && isset( $data['astra-page-options-data']['elementor_load_fa4_shim'] ) ) {
				update_option( 'elementor_load_fa4_shim', $data['astra-page-options-data']['elementor_load_fa4_shim'] );
			}

			// Check flexbox container, If inactive then activate it.
			$flexbox_container = get_option( 'elementor_experiment-container' );
			// Check if the value is 'inactive'.
			if ( 'inactive' === $flexbox_container ) {
				// Delete the option to clear the cache.
				delete_option( 'elementor_experiment-container' );
				
				// Update the option to 'active' to activate the flexbox container.
				update_option( 'elementor_experiment-container', 'active' );
			}
			
			$import      = new \Elementor\TemplateLibrary\Astra_Sites_Elementor_Pages();
			$import_data = $import->import( $post_id, $meta );

			delete_option( 'astra_sites_import_elementor_data_' . $id );
			wp_send_json_success( $import_data );
		}

		/**
		 * API Request
		 *
		 * @since 2.0.0
		 */
		public function api_request() {

			// Verify Nonce.
			check_ajax_referer( 'astra-sites', '_ajax_nonce' );

			if ( ! current_user_can( 'edit_posts' ) ) {
				wp_send_json_error();
			}

			$url = isset( $_POST['url'] ) ? sanitize_text_field( $_POST['url'] ) : '';

			if ( empty( $url ) ) {
				wp_send_json_error(
					array(
						'message' => __( 'Provided API URL is empty! Please try again!', 'astra-sites' ),
						'code'    => 'Error',
					)
				);
			}

			$api_args = apply_filters(
				'astra_sites_api_params', array(
					'template_status' => '',
					'version' => ASTRA_SITES_VER,
				)
			);

			$api_url = add_query_arg( $api_args, trailingslashit( self::get_instance()->get_api_domain() ) . 'wp-json/wp/v2/' . $url );

			if ( ! astra_sites_is_valid_url( $api_url ) ) {
				wp_send_json_error(
					array(
						/* Translators: %s is API URL. */
						'message' => sprintf( __( 'Invalid API Request URL - %s', 'astra-sites' ), $api_url ),
						'code'    => 'Error',
					)
				);
			}

			Astra_Sites_Error_Handler::get_instance()->start_error_handler();

			$api_args = apply_filters(
				'astra_sites_api_args', array(
					'timeout' => 15,
				)
			);

			$request = wp_safe_remote_get( $api_url, $api_args );

			Astra_Sites_Error_Handler::get_instance()->stop_error_handler();

			if ( is_wp_error( $request ) ) {
				$wp_error_code = $request->get_error_code();
				switch ( $wp_error_code ) {
					case 'http_request_not_executed':
						/* translators: %s Error Message */
						$message = sprintf( __( 'API Request could not be performed - %s', 'astra-sites' ), $request->get_error_message() );
						break;
					case 'http_request_failed':
					default:
						/* translators: %s Error Message */
						$message = sprintf( __( 'API Request has failed - %s', 'astra-sites' ), $request->get_error_message() );
						break;
				}

				wp_send_json_error(
					array(
						'message'       => $request->get_error_message(),
						'code'          => 'WP_Error',
						'response_code' => $wp_error_code,
					)
				);
			}

			$code      = (int) wp_remote_retrieve_response_code( $request );
			$demo_data = json_decode( wp_remote_retrieve_body( $request ), true );

			if ( 200 === $code ) {
				Astra_Sites_File_System::get_instance()->update_json_file( 'astra_sites_import_data.json', $demo_data );
				set_transient( 'astra_sites_current_import_template_type', 'classic', HOUR_IN_SECONDS );
				wp_send_json_success( $demo_data );
			}

			$message       = wp_remote_retrieve_body( $request );
			$response_code = $code;

			if ( 200 !== $code && is_array( $demo_data ) && isset( $demo_data['code'] ) ) {
				$message = $demo_data['message'];
			}

			if ( 500 === $code ) {
				$message = __( 'Internal Server Error.', 'astra-sites' );
			}

			if ( 200 !== $code && false !== strpos( $message, 'Cloudflare' ) ) {
				$ip = Astra_Sites_Helper::get_client_ip();
				/* translators: %s IP address. */
				$message = sprintf( __( 'Client IP: %1$s </br> Error code: %2$s', 'astra-sites' ), $ip, $code );
				$code    = 'Cloudflare';
			}

			wp_send_json_error(
				array(
					'message'       => $message,
					'code'          => $code,
					'response_code' => $response_code,
				)
			);
		}

		/**
		 * API Request
		 *
		 * @since 3.2.4
		 */
		public function elementor_api_request() {

			// Verify Nonce.
			check_ajax_referer( 'astra-sites', '_ajax_nonce' );

			if ( ! current_user_can( 'edit_posts' ) ) {
				wp_send_json_error();
			}

			$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : '';
			$type = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : '';

			if ( empty( $id ) || empty( $type ) ) {
				wp_send_json_error(
					array(
						'message' => __( 'Provided API details are empty! Please try again!', 'astra-sites' ),
						'code'    => 'Error',
					)
				);
			}

			$api_args = apply_filters(
				'astra_sites_api_params', array(
					'url' => site_url(),
					'version' => ASTRA_SITES_VER,
				)
			);

			$api_url = add_query_arg( $api_args, trailingslashit( self::get_instance()->get_api_domain() ) . 'wp-json/wp/v2/' . $type . '/' . $id );

			if ( ! astra_sites_is_valid_url( $api_url ) ) {
				wp_send_json_error(
					array(
						/* Translators: %s is Request URL. */
						'message' => sprintf( __( 'Invalid Request URL - %s', 'astra-sites' ), $api_url ),
						'code'    => 'Error',
					)
				);
			}

			Astra_Sites_Error_Handler::get_instance()->start_error_handler();

			$api_args = apply_filters(
				'astra_sites_api_args', array(
					'timeout' => 15,
				)
			);

			$request = wp_safe_remote_get( $api_url, $api_args );

			Astra_Sites_Error_Handler::get_instance()->stop_error_handler();

			if ( is_wp_error( $request ) ) {
				$wp_error_code = $request->get_error_code();
				switch ( $wp_error_code ) {
					case 'http_request_not_executed':
						/* translators: %s Error Message */
						$message = sprintf( __( 'API Request could not be performed - %s', 'astra-sites' ), $request->get_error_message() );
						break;
					case 'http_request_failed':
					default:
						/* translators: %s Error Message */
						$message = sprintf( __( 'API Request has failed - %s', 'astra-sites' ), $request->get_error_message() );
						break;
				}

				wp_send_json_error(
					array(
						'message'       => $request->get_error_message(),
						'code'          => 'WP_Error',
						'response_code' => $wp_error_code,
					)
				);
			}

			$code      = (int) wp_remote_retrieve_response_code( $request );
			$demo_data = json_decode( wp_remote_retrieve_body( $request ), true );

			if ( 200 === $code ) {
				update_option( 'astra_sites_import_elementor_data_' . $id, $demo_data, 'no' );
				wp_send_json_success( $demo_data );
			}

			$message       = wp_remote_retrieve_body( $request );
			$response_code = $code;

			if ( 200 !== $code && is_array( $demo_data ) && isset( $demo_data['code'] ) ) {
				$message = $demo_data['message'];
			}

			if ( 500 === $code ) {
				$message = __( 'Internal Server Error.', 'astra-sites' );
			}

			if ( 200 !== $code && false !== strpos( $message, 'Cloudflare' ) ) {
				$ip = Astra_Sites_Helper::get_client_ip();
				/* translators: %s IP address. */
				$message = sprintf( __( 'Client IP: %1$s </br> Error code: %2$s', 'astra-sites' ), $ip, $code );
				$code    = 'Cloudflare';
			}

			wp_send_json_error(
				array(
					'message'       => $message,
					'code'          => $code,
					'response_code' => $response_code,
				)
			);
		}

		/**
		 * API Flush Request
		 *
		 * @since 3.2.4
		 */
		public function elementor_flush_request() {

			// Verify Nonce.
			check_ajax_referer( 'astra-sites', '_ajax_nonce' );

			if ( ! current_user_can( 'edit_posts' ) ) {
				wp_send_json_error();
			}

			$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : '';

			delete_option( 'astra_sites_import_elementor_data_' . $id );

			wp_send_json_success();
		}

		/**
		 * Insert Template
		 *
		 * @return void
		 */
		public function insert_image_templates() {
			ob_start();
			require_once ASTRA_SITES_DIR . 'inc/includes/image-templates.php';
			ob_end_flush();
		}

		/**
		 * Insert Template
		 *
		 * @return void
		 */
		public function insert_image_templates_bb_and_brizy() {

			if (
				class_exists( 'FLBuilderModel' ) && FLBuilderModel::is_builder_active() // BB Builder is on?
				||
				(
					class_exists( 'Brizy_Editor_Post' ) && // Brizy Builder is on?
					( isset( $_GET['brizy-edit'] ) || isset( $_GET['brizy-edit-iframe'] ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Fetching GET parameter, no nonce associated with this action.
				)
			) {
				// Image Search Templates.
				ob_start();
				require_once ASTRA_SITES_DIR . 'inc/includes/image-templates.php';
				ob_end_flush();
			}
		}

		/**
		 * Insert Template
		 *
		 * @return void
		 */
		public function insert_templates() {
			ob_start();
			require_once ASTRA_SITES_DIR . 'inc/includes/templates.php';
			require_once ASTRA_SITES_DIR . 'inc/includes/image-templates.php';
			ob_end_flush();
		}

		/**
		 * Add/Remove Favorite.
		 *
		 * @since  2.0.0
		 */
		public function add_to_favorite() {

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( 'You are not allowed to perform this action', 'astra-sites' );
			}
			// Verify Nonce.
			check_ajax_referer( 'astra-sites', '_ajax_nonce' );

			$new_favorites = array();
			$site_id       = isset( $_POST['site_id'] ) ? sanitize_key( $_POST['site_id'] ) : '';

			if ( empty( $site_id ) ) {
				wp_send_json_error();
			}

			$favorite_settings = get_option( 'astra-sites-favorites', array() );

			if ( false !== $favorite_settings && is_array( $favorite_settings ) ) {
				$new_favorites = $favorite_settings;
			}

			$is_favorite = isset( $_POST['is_favorite'] ) ? sanitize_key( $_POST['is_favorite'] ) : '';

			if ( 'false' === $is_favorite ) {
				if ( in_array( $site_id, $new_favorites, true ) ) {
					$key = array_search( $site_id, $new_favorites, true );
					unset( $new_favorites[ $key ] );
				}
			} else {
				if ( ! in_array( $site_id, $new_favorites, true ) ) {
					array_push( $new_favorites, $site_id );
				}
			}

			update_option( 'astra-sites-favorites', $new_favorites, 'no' );

			wp_send_json_success(
				array(
					'all_favorites' => $new_favorites,
				)
			);
		}

		/**
		 * Import Template.
		 *
		 * @since  2.0.0
		 */
		public function create_template() {

			// Verify Nonce.
			check_ajax_referer( 'astra-sites', '_ajax_nonce' );

			if ( ! current_user_can( 'customize' ) ) {
				wp_send_json_error( __( 'You are not allowed to perform this action', 'astra-sites' ) );
			}

			$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : '';
			$type = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : '';
			$demo_data = get_option( 'astra_sites_import_elementor_data_' . $id, array() );

			if ( 'astra-blocks' == $type ) {
				$url = trailingslashit( self::get_instance()->get_api_domain() ) . 'wp-json/wp/v2/' . $type . '/' . $id;
			} else {
				$url = $demo_data['astra-page-api-url'];
			}

			$api_url = add_query_arg(
				array(
					'site_url' => site_url(),
					'version' => ASTRA_SITES_VER,
				), $url
			);

			if ( ! astra_sites_is_valid_url( $api_url ) ) {
				wp_send_json_error( __( 'Invalid API URL.', 'astra-sites' ) );
			}

			$response = wp_safe_remote_get( $api_url );

			if ( is_wp_error( $response ) || 200 !== $response['response']['code'] ) {
				wp_send_json_error( wp_remote_retrieve_body( $response ) );
			}

			$body = wp_remote_retrieve_body( $response );
			$data = json_decode( $body, true );

			if ( empty( $data ) ) {
				wp_send_json_error( 'Empty page data.' );
			}

			$content = isset( $data['content']['rendered'] ) ? $data['content']['rendered'] : '';

			$page_id = isset( $data['id'] ) ? sanitize_text_field( $data['id'] ) : '';

			$title = '';
			$rendered_title = isset( $data['title']['rendered'] ) ? sanitize_text_field( $data['title']['rendered'] ) : '';
			if ( isset( $rendered_title ) ) {
				$title = ( isset( $_POST['title'] ) && '' !== $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) . ' - ' . $rendered_title : $rendered_title;
			}

			$excerpt = isset( $data['excerpt']['rendered'] ) ? sanitize_text_field( $data['excerpt']['rendered'] ) : '';

			$post_args = array(
				'post_type'    => 'elementor_library',
				'post_status'  => 'publish',
				'post_title'   => $title,
				'post_content' => $content,
				'post_excerpt' => $excerpt,
			);

			$new_page_id = wp_insert_post( $post_args );
			update_post_meta( $new_page_id, '_astra_sites_enable_for_batch', true );
			$post_meta = isset( $data['post-meta'] ) ? $data['post-meta'] : array();

			if ( ! empty( $post_meta ) ) {
				$this->import_template_meta( $new_page_id, $post_meta );
			}

			$term_value = ( 'pages' === $type ) ? 'page' : 'section';
			update_post_meta( $new_page_id, '_elementor_template_type', $term_value );
			wp_set_object_terms( $new_page_id, $term_value, 'elementor_library_type' );

			update_post_meta( $new_page_id, '_wp_page_template', 'elementor_header_footer' );

			do_action( 'astra_sites_process_single', $new_page_id );

			// Flush the object when import is successful.
			delete_option( 'astra_sites_import_elementor_data_' . $id );

			wp_send_json_success(
				array(
					'remove-page-id' => $page_id,
					'id'             => $new_page_id,
					'link'           => get_permalink( $new_page_id ),
				)
			);
		}

		/**
		 * Search Images.
		 *
		 * @since 2.7.3.
		 */
		public function search_images() {
			// Verify Nonce.
			check_ajax_referer( 'astra-sites', '_ajax_nonce' );

			if ( ! current_user_can( 'upload_files' ) ) {
				wp_send_json_error( __( 'You are not allowed to perform this action', 'astra-sites' ) );
			}

			$params = isset( $_POST['params'] ) ? array_map( 'sanitize_text_field', $_POST['params'] ) : array();

			$params['key'] = $this->pixabay_api_key;

			$api_url = add_query_arg( $params, $this->pixabay_url );

			$response = wp_safe_remote_get( $api_url );

			if ( is_wp_error( $response ) ) {
				wp_send_json_error( wp_remote_retrieve_body( $response ) );
			}

			$body = wp_remote_retrieve_body( $response );
			$data = json_decode( $body, true );

			wp_send_json_success( $data );
		}

		/**
		 * Download and save the image in the media library.
		 *
		 * @since  2.0.0
		 */
		public function create_image() {
			// Verify Nonce.
			check_ajax_referer( 'astra-sites', '_ajax_nonce' );

			if ( ! current_user_can( 'upload_files' ) ) {
				wp_send_json_error( __( 'You are not allowed to perform this action', 'astra-sites' ) );
			}

			$url      = isset( $_POST['url'] ) ? sanitize_url( $_POST['url'] ) : false; // phpcs:ignore -- We need to remove this ignore once the WPCS has released this issue fix - https://github.com/WordPress/WordPress-Coding-Standards/issues/2189.
			$name     = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : false;
			$photo_id = isset( $_POST['id'] ) ? absint( sanitize_key( $_POST['id'] ) ) : 0;

			if ( false === $url ) {
				wp_send_json_error( __( 'Need to send URL of the image to be downloaded', 'astra-sites' ) );
			}

			$image  = '';
			$result = array();

			$name  = preg_replace( '/\.[^.]+$/', '', $name ) . '-' . $photo_id . '.jpg';
			$image = $this->create_image_from_url( $url, $name, $photo_id );

			if ( is_wp_error( $image ) ) {
				wp_send_json_error( $image );
			}

			if ( 0 !== $image ) {
				$result['attachmentData'] = wp_prepare_attachment_for_js( $image );
				if ( did_action( 'elementor/loaded' ) ) {
					$result['data'] = Astra_Sites_Elementor_Images::get_instance()->get_attachment_data( $image );
				}
				if ( 0 === $photo_id ) {
					/**
					 * This flag ensures these files are deleted in the Reset Process.
					 */
					update_post_meta( $image, '_astra_sites_imported_post', true );
				}
			} else {
				wp_send_json_error( __( 'Could not download the image.', 'astra-sites' ) );
			}

			// Save downloaded image reference to an option.
			if ( 0 !== $photo_id ) {
				$saved_images = get_option( 'astra-sites-saved-images', array() );

				if ( empty( $saved_images ) || false === $saved_images ) {
					$saved_images = array();
				}

				$saved_images[] = $photo_id;
				update_option( 'astra-sites-saved-images', $saved_images, 'no' );
			}

			$result['updated-saved-images'] = get_option( 'astra-sites-saved-images', array() );

			wp_send_json_success( $result );
		}

		/**
		 * Set the upload directory
		 */
		public function get_wp_upload_url() {
			$wp_upload_dir = wp_upload_dir();
			return isset( $wp_upload_dir['url'] ) ? $wp_upload_dir['url'] : false;
		}

		/**
		 * Create the image and return the new media upload id.
		 *
		 * @param String $url URL to pixabay image.
		 * @param String $name Name to pixabay image.
		 * @param String $photo_id Photo ID to pixabay image.
		 * @param String $description Description to pixabay image.
		 * @see http://codex.wordpress.org/Function_Reference/wp_insert_attachment#Example
		 */
		public function create_image_from_url( $url, $name, $photo_id, $description = '' ) {
			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';
			$file_array         = array();
			$file_array['name'] = wp_basename( $name );

			// Download file to temp location.
			$file_array['tmp_name'] = download_url( $url );

			// If error storing temporarily, return the error.
			if ( is_wp_error( $file_array['tmp_name'] ) ) {
				return $file_array;
			}

			// Do the validation and storage stuff.
			$id = media_handle_sideload( $file_array, 0, null );

			// If error storing permanently, unlink.
			if ( is_wp_error( $id ) ) {
				@unlink( $file_array['tmp_name'] ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_unlink -- Deleting the file from temp location.
				return $id;
			}

			$alt = ( '' === $description ) ? $name : $description;

			// Store the original attachment source in meta.
			add_post_meta( $id, '_source_url', $url );

			update_post_meta( $id, 'astra-images', $photo_id );
			update_post_meta( $id, '_wp_attachment_image_alt', $alt );
			update_post_meta( $id, '_astra_sites_imported_post', true );
			return $id;
		}

		/**
		 * Import Post Meta
		 *
		 * @since 2.0.0
		 *
		 * @param  integer $post_id  Post ID.
		 * @param  array   $metadata  Post meta.
		 * @return void
		 */
		public function import_post_meta( $post_id, $metadata ) {

			$metadata = (array) $metadata;

			foreach ( $metadata as $meta_key => $meta_value ) {

				if ( $meta_value ) {

					if ( '_elementor_data' === $meta_key ) {

						$raw_data = json_decode( stripslashes( $meta_value ), true );

						if ( is_array( $raw_data ) ) {
							$raw_data = wp_slash( wp_json_encode( $raw_data ) );
						} else {
							$raw_data = wp_slash( $raw_data );
						}
					} else {

						if ( is_serialized( $meta_value, true ) ) {
							$raw_data = maybe_unserialize( stripslashes( $meta_value ) );
						} elseif ( is_array( $meta_value ) ) {
							$raw_data = json_decode( stripslashes( $meta_value ), true );
						} else {
							$raw_data = $meta_value;
						}
					}

					update_post_meta( $post_id, $meta_key, $raw_data );
				}
			}
		}

		/**
		 * Import Post Meta
		 *
		 * @since 2.0.0
		 *
		 * @param  integer $post_id  Post ID.
		 * @param  array   $metadata  Post meta.
		 * @return void
		 */
		public function import_template_meta( $post_id, $metadata ) {

			$metadata = (array) $metadata;

			foreach ( $metadata as $meta_key => $meta_value ) {

				if ( $meta_value ) {

					if ( '_elementor_data' === $meta_key ) {

						$raw_data = json_decode( stripslashes( $meta_value ), true );

						if ( is_array( $raw_data ) ) {
							$raw_data = wp_slash( wp_json_encode( $raw_data ) );
						} else {
							$raw_data = wp_slash( $raw_data );
						}
					} else {

						if ( is_serialized( $meta_value, true ) ) {
							$raw_data = maybe_unserialize( stripslashes( $meta_value ) );
						} elseif ( is_array( $meta_value ) ) {
							$raw_data = json_decode( stripslashes( $meta_value ), true );
						} else {
							$raw_data = $meta_value;
						}
					}

					update_post_meta( $post_id, $meta_key, $raw_data );
				}
			}
		}

		/**
		 * Close getting started notice for current user
		 *
		 * @since 1.3.5
		 * @return void
		 */
		public function getting_started_notice() {
			// Verify Nonce.
			check_ajax_referer( 'astra-sites', '_ajax_nonce' );

			if ( ! current_user_can( 'customize' ) ) {
				wp_send_json_error( __( 'You are not allowed to perform this action', 'astra-sites' ) );
			}

			update_option( '_astra_sites_gettings_started', 'yes', 'no' );
			wp_send_json_success();
		}

		/**
		 * Get theme install, active or inactive status.
		 *
		 * @since 1.3.2
		 *
		 * @return string Theme status
		 */
		public function get_theme_status() {

			$theme = wp_get_theme();

			// Theme installed and activate.
			if ( 'Astra' === $theme->name || 'Astra' === $theme->parent_theme ) {
				return 'installed-and-active';
			}

			// Theme installed but not activate.
			foreach ( (array) wp_get_themes() as $theme_dir => $theme ) {
				if ( 'Astra' === $theme->name || 'Astra' === $theme->parent_theme ) {
					return 'installed-but-inactive';
				}
			}

			return 'not-installed';
		}

		/**
		 * Loads textdomain for the plugin.
		 *
		 * @since 1.0.1
		 */
		public function load_textdomain() {
			// Default languages directory.
			$lang_dir = ASTRA_PRO_SITES_DIR . 'languages/';

			// Traditional WordPress plugin locale filter.
			global $wp_version;

			$get_locale = get_locale();

			if ( $wp_version >= 4.7 ) {
				$get_locale = get_user_locale();
			}

			/**
			 * Language Locale for plugin
			 *
			 * @var $get_locale The locale to use.
			 * Uses get_user_locale()` in WordPress 4.7 or greater,
			 * otherwise uses `get_locale()`.
			 */
			$locale = apply_filters( 'plugin_locale', $get_locale, 'astra-sites' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'astra-sites', $locale );

			// Setup paths to current locale file.
			$mofile_global = WP_LANG_DIR . '/plugins/' . $mofile;
			$mofile_local  = $lang_dir . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/astra-sites/ folder.
				load_textdomain( 'astra-sites', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/astra-sites/languages/ folder.
				load_textdomain( 'astra-sites', $mofile_local );
			} else {
				// Load the default language files.
				load_plugin_textdomain( 'astra-sites', false, $lang_dir );
			}
		}

		/**
		 * Show action links on the plugin screen.
		 *
		 * @param   mixed $links Plugin Action links.
		 * @return  array
		 */
		public function action_links( $links ) {

			$arguments = array(
				'page' => 'starter-templates',
			);

			$url = add_query_arg( $arguments, admin_url( 'themes.php' ) );

			$action_links = array(
				'settings' => '<a href="' . esc_url( $url ) . '" aria-label="' . esc_attr__( 'Get Started', 'astra-sites' ) . '">' . esc_html__( 'Get Started', 'astra-sites' ) . '</a>',
			);

			return array_merge( $action_links, $links );
		}

		/**
		 * Get the API URL.
		 *
		 * @since  1.0.0
		 * 
		 * @return string
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

			$this->pixabay_url     = 'https://pixabay.com/api/';
			$this->pixabay_api_key = '2727911-c4d7c1031949c7e0411d7e81e';
		}

		/**
		 * Getter for $api_url
		 *
		 * @since  1.0.0
		 * 
		 * @return string
		 */
		public function get_api_url() {
			return $this->api_url;
		}

		/**
		 * Enqueue admin scripts.
		 *
		 * @since  1.3.2    Added 'install-theme.js' to install and activate theme.
		 * @since  1.0.5    Added 'getUpgradeText' and 'getUpgradeURL' localize variables.
		 *
		 * @since  1.0.0
		 *
		 * @param  string $hook Current hook name.
		 * @return void
		 */
		public function admin_enqueue( $hook = '' ) {

			// Avoid scripts from customizer.
			if ( is_customize_preview() ) {
				return;
			}

			if ( 'appearance_page_starter-templates' !== $hook ) {
				return;
			}

			global $is_IE, $is_edge;

			if ( $is_IE || $is_edge ) {
				wp_enqueue_script( 'astra-sites-eventsource', ASTRA_SITES_URI . 'inc/assets/js/eventsource.min.js', array( 'jquery', 'wp-util', 'updates' ), ASTRA_SITES_VER, true );
			}

			// Fetch.
			wp_register_script( 'astra-sites-fetch', ASTRA_SITES_URI . 'inc/assets/js/fetch.umd.js', array( 'jquery' ), ASTRA_SITES_VER, true );

			// History.
			wp_register_script( 'astra-sites-history', ASTRA_SITES_URI . 'inc/assets/js/history.js', array( 'jquery' ), ASTRA_SITES_VER, true );

			// Admin Page.
			wp_enqueue_style( 'astra-sites-admin', ASTRA_SITES_URI . 'inc/assets/css/admin.css', ASTRA_SITES_VER, true );
			wp_set_script_translations( 'astra-sites-admin', 'astra-sites' );
			wp_style_add_data( 'astra-sites-admin', 'rtl', 'replace' );

			$data = $this->get_local_vars();
		}

		/**
		 * Get CTA link
		 *
		 * @param string $source    The source of the link.
		 * @param string $medium    The medium of the link.
		 * @param string $campaign  The campaign of the link.
		 * @return array
		 */
		public function get_cta_link( $source = '', $medium = '', $campaign = '' ) {
			$default_page_builder = Astra_Sites_Page::get_instance()->get_setting( 'page_builder' );
			$cta_links = $this->get_cta_links( $source, $medium, $campaign );
			return isset( $cta_links[ $default_page_builder ] ) ? $cta_links[ $default_page_builder ] : 'https://wpastra.com/starter-templates-plans/?utm_source=StarterTemplatesPlugin&utm_campaign=WPAdmin';
		}

		/**
		 * Get CTA Links
		 *
		 * @since 2.6.18
		 *
		 * @param string $source    The source of the link.
		 * @param string $medium    The medium of the link.
		 * @param string $campaign  The campaign of the link.
		 * @return array
		 */
		public function get_cta_links( $source = '', $medium = '', $campaign = '' ) {
			return array(
				'elementor' => add_query_arg(
					array(
						'utm_source' => ! empty( $source ) ? $source : 'elementor-templates',
						'utm_medium' => 'dashboard',
						'utm_campaign' => 'Starter-Template-Backend',
					), 'https://wpastra.com/elementor-starter-templates/'
				),
				'beaver-builder' => add_query_arg(
					array(
						'utm_source' => ! empty( $source ) ? $source : 'beaver-templates',
						'utm_medium' => 'dashboard',
						'utm_campaign' => 'Starter-Template-Backend',
					), 'https://wpastra.com/beaver-builder-starter-templates/'
				),
				'gutenberg' => add_query_arg(
					array(
						'utm_source' => ! empty( $source ) ? $source : 'gutenberg-templates',
						'utm_medium' => 'dashboard',
						'utm_campaign' => 'Starter-Template-Backend',
					), 'https://wpastra.com/starter-templates-plans/'
				),
				'brizy' => add_query_arg(
					array(
						'utm_source' => ! empty( $source ) ? $source : 'brizy-templates',
						'utm_medium' => 'dashboard',
						'utm_campaign' => 'Starter-Template-Backend',
					), 'https://wpastra.com/starter-templates-plans/'
				),
			);
		}

		/**
		 * Returns Localization Variables.
		 *
		 * @since 2.0.0
		 */
		public function get_local_vars() {

			$stored_data = array(
				'astra-sites-site-category'        => array(),
				'astra-site-page-builder'    => array(),
				'astra-sites'                => array(),
				'site-pages-category'        => array(),
				'site-pages-page-builder'    => array(),
				'site-pages-parent-category' => array(),
				'site-pages'                 => array(),
				'favorites'                  => get_option( 'astra-sites-favorites' ),
			);

			$favorite_data = get_option( 'astra-sites-favorites' );

			$license_status = false;
			if ( is_callable( 'BSF_License_Manager::bsf_is_active_license' ) ) {
				$license_status = BSF_License_Manager::bsf_is_active_license( 'astra-pro-sites' );
			}

			$spectra_theme = 'not-installed';
			// Theme installed and activate.
			if ( 'spectra-one' === get_option( 'stylesheet', 'astra' ) ) {
				$spectra_theme = 'installed-and-active';
			}
			$enable_block_builder = apply_filters( 'st_enable_block_page_builder', false );
			$saved_page_builder = Astra_Sites_Page::get_instance()->get_setting( 'page_builder' );
			if ( 'beaver-builder' === $saved_page_builder && get_option( 'st-beaver-builder-flag' ) === '1' ) {
				$saved_page_builder = 'gutenberg';
			}
			if ( 'elementor' === $saved_page_builder && get_option( 'st-elementor-builder-flag' ) === '1' ) {
				$saved_page_builder = 'gutenberg';
			}
			$default_page_builder = ( 'installed-and-active' === $spectra_theme ) ? 'fse' : $saved_page_builder;
			$default_page_builder = ( $enable_block_builder && empty( $default_page_builder ) ) ? 'gutenberg' : $default_page_builder;
			
			$remove_parameters = array( 'credit_token', 'token', 'email', 'ast_action', 'nonce' );
			$credit_request_params = array(
				'success_url' => isset( $_SERVER['REQUEST_URI'] ) ? urlencode( $this->remove_query_params( network_home_url() . $_SERVER['REQUEST_URI'], $remove_parameters ) . '&ast_action=credits' ) : '', // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			);

			$credit_purchase_url = defined( 'ZIP_AI_CREDIT_TOPUP_URL' ) ? ZIP_AI_CREDIT_TOPUP_URL : 'https://app.zipwp.com/credits-pricing';
			$credit_purchase_url = add_query_arg( $credit_request_params, $credit_purchase_url );
			
			if ( is_callable( '\SureCart\Models\ApiToken::get()' ) ) {
				$surecart_store_exist = \SureCart\Models\ApiToken::get();
			}

			$plans = Astra_Sites_ZipWP_Integration::get_instance()->get_zip_plans();

			$data = apply_filters(
				'astra_sites_localize_vars',
				array(
					'subscribed'                         => get_user_meta( get_current_user_ID(), 'astra-sites-subscribed', true ),
					'debug'                              => defined( 'WP_DEBUG' ) ? true : false,
					'isPro'                              => defined( 'ASTRA_PRO_SITES_NAME' ) ? true : false,
					'isWhiteLabeled'                     => Astra_Sites_White_Label::get_instance()->is_white_labeled(),
					'whiteLabelName'                     => Astra_Sites_White_Label::get_instance()->get_white_label_name(),
					'whiteLabelUrl'                      => Astra_Sites_White_Label::get_instance()->get_white_label_link( '#' ),
					'ajaxurl'                            => esc_url( admin_url( 'admin-ajax.php' ) ),
					'siteURL'                            => site_url(),
					'adminURL'                           => esc_url( admin_url() ),
					'getProText'                         => __( 'Get Access!', 'astra-sites' ),
					'getProURL'                          => esc_url( 'https://wpastra.com/starter-templates-plans/?utm_source=demo-import-panel&utm_campaign=astra-sites&utm_medium=wp-dashboard' ),
					'getUpgradeText'                     => __( 'Upgrade', 'astra-sites' ),
					'getUpgradeURL'                      => esc_url( 'https://wpastra.com/starter-templates-plans/?utm_source=demo-import-panel&utm_campaign=astra-sites&utm_medium=wp-dashboard' ),
					'_ajax_nonce'                        => current_user_can( 'edit_posts' ) ? wp_create_nonce( 'astra-sites' ) : '',
					'requiredPlugins'                    => array(),
					'syncLibraryStart'                   => '<span class="message">' . esc_html__( 'Syncing template library in the background. The process can take anywhere between 2 to 3 minutes. We will notify you once done.', 'astra-sites' ) . '</span>',
					'xmlRequiredFilesMissing'            => __( 'Some of the files required during the import process are missing.<br/><br/>Please try again after some time.', 'astra-sites' ),
					'importFailedMessageDueToDebug'      => __( '<p>WordPress debug mode is currently enabled on your website. This has interrupted the import process..</p><p>Kindly disable debug mode and try importing Starter Template again.</p><p>You can add the following code into the wp-config.php file to disable debug mode.</p><p><code>define(\'WP_DEBUG\', false);</code></p>', 'astra-sites' ),
					/* translators: %s is a documentation link. */
					'importFailedMessage'                => sprintf( __( '<p>We are facing a temporary issue in importing this template.</p><p>Read <a href="%s" target="_blank">article</a> to resolve the issue and continue importing template.</p>', 'astra-sites' ), esc_url( 'https://wpastra.com/docs/fix-starter-template-importing-issues/' ) ),
					/* translators: %s is a documentation link. */
					'importFailedRequiredPluginsMessage' => sprintf( __( '<p>We are facing a temporary issue in installing the required plugins for this template.</p><p>Read&nbsp;<a href="%s" target="_blank">article</a>&nbsp;to resolve the issue and continue importing template.</p>', 'astra-sites' ), esc_url( 'https://wpastra.com/docs/plugin-installation-failed-multisite/' ) ),

					'strings'                            => array(
						/* translators: %s are white label strings. */
						'warningBeforeCloseWindow' => sprintf( __( 'Warning! %1$s Import process is not complete. Don\'t close the window until import process complete. Do you still want to leave the window?', 'astra-sites' ), Astra_Sites_White_Label::get_instance()->get_white_label_name() ),
						'viewSite'                 => __( 'Done! View Site', 'astra-sites' ),
						'syncCompleteMessage'      => self::get_instance()->get_sync_complete_message(),
						/* translators: %s is a template name */
						'importSingleTemplate'     => __( 'Import "%s" Template', 'astra-sites' ),
					),
					'log'                                => array(
						'bulkInstall'  => __( 'Installing Required Plugins..', 'astra-sites' ),
						/* translators: %s are white label strings. */
						'themeInstall' => sprintf( __( 'Installing %1$s Theme..', 'astra-sites' ), Astra_Sites_White_Label::get_instance()->get_option( 'astra', 'name', 'Astra' ) ),
					),
					'default_page_builder'               => $default_page_builder,
					'default_page_builder_data'          => Astra_Sites_Page::get_instance()->get_default_page_builder(),
					'default_page_builder_sites'         => Astra_Sites_Page::get_instance()->get_sites_by_page_builder( $default_page_builder ),
					'sites'                              => astra_sites_get_api_params(),
					'categories'                         => array(),
					'page-builders'                      => array(),
					'all_sites'                          => $this->get_all_sites(),
					'all_site_categories'                => Astra_Sites_File_System::get_instance()->get_json_file_content( 'astra-sites-all-site-categories.json' ),
					'all_site_categories_and_tags'       => Astra_Sites_File_System::get_instance()->get_json_file_content( 'astra-sites-all-site-categories-and-tags.json' ),
					'license_status'                     => $license_status,
					'license_page_builder'               => get_option( 'astra-sites-license-page-builder', '' ),
					'ApiDomain'                          => $this->api_domain,
					'ApiURL'                             => $this->api_url,
					'stored_data'                        => $stored_data,
					'favorite_data'                      => $favorite_data,
					'category_slug'                      => 'astra-sites-site-category',
					'page_builder'                       => 'astra-site-page-builder',
					'cpt_slug'                           => 'astra-sites',
					'parent_category'                    => '',
					'compatibilities'                    => $this->get_compatibilities(),
					'compatibilities_data'               => $this->get_compatibilities_data(),
					'dismiss'                            => __( 'Dismiss this notice.', 'astra-sites' ),
					'headings'                           => array(
						'subscription' => esc_html__( 'One Last Step..', 'astra-sites' ),
						'site_import'  => esc_html__( 'Your Selected Website is Being Imported.', 'astra-sites' ),
						'page_import'  => esc_html__( 'Your Selected Template is Being Imported.', 'astra-sites' ),
					),
					'subscriptionSuccessMessage'         => esc_html__( 'We have sent you a surprise gift on your email address! Please check your inbox!', 'astra-sites' ),
					'first_import_complete'              => get_option( 'astra_sites_import_complete' ),
					'server_import_primary_error'        => __( 'Looks like the template you are importing is temporarily not available.', 'astra-sites' ),
					'client_import_primary_error'        => __( 'We could not start the import process and this is the message from WordPress:', 'astra-sites' ),
					'cloudflare_import_primary_error'    => __( 'There was an error connecting to the Starter Templates API.', 'astra-sites' ),
					'xml_import_interrupted_primary'     => __( 'There was an error while importing the content.', 'astra-sites' ),
					'xml_import_interrupted_secondary'   => __( 'To import content, WordPress needs to store XML file in /wp-content/ folder. Please get in touch with your hosting provider.', 'astra-sites' ),
					'xml_import_interrupted_error'       => __( 'Looks like your host probably could not store XML file in /wp-content/ folder.', 'astra-sites' ),
					/* translators: %s HTML tags */
					'ajax_request_failed_primary'        => sprintf( __( '%1$sWe could not start the import process due to failed AJAX request and this is the message from WordPress:%2$s', 'astra-sites' ), '<p>', '</p>' ),
					/* translators: %s URL to document. */
					'ajax_request_failed_secondary'      => sprintf( __( '%1$sRead&nbsp;<a href="%2$s" target="_blank">article</a>&nbsp;to resolve the issue and continue importing template.%3$s', 'astra-sites' ), '<p>', esc_url( 'https://wpastra.com/docs/internal-server-error-starter-templates/' ), '</p>' ),
					'cta_links' => $this->get_cta_links(),
					'cta_quick_corner_links' => $this->get_cta_links( 'quick-links-corner' ),
					'cta_premium_popup_links' => $this->get_cta_links( 'get-premium-access-popup' ),
					'cta_link' => $this->get_cta_link(),
					'cta_quick_corner_link' => $this->get_cta_link( 'quick-links-corner' ),
					'cta_premium_popup_link' => $this->get_cta_link( 'get-premium-access-popup' ),

					/* translators: %s URL to document. */
					'process_failed_primary'        => sprintf( __( '%1$sWe could not complete the import process due to failed AJAX request and this is the message:%2$s', 'astra-sites' ), '<p>', '</p>' ),
					/* translators: %s URL to document. */
					'process_failed_secondary'      => sprintf( __( '%1$sPlease report this <a href="%2$s" target="_blank">here</a>.%3$s', 'astra-sites' ), '<p>', esc_url( 'https://wpastra.com/starter-templates-support/?url=#DEMO_URL#&subject=#SUBJECT#' ), '</p>' ),
					'st_page_url' => admin_url( 'themes.php?page=starter-templates' ),
					'staging_connected' => apply_filters( 'astra_sites_staging_connected', '' ),
					'isRTLEnabled' => is_rtl(),
					/* translators: %s Anchor link to support URL. */
					'support_text' => sprintf( __( 'Please report this error %1$shere%2$s, so we can fix it.', 'astra-sites' ), '<a href="https://wpastra.com/support/open-a-ticket/" target="_blank">', '</a>' ),
					'surecart_store_exists' => isset( $surecart_store_exist ) ? $surecart_store_exist : false,
					'default_ai_categories' => $this->get_default_ai_categories(),
					'block_color_palette'     => $this->get_block_palette_colors(),
					'page_color_palette'      => $this->get_page_palette_colors(),
					'rest_api_nonce' => ( current_user_can( 'manage_options' ) ) ? wp_create_nonce( 'wp_rest' ) : '',
					'zip_token_exists' => Astra_Sites_ZipWP_Helper::get_token() !== '' ? true : false,
					'zip_plans' => ( $plans && isset( $plans['data'] ) ) ? $plans['data'] : array(),
					'dashboard_url' => admin_url(),
					'placeholder_images' => Astra_Sites_ZipWP_Helper::get_image_placeholders(),
					'get_more_credits_url' => $credit_purchase_url,
					'dismiss_ai_notice' => Astra_Sites_Page::get_instance()->get_setting( 'dismiss_ai_promotion' ),
					'showClassicTemplates' => apply_filters( 'astra_sites_show_classic_templates', true ),
					'bgSyncInProgress' => 'in-process' === get_site_option( 'astra-sites-batch-status', '' ),
				)
			);

			return $data;
		}

		/**
		 * Get palette colors
		 *
		 * @since 4.0.0
		 *
		 * @return mixed
		 */
		public function get_page_palette_colors() { 
			$default_palette_color = array(
				'#046bd2',
				'#045cb4',
				'#1e293b',
				'#334155',
				'#f9fafb',
				'#FFFFFF',
				'#e2e8f0',
				'#cbd5e1',
				'#94a3b8',
			);

			if ( class_exists( 'Astra_Global_Palette' ) ) {
				$astra_palette_colors = astra_get_palette_colors();
				$default_palette_color = $astra_palette_colors['palettes'][ $astra_palette_colors['currentPalette'] ];
			}

			$palette_one = $default_palette_color;

			$palette_two = array(
				$default_palette_color[0],
				$default_palette_color[1],
				$default_palette_color[5],
				$default_palette_color[4],
				$default_palette_color[3],
				$default_palette_color[2],
				$default_palette_color[6],
				$default_palette_color[7],
				$default_palette_color[8],
			);

			$color_palettes = array(
				'style-1' =>
				array(
					'slug' => 'style-1',
					'title' => 'Light',
					'default_color' => $default_palette_color[4],
					'colors' => $palette_one,
				),
				'style-2' => array(
					'slug' => 'style-2',
					'title' => 'Dark',
					'default_color' => '#1E293B',
					'colors' => $palette_two,
				),
			);

			return $color_palettes;
		}

		/**
		 * Get default AI categories.
		 *
		 * @since 2.0.0
		 *
		 * @return array
		 */
		public function get_default_ai_categories() {
			return array(
				'business' => 'Business',
				'person' => 'Person',
				'organisation' => 'Organisation',
				'restaurant' => 'Restaurant',
				'product' => 'Product',
				'event' => 'Event',
				'landing-page' => 'Landing Page',
				'medical' => 'Medical',
			);
		}

		/**
		 * Get palette colors
		 *
		 * @since 4.0.0
		 *
		 * @return mixed
		 */
		public function get_block_palette_colors() { 
			$default_palette_color = array(
				'#046bd2',
				'#045cb4',
				'#1e293b',
				'#334155',
				'#f9fafb',
				'#FFFFFF',
				'#e2e8f0',
				'#cbd5e1',
				'#94a3b8',
			);

			if ( class_exists( 'Astra_Global_Palette' ) ) {
				$astra_palette_colors = astra_get_palette_colors();
				$default_palette_color = $astra_palette_colors['palettes'][ $astra_palette_colors['currentPalette'] ];
			}

			$palette_one = array(
				$default_palette_color[0],
				$default_palette_color[1],
				$default_palette_color[2],
				$default_palette_color[3],
				$default_palette_color[5],
				$default_palette_color[5],
				$default_palette_color[6],
				$default_palette_color[7],
				$default_palette_color[8],
			);

			$palette_two = $default_palette_color;

			$palette_three = array(
				$default_palette_color[3],
				$default_palette_color[2],
				$default_palette_color[5],
				$default_palette_color[4],
				$default_palette_color[0],
				$default_palette_color[1],
				$default_palette_color[6],
				$default_palette_color[7],
				$default_palette_color[8],
			);


			$color_palettes = array(
				'style-1' =>
				array(
					'slug' => 'style-1',
					'title' => 'Light',
					'default_color' => $default_palette_color[5],
					'colors' => $palette_one,
				),
				'style-2' => array(
					'slug' => 'style-2',
					'title' => 'Dark',
					'default_color' => $default_palette_color[4],
					'colors' => $palette_two,
				),
				'style-3' => array(
					'slug' => 'style-3',
					'title' => 'Highlight',
					'default_color' => $default_palette_color[0],
					'colors' => $palette_three,
				),
			);

			return $color_palettes;
		}

		/**
		 * Display subscription form
		 *
		 * @since 2.6.1
		 *
		 * @return boolean
		 */
		public function should_display_subscription_form() {

			$subscription = apply_filters( 'astra_sites_should_display_subscription_form', null );
			if ( null !== $subscription ) {
				return $subscription;
			}

			// Is WhiteLabel enabled?
			if ( Astra_Sites_White_Label::get_instance()->is_white_labeled() ) {
				return false;
			}

			// Is Premium Starter Templates pluign?
			if ( defined( 'ASTRA_PRO_SITES_NAME' ) ) {
				return false;
			}

			// User already subscribed?
			$subscribed = get_user_meta( get_current_user_ID(), 'astra-sites-subscribed', true );
			if ( $subscribed ) {
				return false;
			}

			return true;
		}

		/**
		 * Import Compatibility Errors
		 *
		 * @since 2.0.0
		 * @return mixed
		 */
		public function get_compatibilities_data() {
			return array(
				'xmlreader'            => array(
					'title'   => esc_html__( 'XMLReader Support Missing', 'astra-sites' ),
					/* translators: %s doc link. */
					'tooltip' => '<p>' . esc_html__( 'You\'re close to importing the template. To complete the process, enable XMLReader support on your website..', 'astra-sites' ) . '</p><p>' . sprintf( __( 'Read an article <a href="%s" target="_blank">here</a> to resolve the issue.', 'astra-sites' ), 'https://wpastra.com/docs/xmlreader-missing/' ) . '</p>',
				),
				'curl'                 => array(
					'title'   => esc_html__( 'cURL Support Missing', 'astra-sites' ),
					/* translators: %s doc link. */
					'tooltip' => '<p>' . esc_html__( 'To run a smooth import, kindly enable cURL support on your website.', 'astra-sites' ) . '</p><p>' . sprintf( __( 'Read an article <a href="%s" target="_blank">here</a> to resolve the issue.', 'astra-sites' ), 'https://wpastra.com/docs/curl-support-missing/' ) . '</p>',
				),
				'wp-debug'             => array(
					'title'   => esc_html__( 'Disable Debug Mode', 'astra-sites' ),
					/* translators: %s doc link. */
					'tooltip' => '<p>' . esc_html__( 'WordPress debug mode is currently enabled on your website. With this, any errors from third-party plugins might affect the import process.', 'astra-sites' ) . '</p><p>' . esc_html__( 'Kindly disable it to continue importing the Starter Template. To do so, you can add the following code into the wp-config.php file.', 'astra-sites' ) . '</p><p><code>define(\'WP_DEBUG\', false);</code></p><p>' . sprintf( __( 'Read an article <a href="%s" target="_blank">here</a> to resolve the issue.', 'astra-sites' ), 'https://wpastra.com/docs/disable-debug-mode/' ) . '</p>',
				),
				'update-available'     => array(
					'title'   => esc_html__( 'Update Plugin', 'astra-sites' ),
					/* translators: %s update page link. */
					'tooltip' => '<p>' . esc_html__( 'Updates are available for plugins used in this starter template.', 'astra-sites' ) . '</p>##LIST##<p>' . sprintf( __( 'Kindly <a href="%s" target="_blank">update</a> them for a successful import. Skipping this step might break the template design/feature.', 'astra-sites' ), esc_url( network_admin_url( 'update-core.php' ) ) ) . '</p>',
				),
				'third-party-required' => array(
					'title'   => esc_html__( 'Required Plugins Missing', 'astra-sites' ),
					'tooltip' => '<p>' . esc_html__( 'This starter template requires premium plugins. As these are third party premium plugins, you\'ll need to purchase, install and activate them first.', 'astra-sites' ) . '</p>',
				),
				'dynamic-page'         => array(
					'title'   => esc_html__( 'Dynamic Page', 'astra-sites' ),
					'tooltip' => '<p>' . esc_html__( 'The page template you are about to import contains a dynamic widget/module. Please note this dynamic data will not be available with the imported page.', 'astra-sites' ) . '</p><p>' . esc_html__( 'You will need to add it manually on the page.', 'astra-sites' ) . '</p><p>' . esc_html__( 'This dynamic content will be available when you import the entire site.', 'astra-sites' ) . '</p>',
				),
				'flexbox-container'         => array(
					'title'   => esc_html__( 'Enable Flexbox Container from Elementor', 'astra-sites' ),
					/* translators: %s doc link. */
					'tooltip' => '<p>' . esc_html__( 'The Flexbox Container widget is disabled on your website. With this disabled, the import process will be affected. Kindly enable it to continue importing the Starter Template.', 'astra-sites' ) . '</p><p>' . sprintf( __( 'Read an article <a href="%s" target="_blank">here</a> to resolve the issue.', 'astra-sites' ), 'https://wpastra.com/docs/enable-flexbox-container-from-elementor' ) . '</p>',
				),
			);
		}

		/**
		 * Get all compatibilities
		 *
		 * @since 2.0.0
		 *
		 * @return array
		 */
		public function get_compatibilities() {

			$data = $this->get_compatibilities_data();

			$compatibilities = array(
				'errors'   => array(),
				'warnings' => array(),
			);

			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				$compatibilities['warnings']['wp-debug'] = $data['wp-debug'];
			}

			if ( ! class_exists( 'XMLReader' ) ) {
				$compatibilities['errors']['xmlreader'] = $data['xmlreader'];
			}

			if ( ! function_exists( 'curl_version' ) ) {
				$compatibilities['errors']['curl'] = $data['curl'];
			}

			$flexbox_container = get_option( 'elementor_experiment-container' );
			// Check if the value is 'inactive'.
			if ( 'inactive' === $flexbox_container ) {
				$compatibilities['warnings']['flexbox-container'] = $data['flexbox-container'];
			}

			return $compatibilities;
		}

		/**
		 * Register module required js on elementor's action.
		 *
		 * @since 2.0.0
		 */
		public function register_widget_scripts() {

			$page_builders = self::get_instance()->get_page_builders();
			$has_elementor = false;

			// Use this filter to remove the Starter Templates button from Elementor Editor.
			$elementor_add_ast_site_button = apply_filters( 'starter_templates_hide_elementor_button', false );

			foreach ( $page_builders as $page_builder ) {

				if ( 'elementor' === $page_builder['slug'] ) {
					$has_elementor = true;
				}
			}

			if ( ! $has_elementor ) {
				return;
			}

			if ( $elementor_add_ast_site_button ) {
				return;
			}

			wp_enqueue_script( 'astra-sites-helper', ASTRA_SITES_URI . 'inc/assets/js/helper.js', array( 'jquery' ), ASTRA_SITES_VER, true );

			wp_enqueue_script( 'masonry' );
			wp_enqueue_script( 'imagesloaded' );

			wp_enqueue_script( 'astra-sites-elementor-admin-page', ASTRA_SITES_URI . 'inc/assets/js/elementor-admin-page.js', array( 'jquery', 'wp-util', 'updates', 'masonry', 'imagesloaded' ), ASTRA_SITES_VER, true );
			wp_add_inline_script( 'astra-sites-elementor-admin-page', sprintf( 'var pagenow = "%s";', ASTRA_SITES_NAME ), 'after' );
			wp_enqueue_style( 'astra-sites-admin', ASTRA_SITES_URI . 'inc/assets/css/admin.css', ASTRA_SITES_VER, true );
			wp_style_add_data( 'astra-sites-admin', 'rtl', 'replace' );

			$license_status = false;
			if ( is_callable( 'BSF_License_Manager::bsf_is_active_license' ) ) {
				$license_status = BSF_License_Manager::bsf_is_active_license( 'astra-pro-sites' );
			}

			/* translators: %s are link. */
			$license_msg = sprintf( __( 'This is a premium template available with Essential and Business Toolkits. you can purchase it from <a href="%s" target="_blank">here</a>.', 'astra-sites' ), 'https://wpastra.com/starter-templates-plans/' );

			if ( defined( 'ASTRA_PRO_SITES_NAME' ) ) {
				/* translators: %s are link. */
				$license_msg = sprintf( __( 'This is a premium template available with Essential and Business Toolkits. <a href="%s" target="_blank">Validate Your License</a> Key to import this template.', 'astra-sites' ), esc_url( admin_url( 'plugins.php?bsf-inline-license-form=astra-pro-sites' ) ) );
			}

			$last_viewed_block_data = array();
			// Retrieve the value of the 'blockID' parameter using filter_input().
			$id = filter_input( INPUT_GET, 'blockID', FILTER_SANITIZE_STRING );
			if ( ! empty( $id ) ) {
				$last_viewed_block_data = get_option( 'astra_sites_import_elementor_data_' . $id ) !== false ? get_option( 'astra_sites_import_elementor_data_' . $id ) : array();
			}

			$data = apply_filters(
				'astra_sites_render_localize_vars',
				array(
					'plugin_name'                => Astra_Sites_White_Label::get_instance()->get_white_label_name(),
					'sites'                      => astra_sites_get_api_params(),
					'version'                    => ASTRA_SITES_VER,
					'settings'                   => array(),
					'page-builders'              => array(),
					'categories'                 => array(),
					'default_page_builder'       => 'elementor',
					'astra_blocks'               => $this->get_all_blocks(),
					'license_status'             => $license_status,
					'ajaxurl'                    => esc_url( admin_url( 'admin-ajax.php' ) ),
					'default_page_builder_sites' => Astra_Sites_Page::get_instance()->get_sites_by_page_builder( 'elementor' ),
					'ApiURL'                     => $this->api_url,
					'_ajax_nonce'                => current_user_can( 'edit_posts' ) ? wp_create_nonce( 'astra-sites' ) : '',
					'isPro'                      => defined( 'ASTRA_PRO_SITES_NAME' ) ? true : false,
					'license_msg'                => $license_msg,
					'isWhiteLabeled'             => Astra_Sites_White_Label::get_instance()->is_white_labeled(),
					'getProText'                 => __( 'Get Access!', 'astra-sites' ),
					'getProURL'                  => esc_url( 'https://wpastra.com/starter-templates-plans/?utm_source=demo-import-panel&utm_campaign=astra-sites&utm_medium=wp-dashboard' ),
					'astra_block_categories'     => Astra_Sites_File_System::get_instance()->get_json_file_content( 'astra-blocks-categories.json' ),
					'siteURL'                    => site_url(),
					'template'                   => esc_html__( 'Template', 'astra-sites' ),
					'block'                      => esc_html__( 'Block', 'astra-sites' ),
					'dismiss_text'               => esc_html__( 'Dismiss', 'astra-sites' ),
					'install_plugin_text'        => esc_html__( 'Install Required Plugins', 'astra-sites' ),
					'syncCompleteMessage'        => self::get_instance()->get_sync_complete_message(),
					/* translators: %s are link. */
					'page_settings'              => array(
						'message'  => __( 'You can locate <strong>Starter Templates Settings</strong> under the <strong>Page Settings</strong> of the Style Tab.', 'astra-sites' ),
						'url'      => '#',
						'url_text' => __( 'Read More →', 'astra-sites' ),
					),

					'last_viewed_block_data'   => $last_viewed_block_data,
				)
			);

			wp_localize_script( 'astra-sites-elementor-admin-page', 'astraElementorSites', $data );
		}

		/**
		 * Register module required js on elementor's action.
		 *
		 * @since 2.0.0
		 */
		public function popup_styles() {

			wp_enqueue_style( 'astra-sites-elementor-admin-page', ASTRA_SITES_URI . 'inc/assets/css/elementor-admin.css', ASTRA_SITES_VER, true );
			wp_enqueue_style( 'astra-sites-elementor-admin-page-dark', ASTRA_SITES_URI . 'inc/assets/css/elementor-admin-dark.css', ASTRA_SITES_VER, true );
			wp_style_add_data( 'astra-sites-elementor-admin-page', 'rtl', 'replace' );

		}

		/**
		 * Get all sites
		 *
		 * @since 2.0.0
		 * @return array All sites.
		 */
		public function get_all_sites() {
			$sites_and_pages = array();
			$total_requests  = (int) Astra_Sites_File_System::get_instance()->get_json_file_content( 'astra-sites-requests.json' );

			for ( $page = 1; $page <= $total_requests; $page++ ) {
				$current_page_data = Astra_Sites_File_System::get_instance()->get_json_file_content( 'astra-sites-and-pages-page-' . $page . '.json' );
				if ( ! empty( $current_page_data ) ) {
					foreach ( $current_page_data as $page_id => $page_data ) {
						$sites_and_pages[ $page_id ] = $page_data;
					}
				}
			}

			return $sites_and_pages;
		}

		/**
		 * Get all sites
		 *
		 * @since 2.2.4
		 * @param  string $option Site options name.
		 * @return mixed Site Option value.
		 */
		public function get_api_option( $option ) {
			return get_site_option( $option, array() );
		}

		/**
		 * Get all blocks
		 *
		 * @since 2.0.0
		 * @return array All Elementor Blocks.
		 */
		public function get_all_blocks() {

			$blocks         = array();
			$total_requests = (int) Astra_Sites_File_System::get_instance()->get_json_file_content( 'astra-blocks-requests.json' );

			for ( $page = 1; $page <= $total_requests; $page++ ) {
				$current_page_data = Astra_Sites_File_System::get_instance()->get_json_file_content( 'astra-blocks-' . $page . '.json' );
				if ( ! empty( $current_page_data ) ) {
					foreach ( $current_page_data as $page_id => $page_data ) {
						$blocks[ $page_id ] = $page_data;
					}
				}
			}

			return $blocks;
		}

		/**
		 * Load all the required files in the importer.
		 *
		 * @since  1.0.0
		 */
		private function includes() {

			require_once ASTRA_SITES_DIR . 'inc/classes/functions.php';
			require_once ASTRA_SITES_DIR . 'inc/classes/class-astra-sites-update.php';
			require_once ASTRA_SITES_DIR . 'inc/classes/class-astra-sites-utils.php';
			require_once ASTRA_SITES_DIR . 'inc/classes/class-astra-sites-error-handler.php';
			require_once ASTRA_SITES_DIR . 'inc/classes/class-astra-sites-white-label.php';
			require_once ASTRA_SITES_DIR . 'inc/classes/class-astra-sites-page.php';
			require_once ASTRA_SITES_DIR . 'inc/classes/class-astra-sites-elementor-pages.php';
			require_once ASTRA_SITES_DIR . 'inc/classes/class-astra-sites-elementor-images.php';
			require_once ASTRA_SITES_DIR . 'inc/classes/compatibility/class-astra-sites-compatibility.php';
			require_once ASTRA_SITES_DIR . 'inc/classes/class-astra-sites-importer.php';
			require_once ASTRA_SITES_DIR . 'inc/classes/class-astra-sites-wp-cli.php';
			require_once ASTRA_SITES_DIR . 'inc/lib/class-astra-sites-ast-block-templates.php';
			require_once ASTRA_SITES_DIR . 'inc/lib/whats-new/class-astra-sites-whats-new.php';
			require_once ASTRA_SITES_DIR . 'inc/lib/class-astra-sites-zip-ai.php';
			require_once ASTRA_SITES_DIR . 'inc/lib/class-astra-sites-zipwp-images.php';
			require_once ASTRA_SITES_DIR . 'inc/lib/onboarding/class-onboarding.php';
			require_once ASTRA_SITES_DIR . 'inc/classes/class-astra-sites-file-system.php';
		}

		/**
		 * Retrieves the required plugins data based on the response and required plugin list.
		 *
		 * @param array             $response            The response containing the plugin data.
		 * @param array             $required_plugins    The list of required plugins.
		 * @param array<int,string> $features    The list of selected features.
		 * @since 3.2.5
		 * @return array                     The array of required plugins data.
		 */
		public function get_required_plugins_data( $response, $required_plugins, $features = array() ) {

			$learndash_course_grid = 'https://www.learndash.com/add-on/course-grid/';
			$learndash_woocommerce = 'https://www.learndash.com/add-on/woocommerce/';
			if ( is_plugin_active( 'sfwd-lms/sfwd_lms.php' ) ) {
				$learndash_addons_url  = admin_url( 'admin.php?page=learndash_lms_addons' );
				$learndash_course_grid = $learndash_addons_url;
				$learndash_woocommerce = $learndash_addons_url;
			}

			$third_party_required_plugins = array();
			$third_party_plugins          = array(
				'sfwd-lms'              => array(
					'init' => 'sfwd-lms/sfwd_lms.php',
					'name' => 'LearnDash LMS',
					'link' => 'https://www.learndash.com/',
				),
				'learndash-course-grid' => array(
					'init' => 'learndash-course-grid/learndash_course_grid.php',
					'name' => 'LearnDash Course Grid',
					'link' => $learndash_course_grid,
				),
				'learndash-woocommerce' => array(
					'init' => 'learndash-woocommerce/learndash_woocommerce.php',
					'name' => 'LearnDash WooCommerce Integration',
					'link' => $learndash_woocommerce,
				),
			);

			$plugin_updates          = get_plugin_updates();
			$update_avilable_plugins = array();
			$incompatible_plugins = array();

			if ( ! empty( $features ) ) {
				$required_plugins = $this->get_feature_plugin_list( $features, $required_plugins );
			}
			
			if ( ! empty( $required_plugins ) ) {
				$php_version = Astra_Sites_Onboarding_Setup::get_instance()->get_php_version();
				foreach ( $required_plugins as $key => $plugin ) {

					$plugin = (array) $plugin;

					if ( 'woocommerce' === $plugin['slug'] && version_compare( $php_version, '7.0', '<' ) ) {
						$plugin['min_php_version'] = '7.0';
						$incompatible_plugins[] = $plugin;
					}

					if ( 'presto-player' === $plugin['slug'] && version_compare( $php_version, '7.3', '<' ) ) {
						$plugin['min_php_version'] = '7.3';
						$incompatible_plugins[] = $plugin;
					}

					/**
					 * Has Pro Version Support?
					 * And
					 * Is Pro Version Installed?
					 */
					$plugin_pro = $this->pro_plugin_exist( $plugin['init'] );
					if ( $plugin_pro ) {

						if ( array_key_exists( $plugin_pro['init'], $plugin_updates ) ) {
							$update_avilable_plugins[] = $plugin_pro;
						}

						// Pro - Active.
						if ( is_plugin_active( $plugin_pro['init'] ) ) {
							$response['active'][] = $plugin_pro;

							$this->after_plugin_activate( $plugin['init'] );

							// Pro - Inactive.
						} else {
							$response['inactive'][] = $plugin_pro;
						}
					} else {
						if ( array_key_exists( $plugin['init'], $plugin_updates ) ) {
							$update_avilable_plugins[] = $plugin;
						}

						// Lite - Installed but Inactive.
						if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin['init'] ) && is_plugin_inactive( $plugin['init'] ) ) {
							$link = wp_nonce_url(
								add_query_arg(
									array(
										'action' => 'activate',
										'plugin' => $plugin['init'],
									),
									admin_url( 'plugins.php' )
								),
								'activate-plugin_' . $plugin['init']
							);
							$link = str_replace( '&amp;', '&', $link );
							$plugin['action'] = $link;
							$response['inactive'][] = $plugin;

							// Lite - Not Installed.
						} elseif ( ! file_exists( WP_PLUGIN_DIR . '/' . $plugin['init'] ) ) {

							// Added premium plugins which need to install first.
							if ( array_key_exists( $plugin['slug'], $third_party_plugins ) ) {
								$third_party_required_plugins[] = $third_party_plugins[ $plugin['slug'] ];
							} else {
								$link = wp_nonce_url(
									add_query_arg(
										array(
											'action' => 'install-plugin',
											'plugin' => $plugin['slug'],
										),
										admin_url( 'update.php' )
									),
									'install-plugin_' . $plugin['slug']
								);
								$link = str_replace( '&amp;', '&', $link );
								$plugin['action'] = $link;
								$response['notinstalled'][] = $plugin;
							}

							// Lite - Active.
						} else {
							$response['active'][] = $plugin;

							$this->after_plugin_activate( $plugin['init'] );
						}
					}
				}
			}

			// Checking the `install_plugins` and `activate_plugins` capability for the current user.
			// To perform plugin installation process.
			if (
				( ! defined( 'WP_CLI' ) && wp_doing_ajax() ) &&
				( ( ! current_user_can( 'install_plugins' ) && ! empty( $response['notinstalled'] ) ) || ( ! current_user_can( 'activate_plugins' ) && ! empty( $response['inactive'] ) ) ) ) {
				$message               = __( 'Insufficient Permission. Please contact your Super Admin to allow the install required plugin permissions.', 'astra-sites' );
				$required_plugins_list = array_merge( $response['notinstalled'], $response['inactive'] );
				$markup                = $message;
				$markup               .= '<ul>';
				foreach ( $required_plugins_list as $key => $required_plugin ) {
					$markup .= '<li>' . esc_html( $required_plugin['name'] ) . '</li>';
				}
				$markup .= '</ul>';

				wp_send_json_error( $markup );
			}

			$data = array(
				'required_plugins'             => $response,
				'third_party_required_plugins' => $third_party_required_plugins,
				'update_avilable_plugins'      => $update_avilable_plugins,
				'incompatible_plugins'         => $incompatible_plugins,
			);

			return $data;
		}

		/**
		 * Get all required plugin list.
		 *
		 * @param  array<int,string>               $features list of features.
		 * @param  array<int,array<string,string>> $required_plugins required plugins.
		 * @return array<int,array<string,string>> The array of required plugins data.
		 */
		public function get_feature_plugin_list( $features, $required_plugins = array() ) {
			foreach ( $features as $feature ) {

				switch ( $feature ) {
					case 'ecommerce':
					case 'donations':
						$required_plugins[] = array(
							'name' => 'SureCart',
							'slug' => 'surecart',
							'init' => 'surecart/surecart.php',
						);
						break;
					case 'automation-integrations':
						$required_plugins[] = array(
							'name' => 'SureTriggers',
							'slug' => 'suretriggers',
							'init' => 'suretriggers/suretriggers.php',
						);
						break;
					case 'sales-funnels':
						$required_plugins[] = array(
							'name' => 'CartFlows',
							'slug' => 'cartflows',
							'init' => 'cartflows/cartflows.php',
						);
						$required_plugins[] = array(
							'name' => 'Woocommerce Cart Abandonment Recovery',
							'slug' => 'woo-cart-abandonment-recovery',
							'init' => 'woo-cart-abandonment-recovery/woo-cart-abandonment-recovery.php',
						);
						break;
					case 'video-player':
						$required_plugins[] = array(
							'name' => 'Preso Player',
							'slug' => 'presto-player',
							'init' => 'presto-player/presto-player.php',
						);
						break;
					case 'live-chat':
						$required_plugins[] = array(
							'name' => 'WP Live Chat Support',
							'slug' => 'wp-live-chat-support',
							'init' => 'wp-live-chat-support/wp-live-chat-support.php',
						);
						break;
					default:
						break;
				}
			}

			return $required_plugins;
		}


		/**
		 * After Plugin Activate
		 *
		 * @since 2.0.0
		 *
		 * @param  string $plugin_init        Plugin Init File.
		 * @param  array  $options            Site Options.
		 * @param  array  $enabled_extensions Enabled Extensions.
		 * @return void
		 */
		public function after_plugin_activate( $plugin_init = '', $options = array(), $enabled_extensions = array() ) {
			$data = array(
				'astra_site_options' => $options,
				'enabled_extensions' => $enabled_extensions,
			);

			do_action( 'astra_sites_after_plugin_activation', $plugin_init, $data );
		}

		/**
		 * Has Pro Version Support?
		 * And
		 * Is Pro Version Installed?
		 *
		 * Check Pro plugin version exist of requested plugin lite version.
		 *
		 * Eg. If plugin 'BB Lite Version' required to import demo. Then we check the 'BB Agency Version' is exist?
		 * If yes then we only 'Activate' Agency Version. [We couldn't install agency version.]
		 * Else we 'Activate' or 'Install' Lite Version.
		 *
		 * @since 1.0.1
		 *
		 * @param  string $lite_version Lite version init file.
		 * @return mixed               Return false if not installed or not supported by us
		 *                                    else return 'Pro' version details.
		 */
		public function pro_plugin_exist( $lite_version = '' ) {

			// Lite init => Pro init.
			$plugins = apply_filters(
				'astra_sites_pro_plugin_exist',
				array(
					'beaver-builder-lite-version/fl-builder.php' => array(
						'slug' => 'bb-plugin',
						'init' => 'bb-plugin/fl-builder.php',
						'name' => 'Beaver Builder Plugin',
					),
					'ultimate-addons-for-beaver-builder-lite/bb-ultimate-addon.php' => array(
						'slug' => 'bb-ultimate-addon',
						'init' => 'bb-ultimate-addon/bb-ultimate-addon.php',
						'name' => 'Ultimate Addon for Beaver Builder',
					),
					'wpforms-lite/wpforms.php' => array(
						'slug' => 'wpforms',
						'init' => 'wpforms/wpforms.php',
						'name' => 'WPForms',
					),
				),
				$lite_version
			);

			if ( isset( $plugins[ $lite_version ] ) ) {

				// Pro plugin directory exist?
				if ( file_exists( WP_PLUGIN_DIR . '/' . $plugins[ $lite_version ]['init'] ) ) {
					return $plugins[ $lite_version ];
				}
			}

			return false;
		}

		/**
		 * Get Default Page Builders
		 *
		 * @since 2.0.0
		 * @return array
		 */
		public function get_default_page_builders() {
			return array(
				array(
					'id'   => 42,
					'slug' => 'gutenberg',
					'name' => 'Gutenberg',
				),
				array(
					'id'   => 33,
					'slug' => 'elementor',
					'name' => 'Elementor',
				),
				array(
					'id'   => 34,
					'slug' => 'beaver-builder',
					'name' => 'Beaver Builder',
				),
				array(
					'id'   => 41,
					'slug' => 'brizy',
					'name' => 'Brizy',
				),
			);
		}

		/**
		 * Get Page Builders
		 *
		 * @since 2.0.0
		 * @return array
		 */
		public function get_page_builders() {
			return $this->get_default_page_builders();
		}

		/**
		 * Get Page Builder Filed
		 *
		 * @since 2.0.0
		 * @param  string $page_builder Page Bulider.
		 * @param  string $field        Field name.
		 * @return mixed
		 */
		public function get_page_builder_field( $page_builder = '', $field = '' ) {
			if ( empty( $page_builder ) ) {
				return '';
			}

			$page_builders = self::get_instance()->get_page_builders();
			if ( empty( $page_builders ) ) {
				return '';
			}

			foreach ( $page_builders as $key => $current_page_builder ) {
				if ( $page_builder === $current_page_builder['slug'] ) {
					if ( isset( $current_page_builder[ $field ] ) ) {
						return $current_page_builder[ $field ];
					}
				}
			}

			return '';
		}

		/**
		 * Get License Key
		 *
		 * @since 2.0.0
		 * @return string
		 */
		public function get_license_key() {
			if ( class_exists( 'BSF_License_Manager' ) ) {
				if ( BSF_License_Manager::bsf_is_active_license( 'astra-pro-sites' ) ) {
					return BSF_License_Manager::instance()->bsf_get_product_info( 'astra-pro-sites', 'purchase_key' );
				}
			}

			return '';
		}

		/**
		 * Get Sync Complete Message
		 *
		 * @since 2.0.0
		 * @param  boolean $echo Echo the message.
		 * @return mixed
		 */
		public function get_sync_complete_message( $echo = false ) {

			$message = __( 'Template library refreshed!', 'astra-sites' );

			if ( $echo ) {
				echo esc_html( $message );
			} else {
				return esc_html( $message );
			}
		}

		/**
		 * Get an instance of WP_Filesystem_Direct.
		 *
		 * @since 2.0.0
		 * @return mixed A WP_Filesystem_Direct instance.
		 */
		public static function get_filesystem() {
			global $wp_filesystem;

			require_once ABSPATH . '/wp-admin/includes/file.php';

			WP_Filesystem();

			return $wp_filesystem;
		}

		/**
		 * Disable WP-Forms redirect.
		 *
		 * @return void.
		 */
		public function disable_wp_forms_redirect() {
			$wp_forms_redirect = get_transient( 'wpforms_activation_redirect' );

			if ( ! empty( $wp_forms_redirect ) && '' !== $wp_forms_redirect ) {
				delete_transient( 'wpforms_activation_redirect' );
			}
		}

		/**
		 * Admin Dashboard Notices.
		 *
		 * @since 3.1.17
		 * @return void
		 */
		public function admin_dashboard_notices() {
			if ( defined( 'ASTRA_SITES_VER' ) ) {
				add_action( 'admin_notices', array( $this, 'admin_welcome_notices' ) );
			} elseif ( defined( 'ASTRA_PRO_SITES_VER' ) ) {
				add_action( 'admin_notices', array( $this, 'admin_welcome_notices' ) );
			}
		}

		/**
		 * Admin Welcome Notice.
		 *
		 * @since 3.1.17
		 * @return void
		 */
		public function admin_welcome_notices() {
			$first_import_status = get_option( 'astra_sites_import_complete', false );
			Astra_Notices::add_notice(
				array(
					'id'      => 'astra-sites-welcome-notice',
					'type'    => 'notice',
					'class'   => 'astra-sites-welcome',
					'show_if' => ( false === Astra_Sites_White_Label::get_instance()->is_white_labeled() && empty( $first_import_status ) ),
					/* translators: %1$s white label plugin name and %2$s deactivation link */
					'message' => sprintf(
						'<div class="notice-welcome-container">
							<div class="text-section">
								<div class="logo-section">
									<img src="' . esc_url( ASTRA_SITES_URI . 'inc/lib/onboarding/assets/images/logo.svg' ) . '" />
									<h3>' . __( 'Starter Templates', 'astra-sites' ) . '</h3>
								</div>
								<h1 class="text-heading">' . __( 'Build Your Dream Site in Minutes With AI', 'astra-sites' ) . '</h1>
								<p>' . __( 'Say goodbye to the days of spending weeks designing and building your website.<br/> You can now create professional-grade websites in minutes.', 'astra-sites' ) . '</p>
								<div class="button-section">
									<a href="' . home_url() . '/wp-admin/themes.php?page=starter-templates" class="text-button">' . __( 'Let’s Get Started', 'astra-sites' ) . '</a>
									<a href="javascript:void(0);" class="scratch-link astra-notice-close">' . __( 'I want to build this website from scratch', 'astra-sites' ) . '</a>
								</div>
							</div>
							<div class="showcase-section">
								<img src="' . esc_url( ASTRA_SITES_URI . 'inc/assets/images/templates-showcase.png' ) . '" />
							</div>
						</div>'
					),
				)
			);
		}

		/**
		 * Enqueue Astra Notices CSS.
		 *
		 * @since 3.1.17
		 *
		 * @return void
		 */
		public static function notice_assets() {
			$file = 'astra-notices.css';
			wp_enqueue_style( 'astra-sites-notices', ASTRA_SITES_URI . 'inc/assets/css/' . $file, array(), ASTRA_SITES_VER );
		}


		/**
		 * Display notice on dashboard if WP_Filesystem() false.
		 *
		 * @return void
		 */
		public function check_filesystem_access_notice() {
			// Check if WP_Filesystem() returns false.
			if ( ! WP_Filesystem() ) {
				// Display a notice on the dashboard.
				echo '<div class="error"><p>' . esc_html__( 'Required WP_Filesystem Permissions to import the templates from Starter Templates are missing.', 'astra-sites' ) . '</p></div>';
			}
		}

		/**
		 * Remove query parameters from the URL.
		 * 
		 * @param  String   $url URL.
		 * @param  String[] $params Query parameters.
		 *
		 * @return string       URL.
		 */
		public function remove_query_params( $url, $params ) {
			$parts = wp_parse_url( $url );
			$query = array();

			if ( isset( $parts['query'] ) ) {
				parse_str( $parts['query'], $query );
			}

			foreach ( $params as $param ) {
				unset( $query[ $param ] );
			}

			$query = http_build_query( $query );

			if ( ! empty( $query ) ) {
				$query = '?' . $query;
			}

			if ( ! isset( $parts['host'] ) ) {
				return $url;
			}

			$parts['scheme'] = isset( $parts['scheme'] ) ? $parts['scheme'] : 'https';
			$parts['path']   = isset( $parts['path'] ) ? $parts['path'] : '/';
			$parts['port']   = isset( $parts['port'] ) ? ':' . $parts['port'] : '';

			return $parts['scheme'] . '://' . $parts['host'] . $parts['port'] . $parts['path'] . $query;
		}
	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	Astra_Sites::get_instance();

endif;
