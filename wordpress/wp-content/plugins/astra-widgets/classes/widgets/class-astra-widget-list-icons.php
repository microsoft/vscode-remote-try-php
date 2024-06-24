<?php
/**
 * Widget List Icons
 *
 * @package Astra Addon
 * @since 1.0.0
 */

if ( ! class_exists( 'Astra_Widget_List_Icons' ) ) :

	/**
	 * Astra_Widget_List_Icons
	 *
	 * @since 1.0.0
	 */
	class Astra_Widget_List_Icons extends WP_Widget {

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
		 * @var string Widget ID base.
		 */
		public $id_base = 'astra-widget-list-icons';

		/**
		 * Stored data
		 *
		 * @since 1.0.0
		 *
		 * @access private
		 * @var array Widget stored data.
		 */
		private $stored_data = array();

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
				__( 'Astra: List Icons', 'astra-widgets' ),
				array(
					'classname'   => $this->id_base,
					'description' => __( 'Display list icons.', 'astra-widgets' ),
				),
				array(
					'id_base' => $this->id_base,
				)
			);

			add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );
		}

		/**
		 * Register admin scripts
		 *
		 * @return void
		 */
		public function register_admin_scripts() {

			/* Directory and Extension */
			$file_prefix = ( SCRIPT_DEBUG ) ? '' : '.min';
			$dir_name    = ( SCRIPT_DEBUG ) ? 'unminified' : 'minified';

			$js_uri  = ASTRA_WIDGETS_URI . 'assets/js/' . $dir_name . '/';
			$css_uri = ASTRA_WIDGETS_URI . 'assets/css/' . $dir_name . '/';

			wp_enqueue_script( 'astra-widgets-' . $this->id_base, $js_uri . 'astra-widget-list-icons' . $file_prefix . '.js', false, array(), ASTRA_WIDGETS_VER, false );
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

			wp_register_style( 'astra-widgets-' . $this->id_base, $css_uri . 'astra-widget-list-icons' . $file_prefix . '.css', array(), ASTRA_WIDGETS_VER );
		}

		/**
		 * Get fields
		 *
		 * @param  string $field Widget field.
		 * @param  mixed  $default Widget field default value.
		 * @return mixed stored/default widget field value.
		 */
		public function get_fields( $field = '', $default = '' ) {

			// Emtpy stored values.
			if ( empty( $this->stored_data ) ) {
				return $default;
			}

			// Emtpy field.
			if ( empty( $field ) ) {
				return $default;
			}

			if ( ! array_key_exists( $field, $this->stored_data ) ) {
				return $default;
			}

			return $this->stored_data[ $field ];
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

			$width      = isset( $instance['width'] ) ? $instance['width'] : '';
			$icon_color = isset( $instance['icon_color'] ) ? $instance['icon_color'] : '';

			if ( ! empty( $width ) ) {
				$image_width = 'style= max-width:' . esc_attr( $width ) . 'px';
			} else {
				$image_width = 'style= max-width: 15px';
			}

			$list  = $this->get_fields( 'list', array() );
			$title = apply_filters( 'widget_title', $this->get_fields( 'title' ) );

			// Before Widget.
			echo $args['before_widget'];// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			if ( $title ) {
				echo $args['before_title'] . $title . $args['after_title'];// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} ?>
			<div id="astra-widget-list-icons-wrapper" class="astra-widget-list-icons clearfix">
				<?php if ( ! empty( $list ) ) { ?>
					<ul class="list-items-wrapper">
						<?php
						foreach ( $list as $index => $list ) {

							$list_data = json_decode( $list['icon'] );
							$target    = ( 'same-page' === $list['link-target'] ) ? '_self' : '_blank';
							$rel       = ( 'enable' === $list['nofollow'] ) ? 'noopener nofollow' : '';
							?>
							<li>
								<div class="link">
									<a href="<?php echo esc_attr( $list['link'] ); ?>" class="list-item-link" target="<?php echo esc_attr( $target ); ?>" rel="<?php echo esc_attr( $rel ); ?>" aria-label="<?php echo ( is_object( $list_data ) ) ? esc_html( $list_data->name ) : ''; ?>">
									<?php if ( 'icon' === $list['imageoricon'] ) { ?>
										<div class="icon">
											<span class="<?php echo ( is_object( $list_data ) ) ? esc_attr( $list_data->name ) : ''; ?>">
												<?php if ( ! empty( $list_data->viewbox ) && ! empty( $list_data->path ) ) { ?>
													<svg xmlns="http://www.w3.org/2000/svg" class="list-icon" fill="<?php echo esc_attr( $icon_color ); ?>" width="<?php echo ( '' !== $width ) ? esc_attr( $width ) : '15px'; ?>" height="<?php echo ( '' !== $width ) ? esc_attr( $width ) : '15px'; ?>" viewBox="<?php echo ( isset( $list_data->viewbox ) ) ? esc_attr( $list_data->viewbox ) : ''; ?>"><path d="<?php echo ( isset( $list_data->path ) ) ? esc_attr( $list_data->path ) : ''; ?>"></path></svg>
												<?php } ?>
											</span>
										</div>
									<?php } else { ?>
										<div class="image" <?php echo ( isset( $image_width ) ) ? esc_attr( $image_width ) : ''; ?>>
											<?php echo wp_get_attachment_image( $list['image'] ); ?>
										</div>
									<?php } ?>

									<span class="link-text"><?php echo esc_html( $list['title'] ); ?></span>

									</a>
								</div>
							</li>
						<?php } ?>
					</ul>
				<?php } ?>
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

			foreach ( $instance as $icon_key => $icon_value ) {

				if ( 'list' !== $icon_key ) {
					$instance[ $icon_key ] = sanitize_text_field( $icon_value );
				} else {
					foreach ( $instance['list'] as $key => $value ) {
						$instance['list'][ $key ] = array_map( 'sanitize_text_field', $value );
					}
				}
			}

			/**
			 * Created new widget meta option to resolve repeater fields not appearing in block editor widgets.
			 *
			 * Case: In WordPress 5.8 block editor for widget areas are released due to that Legacy widget's repeater fields are not appearing when user triggers widget to edit.
			 * Usecase: So that's this new meta option added here & it funrther use for that widget instance number.
			 */
			$instance['widget_unique_id'] = ! empty( $_POST[ 'widget-' . $this->id_base ] ) ? absint( array_keys( $_POST[ 'widget-' . $this->id_base ] )[0] ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Missing, PHPCompatibility.Syntax.NewFunctionArrayDereferencing.Found
			return $instance;
		}

		/**
		 * Widget Form
		 *
		 * @param  array $instance Widget instance.
		 * @return void
		 */
		public function form( $instance ) {

			$custom_css = " .body{ background: '#000'; }";
			wp_enqueue_script( 'astra-widgets-' . $this->id_base );
			wp_add_inline_style( 'astra-font-style-style', $custom_css );

			$notice_link = __( 'If repeater fields are not appearing then click on the Update button of the widgets page. For more information,', 'astra-widgets' );

			$fields = array(
				array(
					'type'    => 'text',
					'id'      => 'title',
					'name'    => __( 'Title:', 'astra-widgets' ),
					'default' => ( isset( $instance['title'] ) && ! empty( $instance['title'] ) ) ? $instance['title'] : '',
				),
				array(
					'id'      => 'list',
					'type'    => 'repeater',
					'title'   => __( 'Add Item:', 'astra-widgets' ),
					'options' => array(
						array(
							'type'    => 'text',
							'id'      => 'title',
							'name'    => __( 'List Item:', 'astra-widgets' ),
							'default' => '',
						),
						array(
							'type'    => 'text',
							'id'      => 'link',
							'name'    => __( 'Link:', 'astra-widgets' ),
							'default' => '',
						),
						array(
							'type'    => 'select',
							'name'    => 'Target',
							'id'      => 'link-target',
							'default' => ( isset( $instance['link-target'] ) && ! empty( $instance['link-target'] ) ) ? $instance['link-target'] : 'same-page',
							'options' => array(
								'same-page' => __( 'Same Page', 'astra-widgets' ),
								'new-page'  => __( 'New Page', 'astra-widgets' ),
							),
						),
						array(
							'type'    => 'select',
							'id'      => 'nofollow',
							'name'    => __( 'No Follow', 'astra-widgets' ),
							'default' => ( isset( $instance['nofollow'] ) && ! empty( $instance['nofollow'] ) ) ? $instance['nofollow'] : 'enable',
							'options' => array(
								'enable'  => __( 'Enable', 'astra-widgets' ),
								'disable' => __( 'Disable', 'astra-widgets' ),
							),
						),
						array(
							'type'    => 'select',
							'id'      => 'imageoricon',
							'name'    => __( 'Image / Icon', 'astra-widgets' ),
							'default' => ( isset( $instance['imageoricon'] ) && ! empty( $instance['imageoricon'] ) ) ? $instance['imageoricon'] : 'icon',
							'options' => array(
								'image' => __( 'Image', 'astra-widgets' ),
								'icon'  => __( 'Icon', 'astra-widgets' ),
							),
						),
						array(
							'type'    => 'image',
							'id'      => 'image',
							'default' => '',
						),
						array(
							'type'    => 'icon',
							'id'      => 'icon',
							'default' => '',
						),
					),
				),
				array(
					'type'    => 'notice',
					'desc'    => /* translators:%s module name .*/
					sprintf( '%1$s %2$s', $notice_link, '<a rel="noopener" target="_blank" href="' . esc_url_raw( 'https://wpastra.com/docs/resolving-repeater-fields-not-working-in-widget-block-editor/' ) . '">' . __( 'click here.', 'astra-widgets' ) . '</a>' ),
					'show_if' => ( ! empty( $instance ) && ! isset( $instance['widget_unique_id'] ) && Astra_Widgets_Helper::get_instance()->is_widget_block_editor() ),
				),
				array(
					'type' => 'separator',
				),
				array(
					'type' => 'heading',
					'name' => __( 'Spacing', 'astra-widgets' ),
				),
				array(
					'type'    => 'number',
					'id'      => 'space_btn_list',
					'name'    => __( '	Space Between List Items:', 'astra-widgets' ),
					'unit'    => 'Px',
					'default' => ( isset( $instance['space_btn_list'] ) && ! empty( $instance['space_btn_list'] ) ) ? $instance['space_btn_list'] : '',
				),
				array(
					'type'    => 'number',
					'id'      => 'space_btn_icon_text',
					'name'    => __( 'Space Between Icon & Text:', 'astra-widgets' ),
					'unit'    => 'Px',
					'default' => ( isset( $instance['space_btn_icon_text'] ) && ! empty( $instance['space_btn_icon_text'] ) ) ? $instance['space_btn_icon_text'] : '',
				),
				array(
					'type' => 'heading',
					'name' => __( 'Divider', 'astra-widgets' ),
				),
				array(
					'type'    => 'select',
					'id'      => 'divider',
					'name'    => __( 'Show Divider:', 'astra-widgets' ),
					'default' => ( isset( $instance['divider'] ) && ! empty( $instance['divider'] ) ) ? $instance['divider'] : 'yes',
					'options' => array(
						'yes' => __( 'Yes', 'astra-widgets' ),
						'no'  => __( 'No', 'astra-widgets' ),
					),
				),
				array(
					'type'    => 'select',
					'id'      => 'divider_style',
					'name'    => __( 'Style:', 'astra-widgets' ),
					'default' => ( isset( $instance['divider_style'] ) && ! empty( $instance['divider_style'] ) ) ? $instance['divider_style'] : 'inherit',
					'options' => array(
						'solid'  => __( 'Solid', 'astra-widgets' ),
						'dotted' => __( 'Dotted', 'astra-widgets' ),
						'double' => __( 'Double', 'astra-widgets' ),
						'dashed' => __( 'Dashed', 'astra-widgets' ),
					),
				),
				array(
					'type'    => 'number',
					'id'      => 'divider_weight',
					'name'    => __( ' Weight:', 'astra-widgets' ),
					'unit'    => 'Px',
					'default' => ( isset( $instance['divider_weight'] ) && ! empty( $instance['divider_weight'] ) ) ? $instance['divider_weight'] : '',
				),
				array(
					'type'    => 'color',
					'id'      => 'divider_color',
					'name'    => __( 'Color:', 'astra-widgets' ),
					'default' => ( isset( $instance['divider_color'] ) && ! empty( $instance['divider_color'] ) ) ? $instance['divider_color'] : '',
				),
				array(
					'type' => 'heading',
					'name' => __( 'Icon / Image Style', 'astra-widgets' ),
				),
				array(
					'type'    => 'color',
					'id'      => 'icon_color',
					'name'    => __( 'Icon Color', 'astra-widgets' ),
					'default' => ( isset( $instance['icon_color'] ) && ! empty( $instance['icon_color'] ) ) ? $instance['icon_color'] : '',
				),
				array(
					'type'    => 'color',
					'id'      => 'background_color',
					'name'    => __( 'Background Color', 'astra-widgets' ),
					'default' => ( isset( $instance['background_color'] ) && ! empty( $instance['background_color'] ) ) ? $instance['background_color'] : '',
				),
				array(
					'type'    => 'number',
					'id'      => 'width',
					'name'    => __( 'Image / Icon Size:', 'astra-widgets' ),
					'default' => ( isset( $instance['width'] ) && ! empty( $instance['width'] ) ) ? $instance['width'] : '',
					'unit'    => 'Px',
				),
				array(
					'type'    => 'hidden',
					'id'      => 'widget_unique_id',
					'default' => ( isset( $instance['widget_unique_id'] ) && ! empty( $instance['widget_unique_id'] ) ) ? $instance['widget_unique_id'] : '',
				),
			);
			?>

			<div class="<?php echo esc_attr( $this->id_base ); ?>-fields">
				<?php
					// Generate fields.
					astra_generate_widget_fields( $this, $fields, $instance );
				?>
			</div>
			<?php
		}


		/**
		 * Dynamic CSS
		 *
		 * @return string
		 */
		public function get_dynamic_css() {

			$dynamic_css = '';

			$instances = get_option( 'widget_' . $this->id_base );

			$id_base = '#' . $this->id;

			if ( array_key_exists( $this->number, $instances ) ) {
				$instance = $instances[ $this->number ];

				$width               = isset( $instance['width'] ) ? $instance['width'] : '';
				$space_btn_list      = isset( $instance['space_btn_list'] ) ? $instance['space_btn_list'] : '';
				$space_btn_icon_text = isset( $instance['space_btn_icon_text'] ) ? $instance['space_btn_icon_text'] : '';
				$background_color    = isset( $instance['background_color'] ) ? $instance['background_color'] : '';
				$divider_color       = isset( $instance['divider_color'] ) ? $instance['divider_color'] : '';
				$divider_weight      = isset( $instance['divider_weight'] ) ? $instance['divider_weight'] : '';
				$divider_style       = isset( $instance['divider_style'] ) ? $instance['divider_style'] : '';
				$divider             = isset( $instance['divider'] ) ? $instance['divider'] : '';

				$css_output = '';

				$width          = ( '' !== $width ) ? $width : '15';
				$space_btn_list = ( '' !== $space_btn_list ) ? $space_btn_list : '5';
				$divider_weight = ( '' !== $divider_weight ) ? $divider_weight : '1';

				if ( isset( $width ) && ! empty( $width ) ) {
					$css_output = array(
						$id_base . ' .astra-widget-list-icons .image img' => array(
							'min-width' => astra_widget_get_css_value( $width, 'px' ),
						),
						$id_base . ' .astra-widget-list-icons .icon svg' => array(
							'width' => astra_widget_get_css_value( $width, 'px' ),
						),
					);

					$dynamic_css = astra_widgets_parse_css( $css_output );
				}

				if ( isset( $divider ) && 'yes' === $divider ) {
					$css_output_2 = array(
						$id_base . '.astra-widget-list-icons .list-items-wrapper li' => array(
							'border-bottom-color' => esc_attr( $divider_color ),
							'border-bottom-width' => esc_attr( $divider_weight ) . 'px',
							'border-bottom-style' => esc_attr( $divider_style ),
						),
						$id_base . '.astra-widget-list-icons .list-items-wrapper li:last-child' => array(
							'border-bottom-style' => 'none',
						),
					);

					$dynamic_css .= astra_widgets_parse_css( $css_output_2 );
				}

				$css_output_1 = array(
					$id_base . ' #astra-widget-list-icons-wrapper .list-items-wrapper li:first-child' => array(
						'padding-top'    => '0',
						'padding-bottom' => esc_attr( $space_btn_list / 2 ) . 'px',
					),
					$id_base . ' #astra-widget-list-icons-wrapper .list-items-wrapper li' => array(
						'padding-top'    => esc_attr( $space_btn_list / 2 ) . 'px',
						'padding-bottom' => esc_attr( $space_btn_list / 2 ) . 'px',
						'margin-bottom'  => '0',
					),
					$id_base . ' #astra-widget-list-icons-wrapper .list-items-wrapper li:last-child' => array(
						'padding-top'    => esc_attr( $space_btn_list / 2 ) . 'px',
						'padding-bottom' => '0',
					),
					$id_base . '.astra-widget-list-icons ul li .link-text' => array(
						'margin-left' => esc_attr( $space_btn_icon_text ) . 'px',
					),
					$id_base . ' .list-item-link .icon' => array(
						'background-color' => esc_attr( $background_color ),
						'width'            => esc_attr( $width ) . 'px',
						'height'           => esc_attr( $width ) . 'px',
					),

				);

				$dynamic_css .= astra_widgets_parse_css( $css_output_1 );

				return $dynamic_css;
			}

			return $dynamic_css;

		}

	}

endif;
