<?php
/**
 * Frontend JS File.
 * 
 * @var int $id
 * @since 2.13.1
 *
 * @package uagb
 */

$selector = '.uagb-block-' . $id;

ob_start();
?>
window.addEventListener( 'load', function() {
	UAGBButtonChild.init( '<?php echo esc_attr( $selector ); ?>' );
});
<?php
return ob_get_clean();
?>
