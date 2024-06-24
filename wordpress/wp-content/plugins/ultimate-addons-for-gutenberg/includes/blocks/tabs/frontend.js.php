<?php
/**
 * Frontend JS File.
 *
 * @since 2.0.0
 * @var int $id
 *
 * @package uagb
 */

$selector = '.uagb-block-' . $id;
ob_start();
?>
window.addEventListener( 'load', function() {
	UAGBTabs.init( '<?php echo esc_attr( $selector ); ?>' );
	UAGBTabs.anchorTabId( '<?php echo esc_attr( $selector ); ?>' );
});
window.addEventListener( 'hashchange', function() {
	UAGBTabs.anchorTabId( '<?php echo esc_attr( $selector ); ?>' );
}, false );
<?php
return ob_get_clean();
?>
