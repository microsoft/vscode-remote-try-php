<?php
/**
 * Frontend JS File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$base_selector = '.uagb-block-';
$selector      = $base_selector . $id;
$js_attr       = array(
	'c_id'              => $attr['c_id'],
	'cookies'           => $attr['cookies'],
	'close_cookie_days' => $attr['close_cookie_days'],
	'noticeDismiss'     => $attr['noticeDismiss'],
	'icon'              => $attr['icon'],
);

ob_start();
?>
window.addEventListener( 'DOMContentLoaded', function() {
	UAGBInlineNotice.init( <?php echo wp_json_encode( $js_attr ); ?>, '<?php echo esc_attr( $selector ); ?>' );
});
<?php
return ob_get_clean();
