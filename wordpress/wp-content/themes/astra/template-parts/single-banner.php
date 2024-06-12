<?php
/**
 * Template part for displaying single post's entry banner.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Astra
 * @since 4.0.0
 */

$astra_post_type      = strval( get_post_type() );
$astra_banner_control = 'ast-dynamic-single-' . esc_attr( $astra_post_type );

// If banner will be with empty markup then better to skip it.
if ( false !== strpos( astra_entry_header_class( false ), 'ast-header-without-markup' ) ) {
	return;
}

// Conditionally updating data section & class.
$astra_attr = 'class="ast-single-entry-banner"';
if ( is_customize_preview() ) {
	$astra_attr = 'class="ast-single-entry-banner ast-post-banner-highlight site-header-focus-item" data-section="' . esc_attr( $astra_banner_control ) . '"';
}

$astra_data_attrs = 'data-post-type="' . $astra_post_type . '"';

$astra_layout_type = astra_get_option( $astra_banner_control . '-layout', 'layout-1' );
$astra_data_attrs .= 'data-banner-layout="' . $astra_layout_type . '"';

if ( 'layout-2' === $astra_layout_type && 'custom' === astra_get_option( $astra_banner_control . '-banner-width-type', 'fullwidth' ) ) {
	$astra_data_attrs .= 'data-banner-width-type="custom"';
}

$astra_featured_background = astra_get_option( $astra_banner_control . '-featured-as-background', false );
if ( 'layout-2' === $astra_layout_type && $astra_featured_background ) {
	$astra_data_attrs .= 'data-banner-background-type="featured"';
}

?>
<section <?php echo wp_kses_post( $astra_attr . ' ' . $astra_data_attrs ); ?>>

<div class="ast-container">
		<?php
		if ( is_customize_preview() ) {
			Astra_Builder_UI_Controller::render_banner_customizer_edit_button();
		}
			astra_banner_elements_order();
		?>
	</div>
</section>
