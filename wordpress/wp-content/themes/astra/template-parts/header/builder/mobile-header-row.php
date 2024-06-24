<?php
/**
 * Template part for displaying a row of the mobile header
 *
 * @package Astra Builder
 */

$astra_row = get_query_var( 'row' );
if ( astra_wp_version_compare( '5.4.99', '>=' ) ) {
	$astra_row = wp_parse_args( $args, array( 'row' => '' ) );
	$astra_row = isset( $astra_row['row'] ) ? $astra_row['row'] : '';
}

if ( Astra_Builder_Helper::is_row_empty( $astra_row, 'header', 'mobile' ) ) {

	$astra_customizer_editor_row        = 'section-' . esc_attr( $astra_row ) . '-header-builder';
	$astra_is_transparent_header_enable = astra_get_option( 'transparent-header-enable' );

	if ( 'primary' === $astra_row && $astra_is_transparent_header_enable ) {
		$astra_customizer_editor_row = 'section-transparent-header';
	}

	$astra_row_label = ( 'primary' === $astra_row ) ? 'main' : $astra_row;
	?>
	<div class="ast-<?php echo esc_attr( $astra_row_label ); ?>-header-wrap <?php echo 'primary' === $astra_row ? 'main-header-bar-wrap' : ''; ?>" >
		<div class="<?php echo esc_attr( 'ast-' . $astra_row . '-header-bar ast-' . $astra_row . '-header ' ); ?><?php echo 'primary' === $astra_row ? 'main-header-bar ' : ''; ?>site-<?php echo esc_attr( $astra_row ); ?>-header-wrap site-header-focus-item ast-builder-grid-row-layout-default ast-builder-grid-row-tablet-layout-default ast-builder-grid-row-mobile-layout-default" data-section="<?php echo esc_attr( $astra_customizer_editor_row ); ?>">
				<?php
				if ( is_customize_preview() ) {
					Astra_Builder_UI_Controller::render_grid_row_customizer_edit_button( 'Header', $astra_row );
				}
				/**
				 * Astra Render before Site Content.
				 */
				do_action( "astra_header_{$astra_row}_container_before" );
				?>
					<div class="ast-builder-grid-row <?php echo Astra_Builder_Helper::has_mobile_side_columns( $astra_row ) ? 'ast-builder-grid-row-has-sides' : 'ast-grid-center-col-layout-only ast-flex'; ?> <?php echo Astra_Builder_Helper::has_mobile_center_column( $astra_row ) ? 'ast-grid-center-col-layout' : 'ast-builder-grid-row-no-center'; ?>">
						<?php if ( Astra_Builder_Helper::has_mobile_side_columns( $astra_row ) ) { ?>
							<div class="site-header-<?php echo esc_attr( $astra_row ); ?>-section-left site-header-section ast-flex site-header-section-left">
								<?php
								/**
								 * Astra Render Header Column
								 */
								do_action( 'astra_render_mobile_header_column', $astra_row, 'left' );

								if ( Astra_Builder_Helper::has_mobile_center_column( $astra_row ) ) {
									/**
									 * Astra Render Header Column
									 */
									do_action( 'astra_render_mobile_header_column', $astra_row, 'left_center' );
								}
								?>
							</div>
						<?php } ?>
						<?php if ( Astra_Builder_Helper::has_mobile_center_column( $astra_row ) ) { ?>
							<div class="site-header-<?php echo esc_attr( $astra_row ); ?>-section-center site-header-section ast-flex ast-grid-section-center">
								<?php
								/**
								 * Astra Render Header Column
								 */
								do_action( 'astra_render_mobile_header_column', $astra_row, 'center' );
								?>
							</div>
						<?php } ?>
						<?php if ( Astra_Builder_Helper::has_mobile_side_columns( $astra_row ) ) { ?>
							<div class="site-header-<?php echo esc_attr( $astra_row ); ?>-section-right site-header-section ast-flex ast-grid-right-section">
								<?php
								if ( Astra_Builder_Helper::has_mobile_center_column( $astra_row ) ) {
									/**
									 * Astra Render Header Column
									 */
									do_action( 'astra_render_mobile_header_column', $astra_row, 'right_center' );
								}
								/**
								 * Astra Render Header Column
								 */
								do_action( 'astra_render_mobile_header_column', $astra_row, 'right' );
								?>
							</div>
						<?php } ?>
					</div>
				<?php
				/**
				 * Astra Render after Site Content.
				 */
				do_action( "astra_header_{$astra_row}_container_after" );
				?>
		</div>
	</div>
	<?php
}
