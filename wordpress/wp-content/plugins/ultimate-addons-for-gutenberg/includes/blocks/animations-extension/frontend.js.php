<?php
/**
 * Frontend JS File.
 *
 * @since 2.6.0
 *
 * @package uagb
 */

$selector = '.uagb-block-' . $id;

$animation_data = apply_filters(
	'uagb_animation_data',
	array(
		'UAGAnimationType' => $attr['UAGAnimationType'],
	),
	$id
);

ob_start();
?>
window.addEventListener( 'load', function() {
	UAGBAnimation.init( <?php echo wp_json_encode( $animation_data ); ?> );
} );
<?php
return ob_get_clean();
?>
