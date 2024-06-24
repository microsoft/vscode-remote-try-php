<?php
/**
 * Tab panel view
 *
 * @package Automattic\WooCommerce\Pinterest\View\PHPView
 */

declare( strict_types=1 );

use Automattic\WooCommerce\Pinterest\View\PHPView;

defined( 'ABSPATH' ) || exit;

/**
 * PHP view.
 *
 * @var PHPView $this
 */

/**
 * Form
 *
 * @var array $form
 */
$form = $this->form

?>

<div id="pinterest_attributes" class="panel woocommerce_options_panel">
	<h2><?php esc_html_e( 'Product attributes', 'pinterest-for-woocommerce' ); ?></h2>
	<p class="show_if_variable"><?php esc_html_e( 'As this is a variable product, you can add additional product attributes by going to Variations > Select one variation > Pinterest.', 'pinterest-for-woocommerce' ); ?></p>
	<?php
	// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $this->render_partial( 'inputs/form', array( 'form' => $form ) );
	?>
</div>

