<?php
/**
 * Frontend JS File.
 *
 * @since 2.4.0
 *
 * @package uagb
 */

$selector = '.uagb-block-' . $id;

$countdown_options = apply_filters(
	'uagb_countdown_options',
	array(
		'block_id'       => $attr['block_id'],
		'endDateTime'    => $attr['endDateTime'],
		'showDays'       => $attr['showDays'],
		'showHours'      => $attr['showHours'],
		'showMinutes'    => $attr['showMinutes'],
		'isFrontend'     => true,
		'timerEndAction' => $attr['timerEndAction'],
		'redirectURL'    => $attr['redirectURL'],
	),
	$id,
	$attr
);

ob_start();
?>
window.addEventListener( 'load', function() {
	UAGBCountdown.init( '<?php echo esc_attr( $selector ); ?>', <?php echo wp_json_encode( $countdown_options ); ?> );
});
<?php
$dynamic_js = apply_filters( 'spectra_countdown_frontend_dynamic_js', ob_get_clean(), $selector, $countdown_options );
return $dynamic_js;
?>
