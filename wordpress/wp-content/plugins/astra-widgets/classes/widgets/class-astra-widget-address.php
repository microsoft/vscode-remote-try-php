<?php
/**
 * Address Widget
 *
 * @package Astra Addon
 * @since 1.0.0
 */

if ( ! class_exists( 'Astra_Widget_Address' ) ) :

	/**
	 * Astra_Widget_Address
	 *
	 * @since 1.0.0
	 */
	class Astra_Widget_Address extends WP_Widget {

		/**
		 * Instance
		 *
		 * @since 1.0.0
		 *
		 * @access private
		 * @var object Class object.
		 */
		private static $instance;

		/**
		 * Widget Base
		 *
		 * @since 1.0.0
		 *
		 * @access public
		 * @var object Class object.
		 */
		public $id_base = 'astra-widget-address';

		/**
		 * Initiator
		 *
		 * @since 1.0.0
		 *
		 * @return object initialized object of class.
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
		 * @since 1.0.0
		 */
		public function __construct() {
			parent::__construct(
				$this->id_base,
				__( 'Astra: Address', 'astra-widgets' ),
				array(
					'classname'   => $this->id_base,
					'description' => __( 'Display Address.', 'astra-widgets' ),
				),
				array(
					'id_base' => $this->id_base,
				)
			);
			add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
		}

		/**
		 * Register scripts
		 *
		 * @return void
		 */
		public function register_scripts() {

			/* Directory and Extension */
			$file_prefix = ( SCRIPT_DEBUG ) ? '' : '.min';
			$dir_name    = ( SCRIPT_DEBUG ) ? 'unminified' : 'minified';

			$js_uri  = ASTRA_WIDGETS_URI . 'assets/js/' . $dir_name . '/';
			$css_uri = ASTRA_WIDGETS_URI . 'assets/css/' . $dir_name . '/';

			wp_register_style( 'astra-widgets-' . $this->id_base, $css_uri . 'astra-widget-address' . $file_prefix . '.css', array(), ASTRA_WIDGETS_VER );

		}

		/**
		 * Frontend setup
		 *
		 * @param  array $args Widget arguments.
		 * @param  array $instance Widget instance.
		 * @return void
		 */
		public function front_setup( $args, $instance ) {

			// Set stored data.
			$this->stored_data = $instance;

			// Enqueue Scripts.
			wp_enqueue_style( 'astra-widgets-' . $this->id_base );

			// Enqueue dynamic Scripts.
			wp_add_inline_style( 'astra-widgets-' . $this->id_base, $this->get_dynamic_css() );
		}

		/**
		 * Widget
		 *
		 * @param  array $args Widget arguments.
		 * @param  array $instance Widget instance.
		 * @return void
		 */
		public function widget( $args, $instance ) {

			$this->front_setup( $args, $instance );

			$title        = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
			$style        = isset( $instance['style'] ) ? $instance['style'] : 'stack';
			$social_icons = isset( $instance['display-icons'] ) ? $instance['display-icons'] : false;
			$address      = isset( $instance['address'] ) ? $instance['address'] : '';
			$phone        = isset( $instance['phone'] ) ? $instance['phone'] : '';
			$fax          = isset( $instance['fax'] ) ? $instance['fax'] : '';
			$email        = isset( $instance['email'] ) ? $instance['email'] : '';

			// Before Widget.
			echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			if ( $title ) {
				echo $args['before_title'] . $title . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} ?>
			<?php
			$widget_content_font_size = '15';
			if ( function_exists( 'astra_get_option' ) ) {
				$widget_content_font_size = astra_get_option( 'font-size-widget-content' );
				$widget_content_font_size = ( isset( $widget_content_font_size['desktop'] ) && ! empty( $widget_content_font_size['desktop'] ) ) ? $widget_content_font_size['desktop'] : '15';
			}
			?>

			<div class="address clearfix">
				<address class="widget-address widget-address-<?php echo esc_attr( $style ); ?> widget-address-icons-<?php echo esc_attr( $social_icons ); ?>">

					<?php if ( ! empty( $address ) ) { ?>
						<div class="widget-address-field">
							<?php if ( $social_icons ) { ?>
									<?php // Font Awesome 5 SVG. ?>
									<svg xmlns="http://www.w3.org/2000/svg" class="address-icons" width="<?php echo esc_attr( $widget_content_font_size ) . 'px'; ?>" height="<?php echo esc_attr( $widget_content_font_size ) . 'px'; ?>" viewBox="0 0 496 512"><path d="M336.5 160C322 70.7 287.8 8 248 8s-74 62.7-88.5 152h177zM152 256c0 22.2 1.2 43.5 3.3 64h185.3c2.1-20.5 3.3-41.8 3.3-64s-1.2-43.5-3.3-64H155.3c-2.1 20.5-3.3 41.8-3.3 64zm324.7-96c-28.6-67.9-86.5-120.4-158-141.6 24.4 33.8 41.2 84.7 50 141.6h108zM177.2 18.4C105.8 39.6 47.8 92.1 19.3 160h108c8.7-56.9 25.5-107.8 49.9-141.6zM487.4 192H372.7c2.1 21 3.3 42.5 3.3 64s-1.2 43-3.3 64h114.6c5.5-20.5 8.6-41.8 8.6-64s-3.1-43.5-8.5-64zM120 256c0-21.5 1.2-43 3.3-64H8.6C3.2 212.5 0 233.8 0 256s3.2 43.5 8.6 64h114.6c-2-21-3.2-42.5-3.2-64zm39.5 96c14.5 89.3 48.7 152 88.5 152s74-62.7 88.5-152h-177zm159.3 141.6c71.4-21.2 129.4-73.7 158-141.6h-108c-8.8 56.9-25.6 107.8-50 141.6zM19.3 352c28.6 67.9 86.5 120.4 158 141.6-24.4-33.8-41.2-84.7-50-141.6h-108z"></path>
									</svg>
							<?php } ?>
							<span class="address-meta"><?php echo nl2br( $address ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						</div>
					<?php } ?>
					<?php if ( ! empty( $phone ) ) { ?>
						<div class="widget-address-field">
							<?php if ( $social_icons ) { ?>
									<?php // Font Awesome 5 SVG. ?>
									<svg xmlns="http://www.w3.org/2000/svg" class="address-icons" width="<?php echo esc_attr( $widget_content_font_size ) . 'px'; ?>" height="<?php echo esc_attr( $widget_content_font_size ) . 'px'; ?>" viewBox="0 0 512 512"><path d="M493.4 24.6l-104-24c-11.3-2.6-22.9 3.3-27.5 13.9l-48 112c-4.2 9.8-1.4 21.3 6.9 28l60.6 49.6c-36 76.7-98.9 140.5-177.2 177.2l-49.6-60.6c-6.8-8.3-18.2-11.1-28-6.9l-112 48C3.9 366.5-2 378.1.6 389.4l24 104C27.1 504.2 36.7 512 48 512c256.1 0 464-207.5 464-464 0-11.2-7.7-20.9-18.6-23.4z"></path>
									</svg>
							<?php } ?>
							<?php
							if ( apply_filters( 'astra_widgets_tel_prefix', false ) ) {
								$prefix = '+';
							} else {
								$prefix = '';
							}

							?>
							<span class="address-meta">
								<a href="tel:<?php echo esc_attr( $prefix ) . esc_attr( preg_replace( '/\D/', '', esc_attr( $phone ) ) ); ?>" ><?php echo esc_html( $phone ); ?></a>
							</span>
						</div>
					<?php } ?>
					<?php if ( ! empty( $fax ) ) { ?>
						<div class="widget-address-field">
							<?php if ( $social_icons ) { ?>
									<?php // Font Awesome 5 SVG. ?>
									<svg xmlns="http://www.w3.org/2000/svg" class="address-icons" width="<?php echo esc_attr( $widget_content_font_size ) . 'px'; ?>" height="<?php echo esc_attr( $widget_content_font_size ) . 'px'; ?>" viewBox="0 0 384 512"><path d="M224 136V0H24C10.7 0 0 10.7 0 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V160H248c-13.2 0-24-10.8-24-24zm160-14.1v6.1H256V0h6.1c6.4 0 12.5 2.5 17 7l97.9 98c4.5 4.5 7 10.6 7 16.9z"></path>
									</svg>
							<?php } ?>
							<span class="address-meta"><?php echo esc_attr( $fax ); ?></span>
						</div>
					<?php } ?>
					<?php
					if ( ! empty( $email ) ) {
						$email = sanitize_email( $email );
						?>
						<div class="widget-address-field">
							<?php if ( $social_icons ) { ?>
									<?php // Font Awesome 5 SVG. ?>
									<svg xmlns="http://www.w3.org/2000/svg" class="address-icons" width="<?php echo esc_attr( $widget_content_font_size ) . 'px'; ?>" height="<?php echo esc_attr( $widget_content_font_size ) . 'px'; ?>" viewBox="0 0 512 512"><path d="M502.3 190.8c3.9-3.1 9.7-.2 9.7 4.7V400c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V195.6c0-5 5.7-7.8 9.7-4.7 22.4 17.4 52.1 39.5 154.1 113.6 21.1 15.4 56.7 47.8 92.2 47.6 35.7.3 72-32.8 92.3-47.6 102-74.1 131.6-96.3 154-113.7zM256 320c23.2.4 56.6-29.2 73.4-41.4 132.7-96.3 142.8-104.7 173.4-128.7 5.8-4.5 9.2-11.5 9.2-18.9v-19c0-26.5-21.5-48-48-48H48C21.5 64 0 85.5 0 112v19c0 7.4 3.4 14.3 9.2 18.9 30.6 23.9 40.7 32.4 173.4 128.7 16.8 12.2 50.2 41.8 73.4 41.4z"></path>
									</svg>
							<?php } ?>
							<span class="address-meta">
								<a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>" ><?php echo esc_html( antispambot( $email ) ); ?></a>
							</span>
						</div>
					<?php } ?>
				</address>
			</div>

			<?php

			// After Widget.
			echo $args['after_widget'];// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Update
		 *
		 * @param  array $new_instance Widget new instance.
		 * @param  array $old_instance Widget old instance.
		 * @return array                Merged updated instance.
		 */
		public function update( $new_instance, $old_instance ) {

			$instance = wp_parse_args( $new_instance, $old_instance );

			/**
			 * Checkbox field support!
			 *
			 * @todo The checkbox field we need to set the boolean value `true/false`
			 *       For now we have not able to detect the `checkbox` field in `generate` of class `Astra_Widgets_Helper`.
			 */
			$instance['display-icons'] = isset( $new_instance['display-icons'] ) ? (bool) $new_instance['display-icons'] : false;

			$instance = array_map( 'sanitize_text_field', $instance );

			// Address is a textarea field and needs to preserve linebreaks and whitespace.
			if ( ! empty( $new_instance['address'] ) ) {
				$instance['address'] = sanitize_textarea_field( $new_instance['address'] );
			}

			return $instance;
		}

		/**
		 * Widget Form
		 *
		 * @param  array $instance Widget instance.
		 * @return void
		 */
		public function form( $instance ) {

			$fields = array(
				array(
					'type'    => 'text',
					'id'      => 'title',
					'name'    => __( 'Title:', 'astra-widgets' ),
					'default' => ( isset( $instance['title'] ) && ! empty( $instance['title'] ) ) ? $instance['title'] : '',
				),
				array(
					'name'    => 'Style',
					'id'      => 'style',
					'type'    => 'select',
					'default' => ( isset( $instance['style'] ) && ! empty( $instance['style'] ) ) ? $instance['style'] : 'stack',
					'options' => array(
						'inline' => __( 'Inline', 'astra-widgets' ),
						'stack'  => __( 'Stack', 'astra-widgets' ),
					),
				),
				array(
					'type'    => 'checkbox',
					'id'      => 'display-icons',
					'name'    => __( 'Display Icons?', 'astra-widgets' ),
					'default' => ( isset( $instance['display-icons'] ) && ! empty( $instance['display-icons'] ) ) ? $instance['display-icons'] : false,
				),
				array(
					'type'    => 'textarea',
					'id'      => 'address',
					'name'    => __( 'Address:', 'astra-widgets' ),
					'default' => ( isset( $instance['address'] ) && ! empty( $instance['address'] ) ) ? $instance['address'] : '',
				),
				array(
					'type'    => 'text',
					'id'      => 'phone',
					'name'    => __( 'Phone:', 'astra-widgets' ),
					'default' => ( isset( $instance['phone'] ) && ! empty( $instance['phone'] ) ) ? $instance['phone'] : '',
				),
				array(
					'type'    => 'text',
					'id'      => 'fax',
					'name'    => __( 'FAX:', 'astra-widgets' ),
					'default' => ( isset( $instance['fax'] ) && ! empty( $instance['fax'] ) ) ? $instance['fax'] : '',
				),
				array(
					'type'    => 'text',
					'id'      => 'email',
					'name'    => __( 'Email:', 'astra-widgets' ),
					'default' => ( isset( $instance['email'] ) && ! empty( $instance['email'] ) ) ? $instance['email'] : '',
				),
				array(
					'type'    => 'color',
					'id'      => 'icon_color',
					'name'    => __( 'Icon Color', 'astra-widgets' ),
					'default' => ( isset( $instance['icon_color'] ) && ! empty( $instance['icon_color'] ) ) ? $instance['icon_color'] : '#fefefe',
				),
				array(
					'type'    => 'number',
					'id'      => 'space_btn_icon_text',
					'name'    => __( 'Space Between Icon & Text:', 'astra-widgets' ),
					'unit'    => 'Px',
					'default' => ( isset( $instance['space_btn_icon_text'] ) && ! empty( $instance['space_btn_icon_text'] ) ) ? $instance['space_btn_icon_text'] : '',
				),
				array(
					'type'    => 'number',
					'id'      => 'space_btn_address_fields',
					'name'    => __( '	Space Between Address Fields:', 'astra-widgets' ),
					'unit'    => 'Px',
					'default' => ( isset( $instance['space_btn_address_fields'] ) && ! empty( $instance['space_btn_address_fields'] ) ) ? $instance['space_btn_address_fields'] : '',
				),
			);

			// Generate fields.
			astra_generate_widget_fields( $this, $fields );
		}
		/**
		 * Dynamic CSS
		 *
		 * @return string
		 */
		public function get_dynamic_css() {

			$dynamic_css = '';
			$instances   = get_option( 'widget_' . $this->id_base );
			$id_base     = '#' . $this->id;

			if ( array_key_exists( $this->number, $instances ) ) {
				$instance                 = $instances[ $this->number ];
				$icon_color               = isset( $instance['icon_color'] ) ? $instance['icon_color'] : '';
				$space_btn_icon_text      = isset( $instance['space_btn_icon_text'] ) ? $instance['space_btn_icon_text'] : '';
				$space_btn_address_fields = isset( $instance['space_btn_address_fields'] ) ? $instance['space_btn_address_fields'] : '';
				$css_output               = array(
					$id_base . ' .widget-address-field svg' => array(
						'fill' => esc_attr( $icon_color ),
					),
					$id_base . ' .widget-address .widget-address-field .address-meta' => array(
						'margin-left' => esc_attr( $space_btn_icon_text ) . 'px',
					),
					$id_base . ' .widget-address.widget-address-stack .widget-address-field' => array(
						'padding-top'    => '0',
						'padding-bottom' => esc_attr( $space_btn_address_fields ) . 'px',
					),
					$id_base . ' .widget-address.widget-address-inline .widget-address-field' => array(
						'padding-right' => esc_attr( $space_btn_address_fields ) . 'px',
					),
					$id_base . ' .address .widget-address.widget-address-stack .widget-address-field:last-child' => array(
						'padding-bottom' => '0',
					),
					$id_base . ' .address .widget-address.widget-address-inline .widget-address-field:last-child' => array(
						'padding-right' => '0',
					),
				);

				$dynamic_css = astra_widgets_parse_css( $css_output );

				return $dynamic_css;
			}

			return $dynamic_css;

		}

	}

endif;
