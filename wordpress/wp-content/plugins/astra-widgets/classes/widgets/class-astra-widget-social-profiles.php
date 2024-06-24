<?php
/**
 * Widget List Icons
 *
 * @package Astra Addon
 * @since 1.6.0
 */

if ( ! class_exists( 'Astra_Widget_Social_Profiles' ) ) :

	/**
	 * Astra_Widget_Social_Profiles
	 *
	 * @since 1.6.0
	 */
	class Astra_Widget_Social_Profiles extends WP_Widget {

		/**
		 * Instance
		 *
		 * @since 1.6.0
		 *
		 * @access private
		 * @var object Class object.
		 */
		private static $instance;

		/**
		 * Widget Base
		 *
		 * @since 1.6.0
		 *
		 * @access public
		 * @var string Widget ID base.
		 */
		public $id_base = 'astra-widget-social-profiles';

		/**
		 * Stored data
		 *
		 * @since 1.6.0
		 *
		 * @access private
		 * @var array Widget stored data.
		 */
		private $stored_data = array();

		/**
		 * Initiator
		 *
		 * @since 1.6.0
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
		 * @since 1.6.0
		 */
		public function __construct() {
			parent::__construct(
				$this->id_base,
				__( 'Astra: Social Profiles', 'astra-widgets' ),
				array(
					'classname'   => $this->id_base,
					'description' => __( 'Display social profiles.', 'astra-widgets' ),
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

			wp_enqueue_script( 'astra-widgets-' . $this->id_base, $js_uri . 'astra-widget-social-profiles' . $file_prefix . '.js', false, array(), ASTRA_WIDGETS_VER, false );
			wp_register_style( 'astra-widget-social-profiles-admin', $css_uri . 'astra-widget-social-profiles-admin' . $file_prefix . '.css', array(), ASTRA_WIDGETS_VER );
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

			wp_register_style( 'astra-widgets-' . $this->id_base, $css_uri . 'astra-widget-social-profiles' . $file_prefix . '.css', array(), ASTRA_WIDGETS_VER );
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
		 * Dynamic CSS
		 *
		 * @return string              Dynamic CSS.
		 */
		public function get_dynamic_css() {

			$dynamic_css = '';

			$instances = get_option( 'widget_' . $this->id_base );

			$id_base = '#' . $this->id;

			if ( array_key_exists( $this->number, $instances ) ) {
				$instance = $instances[ $this->number ];

				$icon_color                = isset( $instance['icon-color'] ) ? $instance['icon-color'] : '';
				$bg_color                  = isset( $instance['bg-color'] ) ? $instance['bg-color'] : '';
				$icon_hover_color          = isset( $instance['icon-hover-color'] ) ? $instance['icon-hover-color'] : '';
				$bg_hover_color            = isset( $instance['bg-hover-color'] ) ? $instance['bg-hover-color'] : '';
				$icon_width                = isset( $instance['width'] ) ? $instance['width'] : '';
				$color_type                = isset( $instance['color-type'] ) ? $instance['color-type'] : '';
				$list                      = isset( $instance['list'] ) ? $instance['list'] : '';
				$icons_color               = Astra_Widgets_Helper::get_default_icons_colors();
				$space_btn_icon_text       = isset( $instance['space_btn_icon_text'] ) ? $instance['space_btn_icon_text'] : '';
				$space_btn_social_profiles = isset( $instance['space_btn_social_profiles'] ) ? $instance['space_btn_social_profiles'] : '';
				$dynamic_css               = '';
				// Official Colors only.
				if ( 'official-color' === $color_type ) {

					$new_color_output = '';
					$uniqueue_icon    = array();

					if ( ! empty( $list ) ) {
						foreach ( $list as $index => $list ) {
							$list_data       = json_decode( $list['icon'] );
							$uniqueue_icon[] = isset( $list_data->name ) ? $list_data->name : '';
						}
					}
					if ( ! empty( $uniqueue_icon ) ) {
						foreach ( array_unique( $uniqueue_icon ) as $key => $name ) {
							$icon_color_official    = isset( $icons_color[ $name ] ) ? $icons_color[ $name ]['color'] : '';
							$icon_bg_color_official = isset( $icons_color[ $name ] ) ? $icons_color[ $name ]['bg-color'] : '';

							$trimmed = str_replace( 'astra-icon-', '', $name );

							$color_output = array(
								$id_base . ' .astra-widget-social-profiles-inner.icon-official-color.simple li .' . $name . '.ast-widget-icon svg' => array(
									'fill' => esc_attr( $icon_bg_color_official ),
								),
								$id_base . ' .astra-widget-social-profiles-inner.icon-official-color li .' . $name . '.ast-widget-icon svg' => array(
									'fill' => esc_attr( $icon_color_official ),
								),
								$id_base . ' .astra-widget-social-profiles-inner.icon-official-color.circle li .' . $name . '.ast-widget-icon, ' . $id_base . ' .astra-widget-social-profiles-inner.icon-official-color.square li .' . $name . '.ast-widget-icon' => array(
									'background-color' => esc_attr( $icon_bg_color_official ),
								),
								$id_base . ' .astra-widget-social-profiles-inner.icon-official-color.square-outline li .' . $name . '.ast-widget-icon svg,' . $id_base . ' .astra-widget-social-profiles-inner.icon-official-color.circle-outline li .' . $name . '.ast-widget-icon svg' => array(
									'fill' => esc_attr( $icon_bg_color_official ),
								),
								$id_base . ' .astra-widget-social-profiles-inner.icon-official-color.square-outline li .' . $name . '.ast-widget-icon, ' . $id_base . ' .astra-widget-social-profiles-inner.icon-official-color.circle-outline li .' . $name . '.ast-widget-icon' => array(
									'border-color' => esc_attr( $icon_bg_color_official ),
								),
							);
							$dynamic_css .= astra_widgets_parse_css( $color_output );
						}
					}
				} else {
					// Custom colors only.
					$css_output = array(
						$id_base . ' .astra-widget-social-profiles-inner li .ast-widget-icon svg' => array(
							'fill' => esc_attr( $icon_color ),
						),
						$id_base . ' .astra-widget-social-profiles-inner li .ast-widget-icon:hover svg' => array(
							'fill' => esc_attr( $icon_hover_color ),
						),
						// square outline.
						$id_base . ' .astra-widget-social-profiles-inner.square-outline li .ast-widget-icon, ' . $id_base . ' .astra-widget-social-profiles-inner.circle-outline li .ast-widget-icon' => array(
							'background'   => 'transparent',
							'border-color' => esc_attr( $bg_color ),
						),
						$id_base . ' .astra-widget-social-profiles-inner.square-outline li .ast-widget-icon svg, ' . $id_base . ' .astra-widget-social-profiles-inner.circle-outline li .ast-widget-icon svg' => array(
							'background' => 'transparent',
							'fill'       => esc_attr( $icon_color ),
						),
						// square & circle.
						$id_base . ' .astra-widget-social-profiles-inner.square .ast-widget-icon, ' . $id_base . ' .astra-widget-social-profiles-inner.circle .ast-widget-icon' => array(
							'background'   => esc_attr( $bg_color ),
							'border-color' => esc_attr( $bg_color ),
						),
						$id_base . ' .astra-widget-social-profiles-inner.square .ast-widget-icon svg, ' . $id_base . ' .astra-widget-social-profiles-inner.circle .ast-widget-icon svg' => array(
							'fill' => esc_attr( $icon_color ),
						),
						$id_base . ' .astra-widget-social-profiles-inner.square .ast-widget-icon:hover svg, ' . $id_base . ' .astra-widget-social-profiles-inner.circle .ast-widget-icon:hover svg' => array(
							'fill' => esc_attr( $icon_hover_color ),
						),
						$id_base . ' .astra-widget-social-profiles-inner.square .ast-widget-icon:hover, ' . $id_base . ' .astra-widget-social-profiles-inner.circle .ast-widget-icon:hover' => array(
							'background'   => esc_attr( $bg_hover_color ),
							'border-color' => esc_attr( $bg_hover_color ),
						),

						// square & circle outline.
						$id_base . ' .astra-widget-social-profiles-inner.square-outline li .ast-widget-icon:hover, ' . $id_base . ' .astra-widget-social-profiles-inner.circle-outline li .ast-widget-icon:hover' => array(
							'background'   => 'transparent',
							'border-color' => esc_attr( $bg_hover_color ),
						),
						$id_base . ' .astra-widget-social-profiles-inner.square-outline li .ast-widget-icon:hover svg, ' . $id_base . ' .astra-widget-social-profiles-inner.circle-outline li .ast-widget-icon:hover svg' => array(
							'fill' => esc_attr( $icon_hover_color ),
						),
					);
					$dynamic_css .= astra_widgets_parse_css( $css_output );
				}

				// Common Property apply to all social icons.
				$common_css_output = array(
					$id_base . ' .astra-widget-social-profiles-inner .ast-widget-icon' => array(
						'font-size' => astra_widget_get_css_value( $icon_width, 'px' ),
					),
					$id_base . ' .astra-widget-social-profiles-inner.circle li .ast-widget-icon, ' . $id_base . ' .astra-widget-social-profiles-inner.circle-outline li .ast-widget-icon' => array(
						'font-size' => astra_widget_get_css_value( $icon_width, 'px' ),
					),
					$id_base . ' .astra-widget-social-profiles-inner li > a .ast-widget-icon' => array(
						'margin-right' => esc_attr( $space_btn_icon_text ) . 'px',
					),
					$id_base . ' .astra-widget-social-profiles-inner.stack li > a ' => array(
						'padding-bottom' => esc_attr( $space_btn_social_profiles ) . 'px',
					),
					$id_base . ' .astra-widget-social-profiles-inner.inline li > a ' => array(
						'padding-right' => esc_attr( $space_btn_social_profiles ) . 'px',
					),
					$id_base . ' .astra-widget-social-profiles-inner.inline li:last-child a ' => array(
						'padding-right' => '0',
					),
					$id_base . ' .astra-widget-social-profiles-inner li:last-child a' => array(
						'margin-right'   => '0',
						'padding-bottom' => '0',
					),
				);
				$dynamic_css      .= astra_widgets_parse_css( $common_css_output );
			}

			return $dynamic_css;
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
			wp_enqueue_style( 'astra-widgets-font-style' );

			$list             = $this->get_fields( 'list', array() );
			$align            = $this->get_fields( 'align' );
			$icon_color       = $this->get_fields( 'icon-color' );
			$bg_color         = $this->get_fields( 'bg-color' );
			$icon_hover_color = $this->get_fields( 'icon-hover-color' );
			$bg_hover_color   = $this->get_fields( 'bg-hover-color' );
			$icon_style       = $this->get_fields( 'icon-style' );
			$display_title    = $this->get_fields( 'display-title', false );
			$color_type       = $this->get_fields( 'color-type', false );
			$icon             = $this->get_fields( 'icon', false );
			$icon_width       = isset( $instance['width'] ) && ! empty( $instance['width'] ) ? $instance['width'] : '15';
			$title            = apply_filters( 'widget_title', $this->get_fields( 'title' ) );

			// Before Widget.
			echo $args['before_widget'];// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			if ( $title ) {
				echo $args['before_title'] . $title . $args['after_title'];// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} ?>

			<div class="astra-widget-social-profiles-inner clearfix <?php echo esc_attr( $align ); ?> <?php echo esc_attr( $icon_style ); ?> <?php echo 'icon-' . esc_attr( $color_type ); ?>">
				<?php if ( ! empty( $list ) ) { ?>
					<ul>
						<?php
						foreach ( $list as $index => $list ) {
							$target = ( 'same-page' === $list['link-target'] ) ? '_self' : '_blank';
							$rel    = ( 'enable' === $list['nofollow'] ) ? 'noopener nofollow' : '';

							$list_data = json_decode( $list['icon'] );

							$trimmed = str_replace( 'astra-icon-', '', $list['icon'] );
							?>
							<li>
								<a href="<?php echo esc_attr( $list['link'] ); ?>" target="<?php echo esc_attr( $target ); ?>" rel="<?php echo esc_attr( $rel ); ?>" aria-label="<?php echo ( is_object( $list_data ) ) ? esc_html( $list_data->name ) : ''; ?>">
										<span class="ast-widget-icon <?php echo ( is_object( $list_data ) ) ? esc_html( $list_data->name ) : ''; ?>">
											<?php if ( ! empty( $list_data->viewbox ) && ! empty( $list_data->path ) ) { ?>
												<svg xmlns="http://www.w3.org/2000/svg" viewBox="<?php echo ( isset( $list_data->viewbox ) ) ? esc_attr( $list_data->viewbox ) : ''; ?>" width=<?php echo esc_attr( $icon_width ); ?> height=<?php echo esc_attr( $icon_width ); ?> ><path d="<?php echo ( isset( $list_data->path ) ) ? esc_attr( $list_data->path ) : ''; ?>"></path></svg>
											<?php } ?>
										</span>
									<?php if ( $display_title ) { ?>
										<span class="link"><?php echo esc_html( $list['title'] ); ?></span>
									<?php } ?>
								</a>
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

			/**
			 * Checkbox field support!
			 *
			 * @todo The checkbox field we need to set the boolean value `true/false`
			 *       For now we have not able to detect the `checkbox` field in `generate` of class `Astra_Widgets_Helper`.
			 */
			$instance['display-title'] = isset( $new_instance['display-title'] ) ? (bool) $new_instance['display-title'] : false;

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

			wp_enqueue_script( 'astra-widgets-' . $this->id_base );
			wp_enqueue_style( 'astra-widget-social-profiles-admin' );
			wp_enqueue_style( 'astra-widgets-font-style' );

			$notice_link = __( 'If repeater fields are not appearing then click on the Update button of the widgets page. For more information,', 'astra-widgets' );

			$fields = array(
				array(
					'type'    => 'text',
					'id'      => 'title',
					'name'    => __( 'Title', 'astra-widgets' ),
					'default' => ( isset( $instance['title'] ) && ! empty( $instance['title'] ) ) ? $instance['title'] : '',
				),
				array(
					'type' => 'separator',
				),
				array(
					'type' => 'heading',
					'name' => __( 'Social Profiles', 'astra-widgets' ),
				),
				array(
					'id'      => 'list',
					'type'    => 'repeater',
					'title'   => __( 'Add Profile', 'astra-widgets' ),
					'options' => array(
						array(
							'type'    => 'text',
							'id'      => 'title',
							'name'    => __( 'Title', 'astra-widgets' ),
							'default' => '',
						),
						array(
							'type'    => 'text',
							'id'      => 'link',
							'name'    => __( 'Link', 'astra-widgets' ),
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
							'type'      => 'icon',
							'id'        => 'icon',
							'name'      => __( 'Icon', 'astra-widgets' ),
							'default'   => '',
							'show_icon' => 'yes',
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
					'name' => __( 'Styling', 'astra-widgets' ),
				),
				array(
					'type'    => 'checkbox',
					'id'      => 'display-title',
					'name'    => __( 'Display profile title?', 'astra-widgets' ),
					'default' => ( isset( $instance['display-title'] ) && ! empty( $instance['display-title'] ) ) ? $instance['display-title'] : false,
				),
				array(
					'type'    => 'select',
					'id'      => 'align',
					'name'    => __( 'Alignment', 'astra-widgets' ),
					'default' => isset( $instance['align'] ) ? $instance['align'] : '',
					'options' => array(
						'inline' => __( 'Inline', 'astra-widgets' ),
						'stack'  => __( 'Stack', 'astra-widgets' ),
					),
				),
				array(
					'type'    => 'select',
					'id'      => 'icon-style',
					'name'    => __( 'Icon Style', 'astra-widgets' ),
					'default' => isset( $instance['icon-style'] ) ? $instance['icon-style'] : '',
					'options' => array(
						'simple'         => __( 'Simple', 'astra-widgets' ),
						'circle'         => __( 'Circle', 'astra-widgets' ),
						'square'         => __( 'Square', 'astra-widgets' ),
						'circle-outline' => __( 'Circle Outline', 'astra-widgets' ),
						'square-outline' => __( 'Square Outline', 'astra-widgets' ),
					),
				),
				array(
					'type'    => 'select',
					'id'      => 'color-type',
					'name'    => __( 'Icon Color', 'astra-widgets' ),
					'default' => isset( $instance['color-type'] ) ? $instance['color-type'] : '',
					'options' => array(
						'official-color' => __( 'Official Color', 'astra-widgets' ),
						'custom-color'   => __( 'Custom', 'astra-widgets' ),
					),
				),
				array(
					'type'    => 'color',
					'id'      => 'icon-color',
					'name'    => __( 'Icon Color', 'astra-widgets' ),
					'default' => ( isset( $instance['icon-color'] ) && ! empty( $instance['icon-color'] ) ) ? $instance['icon-color'] : '',
				),
				array(
					'type'    => 'color',
					'id'      => 'bg-color',
					'name'    => __( 'Background Color', 'astra-widgets' ),
					'default' => ( isset( $instance['bg-color'] ) && ! empty( $instance['bg-color'] ) ) ? $instance['bg-color'] : '',
				),
				array(
					'type'    => 'color',
					'id'      => 'icon-hover-color',
					'name'    => __( 'Icon Hover Color', 'astra-widgets' ),
					'default' => ( isset( $instance['icon-hover-color'] ) && ! empty( $instance['icon-hover-color'] ) ) ? $instance['icon-hover-color'] : '',
				),
				array(
					'type'    => 'color',
					'id'      => 'bg-hover-color',
					'name'    => __( 'Background Hover Color', 'astra-widgets' ),
					'default' => ( isset( $instance['bg-hover-color'] ) && ! empty( $instance['bg-hover-color'] ) ) ? $instance['bg-hover-color'] : '',
				),
				array(
					'type'    => 'number',
					'id'      => 'width',
					'name'    => __( 'Icon Width:', 'astra-widgets' ),
					'default' => ( isset( $instance['width'] ) && ! empty( $instance['width'] ) ) ? $instance['width'] : '',
					'unit'    => 'Px',
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
					'id'      => 'space_btn_social_profiles',
					'name'    => __( '	Space Between Social Profiles:', 'astra-widgets' ),
					'unit'    => 'Px',
					'default' => ( isset( $instance['space_btn_social_profiles'] ) && ! empty( $instance['space_btn_social_profiles'] ) ) ? $instance['space_btn_social_profiles'] : '',
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

	}

endif;
