<?php
/**
 * UAGB Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 *
 * @since   1.0.0
 * @package UAGB
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * UAGB_Init_Blocks.
 *
 * @package UAGB
 */
class UAGB_Init_Blocks {


	/**
	 * Member Variable
	 *
	 * @var instance
	 */
	private static $instance;

	/**
	 * Member Variable
	 *
	 * @var block activation
	 */
	private $active_blocks;

	/**
	 *  Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {

		// Hook: Editor assets.
		add_action( 'enqueue_block_editor_assets', array( $this, 'editor_assets' ) );

		if ( version_compare( get_bloginfo( 'version' ), '5.8', '>=' ) ) {
			add_filter( 'block_categories_all', array( $this, 'register_block_category' ), 999999, 2 );
		} else {
			add_filter( 'block_categories', array( $this, 'register_block_category' ), 999999, 2 );
		}

		add_action( 'wp_ajax_uagb_get_taxonomy', array( $this, 'get_taxonomy' ) );

		add_action( 'wp_ajax_uagb_gf_shortcode', array( $this, 'gf_shortcode' ) );
		add_action( 'wp_ajax_nopriv_uagb_gf_shortcode', array( $this, 'gf_shortcode' ) );

		add_action( 'wp_ajax_uagb_cf7_shortcode', array( $this, 'cf7_shortcode' ) );
		add_action( 'wp_ajax_nopriv_uagb_cf7_shortcode', array( $this, 'cf7_shortcode' ) );

		add_action( 'wp_ajax_uagb_forms_recaptcha', array( $this, 'forms_recaptcha' ) );

		// For Spectra Global Block Styles.
		add_action( 'wp_ajax_uag_global_block_styles', array( $this, 'uag_global_block_styles' ) );
		// For Spectra Global Quick Action Bar.
		add_action( 'wp_ajax_uag_global_sidebar_enabled', array( $this, 'uag_global_sidebar_enabled' ) );
		add_action( 'wp_ajax_uag_global_update_allowed_block', array( $this, 'uag_global_update_allowed_block' ) );

		if ( ! is_admin() ) {
			add_action( 'render_block', array( $this, 'render_block' ), 5, 2 );

			// For Spectra Global Block Styles.
			add_filter( 'render_block', array( $this, 'add_gbs_class' ), 10, 2 );
		}

		if ( current_user_can( 'edit_posts' ) ) {
			add_action( 'wp_ajax_uagb_svg_confirmation', array( $this, 'confirm_svg_upload' ) );
		}

		add_action( 'init', array( $this, 'register_popup_builder' ) );
	}

	/**
	 * Register the Popup Builder CPT.
	 *
	 * @return void
	 *
	 * @since 2.6.0
	 */
	public function register_popup_builder() {
		$supports = array(
			'title',
			'editor',
			'custom-fields',
			'author',
		);

		$labels = array(
			'name'               => _x( 'Popup Builder', 'plural', 'ultimate-addons-for-gutenberg' ),
			'singular_name'      => _x( 'Spectra Popup', 'singular', 'ultimate-addons-for-gutenberg' ),
			'view_item'          => __( 'View Popup', 'ultimate-addons-for-gutenberg' ),
			'add_new'            => __( 'Create Popup', 'ultimate-addons-for-gutenberg' ),
			'add_new_item'       => __( 'Create New Popup', 'ultimate-addons-for-gutenberg' ),
			'edit_item'          => __( 'Edit Popup', 'ultimate-addons-for-gutenberg' ),
			'new_item'           => __( 'New Popup', 'ultimate-addons-for-gutenberg' ),
			'search_items'       => __( 'Search Popups', 'ultimate-addons-for-gutenberg' ),
			'not_found'          => __( 'No Popups Found', 'ultimate-addons-for-gutenberg' ),
			'not_found_in_trash' => __( 'No Popups in Trash', 'ultimate-addons-for-gutenberg' ),
			'all_items'          => __( 'All Popups', 'ultimate-addons-for-gutenberg' ),
			'item_published'     => __( 'Popup Published', 'ultimate-addons-for-gutenberg' ),
			'item_updated'       => __( 'Popup Updated', 'ultimate-addons-for-gutenberg' ),
		);

		$type_args = array(
			'supports'          => $supports,
			'labels'            => $labels,
			'public'            => false,
			'show_in_menu'      => false,
			'show_in_admin_bar' => true,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'template_lock'     => 'all',
			'template'          => array(
				array( 'uagb/popup-builder', array() ),
			),
			'rewrite'           => array(
				'slug'       => 'spectra-popup',
				'with-front' => false,
				'pages'      => false,
			),
		);

		$meta_args_popup_type = array(
			'single'        => true,
			'type'          => 'string',
			'default'       => 'unset',
			'auth_callback' => '__return_true',
			'show_in_rest'  => true,
		);

		$meta_args_popup_enabled = array(
			'single'        => true,
			'type'          => 'boolean',
			'default'       => false,
			'auth_callback' => '__return_true',
			'show_in_rest'  => true,
		);

		$meta_args_popup_repetition = array(
			'single'        => true,
			'type'          => 'number',
			'default'       => 1,
			'auth_callback' => '__return_true',
			'show_in_rest'  => true,
		);

		register_post_type( 'spectra-popup', $type_args );

		register_post_meta( 'spectra-popup', 'spectra-popup-type', $meta_args_popup_type );
		register_post_meta( 'spectra-popup', 'spectra-popup-enabled', $meta_args_popup_enabled );
		register_post_meta( 'spectra-popup', 'spectra-popup-repetition', $meta_args_popup_repetition );
		do_action( 'register_spectra_pro_popup_meta' );

		$spectra_popup_dashboard = UAGB_Popup_Builder::create_for_admin();

		add_action( 'admin_enqueue_scripts', array( $spectra_popup_dashboard, 'popup_toggle_scripts' ) );
		add_action( 'wp_ajax_uag_update_popup_status', array( $spectra_popup_dashboard, 'update_popup_status' ) );

		do_action( 'spectra_pro_popup_dashboard' );

		add_filter( 'manage_spectra-popup_posts_columns', array( $spectra_popup_dashboard, 'popup_builder_admin_headings' ) );
		add_action( 'manage_spectra-popup_posts_custom_column', array( $spectra_popup_dashboard, 'popup_builder_admin_content' ), 10, 2 );
	}

	/**
	 * Render block.
	 *
	 * @param mixed $block_content The block content.
	 * @param array $block The block data.
	 * @since 1.21.0
	 * @return mixed Returns the new block content.
	 */
	public function render_block( $block_content, $block ) {

		if ( ! empty( $block['attrs']['UAGDisplayConditions'] ) ) {
			switch ( $block['attrs']['UAGDisplayConditions'] ) {
				case 'userstate':
					$block_content = $this->user_state_visibility( $block['attrs'], $block_content );
					break;

				case 'userRole':
					$block_content = $this->user_role_visibility( $block['attrs'], $block_content );
					break;

				case 'browser':
					$block_content = $this->browser_visibility( $block['attrs'], $block_content );
					break;

				case 'os':
					$block_content = $this->os_visibility( $block['attrs'], $block_content );
					break;
				case 'day':
					$block_content = $this->day_visibility( $block['attrs'], $block_content );
					break;
				default:
					// code...
					break;
			}
		}

		// Check if animations extension is enabled and an animation type is selected.
		if (
			'enabled' === \UAGB_Admin_Helper::get_admin_settings_option( 'uag_enable_animations_extension', 'enabled' ) &&
			! empty( $block['attrs']['UAGAnimationType'] )
		) {

			$attrs                                      = $block['attrs'];
			$attrs['UAGAnimationDoNotApplyToContainer'] = isset( $attrs['UAGAnimationDoNotApplyToContainer'] ) ? $attrs['UAGAnimationDoNotApplyToContainer'] : false;
			$block_positioning                          = ! empty( $attrs['UAGPosition'] ) && is_string( $attrs['UAGPosition'] ) ? $attrs['UAGPosition'] : false;

			// Container-specific animation attributes.
			if ( ! $attrs['UAGAnimationDoNotApplyToContainer'] ) {
				// Defaults aren't received here, hence we set them.
				// Without these defaults, empty data is sent to markup (which doesn't affect the functionality at all but still it's a good practice to follow).
				$attrs['UAGAnimationTime']   = isset( $attrs['UAGAnimationTime'] ) ? $attrs['UAGAnimationTime'] : 400;
				$attrs['UAGAnimationDelay']  = isset( $attrs['UAGAnimationDelay'] ) ? $attrs['UAGAnimationDelay'] : 0;
				$attrs['UAGAnimationEasing'] = isset( $attrs['UAGAnimationEasing'] ) ? $attrs['UAGAnimationEasing'] : 'ease';
				$attrs['UAGAnimationRepeat'] = isset( $attrs['UAGAnimationRepeat'] ) ? 'false' : 'true';

				// Container-specific animation attributes.
				$attrs['UAGAnimationDelayInterval'] = isset( $attrs['UAGAnimationDelayInterval'] ) ? $attrs['UAGAnimationDelayInterval'] : 200;

				// If this is a sticky element, don't update the attributes of this element just yet.
				if ( 'sticky' !== $block_positioning ) {
					$aos_attributes = '<div data-aos= "' . esc_attr( $attrs['UAGAnimationType'] ) . '" data-aos-duration="' . esc_attr( $attrs['UAGAnimationTime'] ) . '" data-aos-delay="' . esc_attr( $attrs['UAGAnimationDelay'] ) . '" data-aos-easing="' . esc_attr( $attrs['UAGAnimationEasing'] ) . '" data-aos-once="' . esc_attr( $attrs['UAGAnimationRepeat'] ) . '" ';
					$block_content  = preg_replace( '/<div /', $aos_attributes, $block_content, 1 );
				}
			}
		}

		// Render Block Manipulation for the required Spectra Blocks.
		$block_content = apply_filters( 'uagb_render_block', $block_content, $block );

		// Render Block Manipulation for the required Spectra Pro Blocks.
		$block_content = apply_filters( 'spectra_pro_render_block', $block_content, $block );

		return $block_content;
	}

	/**
	 * User State Visibility.
	 *
	 * @param array $block_attributes The block data.
	 * @param mixed $block_content The block content.
	 *
	 * @since 1.21.0
	 * @return mixed Returns the new block content.
	 */
	public function user_role_visibility( $block_attributes, $block_content ) {
		if ( empty( $block_attributes['UAGUserRole'] ) ) {
			return $block_content;
		}

		$user = wp_get_current_user();
		return is_user_logged_in() && ! empty( $user->roles ) && in_array( $block_attributes['UAGUserRole'], $user->roles, true ) ? '' : $block_content;
	}

	/**
	 * User State Visibility.
	 *
	 * @param array $block_attributes The block data.
	 * @param mixed $block_content The block content.
	 * @since 1.21.0
	 * @return mixed Returns the new block content.
	 */
	public function os_visibility( $block_attributes, $block_content ) {

		if ( empty( $block_attributes['UAGSystem'] ) ) {
			return $block_content;
		}

		$os = array(
			'iphone'   => '(iPhone)',
			'android'  => '(Android)',
			'windows'  => 'Win16|(Windows 95)|(Win95)|(Windows_95)|(Windows 98)|(Win98)|(Windows NT 5.0)|(Windows 2000)|(Windows NT 5.1)|(Windows XP)|(Windows NT 5.2)|(Windows NT 6.0)|(Windows Vista)|(Windows NT 6.1)|(Windows 7)|(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)|Windows ME',
			'open_bsd' => 'OpenBSD',
			'sun_os'   => 'SunOS',
			'linux'    => '(Linux)|(X11)',
			'mac_os'   => '(Mac_PowerPC)|(Macintosh)',
		);

		$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ) : '';

		return isset( $os[ $block_attributes['UAGSystem'] ] ) && preg_match( '@' . $os[ $block_attributes['UAGSystem'] ] . '@', $user_agent ) ? '' : $block_content;
	}

	/**
	 * User State Visibility.
	 *
	 * @param array $block_attributes The block data.
	 * @param mixed $block_content The block content.
	 *
	 * @since 1.21.0
	 * @return mixed Returns the new block content.
	 */
	public function browser_visibility( $block_attributes, $block_content ) {

		if ( empty( $block_attributes['UAGBrowser'] ) ) {
			return $block_content;
		}

		$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? UAGB_Helper::get_browser_name( sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ) ) : '';

		return $block_attributes['UAGBrowser'] === $user_agent ? '' : $block_content;
	}

	/**
	 * User State Visibility.
	 *
	 * @param array $block_attributes The block data.
	 * @param mixed $block_content The block content.
	 *
	 * @since 1.21.0
	 * @return mixed Returns the new block content.
	 */
	public function user_state_visibility( $block_attributes, $block_content ) {

		if ( ! empty( $block_attributes['UAGLoggedIn'] ) && is_user_logged_in() ) {
			return '';
		}

		if ( ! empty( $block_attributes['UAGLoggedOut'] ) && ! is_user_logged_in() ) {
			return '';
		}

		return $block_content;

	}

	/**
	 * Day Visibility.
	 *
	 * @param array $block_attributes The block data.
	 * @param mixed $block_content The block content.
	 *
	 * @since 2.1.3
	 * @return mixed Returns the new block content.
	 */
	public function day_visibility( $block_attributes, $block_content ) {

		// If not set restriction.
		if ( empty( $block_attributes['UAGDay'] ) ) {
			return $block_content;
		}

		$current_day = strtolower( current_datetime()->format( 'l' ) );
		// Check in restricted day.
		return ! in_array( $current_day, $block_attributes['UAGDay'] ) ? $block_content : '';

	}

	/**
	 * Ajax call to get Taxonomy List.
	 *
	 * @since 2.0.0
	 */
	public function get_taxonomy() {

		$response_data = array(
			'messsage' => __( 'User is not authenticated!', 'ultimate-addons-for-gutenberg' ),
		);

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( $response_data );
		}

		check_ajax_referer( 'uagb_ajax_nonce', 'nonce' );

		$post_types = UAGB_Helper::get_post_types();

		$return_array = array();

		foreach ( $post_types as $key => $value ) {
			$post_type = $value['value'];

			$taxonomies = get_object_taxonomies( $post_type, 'objects' );
			$data       = array();

			$get_singular_name = get_post_type_object( $post_type );
			foreach ( $taxonomies as $tax_slug => $tax ) {
				if ( ! $tax->public || ! $tax->show_ui || ! $tax->show_in_rest ) {
					continue;
				}

				$data[ $tax_slug ] = $tax;

				$terms = get_terms( $tax_slug );

				$related_tax_terms = array();

				if ( ! empty( $terms ) ) {
					foreach ( $terms as $t_index => $t_obj ) {
						$related_tax_terms[] = array(
							'id'            => $t_obj->term_id,
							'name'          => $t_obj->name,
							'count'         => $t_obj->count,
							'link'          => get_term_link( $t_obj->term_id ),
							'singular_name' => $get_singular_name->labels->singular_name,
						);
					}

					$return_array[ $post_type ]['terms'][ $tax_slug ] = $related_tax_terms;
				}

				$newcategoriesList = get_terms(
					$tax_slug,
					array(
						'hide_empty' => true,
						'parent'     => 0,
					)
				);

				$related_tax = array();

				if ( ! empty( $newcategoriesList ) ) {
					foreach ( $newcategoriesList as $t_index => $t_obj ) {
						$child_arg     = array(
							'hide_empty' => true,
							'parent'     => $t_obj->term_id,
						);
						$child_cat     = get_terms( $tax_slug, $child_arg );
						$child_cat_arr = $child_cat ? $child_cat : null;
						$related_tax[] = array(
							'id'            => $t_obj->term_id,
							'name'          => $t_obj->name,
							'count'         => $t_obj->count,
							'link'          => get_term_link( $t_obj->term_id ),
							'singular_name' => $get_singular_name->labels->singular_name,
							'children'      => $child_cat_arr,
						);

					}

					$return_array[ $post_type ]['without_empty_taxonomy'][ $tax_slug ] = $related_tax;

				}

				$newcategoriesList_empty_tax = get_terms(
					$tax_slug,
					array(
						'hide_empty' => false,
						'parent'     => 0,
					)
				);

				$related_tax_empty_tax = array();

				if ( ! empty( $newcategoriesList_empty_tax ) ) {
					foreach ( $newcategoriesList_empty_tax as $t_index => $t_obj ) {
						$child_arg_empty_tax     = array(
							'hide_empty' => false,
							'parent'     => $t_obj->term_id,
						);
						$child_cat_empty_tax     = get_terms( $tax_slug, $child_arg_empty_tax );
						$child_cat_empty_tax_arr = $child_cat_empty_tax ? $child_cat_empty_tax : null;
						$related_tax_empty_tax[] = array(
							'id'            => $t_obj->term_id,
							'name'          => $t_obj->name,
							'count'         => $t_obj->count,
							'link'          => get_term_link( $t_obj->term_id ),
							'singular_name' => $get_singular_name->labels->singular_name,
							'children'      => $child_cat_empty_tax_arr,
						);
					}

					$return_array[ $post_type ]['with_empty_taxonomy'][ $tax_slug ] = $related_tax_empty_tax;

				}
			}
			$return_array[ $post_type ]['taxonomy'] = $data;

		}

		wp_send_json_success( apply_filters( 'uagb_taxonomies_list', $return_array ) );
	}

	/**
	 * Renders the Gravity Form shortcode.
	 *
	 * @since 1.12.0
	 */
	public function gf_shortcode() {

		check_ajax_referer( 'uagb_ajax_nonce', 'nonce' );

		$id = isset( $_POST['formId'] ) ? intval( $_POST['formId'] ) : 0;

		if ( $id && 0 !== $id && -1 !== $id ) {
			$data['html'] = do_shortcode( '[gravityforms id="' . $id . '" ajax="true"]' );
		} else {
			$data['html'] = '<p>' . __( 'Please select a valid Gravity Form.', 'ultimate-addons-for-gutenberg' ) . '</p>';
		}
		wp_send_json_success( $data );
	}

	/**
	 * Renders the forms recaptcha keys.
	 *
	 * @since 2.0.0
	 */
	public function forms_recaptcha() {

		$response_data = array(
			'messsage' => __( 'User is not authenticated!', 'ultimate-addons-for-gutenberg' ),
		);

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( $response_data );
		}

		check_ajax_referer( 'uagb_ajax_nonce', 'nonce' );

		$value = isset( $_POST['value'] ) ? json_decode( stripslashes( $_POST['value'] ), true ) : array(); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		\UAGB_Admin_Helper::update_admin_settings_option( 'uag_recaptcha_secret_key_v2', sanitize_text_field( $value['reCaptchaSecretKeyV2'] ) );
		\UAGB_Admin_Helper::update_admin_settings_option( 'uag_recaptcha_secret_key_v3', sanitize_text_field( $value['reCaptchaSecretKeyV3'] ) );
		\UAGB_Admin_Helper::update_admin_settings_option( 'uag_recaptcha_site_key_v2', sanitize_text_field( $value['reCaptchaSiteKeyV2'] ) );
		\UAGB_Admin_Helper::update_admin_settings_option( 'uag_recaptcha_site_key_v3', sanitize_text_field( $value['reCaptchaSiteKeyV3'] ) );

		$response_data = array(
			'messsage' => __( 'Successfully saved data!', 'ultimate-addons-for-gutenberg' ),
		);
		wp_send_json_success( $response_data );

	}

	/**
	 * Renders the Contect Form 7 shortcode.
	 *
	 * @since 1.10.0
	 */
	public function cf7_shortcode() {

		check_ajax_referer( 'uagb_ajax_nonce', 'nonce' );

		$id = isset( $_POST['formId'] ) ? intval( $_POST['formId'] ) : 0;

		if ( $id && 0 !== $id && -1 !== $id ) {
			$data['html'] = do_shortcode( '[contact-form-7 id="' . $id . '" ajax="true"]' );
		} else {
			$data['html'] = '<p>' . __( 'Please select a valid Contact Form 7.', 'ultimate-addons-for-gutenberg' ) . '</p>';
		}
		wp_send_json_success( $data );
	}

	/**
	 * Gutenberg block category for UAGB.
	 *
	 * @param array  $categories Block categories.
	 * @param object $post Post object.
	 * @since 1.0.0
	 */
	public function register_block_category( $categories, $post ) {
		return array_merge(
			array(
				array(
					'slug'  => 'uagb',
					'title' => __( 'Spectra', 'ultimate-addons-for-gutenberg' ),
				),
			),
			$categories
		);
	}

	/**
	 * Localize SVG icon scripts in chunks.
	 * Ex - if 1800 icons available so we will localize 4 variables for it.
	 *
	 * @since 2.7.0
	 * @return void
	 */
	public function add_svg_icon_assets() {
		$localize_icon_chunks = UAGB_Helper::backend_load_font_awesome_icons();
		if ( ! $localize_icon_chunks ) {
			return;
		}

		foreach ( $localize_icon_chunks as $chunk_index => $value ) {
			wp_localize_script( 'uagb-block-editor-js', "uagb_svg_icons_{$chunk_index}", $value );
		}
	}

	/**
	 * Enqueue Gutenberg block assets for backend editor.
	 *
	 * @since 1.0.0
	 */
	public function editor_assets() {

		$uagb_ajax_nonce = wp_create_nonce( 'uagb_ajax_nonce' );

		$script_dep_path = UAGB_DIR . 'dist/blocks.asset.php';
		$script_info     = file_exists( $script_dep_path )
			? include $script_dep_path
			: array(
				'dependencies' => array(),
				'version'      => UAGB_VER,
			);
		global $pagenow;

		$script_dep = array_merge( $script_info['dependencies'], array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-api-fetch' ) );

		if ( 'widgets.php' !== $pagenow ) {
			$script_dep = array_merge( $script_info['dependencies'], array( 'wp-editor' ) );
		}

		$js_ext = ( SCRIPT_DEBUG ) ? '.js' : '.min.js';

		wp_enqueue_code_editor( array( 'type' => 'text/css' ) );
		wp_enqueue_script( 'wp-theme-plugin-editor' );
		wp_enqueue_style( 'wp-codemirror' );

		// Scripts.
		wp_enqueue_script(
			'uagb-block-editor-js', // Handle.
			UAGB_URL . 'dist/blocks.js',
			$script_dep, // Dependencies, defined above.
			$script_info['version'], // UAGB_VER.
			true // Enqueue the script in the footer.
		);

		wp_set_script_translations( 'uagb-block-editor-js', 'ultimate-addons-for-gutenberg' );

		// Common Editor style.
		wp_enqueue_style(
			'uagb-block-common-editor-css', // Handle.
			UAGB_URL . 'dist/common-editor.css', // Block editor CSS.
			array( 'wp-edit-blocks' ), // Dependency to include the CSS after it.
			UAGB_VER
		);

		wp_enqueue_script( 'uagb-deactivate-block-js', UAGB_URL . 'admin/assets/blocks-deactivate.js', array( 'wp-blocks' ), UAGB_VER, true );

		$blocks       = array();
		$saved_blocks = UAGB_Admin_Helper::get_admin_settings_option( '_uagb_blocks' );

		if ( is_array( $saved_blocks ) ) {
			foreach ( $saved_blocks as $slug => $data ) {

				$_slug       = 'uagb/' . $slug;
				$blocks_info = UAGB_Block_Module::get_blocks_info();

				if ( ! isset( $blocks_info[ $_slug ] ) ) {
					continue;
				}

				$current_block = $blocks_info[ $_slug ];

				if ( isset( $current_block['is_child'] ) && $current_block['is_child'] ) {
					continue;
				}

				if ( isset( $current_block['is_active'] ) && ! $current_block['is_active'] ) {
					continue;
				}

				if ( isset( $saved_blocks[ $slug ] ) ) {
					if ( 'disabled' === $saved_blocks[ $slug ] ) {
						array_push( $blocks, $_slug );
					}
				}
			}
		}

		wp_localize_script(
			'uagb-deactivate-block-js',
			'uagb_deactivate_blocks',
			array(
				'deactivated_blocks' => $blocks,
			)
		);
		$display_condition            = UAGB_Admin_Helper::get_admin_settings_option( 'uag_enable_block_condition', 'enabled' );
		$display_responsive_condition = UAGB_Admin_Helper::get_admin_settings_option( 'uag_enable_block_responsive', 'enabled' );

		$enable_selected_fonts = UAGB_Admin_Helper::get_admin_settings_option( 'uag_load_select_font_globally', 'disabled' );
		$selected_fonts        = array();

		if ( 'enabled' === $enable_selected_fonts ) {

			/**
			 * Selected fonts variable
			 *
			 * @var array
			 */
			$selected_fonts = UAGB_Admin_Helper::get_admin_settings_option( 'uag_select_font_globally', array() );

			if ( ! empty( $selected_fonts ) ) {
				usort(
					$selected_fonts,
					function( $a, $b ) {
						return strcmp( $a['label'], $b['label'] );
					}
				);

				$default_selected = array(
					array(
						'value' => 'Default',
						'label' => __( 'Default', 'ultimate-addons-for-gutenberg' ),
					),
				);
				$selected_fonts   = array_merge( $default_selected, $selected_fonts );
			}
		}

		$uagb_exclude_blocks_from_extension = array( 'core/archives', 'core/calendar', 'core/latest-comments', 'core/tag-cloud', 'core/rss' );

		$content_width = \UAGB_Admin_Helper::get_global_content_width();


		$container_padding = UAGB_Admin_Helper::get_admin_settings_option( 'uag_container_global_padding', 'default' );

		if ( 'default' === $container_padding ) {
			\UAGB_Admin_Helper::update_admin_settings_option( 'uag_container_global_padding', 10 );
			$container_padding = 10;
		}

		$container_elements_gap = UAGB_Admin_Helper::get_admin_settings_option( 'uag_container_global_elements_gap', 20 );
		$screen                 = get_current_screen();

		$uag_enable_quick_action_sidebar = apply_filters( 'uag_enable_quick_action_sidebar', UAGB_Admin_Helper::get_admin_settings_option( 'uag_enable_quick_action_sidebar', 'enabled' ) );

		// An array of all the required Spectra Admin URLs.
		$spectra_admin_urls = array(
			'settings' => array(
				'editor_enhancements' => admin_url( 'admin.php?page=spectra&path=settings&settings=editor-enhancements' ),
			),
		);

		$inherit_from_theme = 'deleted' !== UAGB_Admin_Helper::get_admin_settings_option( 'uag_btn_inherit_from_theme_fallback', 'deleted' ) ? 'disabled' : UAGB_Admin_Helper::get_admin_settings_option( 'uag_btn_inherit_from_theme', 'disabled' );

		$localized_params = array(
			'cf7_is_active'                           => class_exists( 'WPCF7_ContactForm' ),
			'gf_is_active'                            => class_exists( 'GFForms' ),
			'category'                                => 'uagb',
			'ajax_url'                                => admin_url( 'admin-ajax.php' ),
			'spectra_admin_urls'                      => $spectra_admin_urls,
			'cf7_forms'                               => $this->get_cf7_forms(),
			'gf_forms'                                => $this->get_gravity_forms(),
			'tablet_breakpoint'                       => UAGB_TABLET_BREAKPOINT,
			'mobile_breakpoint'                       => UAGB_MOBILE_BREAKPOINT,
			'image_sizes'                             => UAGB_Helper::get_image_sizes(),
			'post_types'                              => UAGB_Helper::get_post_types(),
			'uagb_ajax_nonce'                         => $uagb_ajax_nonce,
			'uagb_svg_confirmation_nonce'             => current_user_can( 'edit_posts' ) ? wp_create_nonce( 'uagb_confirm_svg_nonce' ) : '',
			'svg_confirmation'                        => current_user_can( 'edit_posts' ) ? get_option( 'spectra_svg_confirmation' ) : '',
			'uagb_home_url'                           => home_url(),
			'user_role'                               => $this->get_user_role(),
			'uagb_url'                                => UAGB_URL,
			'uagb_mime_type'                          => UAGB_Helper::get_mime_type(),
			'uagb_site_url'                           => UAGB_URI,
			'enableConditions'                        => apply_filters_deprecated( 'enable_block_condition', array( $display_condition ), '1.23.4', 'uag_enable_block_condition' ),
			'enableConditionsForCoreBlocks'           => apply_filters( 'enable_block_condition_for_core', true ),
			'enableResponsiveConditionsForCoreBlocks' => apply_filters( 'enable_responsive_condition_for_core', true ),
			'enableMasonryGallery'                    => apply_filters( 'uag_enable_masonry_gallery', UAGB_Admin_Helper::get_admin_settings_option( 'uag_enable_masonry_gallery', 'enabled' ) ),
			'enableQuickActionSidebar'                => $uag_enable_quick_action_sidebar,
			'enableAnimationsExtension'               => apply_filters( 'uag_enable_animations_extension', UAGB_Admin_Helper::get_admin_settings_option( 'uag_enable_animations_extension', 'enabled' ) ),
			'enableResponsiveConditions'              => apply_filters( 'enable_block_responsive', UAGB_Admin_Helper::get_admin_settings_option( 'uag_enable_block_responsive', 'enabled' ) ),
			'number_of_icon_chunks'                   => UAGB_Helper::$number_of_icon_chunks,
			'uagb_enable_extensions_for_blocks'       => apply_filters( 'uagb_enable_extensions_for_blocks', array() ),
			'uagb_exclude_blocks_from_extension'      => $uagb_exclude_blocks_from_extension,
			'uag_load_select_font_globally'           => $enable_selected_fonts,
			'uag_select_font_globally'                => $selected_fonts,
			'uagb_old_user_less_than_2'               => get_option( 'uagb-old-user-less-than-2' ),
			'collapse_panels'                         => UAGB_Admin_Helper::get_admin_settings_option( 'uag_collapse_panels', 'enabled' ),
			'enable_legacy_blocks'                    => UAGB_Admin_Helper::get_admin_settings_option( 'uag_enable_legacy_blocks', ( 'yes' === get_option( 'uagb-old-user-less-than-2' ) ) ? 'yes' : 'no' ),
			'copy_paste'                              => UAGB_Admin_Helper::get_admin_settings_option( 'uag_copy_paste', 'enabled' ),
			'enable_on_page_css_button'               => UAGB_Admin_Helper::get_admin_settings_option( 'uag_enable_on_page_css_button', 'yes' ),
			'content_width'                           => $content_width,
			'container_global_padding'                => $container_padding,
			'container_elements_gap'                  => $container_elements_gap,
			'recaptcha_site_key_v2'                   => UAGB_Admin_Helper::get_admin_settings_option( 'uag_recaptcha_site_key_v2', '' ),
			'recaptcha_site_key_v3'                   => UAGB_Admin_Helper::get_admin_settings_option( 'uag_recaptcha_site_key_v3', '' ),
			'recaptcha_secret_key_v2'                 => UAGB_Admin_Helper::get_admin_settings_option( 'uag_recaptcha_secret_key_v2', '' ),
			'recaptcha_secret_key_v3'                 => UAGB_Admin_Helper::get_admin_settings_option( 'uag_recaptcha_secret_key_v3', '' ),
			'blocks_editor_spacing'                   => apply_filters( 'uagb_default_blocks_editor_spacing', UAGB_Admin_Helper::get_admin_settings_option( 'uag_blocks_editor_spacing', 0 ) ),
			'load_font_awesome_5'                     => UAGB_Admin_Helper::get_admin_settings_option( 'uag_load_font_awesome_5', ( 'yes' === get_option( 'uagb-old-user-less-than-2' ) ) ? 'enabled' : 'disabled' ),
			'auto_block_recovery'                     => UAGB_Admin_Helper::get_admin_settings_option( 'uag_auto_block_recovery', ( 'yes' === get_option( 'uagb-old-user-less-than-2' ) ) ? 'enabled' : 'disabled' ),
			'font_awesome_5_polyfill'                 => array(),
			'spectra_custom_fonts'                    => apply_filters( 'spectra_system_fonts', array() ),
			'spectra_pro_status'                      => is_plugin_active( 'spectra-pro/spectra-pro.php' ),
			'spectra_custom_css_example'              => __(
				'Use custom class added in block\'s advanced settings to target your desired block. Examples:
		.my-class {text-align: center;} // my-class is a custom selector',
				'ultimate-addons-for-gutenberg'
			),
			'is_rtl'                                  => is_rtl(),
			'insta_linked_accounts'                   => UAGB_Admin_Helper::get_admin_settings_option( 'uag_insta_linked_accounts', array() ),
			'insta_all_users_media'                   => apply_filters( 'uag_instagram_transients', array() ),
			'is_site_editor'                          => $screen->id,
			'current_post_id'                         => get_the_ID(),
			'btn_inherit_from_theme'                  => UAGB_Admin_Helper::get_admin_settings_option( 'uag_btn_inherit_from_theme', 'disabled' ),
			'btn_inherit_from_theme_fallback'         => $inherit_from_theme,
			'wp_version'                              => get_bloginfo( 'version' ),
			'is_block_theme'                          => UAGB_Admin_Helper::is_block_theme(),
			'is_customize_preview'                    => is_customize_preview(),
			'uag_enable_gbs_extension'                => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_enable_gbs_extension', 'enabled' ),
			'current_theme'                           => wp_get_theme()->get( 'Name' ),
			'is_gutenberg_activated'                  => is_plugin_active( 'gutenberg/gutenberg.php' ), // TODO: Once Gutenberg merged the rename functionality code in WP then we need to remove localization part for is_gutenberg_activated.
			'header_titlebar_status'                  => UAGB_Admin_Helper::get_admin_settings_option( 'uag_enable_header_titlebar', 'enabled' ),
			'is_astra_based_theme'                    => defined( 'ASTRA_THEME_SETTINGS' ),
		);

		wp_localize_script(
			'uagb-block-editor-js',
			'uagb_blocks_info',
			$localized_params
		);

		// To match the editor with frontend.
		// Scripts Dependency.
		UAGB_Scripts_Utils::enqueue_blocks_dependency_both();
		// Style.
		UAGB_Scripts_Utils::enqueue_blocks_styles();
		// RTL Styles.
		UAGB_Scripts_Utils::enqueue_blocks_rtl_styles();

		// Add svg icons in chunks.
		$this->add_svg_icon_assets();
	}

	/**
	 *  Get the User Roles
	 *
	 *  @since 1.21.0
	 */
	public function get_user_role() {

		global $wp_roles;

		$field_options = array();

		$role_lists = $wp_roles->get_names();

		$field_options[0] = array(
			'value' => '',
			'label' => __( 'None', 'ultimate-addons-for-gutenberg' ),
		);

		foreach ( $role_lists as $key => $role_list ) {
			$field_options[] = array(
				'value' => $key,
				'label' => $role_list,
			);
		}

		return $field_options;
	}

	/**
	 * Function to integrate CF7 Forms.
	 *
	 * @since 1.10.0
	 */
	public function get_cf7_forms() {
		$field_options = array();

		if ( class_exists( 'WPCF7_ContactForm' ) ) {
			$args             = array(
				'post_type'      => 'wpcf7_contact_form',
				'posts_per_page' => -1,
			);
			$forms            = get_posts( $args );
			$field_options[0] = array(
				'value' => -1,
				'label' => __( 'Select Form', 'ultimate-addons-for-gutenberg' ),
			);
			if ( $forms ) {
				foreach ( $forms as $form ) {
					$field_options[] = array(
						'value' => $form->ID,
						'label' => $form->post_title,
					);
				}
			}
		}

		if ( empty( $field_options ) ) {
			$field_options = array(
				'-1' => __( 'You have not added any Contact Form 7 yet.', 'ultimate-addons-for-gutenberg' ),
			);
		}
		return $field_options;
	}

	/**
	 * Returns all gravity forms with ids
	 *
	 * @since 1.12.0
	 * @return array Key Value paired array.
	 */
	public function get_gravity_forms() {
		$field_options = array();

		if ( class_exists( 'GFForms' ) ) {
			$forms            = RGFormsModel::get_forms( null, 'title' );
			$field_options[0] = array(
				'value' => -1,
				'label' => __( 'Select Form', 'ultimate-addons-for-gutenberg' ),
			);
			if ( is_array( $forms ) ) {
				foreach ( $forms as $form ) {
					$field_options[] = array(
						'value' => $form->id,
						'label' => $form->title,
					);
				}
			}
		}

		if ( empty( $field_options ) ) {
			$field_options = array(
				'-1' => __( 'You have not added any Gravity Forms yet.', 'ultimate-addons-for-gutenberg' ),
			);
		}

		return $field_options;
	}

	/**
	 * Ajax call to confirm add users confirmation option in database
	 *
	 * @return void
	 * @since 2.4.0
	 */
	public function confirm_svg_upload() {
		check_ajax_referer( 'uagb_confirm_svg_nonce', 'svg_nonce' );
		if ( empty( $_POST['confirmation'] ) || 'yes' !== sanitize_text_field( $_POST['confirmation'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid request', 'ultimate-addons-for-gutenberg' ) ) );
		}

		update_option( 'spectra_svg_confirmation', 'yes' );
		wp_send_json_success();
	}

	/**
	 * Add Global Block Styles Class.
	 *
	 * @param string $block_content The block content.
	 * @param array  $block The block data.
	 * @since 2.9.0
	 * @return mixed Returns the new block content.
	 */
	public function add_gbs_class( $block_content, $block ) {
		if ( empty( $block['blockName'] ) || ! is_string( $block['blockName'] ) || false === strpos( $block['blockName'], 'uagb/' ) || empty( $block['attrs']['globalBlockStyleId'] ) || empty( $block['attrs']['block_id'] ) ) {
			return $block_content;
		}

		// Check if GBS is enabled.
		$gbs_status = \UAGB_Admin_Helper::get_admin_settings_option( 'uag_enable_gbs_extension', 'enabled' );

		$style_name       = $block['attrs']['globalBlockStyleId'];
		$style_class_name = 'spectra-gbs-' . $style_name;

		// If GBS extension is disabled then add static class name.
		if ( 'disabled' === $gbs_status ) {
			$_block_slug      = str_replace( 'uagb/', '', $block['blockName'] );
			$class_name       = 'spectra-gbs-uagb-gbs-default-' . $_block_slug;
			$style_class_name = $class_name;
		}

		$block_id = 'uagb-block-' . $block['attrs']['block_id'];

		// Replace the block id with the block id and the style class name.
		$html = str_replace( $block_id, $block_id . ' ' . $style_class_name, $block_content );

		return $html;
	}

	/**
	 * Function to save enable/disable data.
	 *
	 * @since 2.12.0
	 * @return void
	 */
	public function uag_global_sidebar_enabled() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error();
		}

		if ( ! check_ajax_referer( 'uagb_ajax_nonce', 'security', false ) ) {
			wp_send_json_error();
		}

		if ( ! empty( $_POST['enableQuickActionSidebar'] ) ) {
			$spectra_enable_quick_action_sidebar = ( 'enabled' === $_POST['enableQuickActionSidebar'] ? 'enabled' : 'disabled' );
			\UAGB_Admin_Helper::update_admin_settings_option( 'uag_enable_quick_action_sidebar', $spectra_enable_quick_action_sidebar );
			wp_send_json_success();
		}
		wp_send_json_error();
	}

	/**
	 * Function to save allowed block data.
	 *
	 * @since 2.12.0
	 * @return void
	 */
	public function uag_global_update_allowed_block() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error();
		}

		if ( ! check_ajax_referer( 'uagb_ajax_nonce', 'security', false ) ) {
			wp_send_json_error();
		}

		if ( ! empty( $_POST['defaultAllowedQuickSidebarBlocks'] ) ) {
			$spectra_default_allowed_quick_sidebar_blocks = json_decode( stripslashes( $_POST['defaultAllowedQuickSidebarBlocks'] ), true ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			\UAGB_Admin_Helper::update_admin_settings_option( 'uagb_quick_sidebar_allowed_blocks', $spectra_default_allowed_quick_sidebar_blocks );
			wp_send_json_success();
		}
		wp_send_json_error();
	}

	/**
	 * Function to save Spectra Global Block Styles data.
	 *
	 * @since 2.9.0
	 * @return void
	 */
	public function uag_global_block_styles() {
		// Check if gbs enabled or not.
		if ( 'enabled' !== \UAGB_Admin_Helper::get_admin_settings_option( 'uag_enable_gbs_extension', 'enabled' ) ) {
			wp_send_json_error();
		}


		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error();
		}

		if ( ! check_ajax_referer( 'uagb_ajax_nonce', 'security', false ) ) {
			wp_send_json_error();
		}

		$response_data = array( 'messsage' => __( 'No post data found!', 'ultimate-addons-for-gutenberg' ) );

		if ( empty( $_POST['spectraGlobalStyles'] ) ) {
			wp_send_json_error( $response_data );
		}

		$global_block_styles = json_decode( stripslashes( $_POST['spectraGlobalStyles'] ), true ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		if ( ! empty( $_POST['bulkUpdateStyles'] ) && 'no' !== $_POST['bulkUpdateStyles'] ) {
			update_option( 'spectra_global_block_styles', $global_block_styles );
			wp_send_json_success( $global_block_styles );
		}

		if ( empty( $_POST ) || empty( $_POST['attributes'] ) || empty( $_POST['blockName'] ) || empty( $_POST['postId'] ) || empty( $_POST['spectraGlobalStyles'] ) || ! is_array( $global_block_styles ) ) {
			wp_send_json_error( $response_data );
		}

		$global_block_styles = is_array( $global_block_styles ) ? $global_block_styles : array();
		$block_attr          = array();

		$post_id = sanitize_text_field( $_POST['postId'] );
		// Not sanitizing this array because $_POST['attributes'] is a very large array of different types of attributes.
		foreach ( $global_block_styles as $key => $style ) {
			if ( ! empty( $_POST['globalBlockStyleId'] ) && ! empty( $style['value'] ) && $style['value'] === $_POST['globalBlockStyleId'] ) {
				$block_attr = $style['attributes'];

				if ( ! $block_attr ) {
					wp_send_json_error( $response_data );
					break;
				}

				$_block_slug = str_replace( 'uagb/', '', sanitize_text_field( $_POST['blockName'] ) );
				$_block_css  = UAGB_Block_Module::get_frontend_css( $_block_slug, $block_attr, $block_attr['block_id'], true );

				$desktop = '';
				$tablet  = '';
				$mobile  = '';

				$tab_styling_css = '';
				$mob_styling_css = '';
				$desktop        .= $_block_css['desktop'];
				$tablet         .= $_block_css['tablet'];
				$mobile         .= $_block_css['mobile'];
				if ( ! empty( $tablet ) ) {
					$tab_styling_css .= '@media only screen and (max-width: ' . UAGB_TABLET_BREAKPOINT . 'px) {';
					$tab_styling_css .= $tablet;
					$tab_styling_css .= '}';
				}

				if ( ! empty( $mobile ) ) {
					$mob_styling_css .= '@media only screen and (max-width: ' . UAGB_MOBILE_BREAKPOINT . 'px) {';
					$mob_styling_css .= $mobile;
					$mob_styling_css .= '}';
				}
				$_block_css                                    = $desktop . $tab_styling_css . $mob_styling_css;
				$global_block_styles[ $key ]['frontendStyles'] = $_block_css;
				$gbs_stored                                    = get_option( 'spectra_global_block_styles', array() );
				$gbs_stored_key_value                          = is_array( $gbs_stored ) && isset( $gbs_stored[ $key ] ) ? $gbs_stored[ $key ] : array();

				if ( ! empty( $gbs_stored_key_value['post_ids'] ) ) {
					$global_block_styles[ $key ]['post_ids'] = array_merge( $global_block_styles[ $key ]['post_ids'], $gbs_stored_key_value['post_ids'] );
				}

				// For FSE template slug.
				if ( ! empty( $gbs_stored_key_value['page_template_slugs'] ) ) {
					$global_block_styles[ $key ]['page_template_slugs'] = array_merge( $global_block_styles[ $key ]['page_template_slugs'], $gbs_stored_key_value['page_template_slugs'] );
				}

				// For global styles (  widget and customize area ).
				if ( ! empty( $gbs_stored_key_value['styleForGlobal'] ) ) {
					$global_block_styles[ $key ]['styleForGlobal'] = array_merge( $global_block_styles[ $key ]['styleForGlobal'], $gbs_stored_key_value['styleForGlobal'] );
				}

				update_option( 'spectra_global_block_styles', $global_block_styles );

				if ( ! empty( $global_block_styles[ $key ]['post_ids'] ) && is_array( $global_block_styles[ $key ]['post_ids'] ) ) {
					foreach ( $global_block_styles[ $key ]['post_ids'] as $post_id ) {
						UAGB_Helper::delete_page_assets( $post_id );
					}
				}
			}
		}

		$spectra_gbs_google_fonts = get_option( 'spectra_gbs_google_fonts', array() );

		// Global Font Families.
		$font_families = array();
		foreach ( $block_attr as $name => $attribute ) {
			if ( false !== strpos( $name, 'Family' ) && '' !== $attribute ) {

				$font_families[] = $attribute;
			}
		}

		if ( isset( $block_attr['globalBlockStyleId'] ) && is_array( $spectra_gbs_google_fonts ) ) {
			$spectra_gbs_google_fonts[ $block_attr['globalBlockStyleId'] ] = $font_families;
			if ( isset( $spectra_gbs_google_fonts[ $block_attr['globalBlockStyleId'] ] ) && is_array( $spectra_gbs_google_fonts[ $block_attr['globalBlockStyleId'] ] ) ) {
				$spectra_gbs_google_fonts[ $block_attr['globalBlockStyleId'] ] = array_unique( $spectra_gbs_google_fonts[ $block_attr['globalBlockStyleId'] ] );
			}
		}

		update_option( 'spectra_gbs_google_fonts', $spectra_gbs_google_fonts );

		if ( ! empty( $_POST['globalBlockStylesFontFamilies'] ) ) {
			$spectra_gbs_google_fonts_editor = json_decode( stripslashes( $_POST['globalBlockStylesFontFamilies'] ), true ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			update_option( 'spectra_gbs_google_fonts_editor', $spectra_gbs_google_fonts_editor );
		}

		wp_send_json_success( $global_block_styles );
	}
}

/**
 *  Prepare if class 'UAGB_Init_Blocks' exist.
 *  Kicking this off by calling 'get_instance()' method
 */
UAGB_Init_Blocks::get_instance();
