<?php
/**
 * Template part for displaying the Mobile Header
 *
 * @package Astra Builder
 */

$astra_mobile_header_type = astra_get_option( 'mobile-header-type' );

if ( 'full-width' === $astra_mobile_header_type ) {

	$astra_mobile_header_type = 'off-canvas';
}

?>
<div id="ast-mobile-header" class="ast-mobile-header-wrap " data-type="<?php echo esc_attr( $astra_mobile_header_type ); ?>">
	<?php
	do_action( 'astra_mobile_header_bar_top' );

	/**
	 * Astra Top Header
	 */
	do_action( 'astra_mobile_above_header' );

	/**
	 * Astra Main Header
	 */
	do_action( 'astra_mobile_primary_header' );

	/**
	 * Astra Mobile Bottom Header
	 */
	do_action( 'astra_mobile_below_header' );

	astra_main_header_bar_bottom();
		
	// Disable toggle menu if the toggle menu button is not exists in the mobile header items.	
	$header_mobile_items = astra_get_option( 'header-mobile-items', array() );
	array_walk_recursive(
		$header_mobile_items,
		function( string $value ) use ( &$show_mobile_toggle_menu ) {
			if ( 'mobile-trigger' === $value ) {
				$show_mobile_toggle_menu = true;
			}
		}
	);

	// Disable toggle menu for sticky header if the main sticky header is disabled.
	$current_id = get_the_ID();
	$display    = is_int( $current_id ) ? get_post_meta( $current_id, 'ast-main-header-display', true ) : false;
	if ( $show_mobile_toggle_menu && 'disabled' !== apply_filters( 'astra_main_header_display', $display ) ) {
		if ( ( 'dropdown' === astra_get_option( 'mobile-header-type' ) && Astra_Builder_Helper::is_component_loaded( 'mobile-trigger', 'header' ) ) || is_customize_preview() ) {
			$astra_content_alignment = astra_get_option( 'header-offcanvas-content-alignment', 'flex-start' );
			$astra_alignment_class   = 'content-align-' . $astra_content_alignment . ' ';
			?>
			<div class="ast-mobile-header-content <?php echo esc_attr( $astra_alignment_class ); ?>">
				<?php do_action( 'astra_mobile_header_content', 'popup', 'content' ); ?>
			</div>
			<?php
		}
	}
	?>
</div>
