<?php
/**
 * Astra Global color palette
 *
 * @package     Astra
 * @subpackage  Class
 * @author      Astra
 * @link        https://wpastra.com/
 * @since       3.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Global palette class
 */
class Astra_Global_Palette {

	/**
	 * Constructor
	 *
	 * @since 3.7.0
	 */
	public function __construct() {
		/**
		 * Support for overriding theme.json from the child theme
		 *
		 * If theme.json is not present in the child theme load Global Color Palette in the editor using add_theme_support( 'editor-color-palette', $editor_palette );.
		 * This is a known issue in Gutenberg - If theme.json is not present in the child theme, it does fallback to the parent theme's theme.json file.
		 * This will be fixed in the future updates of WordPress/Gutenberg.
		 *
		 * @see https://github.com/WordPress/gutenberg/pull/34354
		 */
		$get_stylesheet = get_stylesheet_directory();
		$is_theme_json  = $get_stylesheet . '/theme.json';
		if ( ( get_template_directory() !== $get_stylesheet && false === file_exists( $is_theme_json ) ) || astra_wp_version_compare( '5.8', '<' ) ) {
			add_action( 'after_setup_theme', array( $this, 'support_editor_color_palette' ) );
		}
		add_filter( 'astra_theme_customizer_js_localize', array( $this, 'localize_variables' ) );
		add_filter( 'astra_before_foreground_color_generation', array( $this, 'get_color_by_palette_variable' ) );
		$this->includes();
		add_filter( 'astra_dynamic_theme_css', array( $this, 'global_border_compatibility' ) );
	}

	/**
	 * Modify color palette from Gutenberg.
	 *
	 * @since 3.7.0
	 * @return void
	 */
	public function support_editor_color_palette() {
		$global_palette = astra_get_option( 'global-color-palette' );
		$editor_palette = $this->format_global_palette( $global_palette );
		add_theme_support( 'editor-color-palette', $editor_palette );
	}

	/**
	 * Format color palette data required to pass for Gutenberg palette.
	 *
	 * @since 3.7.0
	 * @param array $global_palette global palette data.
	 * @return array
	 */
	public function format_global_palette( $global_palette ) {
		$editor_palette = array();
		$labels         = self::get_palette_labels();

		if ( isset( $global_palette['palette'] ) ) {
			foreach ( $global_palette['palette'] as $key => $color ) {

				$label = 'Theme ' . $labels[ $key ];

				$editor_palette[] = array(
					'name'  => $label,
					'slug'  => str_replace( '--', '', self::get_css_variable_prefix() ) . $key,
					'color' => 'var(' . self::get_css_variable_prefix() . $key . ')',
				);
			}
		}
		return $editor_palette;
	}

	/**
	 * Get CSS variable prefix used for styling.
	 *
	 * @since 3.7.0
	 * @return string variable prefix
	 */
	public static function get_css_variable_prefix() {
		return '--ast-global-color-';
	}

	/**
	 * Localize variables used in the customizer.
	 *
	 * @since 3.7.0
	 * @param array $object localize object.
	 * @return array<array-key, mixed> $object localize object.
	 */
	public function localize_variables( $object ) {

		if ( isset( $object['customizer'] ) ) {
			$object['customizer']['globalPaletteStylePrefix']       = self::get_css_variable_prefix();
			$object['customizer']['isElementorActive']              = astra_is_elemetor_active();
			$object['customizer']['isGlobalColorElementorDisabled'] = astra_maybe_disable_global_color_in_elementor();
			$object['customizer']['globalPaletteSlugs']             = self::get_palette_slugs();
			$object['customizer']['globalPaletteLabels']            = self::get_palette_labels();
		}
		return $object;
	}

	/**
	 * Default global palette options.
	 *
	 * @since 3.7.0
	 * @return array Palette options.
	 */
	public static function get_default_color_palette() {
		$update_colors_for_starter_library = Astra_Dynamic_CSS::astra_4_4_0_compatibility();
		$update_color_styles_with_presets  = Astra_Dynamic_CSS::astra_4_5_0_compatibility();
		$update_color_for_forms_ui         = Astra_Dynamic_CSS::astra_4_6_0_compatibility();

		return array(
			'currentPalette' => 'palette_1',
			'palettes'       => $update_color_styles_with_presets ? array(
				'palette_1' => array(
					'#046bd2',
					'#045cb4',
					'#1e293b',
					'#334155',
					'#F0F5FA',
					'#FFFFFF',
					$update_color_for_forms_ui ? '#D1D5DB' : '#ADB6BE',
					'#111111',
					'#111111',
				),
				'palette_2' => array(
					'#0067FF',
					'#005EE9',
					'#0F172A',
					'#364151',
					'#E7F6FF',
					'#FFFFFF',
					'#D1DAE5',
					'#070614',
					'#222222',
				),
				'palette_3' => array(
					'#6528F7',
					'#5511F8',
					'#0F172A',
					'#454F5E',
					'#F2F0FE',
					'#FFFFFF',
					'#D8D8F5',
					'#0D0614',
					'#222222',
				),
			) : array(
				'palette_1' => array(
					'#046bd2',
					'#045cb4',
					'#1e293b',
					'#334155',
					$update_colors_for_starter_library ? '#F0F5FA' : '#f9fafb',
					'#FFFFFF',
					$update_colors_for_starter_library ? '#ADB6BE' : '#e2e8f0',
					$update_colors_for_starter_library ? '#111111' : '#cbd5e1',
					$update_colors_for_starter_library ? '#111111' : '#94a3b8',
				),
				'palette_2' => array(
					'#0170B9',
					$update_colors_for_starter_library ? '#045cb4' : '#3a3a3a',
					'#3a3a3a',
					'#4B4F58',
					$update_colors_for_starter_library ? '#F0F5FA' : '#F5F5F5',
					'#FFFFFF',
					$update_colors_for_starter_library ? '#ADB6BE' : '#F2F5F7',
					$update_colors_for_starter_library ? '#111111' : '#424242',
					$update_colors_for_starter_library ? '#111111' : '#000000',
				),
				'palette_3' => array(
					'#0170B9',
					$update_colors_for_starter_library ? '#045cb4' : '#3a3a3a',
					'#3a3a3a',
					'#4B4F58',
					$update_colors_for_starter_library ? '#F0F5FA' : '#F5F5F5',
					'#FFFFFF',
					$update_colors_for_starter_library ? '#ADB6BE' : '#F2F5F7',
					$update_colors_for_starter_library ? '#111111' : '#424242',
					$update_colors_for_starter_library ? '#111111' : '#000000',
				),
			),
			'presets'        => astra_get_palette_presets(),
		);
	}

	/**
	 * Get labels for palette colors.
	 *
	 * @since 3.7.0
	 * @return array Palette labels.
	 */
	public static function get_palette_labels() {
		return array(
			__( 'Color  1', 'astra' ),
			__( 'Color  2', 'astra' ),
			__( 'Color  3', 'astra' ),
			__( 'Color  4', 'astra' ),
			__( 'Color  5', 'astra' ),
			__( 'Color  6', 'astra' ),
			__( 'Color  7', 'astra' ),
			__( 'Color  8', 'astra' ),
			__( 'Color  9', 'astra' ),
		);
	}

	/**
	 * Get slugs for palette colors.
	 *
	 * @since 3.7.0
	 * @return array Palette slugs.
	 */
	public static function get_palette_slugs() {
		return array(
			'ast-global-color-0',
			'ast-global-color-1',
			'ast-global-color-2',
			'ast-global-color-3',
			'ast-global-color-4',
			'ast-global-color-5',
			'ast-global-color-6',
			'ast-global-color-7',
			'ast-global-color-8',
		);
	}

	/**
	 * Include required files.
	 *
	 * @since 3.7.0
	 */
	public function includes() {
		require_once ASTRA_THEME_DIR . 'inc/dynamic-css/global-color-palette.php';// PHPCS:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
	}

	/**
	 * Generate palette CSS required to display on front end.
	 *
	 * @since 3.7.0
	 * @return array palette style array.
	 */
	public static function generate_global_palette_style() {
		$palette_data = astra_get_option( 'global-color-palette' );

		$palette_style = array();

		if ( isset( $palette_data['palette'] ) ) {
			foreach ( $palette_data['palette'] as $key => $color ) {
				$palette_key                   = self::get_css_variable_prefix() . $key;
				$palette_style[ $palette_key ] = $color;
			}
		}

		return $palette_style;
	}

	/**
	 * Pass hex value for global palette to process forground color.
	 *
	 * @since 3.7.0
	 * @param string $color hex color / css variable.
	 * @return string
	 */
	public function get_color_by_palette_variable( $color ) {
		// Check if color is CSS variable.
		if ( 0 === strpos( $color, 'var(--' ) ) {

			$global_palette = astra_get_option( 'global-color-palette' );

			foreach ( $global_palette['palette'] as $palette_index => $value ) {

				if ( 'var(' . self::get_css_variable_prefix() . $palette_index . ')' === $color ) {
					return $value;
				}
			}
		}

		return $color;
	}

	/**
	 * Add dynamic CSS for the global border color.
	 *
	 * @since 3.9.0
	 *
	 * @param  string $dynamic_css          Astra Dynamic CSS.
	 *
	 * @return String Generated dynamic CSS for global border.
	 */
	public function global_border_compatibility( $dynamic_css ) {
		$global_border_color = astra_get_option( 'border-color', '#dddddd' );

		$global_border = '
			:root {
				--ast-border-color : ' . $global_border_color . ';
			}
		';

		return $dynamic_css .= Astra_Enqueue_Scripts::trim_css( $global_border );
	}
}

new Astra_Global_Palette();
