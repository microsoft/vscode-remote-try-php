<?php
/**
 * Term Meta Config.
 *
 * @package variation-swatches-woo
 * @since 1.0.0
 */

namespace CFVSW\Admin;

use CFVSW\Inc\Traits\Get_Instance;
use CFVSW\Inc\Helper;


/**
 * Attribute Config
 *
 * @since 1.0.0
 */
class Term_Meta_Config {

	use Get_Instance;

	/**
	 * Current taxonomy id
	 *
	 * @var string
	 * @since  1.0.0
	 */
	public $taxonomy;

	/**
	 * Helper class object
	 *
	 * @var Helper
	 * @since  1.0.0
	 */
	public $helper;

	/**
	 * Constructor
	 *
	 * @since  1.0.0
	 */
	public function __construct() {
		// Taking taxonomy name from $_REQUEST['taxonomy'] and whatever string we get from $_REQUEST['taxonomy'] we will add in prefix in these actions we are not saving directly so nonce is not required.
		$this->taxonomy = isset( $_REQUEST['taxonomy'] ) ? sanitize_title( $_REQUEST['taxonomy'] ) : ''; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$this->helper   = new Helper();
		add_action( $this->taxonomy . '_add_form_fields', [ $this, 'add_form_fields' ] );
		add_action( $this->taxonomy . '_edit_form_fields', [ $this, 'edit_form_fields' ], 10 );
		add_action( 'created_' . $this->taxonomy, [ $this, 'save_term_fields' ] );
		add_action( 'edited_' . $this->taxonomy, [ $this, 'save_term_fields' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'term_meta_enqueue_scripts' ] );
	}

	/**
	 * Term meta markup for add form
	 *
	 * @param object $term current term object.
	 * @return void
	 * @since  1.0.0
	 */
	public function add_form_fields( $term ) {
		$type         = $this->helper->get_attr_type_by_name( $this->taxonomy );
		$fields_array = $this->term_meta_fields( $type );
		if ( ! empty( $fields_array ) ) {
			?>
			<div class="form-field <?php echo esc_attr( $fields_array['id'] ); ?>">
				<label for="<?php echo esc_attr( $fields_array['id'] ); ?>"><?php echo esc_html( $fields_array['label'] ); ?></label>
				<?php $this->term_meta_fields_markup( $fields_array, $term ); ?>
				<p class="description"><?php echo esc_html( $fields_array['desc'] ); ?></p>
			</div>
			<?php
		}
	}

	/**
	 * Term meta markup for edit form
	 *
	 * @param object $term current term object.
	 * @return void
	 * @since  1.0.0
	 */
	public function edit_form_fields( $term ) {
		$type         = $this->helper->get_attr_type_by_name( $this->taxonomy );
		$fields_array = $this->term_meta_fields( $type );
		if ( ! empty( $fields_array ) ) {
			?>
			<tr class="form-field">
				<th>
					<label for="<?php echo esc_attr( $fields_array['id'] ); ?>"><?php echo esc_html( $fields_array['label'] ); ?></label>
				</th>
				<td>
					<?php $this->term_meta_fields_markup( $fields_array, $term ); ?>
					<p class="description"><?php echo esc_html( $fields_array['desc'] ); ?></p>
				</td>
			</tr>
			<?php
		}
	}

	/**
	 * Saves term meta on add/edit term
	 *
	 * @param int $term_id cureent term id.
	 * @return void
	 * @since  1.0.0
	 */
	public function save_term_fields( $term_id ) {
		$meta_key = '';
		$value    = '';
		// Saving term fields. This is admin action and in this action nonce is verified so nonce is not required.
		if ( isset( $_REQUEST['cfvsw_color'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$meta_key = 'cfvsw_color';
			$value    = sanitize_text_field( $_REQUEST['cfvsw_color'] ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		} elseif ( isset( $_REQUEST['cfvsw_image'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$meta_key = 'cfvsw_image';
			$value    = esc_url_raw( $_REQUEST['cfvsw_image'] ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		update_term_meta( $term_id, $meta_key, $value );
	}

	/**
	 * Term meta fields array
	 *
	 * @param string $type term meta type.
	 * @return array
	 * @since  1.0.0
	 */
	public function term_meta_fields( $type ) {
		if ( empty( $type ) ) {
			return [];
		}

		$fields = [
			'color' => [
				'label' => __( 'Color', 'variation-swatches-woo' ),
				'desc'  => __( 'Choose a color', 'variation-swatches-woo' ),
				'id'    => 'cfvsw_product_attribute_color',
				'type'  => 'color',
			],
			'image' => [
				'label' => __( 'Image', 'variation-swatches-woo' ),
				'desc'  => __( 'Choose an image', 'variation-swatches-woo' ),
				'id'    => 'cfvsw_product_attribute_image',
				'type'  => 'image',
			],

		];

		return isset( $fields[ $type ] ) ? $fields[ $type ] : [];
	}

	/**
	 * Returns html markup for selected term meta type
	 *
	 * @param array  $field term meta type data array.
	 * @param object $term current term data.
	 * @return void
	 * @since  1.0.0
	 */
	public function term_meta_fields_markup( $field, $term ) {
		if ( ! is_array( $field ) ) {
			return;
		}

		$value = '';
		if ( is_object( $term ) && ! empty( $term->term_id ) ) {
			$value = get_term_meta( $term->term_id, 'cfvsw_' . $field['type'], true );
		}

		switch ( $field['type'] ) {
			case 'image':
				$value = ! empty( $value ) ? $value : '';
				?>
				<div class="meta-image-field-wrapper">
					<img class="cfvsw-image-preview" height="60px" width="60px" src="<?php echo esc_url( $value ); ?>" alt="<?php esc_attr_e( 'Variation swatches image preview', 'variation-swatches-woo' ); ?>" style="<?php echo ( empty( $value ) ? 'display:none' : '' ); ?>" />
					<div class="button-wrapper">
						<input type="hidden" class="<?php echo esc_attr( $field['id'] ); ?>" name="cfvsw_image" value="<?php echo esc_attr( $value ); ?>" />
						<button type="button" class="cfvsw_upload_image_button button button-primary button-small"><?php esc_html_e( 'Upload image', 'variation-swatches-woo' ); ?></button>
						<button type="button" style="<?php echo ( empty( $value ) ? 'display:none' : '' ); ?>" class="cfvsw_remove_image_button button button-small"><?php esc_html_e( 'Remove image', 'variation-swatches-woo' ); ?></button>
					</div>
				</div>
				<?php
				break;
			case 'color':
				$value = ! empty( $value ) ? $value : '';
				?>
				<input id="cfvsw_color" class="cfvsw_color" type="text" name="cfvsw_color" value="<?php echo esc_attr( $value ); ?>" />
				<?php
				break;
		}
	}

	/**
	 * Enqueue scripts for term meta types
	 *
	 * @return void
	 * @since  1.0.0
	 */
	public function term_meta_enqueue_scripts() {
		// By checking $_REQUEST['taxonomy'] we are adding scripts so nonce is not required.
		if ( ! isset( $_REQUEST['taxonomy'] ) || ! isset( $_REQUEST['post_type'] ) || 'product' !== $_REQUEST['post_type'] ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}
		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		wp_register_script( 'cfvsw-term-meta-type', CFVSW_URL . 'admin/assets/js/term-meta-type.js', [ 'jquery', 'wp-color-picker' ], CFVSW_VER, true );
		wp_enqueue_script( 'cfvsw-term-meta-type' );
		wp_localize_script(
			'cfvsw-term-meta-type',
			'cfvsw_swatches_term_meta',
			[
				'image_upload_text' => [
					'title'        => __(
						'Select a image to upload',
						'variation-swatches-woo'
					),
					'button_title' => __(
						'Use this image',
						'variation-swatches-woo'
					),
				],
			]
		);

		wp_register_style( 'cfvsw-term-meta', CFVSW_URL . 'admin/assets/css/term-meta.css', [], CFVSW_VER, 'all' );
		wp_enqueue_style( 'cfvsw-term-meta' );
	}
}
