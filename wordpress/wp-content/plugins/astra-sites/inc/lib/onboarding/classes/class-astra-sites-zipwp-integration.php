<?php


class Astra_Sites_ZipWP_Integration {

    /**
     * Member Variable
     *
     * @var instance
     */
    private static $instance;

    /**
     * Initiator
     *
     * @since 4.0.0
     */
    public static function get_instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     *
     * @since 4.0.0
     */
    public function __construct() {
        $this->define_constants();
        add_action( 'admin_init' , array( $this, 'save_auth_token' )  );
        add_action( 'wp_enqueue_scripts', array( $this, 'register_preview_scripts' ) );
    }

    /**
	 * Check whether a given request has permission to read notes.
	 *
	 * @param  object $request WP_REST_Request Full details about the request.
	 * @return object|boolean
	 */
	public function get_items_permissions_check( $request ) {

		if ( ! current_user_can( 'manage_options' ) ) {
			return new \WP_Error(
				'gt_rest_cannot_access',
				__( 'Sorry, you are not allowed to do that.', 'ast-block-templates', 'astra-sites' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}
		return true;
	}

    /**
	 * Register scripts.
	 *
	 * @return void
	 * @since  4.0.0
	 */
	public function register_preview_scripts() {

        if ( is_customize_preview() ) {
            return;
        }

		$handle       = 'starter-templates-zip-preview';
		$js_deps_file = INTELLIGENT_TEMPLATES_DIR . 'assets/dist/template-preview/main.asset.php';
		$js_dep       = [
			'dependencies' => array(),
			'version'      => ASTRA_SITES_VER,
		];

		if ( file_exists( $js_deps_file ) ) {

			$script_info = include_once $js_deps_file;

			if ( isset( $script_info['dependencies'] ) && isset( $script_info['version'] ) ) {
				$js_dep['dependencies'] = $script_info['dependencies'];
				$js_dep['version']      = $script_info['version'];
			}
		}

		wp_register_script( $handle, INTELLIGENT_TEMPLATES_URI . 'assets/dist/template-preview/main.js', $js_dep['dependencies'], $js_dep['version'], true );

		$color_palette_prefix     = '--ast-global-';
		$ele_color_palette_prefix = '--ast-global-';

		if ( class_exists( 'Astra_Global_Palette' ) ) {

			$astra_callable_class = new \Astra_Global_Palette();

			if ( is_callable( array( $astra_callable_class, 'get_css_variable_prefix' ) ) ) {
				$color_palette_prefix = \Astra_Global_Palette::get_css_variable_prefix();
			}

			if ( is_callable( array( $astra_callable_class, 'get_palette_slugs' ) ) ) {
				$ele_color_palette_prefix = \Astra_Global_Palette::get_palette_slugs();
			}
		}

		wp_localize_script(
			$handle,
			'starter_templates_zip_preview',
			array(
				'AstColorPaletteVarPrefix'    => $color_palette_prefix,
				'AstEleColorPaletteVarPrefix' => $ele_color_palette_prefix,
			)
		);

		wp_enqueue_script( $handle );
		wp_add_inline_style( 'starter-templates-zip-preview-custom', '#wpadminbar { display: none !important; }' );
	}

    /**
     * Define Constants
     *
     * @since 4.0.0
     * @return void
     */
    public function define_constants() : void {

        if ( ! defined( 'ZIPWP_APP' ) ) {
            define( 'ZIPWP_APP', apply_filters( 'ast_block_templates_zip_app_url', 'https://app.zipwp.com/auth' ) );
        }

        if ( ! defined( 'ZIPWP_API' ) ) {
            define( 'ZIPWP_API', apply_filters( 'ast_block_templates_zip_api_url', 'https://api.zipwp.com/api' ) );
        }
    }

    /**
     * Save auth token
     *
     * @since 4.0.0
     * @return void
     */
    public function save_auth_token() {

        global $pagenow;

        if( ! is_admin() || ! isset( $_GET['page'] ) ) {
            return;
        }

        // Check if we are on the starter templates page.
        if( 'themes.php' !== $pagenow || 'starter-templates' !== $_GET['page'] ) {
            return;
        }

        if ( isset( $_GET['token'] ) && isset( $_GET['email'] ) && isset( $_GET['credit_token'] ) ) {

            $spec_ai_settings = Astra_Sites_ZipWP_Helper::get_setting();

			// Update the auth token if needed.
			if ( isset( $_GET['credit_token'] ) && is_string( $_GET['credit_token'] ) ) {
				$spec_ai_settings['auth_token'] = Astra_Sites_ZipWP_Helper::encrypt( sanitize_text_field( $_GET['credit_token'] ) );
			}

			// Update the Zip token if needed.
			if ( isset( $_GET['token'] ) && is_string( $_GET['token'] ) ) {
				$spec_ai_settings['zip_token'] = Astra_Sites_ZipWP_Helper::encrypt( sanitize_text_field( $_GET['token'] ) );
			}

			// Update the email if needed.
			if ( isset( $_GET['email'] ) && is_string( $_GET['email'] ) ) {
				$spec_ai_settings['email'] = sanitize_email( $_GET['email'] );
			}

			update_option( 'zip_ai_settings', $spec_ai_settings );
        }
    }

    /**
     * Get ZIP Plans.
     */
    public function get_zip_plans() {
        $api_endpoint = Astra_Sites_ZipWP_Api::get_instance()->get_api_domain() . '/plan/current-plan';

		$request_args = array(
			'headers' => Astra_Sites_ZipWP_Api::get_instance()->get_api_headers(),
			'timeout' => 100,
            'sslverify' => false,
		);
		$response = wp_safe_remote_get( $api_endpoint, $request_args );

		if ( is_wp_error( $response ) ) {
			// There was an error in the request.
			return array(
                'data' => 'Failed ' . $response->get_error_message(),
                'status'  => false,
            );
		} else {
			$response_code = wp_remote_retrieve_response_code( $response );
			$response_body = wp_remote_retrieve_body( $response );
			if ( 200 === $response_code ) {
				$response_data = json_decode( $response_body, true );
				if ( $response_data ) {
                    return array(
                        'data' => $response_data,
                        'status'  => true,
                    );
				} else {
					return array(
                        'data' => 'Failed ' . $response_data,
                        'status'  => false,
                    );
				}
			} else {
				return array(
                    'data' => 'Failed',
                    'status'  => false,
                );
			}
		}
    }
}

Astra_Sites_ZipWP_Integration::get_instance();
