<?php
/**
 * Astra Theme Customizer Configuration Base.
 *
 * @package     Astra
 * @author      Astra
 * @copyright   Copyright (c) 2020, Astra
 * @link        https://wpastra.com/
 * @since       Astra 1.4.3
 */

// No direct access, please.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Base Class for Registering Customizer Controls.
 *
 * @since 1.4.3
 */
if ( ! class_exists( 'Astra_Customizer_Control_Base' ) ) {

	/**
	 * Customizer Sanitizes Initial setup
	 */
	class Astra_Customizer_Control_Base {

		/**
		 * Registered Controls.
		 *
		 * @since 1.4.3
		 * @var Array
		 */
		private static $controls;

		/**
		 *  Constructor
		 */
		public function __construct() {

			add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		/**
		 * Enqueue Admin Scripts
		 *
		 * @since 1.4.3
		 */
		public function enqueue_scripts() {

			$dir_name    = ( SCRIPT_DEBUG ) ? 'unminified' : 'minified';
			$file_prefix = ( SCRIPT_DEBUG ) ? '' : '.min';
			$file_rtl    = ( is_rtl() ) ? '-rtl' : '';
			$css_uri     = ASTRA_THEME_URI . 'inc/customizer/custom-controls/assets/css/' . $dir_name . '/';
			$js_uri      = ASTRA_THEME_URI . 'inc/customizer/custom-controls/assets/js/unminified/';

			wp_enqueue_style( 'astra-custom-control-style' . $file_rtl, $css_uri . 'custom-controls' . $file_prefix . $file_rtl . '.css', null, ASTRA_THEME_VERSION );

			// Enqueue Customizer Plain script.
			$custom_controls_plain_deps = array(
				'jquery',
				'customize-base',
				'jquery-ui-tabs',
				'jquery-ui-sortable',
			);

			wp_enqueue_script( 'astra-custom-control-plain-script', $js_uri . 'custom-controls-plain.js', $custom_controls_plain_deps, ASTRA_THEME_VERSION, true );

			// Enqueue Customizer React.JS script.
			$custom_controls_react_deps = array(
				'astra-custom-control-plain-script',
				'astra-customizer-controls-js',
				'wp-i18n',
				'wp-components',
				'wp-element',
				'wp-media-utils',
				'wp-block-editor',
			);

			wp_enqueue_script( 'astra-custom-control-script', ASTRA_THEME_URI . 'inc/customizer/extend-custom-controls/build/index.js', $custom_controls_react_deps, ASTRA_THEME_VERSION, true );
			wp_set_script_translations( 'astra-custom-control-script', 'astra' );


			/**
			 * Had to go this route because the default context check
			 * from the core was not working properly for advanced conditions in `inc/customizer/configurations/builder/header/configs/account.php`.
			 *
			 * @since 4.6.15
			 */
			wp_add_inline_script(
				'astra-custom-control-script',
				"
				(function(){
					window.addEventListener('load', function() {

						wp.customize.state('astra-customizer-tab').bind(function(state) {

							if ( 'general' === state ) {
								wp.customize.control('astra-settings[header-account-icon-size]').container.hide();
								wp.customize.control('astra-settings[header-account-icon-color]').container.hide();
								return;
							}

							var loginStyleIsText = 'text' === wp.customize('astra-settings[header-account-login-style]').get();
							var logoutStyleIsText = 'text' === wp.customize('astra-settings[header-account-logout-style]').get();

							var loginIsIconExtend = 'icon' === wp.customize('astra-settings[header-account-login-style-extend-text-profile-type]').get();
							var logoutIsIconExtend = 'icon' === wp.customize('astra-settings[header-account-logout-style-extend-text-profile-type]').get();

							if ( ( loginStyleIsText && loginIsIconExtend ) || ( logoutStyleIsText && logoutIsIconExtend ) ) {
								wp.customize.control('astra-settings[header-account-icon-size]').container.show();
								wp.customize.control('astra-settings[header-account-icon-color]').container.show();
							}
						});

					});
				})();
				"
			);

			$astra_typo_localize = array(
				'100'       => __( 'Thin 100', 'astra' ),
				'100italic' => __( '100 Italic', 'astra' ),
				'200'       => __( 'Extra-Light 200', 'astra' ),
				'200italic' => __( '200 Italic', 'astra' ),
				'300'       => __( 'Light 300', 'astra' ),
				'300italic' => __( '300 Italic', 'astra' ),
				'400'       => __( 'Normal 400', 'astra' ),
				'normal'    => __( 'Normal 400', 'astra' ),
				'italic'    => __( '400 Italic', 'astra' ),
				'500'       => __( 'Medium 500', 'astra' ),
				'500italic' => __( '500 Italic', 'astra' ),
				'600'       => __( 'Semi-Bold 600', 'astra' ),
				'600italic' => __( '600 Italic', 'astra' ),
				'700'       => __( 'Bold 700', 'astra' ),
				'700italic' => __( '700 Italic', 'astra' ),
				'800'       => __( 'Extra-Bold 800', 'astra' ),
				'800italic' => __( '800 Italic', 'astra' ),
				'900'       => __( 'Ultra-Bold 900', 'astra' ),
				'900italic' => __( '900 Italic', 'astra' ),
			);
			wp_localize_script( 'astra-custom-control-script', 'astraTypo', $astra_typo_localize );

			$css_uri = ASTRA_THEME_URI . 'inc/customizer/custom-controls/typography/';

			wp_enqueue_style( 'astra-select-woo-style', $css_uri . 'selectWoo.css', null, ASTRA_THEME_VERSION );
		}

		/**
		 * Add Control to self::$controls and Register control to WordPress Customizer.
		 *
		 * @param String $name Slug for the control.
		 * @param Array  $atts Control Attributes.
		 * @return void
		 */
		public static function add_control( $name, $atts ) {
			global $wp_customize;
			self::$controls[ $name ] = $atts;

			if ( isset( $atts['callback'] ) ) {
				/**
				 * Register controls
				 */
				$wp_customize->register_control_type( $atts['callback'] );
			}
		}

		/**
		 * Returns control instance
		 *
		 * @param  string $control_type control type.
		 * @since 1.4.3
		 * @return string
		 */
		public static function get_control_instance( $control_type ) {
			$control_class = self::get_control( $control_type );

			if ( isset( $control_class['callback'] ) ) {
				return class_exists( $control_class['callback'] ) ? $control_class['callback'] : false;
			}

			return false;
		}

		/**
		 * Returns control and its attributes
		 *
		 * @param  string $control_type control type.
		 * @since 1.4.3
		 * @return array
		 */
		public static function get_control( $control_type ) {
			if ( isset( self::$controls[ $control_type ] ) ) {
				return self::$controls[ $control_type ];
			}

			return array();
		}

		/**
		 * Returns Santize callback for control
		 *
		 * @param  string $control control.
		 * @since 1.4.3
		 * @return string
		 */
		public static function get_sanitize_call( $control ) {

			if ( isset( self::$controls[ $control ]['sanitize_callback'] ) ) {
				return self::$controls[ $control ]['sanitize_callback'];
			}

			return false;
		}
	}
}

/**
 * Kicking this off by calling 'get_instance()' method
 */
new Astra_Customizer_Control_Base();
