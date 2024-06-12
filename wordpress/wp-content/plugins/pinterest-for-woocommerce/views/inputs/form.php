<?php
/**
 * Form
 *
 * @package Automattic\WooCommerce\Pinterest\View\PHPView
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * PHP view.
 *
 * @var \Automattic\WooCommerce\Pinterest\View\PHPView $this
 */

/**
 * Form.
 *
 * @var array $form
 */
$form = $this->form;
?>

<div class="pinterest-input <?php echo esc_attr( $form['pinterest_wrapper_class'] ?? '' ); ?>">
	<?php
	if ( ! empty( $form['type'] ) ) {
		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $this->render_partial( path_join( 'inputs/', $form['type'] ), array( 'input' => $form ) );
	}

	if ( ! empty( $form['children'] ) ) {
		foreach ( $form['children'] as $form ) {
			// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $this->render_partial( 'inputs/form', array( 'form' => $form ) );
		}
	}
	?>
</div>


