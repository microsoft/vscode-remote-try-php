<?php
/**
 * Variation forms.
 *
 * @package Automattic\WooCommerce\Pinterest\View\PHPView
 */

declare( strict_types=1 );

use Automattic\WooCommerce\Pinterest\Admin\Input\SelectWithTextInput;
use Automattic\WooCommerce\Pinterest\View\PHPView;

defined( 'ABSPATH' ) || exit;

/**
 * PHP view.
 *
 * @var PHPView $this
 */

/**
 * Form children.
 *
 * @var array $children
 */
$children = $this->children;
?>

<?php if ( $this->is_root ) : ?>
<div class="pinterest-metabox wc-metabox closed">
	<h3>
		<strong><?php esc_html_e( 'Pinterest', 'pinterest-for-woocommerce' ); ?></strong>
		<div class="handlediv" aria-label="Click to toggle"></div>
	</h3>
	<div class="wc-metabox-content" style="display: none;">
	<?php endif; ?>
	<?php
	foreach ( $children as $form ) {
		if ( ! empty( $form['type'] ) ) {
			$form['wrapper_class'] =
				sprintf( '%s %s', $form['wrapper_class'] ?? '', 'form-row form-row-full' );

			if ( 'select-with-text-input' === $form['type'] && ! empty( $form['children'][ SelectWithTextInput::SELECT_INPUT_KEY ] ) && ! empty( $form['children'][ SelectWithTextInput::CUSTOM_INPUT_KEY ] ) ) {
				$form['children'][ SelectWithTextInput::SELECT_INPUT_KEY ]['wrapper_class'] =
					sprintf( '%s %s', $form['children'][ SelectWithTextInput::SELECT_INPUT_KEY ]['wrapper_class'] ?? '', 'form-row form-row-first' );

				$form['children'][ SelectWithTextInput::CUSTOM_INPUT_KEY ]['wrapper_class'] =
					sprintf( '%s %s', $form['children'][ SelectWithTextInput::CUSTOM_INPUT_KEY ]['wrapper_class'] ?? '', 'form-row form-row-last' );
			}

			// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $this->render_partial( 'inputs/form', array( 'form' => $form ) );
		} else {
			// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $this->render_partial( 'attributes/variations-form', $form );
		}
	}
	?>
	<?php if ( $this->is_root ) : ?>
	</div>
</div>
<?php endif; ?>
