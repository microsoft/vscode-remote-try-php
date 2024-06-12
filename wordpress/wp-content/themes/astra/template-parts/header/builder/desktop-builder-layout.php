<?php
/**
 * Template part for displaying header row.
 *
 * @package Astra Builder
 */

$astra_mobile_header_type = astra_get_option( 'mobile-header-type' );

if ( 'full-width' === $astra_mobile_header_type ) {

	$astra_mobile_header_type = 'off-canvas';
}
?>
<div id="ast-desktop-header" data-toggle-type="<?php echo esc_attr( $astra_mobile_header_type ); ?>">
	<?php
	astra_main_header_bar_top();

	/**
	 * Astra Top Header
	 */
	do_action( 'astra_above_header' );

	/**
	 * Astra Main Header
	 */
	do_action( 'astra_primary_header' );

	/**
	 * Astra Bottom Header
	 */
	do_action( 'astra_below_header' );

	astra_main_header_bar_bottom();

	
	// Disable toggle menu if the toggle menu button is not exists in the desktop header items.	
	$header_desktop_items = astra_get_option( 'header-desktop-items', array() );
	array_walk_recursive(
		$header_desktop_items,
		function( string $value ) use ( &$show_desktop_toggle_menu ) {
			if ( 'mobile-trigger' === $value ) {
				$show_desktop_toggle_menu = true;
			}
		}
	);
	
	// Disable toggle menu for sticky header if the main sticky header is disabled.
	$current_id = get_the_ID();
	$display    = is_int( $current_id ) ? get_post_meta( $current_id, 'ast-main-header-display', true ) : false;
	if ( $show_desktop_toggle_menu && 'disabled' !== apply_filters( 'astra_main_header_display', $display ) ) {
		if ( ( 'dropdown' === $astra_mobile_header_type && Astra_Builder_Helper::is_component_loaded( 'mobile-trigger', 'header' ) ) || is_customize_preview() ) {
			$astra_content_alignment = astra_get_option( 'header-offcanvas-content-alignment', 'flex-start' );
			$astra_alignment_class   = 'content-align-' . $astra_content_alignment . ' ';
			?>
			<div class="ast-desktop-header-content <?php echo esc_attr( $astra_alignment_class ); ?>">
				<?php do_action( 'astra_desktop_header_content', 'popup', 'content' ); ?>
			</div>
			<?php
		}
	}
	?>
</div> <!-- Main Header Bar Wrap -->
<?php
/**
 * Astra Mobile Header
 */
do_action( 'astra_mobile_header' );
?>
