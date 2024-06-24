<?php
/**
 * Header Navigation Menu component.
 *
 * @package     Astra Builder
 * @author      Brainstorm Force
 * @copyright   Copyright (c) 2020, Brainstorm Force
 * @link        https://www.brainstormforce.com
 * @since       Astra 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'ASTRA_BUILDER_MOBILE_MENU_DIR', ASTRA_THEME_DIR . 'inc/builder/type/header/mobile-menu' );
define( 'ASTRA_BUILDER_MOBILE_MENU_URI', ASTRA_THEME_URI . 'inc/builder/type/header/mobile-menu' );

/**
 * Header Navigation Menu Initial Setup
 *
 * @since 3.0.0
 */
class Astra_Mobile_Menu_Component {

	/**
	 * Constructor function that initializes required actions and hooks
	 */
	public function __construct() {

		// @codingStandardsIgnoreStart WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
		require_once ASTRA_BUILDER_MOBILE_MENU_DIR . '/class-astra-mobile-menu-component-loader.php';

		// Include front end files.
		if ( ! is_admin() || Astra_Builder_Customizer::astra_collect_customizer_builder_data() ) {
			require_once ASTRA_BUILDER_MOBILE_MENU_DIR . '/dynamic-css/dynamic.css.php';
		}
		// @codingStandardsIgnoreEnd WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
	}

	/**
	 * Secondary navigation markup
	 *
	 * @param string $device Checking where mobile-menu is dropped.
	 *
	 * @since 3.0.0.
	 */
	public static function menu_markup( $device = 'mobile' ) {
		$astra_builder         = astra_builder();
		$theme_location        = 'mobile_menu';
		$submenu_class         = apply_filters( 'astra_secondary_submenu_border_class', ' submenu-with-border' );
		$stack_on_mobile_class = 'stack-on-mobile';

		// Menu Animation.
		$menu_animation = astra_get_option( 'header-mobile-menu-submenu-container-animation' );
		if ( ! empty( $menu_animation ) ) {
			$submenu_class .= ' astra-menu-animation-' . esc_attr( $menu_animation ) . ' ';
		}

		// Resolving duplicate ID for 'ast-hf-mobile-menu' in W3C Validator.
		$menu_id = 'ast-hf-mobile-menu';
		if ( 'desktop' === $device ) {
			$menu_id = 'ast-desktop-toggle-menu';
		}

		/**
		 * Filter the classes(array) for Menu (<ul>).
		 *
		 * @since  3.0.0
		 * @var Array
		 */
		$menu_classes = apply_filters( 'astra_primary_menu_classes', array( 'main-header-menu', 'ast-nav-menu', 'ast-flex', $submenu_class, $stack_on_mobile_class ) );

		$menu_name   = wp_get_nav_menu_name( $theme_location );
		$items_wrap  = '<nav ';
		$items_wrap .= astra_attr(
			'site-navigation',
			array(
				'id'         => 'ast-' . esc_attr( $device ) . '-site-navigation',
				'class'      => 'site-navigation ast-flex-grow-1 navigation-accessibility site-header-focus-item',
				'aria-label' => esc_attr__( 'Site Navigation: ', 'astra' ) . $menu_name,
			)
		);
		$items_wrap .= '>';
		$items_wrap .= '<div class="main-navigation">';
		$items_wrap .= '<ul id="%1$s" class="%2$s">%3$s</ul>';
		$items_wrap .= '</div>';
		$items_wrap .= '</nav>';

		// Fallback Menu if primary menu not set.
		$fallback_menu_args = array(
			'theme_location' => $theme_location,
			'menu_id'        => $menu_id,
			'menu_class'     => 'main-navigation',
			'container'      => 'div',
			'before'         => '<ul class="' . esc_attr( implode( ' ', $menu_classes ) ) . '">',
			'after'          => '</ul>',
			'walker'         => new Astra_Walker_Page(),
			'echo'           => false,
		);

		// To add default alignment for navigation which can be added through any third party plugin.
		// Do not add any CSS from theme except header alignment.
		echo '<div ' . astra_attr( 'ast-main-header-bar-alignment' ) . '>';

		if ( is_customize_preview() ) {
			Astra_Builder_UI_Controller::render_customizer_edit_button();
		}
		if ( has_nav_menu( $theme_location ) ) {
			$mobile_menu_markup = wp_nav_menu(
				array(
					'menu_id'         => $menu_id,
					'menu_class'      => esc_attr( implode( ' ', $menu_classes ) ),
					'container'       => 'div',
					'container_class' => 'main-header-bar-navigation',
					'items_wrap'      => $items_wrap,
					'theme_location'  => $theme_location,
					'echo'            => false,
				)
			);

			// Adding rel="nofollow" for duplicate menu render.
			$mobile_menu_markup = $astra_builder->nofollow_markup( $theme_location, $mobile_menu_markup );
			echo $mobile_menu_markup;
		} else {
			echo '<div class="main-header-bar-navigation">';
			echo '<nav ';
			echo astra_attr(
				'site-navigation',
				array(
					'id' => 'ast-' . esc_attr( $device ) . '-site-navigation',
				)
			);
			echo ' class="site-navigation ast-flex-grow-1 navigation-accessibility" aria-label="' . esc_attr__( 'Site Navigation', 'astra' ) . '">';
			$mobile_menu_markup = wp_page_menu( $fallback_menu_args );
			// Adding rel="nofollow" for duplicate menu render.
			$mobile_menu_markup = $astra_builder->nofollow_markup( $theme_location, $mobile_menu_markup );
			echo $mobile_menu_markup;
			echo '</nav>';
			echo '</div>';
		}
		echo '</div>';
	}
}

/**
 *  Kicking this off by creating an object.
 */
new Astra_Mobile_Menu_Component();
