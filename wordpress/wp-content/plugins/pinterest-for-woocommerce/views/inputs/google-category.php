<?php
/**
 * Google Category Input.
 *
 * @package Automattic\WooCommerce\Pinterest\View\PHPView
 */

declare( strict_types=1 );

use Automattic\WooCommerce\Pinterest\View\PHPView;

defined( 'ABSPATH' ) || exit;

/**
 * PHP View.
 *
 * @var PHPView $this
 */

/**
 * Input.
 *
 * @var array $input
 */
$input = $this->input;

$input['class']         = $input['class'] ?? '';
$input['wrapper_class'] = $input['wrapper_class'] ?? '';
$input['name']          = $input['name'] ?? $input['id'];
$input['value']         = $input['value'] ?? '';
$input['desc_tip']      = $input['desc_tip'] ?? false;

?>
<p class="form-field <?php echo esc_attr( $input['id'] ); ?>_field pinterest-input-google-category <?php echo esc_attr( $input['wrapper_class'] ); ?>">
	<label for="<?php echo esc_attr( $input['id'] ); ?>_google_category"><?php echo wp_kses_post( $input['label'] ); ?></label>
	<select class="wc-product-search" style="width: 50%;" id="<?php echo esc_attr( $input['id'] ); ?>" name="<?php echo esc_attr( $input['name'] ); ?>" data-placeholder="<?php esc_attr_e( 'Search for a category…', 'pinterest-for-woocommerce' ); ?>" data-action="woocommerce_json_search_google_category" data-allow_clear="yes">
		<?php echo '<option value="' . esc_attr( $input['value'] ) . '"' . selected( true, true, false ) . '>' . esc_html( wp_strip_all_tags( $input['value'] ) ) . '</option>'; ?>
	</select>
	<?php
	if ( ! empty( $input['description'] ) && false !== $input['desc_tip'] ) {
		echo wc_help_tip( $input['description'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	if ( ! empty( $input['description'] ) && false === $input['desc_tip'] ) {
		echo '<span class="description">' . wp_kses_post( $input['description'] ) . '</span>';
	}
	?>
</p>
