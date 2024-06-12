<?php
/**
 * Customizer Control: Customizer Link
 *
 * @package     Astra
 * @author      Astra
 * @copyright   Copyright (c) 2020, Astra
 * @link        https://wpastra.com/
 * @since       1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * A text control with validation for CSS units.
 */
class Astra_Control_Customizer_Link extends WP_Customize_Control {

	/**
	 * The control type.
	 *
	 * @access public
	 * @var string
	 */
	public $type = 'ast-customizer-link';

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
	 * Refresh the parameters passed to the JavaScript via JSON.
	 *
	 * @see WP_Customize_Control::to_json()
	 */
	public function to_json() {
		parent::to_json();
		$this->json['link_text']      = $this->link_text;
		$this->json['linked']         = $this->linked;
		$this->json['link_type']      = $this->link_type;
		$this->json['is_button_link'] = $this->is_button_link;
	}

	/**
	 * Render the control's content.
	 *
	 * @see WP_Customize_Control::render_content()
	 */
	protected function render_content() {}
}
