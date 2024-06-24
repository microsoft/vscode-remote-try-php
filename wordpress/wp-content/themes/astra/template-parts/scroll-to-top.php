<?php
/**
 * Scroll To Top Template
 *
 * @package Astra
 * @since 4.0.0
 */

$astra_addon_scroll_top_alignment = astra_get_option( 'scroll-to-top-icon-position' );
$astra_addon_scroll_top_devices   = astra_get_option( 'scroll-to-top-on-devices' );
?>

<div id="ast-scroll-top" tabindex="0" class="<?php echo esc_attr( apply_filters( 'astra_scroll_top_icon', 'ast-scroll-top-icon' ) ); ?> ast-scroll-to-top-<?php echo esc_attr( $astra_addon_scroll_top_alignment ); ?>" data-on-devices="<?php echo esc_attr( $astra_addon_scroll_top_devices ); ?>">
	<?php
	if ( Astra_Icons::is_svg_icons() ) {
		Astra_Icons::get_icons( 'arrow', true );
	}
	?>
	<span class="screen-reader-text"><?php esc_html_e( 'Scroll to Top', 'astra' ); ?></span>
</div>
