<?php
/**
 * Template part for displaying the a row of the footer
 *
 * @package Astra Builder
 */

$astra_footer_row = get_query_var( 'row' );
if ( astra_wp_version_compare( '5.4.99', '>=' ) ) {
	$astra_footer_row = wp_parse_args( $args, array( 'row' => '' ) );
	$astra_footer_row = isset( $astra_footer_row['row'] ) ? $astra_footer_row['row'] : '';
}

if ( Astra_Builder_Helper::is_footer_row_empty( $astra_footer_row ) ) {

	$astra_footer_row_option = ( 'above' === $astra_footer_row ) ? 'hba' : ( ( 'below' === $astra_footer_row ) ? 'hbb' : 'hb' );
	$astra_footer_columns    = astra_get_option( $astra_footer_row_option . '-footer-column' );
	$astra_footer_layout     = astra_get_option( $astra_footer_row_option . '-footer-layout' );
	$astra_row_stack_layout  = astra_get_option( $astra_footer_row_option . '-stack' );

	$astra_row_desk_layout = ( isset( $astra_footer_layout['desktop'] ) ) ? $astra_footer_layout['desktop'] : 'full';
	$astra_tab_layout      = ( isset( $astra_footer_layout['tablet'] ) ) ? $astra_footer_layout['tablet'] : 'full';
	$astra_mob_layout      = ( isset( $astra_footer_layout['mobile'] ) ) ? $astra_footer_layout['mobile'] : 'full';

	$astra_desk_stack_layout = ( isset( $astra_row_stack_layout['desktop'] ) ) ? $astra_row_stack_layout['desktop'] : 'stack';
	$astra_tab_stack_layout  = ( isset( $astra_row_stack_layout['tablet'] ) ) ? $astra_row_stack_layout['tablet'] : 'stack';
	$astra_mob_stack_layout  = ( isset( $astra_row_stack_layout['mobile'] ) ) ? $astra_row_stack_layout['mobile'] : 'stack';

	$astra_footer_row_classes = array(
		'site-' . esc_attr( $astra_footer_row ) . '-footer-wrap',
		'ast-builder-grid-row-container',
		'site-footer-focus-item',
		'ast-builder-grid-row-' . esc_attr( $astra_row_desk_layout ),
		'ast-builder-grid-row-tablet-' . esc_attr( $astra_tab_layout ),
		'ast-builder-grid-row-mobile-' . esc_attr( $astra_mob_layout ),
		'ast-footer-row-' . esc_attr( $astra_desk_stack_layout ),
		'ast-footer-row-tablet-' . esc_attr( $astra_tab_stack_layout ),
		'ast-footer-row-mobile-' . esc_attr( $astra_mob_stack_layout ),
	);
	?>
<div class="<?php echo esc_attr( implode( ' ', $astra_footer_row_classes ) ); ?>" data-section="section-<?php echo esc_attr( $astra_footer_row ); ?>-footer-builder">
	<div class="ast-builder-grid-row-container-inner">
		<?php
		if ( is_customize_preview() ) {
			Astra_Builder_UI_Controller::render_grid_row_customizer_edit_button( 'Footer', $astra_footer_row );
		}

		/**
		 * Astra Render before Site container of Footer.
		 */
		do_action( "astra_footer_{$astra_footer_row}_container_before" );
		?>
			<div class="ast-builder-footer-grid-columns site-<?php echo esc_attr( $astra_footer_row ); ?>-footer-inner-wrap ast-builder-grid-row">
			<?php for ( $astra_builder_zones = 1; $astra_builder_zones <= Astra_Builder_Helper::$num_of_footer_columns; $astra_builder_zones++ ) { ?>
				<?php
				if ( $astra_builder_zones > $astra_footer_columns ) {
					break;
				}
				?>
				<div class="site-footer-<?php echo esc_attr( $astra_footer_row ); ?>-section-<?php echo absint( $astra_builder_zones ); ?> site-footer-section site-footer-section-<?php echo absint( $astra_builder_zones ); ?>">
					<?php do_action( 'astra_render_footer_column', $astra_footer_row, $astra_builder_zones ); ?>
				</div>
			<?php } ?>
			</div>
		<?php
		/**
		 * Astra Render before Site container of Footer.
		 */
		do_action( "astra_footer_{$astra_footer_row}_container_after" );
		?>
	</div>

</div>
<?php } ?>
