<?php
/**
 * Zip AI - Admin Configurations.
 *
 * @package zip-ai
 */

namespace ZipAI\Classes;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Classes to be used, in alphabetical order.
use ZipAI\Classes\Helper;
use ZipAI\Classes\Module;

/**
 * The Sidebar_Configurations Class.
 */
class Sidebar_Configurations {

	/**
	 * The namespace for the Rest Routes.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $namespace = 'zip_ai';

	/**
	 * Instance of this class.
	 *
	 * @since 1.0.0
	 * @var object Class object.
	 */
	private static $instance;

	/**
	 * Initiator of this class.
	 *
	 * @since 1.0.0
	 * @return object initialized object of this class.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor of this class.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct() {
		// Setup the Sidebar Rest Routes.
		add_action( 'rest_api_init', array( $this, 'register_route' ) );

		// Setup the Sidebar Auth Ajax.
		add_action( 'wp_ajax_verify_zip_ai_authenticity', array( $this, 'verify_authenticity' ) );

		add_action( 'admin_bar_menu', array( $this, 'add_admin_trigger' ), 999 );

		// Render the Sidebar React App in the Footer in the Gutenberg Editor, Admin, and the Front-end.
		add_action( 'admin_footer', array( $this, 'render_sidebar_markup' ) );
		add_action( 'wp_footer', array( $this, 'render_sidebar_markup' ) );

		// Add the Sidebar to the Gutenberg Editor, Admin, and the Front-end.
		add_action( 'admin_enqueue_scripts', array( $this, 'load_sidebar_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_sidebar_assets' ) );
	}

	/**
	 * Register All Routes.
	 *
	 * @hooked - rest_api_init
	 * @since 1.0.0
	 * @return void
	 */
	public function register_route() {
		register_rest_route(
			$this->namespace,
			'/generate',
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'generate_ai_content' ),
					'permission_callback' => function () {
						return current_user_can( 'edit_posts' );
					},
					'args'                => array(
						'use_system_message' => array(
							'sanitize_callback' => array( $this, 'sanitize_boolean_field' ),
						),
					),
				),
			)
		);
	}

	/**
	 * Checks whether the value is boolean or not.
	 *
	 * @param mixed $value value to be checked.
	 * @since 1.0.0
	 * @return boolean
	 */
	public function sanitize_boolean_field( $value ) {
		return filter_var( $value, FILTER_VALIDATE_BOOLEAN );
	}

	/**
	 * Update ZIP AI Assistant options.
	 *
	 * @param array $params Parameters for updating options.
	 * @since 1.1.6
	 * @return void
	 */
	public function update_zip_ai_assistant_options( $params ) {

		$last_message_tone = '';
		$last_index        = count( $params['message_array'] ) - 1;

		// Find the last match if it exist.
		for ( $i = $last_index; $i >= 0; $i-- ) {
			$content = $params['message_array'][ $i ]['content'];

			preg_match( '/Rewrite in a (\w+) tone/', $content, $matches_tone );
			if ( ! empty( $matches_tone ) && empty( $last_message_tone ) ) {
				$last_message_tone = $matches_tone[1];
			}

			// If both language and message tone are found, break the loop.
			if ( ! empty( $last_message_tone ) ) {
				break;
			}
		}

		$option_name     = 'zip_ai_assistant_option';
		$current_options = array();

		// If options exist, fetch them.
		if ( get_option( $option_name ) ) {
			$current_options = get_option( $option_name );
		}

		if ( ! empty( $last_message_tone ) ) {
			$current_options['last_used']['changeTone'] = [
				'value' => $last_message_tone,
				'label' => __( ucfirst( $last_message_tone ), 'zip-ai' ), //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
			];
		}

		// Update options in the database.
		Helper::update_admin_settings_option( $option_name, $current_options );
	}



	/**
	 * Fetches ai data from the middleware server.
	 *
	 * @param \WP_REST_Request $request request object.
	 * @since 1.0.0
	 * @return void
	 */
	public function generate_ai_content( $request ) {

		// Get the params.
		$params = $request->get_params();

		// Update the ZIP AI Assistant options for last used language and tone.
		$this->update_zip_ai_assistant_options( $params );

		// If the nessage array doesn't exist, abandon ship.
		if ( empty( $params['message_array'] ) || ! is_array( $params['message_array'] ) ) {
			wp_send_json_error( array( 'message' => __( 'The message array was not supplied', 'zip-ai' ) ) );
		}

		// Set the token count to 0, and create messages array.
		$token_count = 0;
		$messages    = array();

		// Start with the last message - going upwards until the token count hits 2000.
		foreach ( array_reverse( $params['message_array'] ) as $current_message ) {
			// If the message content doesn't exist, skip it.
			if ( empty( $current_message['content'] ) ) {
				continue;
			}

			// Get the token count, and if it's greater than 2000, break out of the loop.
			$token_count += Helper::get_token_count( $current_message['content'] );
			if ( $token_count >= 2000 ) {
				break;
			}

			// Add the message to the start of the messages to send to the SCS Middleware.
			array_unshift( $messages, $current_message );
		}

		// Finally add the system message to the start of the array.
		if ( ! empty( $params['use_system_message'] ) ) {
			array_unshift(
				$messages,
				array(
					'role'    => 'system',
					'content' => 'You are my AI Assistant. You are a content writer that writes content for my website.\n\n\nYou will only generate content for what you are asked.',
				)
			);
		}

		// Set the required values to send to the middleware server.
		$endpoint = 'chat/completions';
		$data     = array(
			'temperature'       => 0.7,
			'top_p'             => 1,
			'frequency_penalty' => 0.8,
			'presence_penalty'  => 1,
			'model'             => 'gpt-3.5-turbo',
			'messages'          => $messages,
		);

		// Get the response from the endpoint.
		$response = Helper::get_credit_server_response( $endpoint, $data );

		if ( ! empty( $response['error'] ) ) {
			// If the response has an error, handle it and report it back.
			$message = '';
			if ( ! empty( $response['error']['message'] ) ) { // If any error message received from OpenAI.
				$message = $response['error']['message'];
			} elseif ( is_string( $response['error'] ) ) {  // If any error message received from server.
				if ( ! empty( $response['code'] && is_string( $response['code'] ) ) ) {
					$message = $this->custom_message( $response['code'] );
				}
				$message = ! empty( $message ) ? $message : $response['error'];
			}
			wp_send_json_error( array( 'message' => $message ) );
		} elseif ( is_array( $response['choices'] ) && ! empty( $response['choices'][0]['message']['content'] ) ) {
			// If the message was sent successfully, send it successfully.
			wp_send_json_success( array( 'message' => $response['choices'][0]['message']['content'] ) );
		} else {
			// If you've reached here, then something has definitely gone amuck. Abandon ship.
			wp_send_json_error( array( 'message' => __( 'Something went wrong', 'zip-ai' ) ) );
		}//end if
	}

	/**
	 * This function converts the code recieved from scs to a readable error message.
	 * Useful to provide better language for error codes.
	 *
	 * @param string $code error code received from SCS ( Credits server ).
	 * @since 1.0.0
	 * @return string
	 */
	private function custom_message( $code ) {
		$message_array = array(
			'no_auth'              => __( 'Invalid auth token.', 'zip-ai' ),
			'insufficient_credits' => __( 'You have no credits left.', 'zip-ai' ),
		);

		return isset( $message_array[ $code ] ) ? $message_array[ $code ] : '';
	}

	/**
	 * Ajax handeler to verify the Zip AI authorization.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function verify_authenticity() {

		// Check the nonce.
		check_ajax_referer( 'zip_ai_ajax_nonce', 'nonce' );

		// Send a boolean based on whether the auth token has been added.
		wp_send_json_success( array( 'is_authorized' => Helper::is_authorized() ) );
	}

	/**
	 * Enqueue the AI Asssitant Sidebar assets.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function load_sidebar_assets() {
		// If the adminbar is not visible on this screen, abandon ship.
		if ( ! is_admin_bar_showing() ) {
			return;
		}

		// Set the required variables.
		$handle            = 'zip-ai-sidebar';
		$build_path        = ZIP_AI_DIR . 'sidebar/build/';
		$build_url         = ZIP_AI_URL . 'sidebar/build/';
		$script_asset_path = $build_path . 'sidebar-app.asset.php';
		$script_info       = file_exists( $script_asset_path )
			? include $script_asset_path
			: array(
				'dependencies' => array(),
				'version'      => ZIP_AI_VERSION,
			);

		// If this is in the front-end, remove any editor-specific dependencies.
		// This will work as intended because the React components for the editor have checks to render the same, leaving no errors.
		$script_dep = ! is_admin() ? array_diff(
			$script_info['dependencies'],
			[
				'wp-block-editor',
				'wp-edit-post',
				'wp-rich-text',
			]
		) : $script_info['dependencies'];

		// Register the sidebar scripts.
		wp_register_script(
			$handle,
			$build_url . 'sidebar-app.js',
			$script_dep,
			$script_info['version'],
			true
		);

		// Register the sidebar styles.
		wp_register_style(
			$handle,
			$build_url . 'sidebar-app.css',
			array(),
			ZIP_AI_VERSION
		);

		// Enqueue the sidebar scripts.
		wp_enqueue_script( $handle );
		// Set the script translations.
		wp_set_script_translations( $handle, 'zip-ai' );
		// Enqueue the sidebar styles.
		wp_enqueue_style( $handle );

		// Create the middleware parameters array.
		$middleware_params = [];

		// Get the collab product details, and extract the slug from there if it exists.
		$collab_product_details = apply_filters( 'zip_ai_collab_product_details', null );

		// If the collab details is an array and has the plugin slug, add it to the middleware params.
		if ( is_array( $collab_product_details )
			&& ! empty( $collab_product_details['product_slug'] )
			&& is_string( $collab_product_details['product_slug'] )
		) {
			$middleware_params['plugin'] = sanitize_text_field( $collab_product_details['product_slug'] );
		}

		// Localize the script required for the Zip AI Sidebar.
		wp_localize_script(
			$handle,
			'zip_ai_react',
			array(
				'ajax_url'                 => admin_url( 'admin-ajax.php' ),
				'ajax_nonce'               => wp_create_nonce( 'zip_ai_ajax_nonce' ),
				'admin_nonce'              => wp_create_nonce( 'zip_ai_admin_nonce' ),
				'current_post_id'          => get_the_ID(),
				'auth_middleware'          => Helper::get_auth_middleware_url( $middleware_params ),
				'is_authorized'            => Helper::is_authorized(),
				'is_ai_assistant_enabled'  => Module::is_enabled( 'ai_assistant' ),
				'is_customize_preview'     => is_customize_preview(),
				'collab_product_details'   => $collab_product_details,
				'zip_ai_assistant_options' => get_option( 'zip_ai_assistant_option' ),
			)
		);
	}

	/**
	 * Add the Zip AI Assistant Sidebar to the admin bar.
	 *
	 * @param object $admin_bar The admin bar object.
	 * @since 1.1.0
	 * @return void
	 */
	public function add_admin_trigger( $admin_bar ) {
		$args = array(
			'id'     => 'zip-ai-assistant',
			'title'  => '<span class="ab-icon" aria-hidden="true" style="margin: 0">
<svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false"><path d="M9.8132 15.9038L9 18.75L8.1868 15.9038C7.75968 14.4089 6.59112 13.2403 5.09619 12.8132L2.25 12L5.09619 11.1868C6.59113 10.7597 7.75968 9.59112 8.1868 8.09619L9 5.25L9.8132 8.09619C10.2403 9.59113 11.4089 10.7597 12.9038 11.1868L15.75 12L12.9038 12.8132C11.4089 13.2403 10.2403 14.4089 9.8132 15.9038Z" stroke="currentColor" stroke-width="1.4" fill="none" stroke-linecap="round" stroke-linejoin="round"></path><path d="M16.8942 20.5673L16.5 21.75L16.1058 20.5673C15.8818 19.8954 15.3546 19.3682 14.6827 19.1442L13.5 18.75L14.6827 18.3558C15.3546 18.1318 15.8818 17.6046 16.1058 16.9327L16.5 15.75L16.8942 16.9327C17.1182 17.6046 17.6454 18.1318 18.3173 18.3558L19.5 18.75L18.3173 19.1442C17.6454 19.3682 17.1182 19.8954 16.8942 20.5673Z" stroke="currentColor" stroke-width="1.4" fill="none" stroke-linecap="round" stroke-linejoin="round"></path><path d="M18.2589 8.71454L18 9.75L17.7411 8.71454C17.4388 7.50533 16.4947 6.56117 15.2855 6.25887L14.25 6L15.2855 5.74113C16.4947 5.43883 17.4388 4.49467 17.7411 3.28546L18 2.25L18.2589 3.28546C18.5612 4.49467 19.5053 5.43883 20.7145 5.74113L21.75 6L20.7145 6.25887C19.5053 6.56117 18.5612 7.50532 18.2589 8.71454Z" stroke="currentColor" stroke-width="1.4" fill="none" stroke-linecap="round" stroke-linejoin="round"></path></svg>
</span>',
			'href'   => 'javascript:void(0);',
			'parent' => 'top-secondary',
		);
		$admin_bar->add_node( $args );
	}

	/**
	 * Render the AI Assistant Sidebar markup.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public static function render_sidebar_markup() {
		// If the current user does not have the required capability, then don't render the empty div.
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}
		// If the adminbar is visible on this screen, render the admin trigger.
		if ( is_admin_bar_showing() ) {
			?>
				<div id="zip-ai-sidebar-admin-trigger"></div>
			<?php
		}
		// Render the sidebar div.
		?>
			<div id="zip-ai-sidebar"></div>
		<?php
	}
}
