<?php
/**
 * Frontend JS File.
 *
 * @since 2.1.0
 *
 * @package uagb
 */

$selector = '.uagb-block-' . $id;

$counter_options = apply_filters(
	'uagb_counter_options',
	array(
		'layout'            => $attr['layout'],
		'heading'           => $attr['heading'],
		'numberPrefix'      => $attr['numberPrefix'],
		'numberSuffix'      => $attr['numberSuffix'],
		'startNumber'       => $attr['startNumber'],
		'endNumber'         => $attr['endNumber'],
		'totalNumber'       => $attr['totalNumber'],
		'decimalPlaces'     => $attr['decimalPlaces'],
		'animationDuration' => $attr['animationDuration'],
		'thousandSeparator' => $attr['thousandSeparator'],
		'circleSize'        => $attr['circleSize'],
		'circleStokeSize'   => $attr['circleStokeSize'],
		'isFrontend'        => $attr['isFrontend'],
	),
	$id
);

ob_start();
?>
window.addEventListener( 'load', function() {
	UAGBCounter.init( '<?php echo esc_attr( $selector ); ?>', <?php echo wp_json_encode( $counter_options ); ?> );
});
<?php
return ob_get_clean();
?>
