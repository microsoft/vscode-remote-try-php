<?php
/**
 * Customizer Control: Logo SVG Icon
 *
 * @package     Astra
 * @author      Astra
 * @copyright   Copyright (c) 2024, Astra
 * @link        https://wpastra.com/
 * @since       4.7.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Customizer custom control for SVG Logo Icon support.
 */
class Astra_Control_Logo_SVG_Icon extends WP_Customize_Control {

	/**
	 * The control type.
	 *
	 * @access public
	 * @var string
	 */
	public $type = 'ast-logo-svg-icon';

	/**
	 * Link text to be added inside the anchor tag.
	 *
	 * @var string
	 */
	public $link_text = '';

	/**
	 * Linked customizer section.
	 *
	 * @var string
	 */
	public $linked = '';

	/**
	 * Linked customizer section.
	 *
	 * @var string
	 */
	public $link_type = '';

	/**
	 * True if the link is button.
	 *
	 * @var boolean
	 */
	public $is_button_link = '';

	/**
	 * Render the control's content.
	 *
	 * @see WP_Customize_Control::render_content()
	 */
	protected function render_content() {}
}
