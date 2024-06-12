<?php
/**
 * Template part for displaying the a row of the header
 *
 * @package Astra Builder
 */

$astra_header_row = get_query_var( 'row' );
if ( astra_wp_version_compare( '5.4.99', '>=' ) ) {
	$astra_header_row = wp_parse_args( $args, array( 'row' => '' ) );
	$astra_header_row = isset( $astra_header_row['row'] ) ? $astra_header_row['row'] : '';
}

if ( Astra_Builder_Helper::is_row_empty( $astra_header_row, 'header', 'desktop' ) ) {

	$astra_customizer_editor_row = 'section-' . esc_attr( $astra_header_row ) . '-header-builder';

	$astra_row_label = ( 'primary' === $astra_header_row ) ? 'main' : $astra_header_row;

	?>
	<div class="ast-<?php echo esc_attr( $astra_row_label ); ?>-header-wrap <?php echo 'primary' === $astra_header_row ? 'main-header-bar-wrap' : ''; ?> ">
		<div class="<?php echo esc_attr( 'ast-' . $astra_header_row . '-header-bar ast-' . $astra_header_row . '-header' ); ?> <?php echo 'primary' === $astra_header_row ? 'main-header-bar' : ''; ?> site-header-focus-item" data-section="<?php echo esc_attr( $astra_customizer_editor_row ); ?>">
			<?php
			if ( is_customize_preview() ) {
				Astra_Builder_UI_Controller::render_grid_row_customizer_edit_button( 'Header', $astra_header_row );
			}
			/**
			 * Astra Render before Site Content.
			 */
			do_action( "astra_header_{$astra_header_row}_container_before" );
			?>
			<div class="site-<?php echo esc_attr( $astra_header_row ); ?>-header-wrap ast-builder-grid-row-container site-header-focus-item ast-container" data-section="<?php echo esc_attr( $astra_customizer_editor_row ); ?>">
				<div class="ast-builder-grid-row <?php echo Astra_Builder_Helper::has_side_columns( $astra_header_row ) ? 'ast-builder-grid-row-has-sides' : 'ast-grid-center-col-layout-only ast-flex'; ?> <?php echo Astra_Builder_Helper::has_center_column( $astra_header_row ) ? 'ast-grid-center-col-layout' : 'ast-builder-grid-row-no-center'; ?>">
					<?php if ( Astra_Builder_Helper::has_side_columns( $astra_header_row ) ) { ?>
						<div class="site-header-<?php echo esc_attr( $astra_header_row ); ?>-section-left site-header-section ast-flex site-header-section-left">
							<?php
								/**
								 * Astra Render Header Column
								 */
								do_action( 'astra_render_header_column', $astra_header_row, 'left' );
							if ( Astra_Builder_Helper::has_center_column( $astra_header_row ) ) {
								?>
										<div class="site-header-<?php echo esc_attr( $astra_header_row ); ?>-section-left-center site-header-section ast-flex ast-grid-left-center-section">
									<?php
									/**
									 * Astra Render Header Column
									 */
									do_action( 'astra_render_header_column', $astra_header_row, 'left_center' );
									?>
										</div>
									<?php
							}
							?>
						</div>
						<?php } ?>
						<?php if ( Astra_Builder_Helper::has_center_column( $astra_header_row ) ) { ?>
							<div class="site-header-<?php echo esc_attr( $astra_header_row ); ?>-section-center site-header-section ast-flex ast-grid-section-center">
								<?php
								/**
								 * Astra Render Header Column
								 */
								do_action( 'astra_render_header_column', $astra_header_row, 'center' );
								?>
							</div>
						<?php } ?>
						<?php if ( Astra_Builder_Helper::has_side_columns( $astra_header_row ) ) { ?>
							<div class="site-header-<?php echo esc_attr( $astra_header_row ); ?>-section-right site-header-section ast-flex ast-grid-right-section">
								<?php
								if ( Astra_Builder_Helper::has_center_column( $astra_header_row ) ) {
									?>
									<div class="site-header-<?php echo esc_attr( $astra_header_row ); ?>-section-right-center site-header-section ast-flex ast-grid-right-center-section">
										<?php
										/**
										 * Astra Render Header Column
										 */
										do_action( 'astra_render_header_column', $astra_header_row, 'right_center' );
										?>
									</div>
									<?php
								}
								/**
								 * Astra Render Header Column
								 */
								do_action( 'astra_render_header_column', $astra_header_row, 'right' );
								?>
							</div>
						<?php } ?>
						</div>
					</div>
					<?php
					/**
					 * Astra Render after Site Content.
					 */
					do_action( "astra_header_{$astra_header_row}_container_after" );
					?>
			</div>
			</div>
	<?php
}
