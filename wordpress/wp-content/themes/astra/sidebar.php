<?php
/**
 * The sidebar containing the main widget area.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Astra
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$astra_sidebar = apply_filters( 'astra_get_sidebar', 'sidebar-1' );

echo '<div ';
	echo astra_attr(
		'sidebar',
		array(
			'id'    => 'secondary',
			'class' => join( ' ', astra_get_secondary_class() ),
		)
	);
	echo '>';
	?>

	<div class="sidebar-main" <?php /** @psalm-suppress TooManyArguments */ echo apply_filters( 'astra_sidebar_data_attrs', '', $astra_sidebar ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, Generic.Commenting.DocComment.MissingShort ?>>
		<?php astra_sidebars_before(); ?>

		<?php

		if ( is_active_sidebar( $astra_sidebar ) ) :
				dynamic_sidebar( $astra_sidebar );
		endif;

		astra_sidebars_after();
		?>

	</div><!-- .sidebar-main -->
</div><!-- #secondary -->
