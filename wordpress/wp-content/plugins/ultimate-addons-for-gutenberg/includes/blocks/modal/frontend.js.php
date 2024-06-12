<?php
/**
 * Frontend JS File.
 *
 * @since 2.2.0
 * @var mixed[] $attr
 * @var int $id
 * @package uagb
 */

$selector = '.uagb-block-' . $id;
ob_start();
?>
	window.addEventListener( 'DOMContentLoaded', function() {
		UAGBModal.init( '<?php echo esc_attr( $selector ); ?>' );
	});
<?php
$dynamic_js = apply_filters( 'spectra_modal_frontend_dynamic_js', ob_get_clean(), $selector, $attr );
return $dynamic_js;
?>
