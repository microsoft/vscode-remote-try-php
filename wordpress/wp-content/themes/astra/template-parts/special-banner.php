<?php
/**
 * Template part for displaying archive post's entry banner.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Astra
 * @since 4.0.0
 */

$special_type         = ! empty( $args ) && ! empty( $args['type'] ) ? $args['type'] : 'search';
$astra_banner_control = 'section-' . esc_attr( $special_type ) . '-page-title';

// If description is the only meta available in structure & its blank then no need to render banner markup.
$astra_archive_structure = astra_get_option( $astra_banner_control . '-structure', array( $astra_banner_control . '-title' ) );

if ( empty( $astra_archive_structure ) ) {
	return;
}

$astra_get_archive_description = astra_get_archive_description( $special_type );
if ( 1 === count( $astra_archive_structure ) && in_array( $astra_banner_control . '-description', $astra_archive_structure ) && empty( $astra_get_archive_description ) ) {
	return;
}

// Conditionally updating data section & class.
$astra_attr = 'class="ast-archive-entry-banner"';
if ( is_customize_preview() ) {
	$astra_attr = 'class="ast-archive-entry-banner ast-post-banner-highlight site-header-focus-item" data-section="' . esc_attr( $astra_banner_control ) . '"';
}

$astra_layout_type = astra_get_option( $astra_banner_control . '-layout' );
$astra_data_attrs  = 'data-post-type="' . $special_type . '" data-banner-layout="' . $astra_layout_type . '"';

if ( 'layout-2' === $astra_layout_type && 'custom' === astra_get_option( $astra_banner_control . '-banner-width-type', 'fullwidth' ) ) {
	$astra_data_attrs .= 'data-banner-width-type="custom"';
}

$astra_background_type = astra_get_option( $astra_banner_control . '-banner-image-type', 'none' );
if ( 'layout-2' === $astra_layout_type && 'none' !== $astra_background_type ) {
	$astra_data_attrs .= 'data-banner-background-type="' . $astra_background_type . '"';
}

?>

<section <?php echo wp_kses_post( $astra_attr . ' ' . $astra_data_attrs ); ?>>
	<div class="ast-container">
		<?php
		if ( is_customize_preview() ) {
			Astra_Builder_UI_Controller::render_banner_customizer_edit_button();
		}
			astra_banner_elements_order( $astra_archive_structure );
		?>
	</div>
</section>
