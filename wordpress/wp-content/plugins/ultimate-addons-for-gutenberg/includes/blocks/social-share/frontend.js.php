<?php
/**
 * Frontend JS File.
 *
 * @since 2.0.0
 * @var mixed[] $attr
 * @var int $id
 * @package uagb
 */

$base_selector = ( isset( $attr['classMigrate'] ) && $attr['classMigrate'] ) ? '.uagb-block-' : '#uagb-social-share-';
$selector      = $base_selector . $id;
global $post;
// Get the featured image.
if ( has_post_thumbnail() ) {
	$thumbnail_id   = get_post_thumbnail_id( $post->ID );
	$thumbnail_data = $thumbnail_id ? wp_get_attachment_image_src( $thumbnail_id, 'large', true ) : '';
	$thumbnail      = is_array( $thumbnail_data ) ? strval( current( $thumbnail_data ) ) : '';
} else {
	$thumbnail = '';
}
ob_start();
?>
var ssLinksParent = document.querySelector( '<?php echo esc_attr( $selector ); ?>' );
ssLinksParent?.addEventListener( 'keyup', function ( e ) {
var link = e.target.closest( '.uagb-ss__link' );
if ( link && e.keyCode === 13 ) {
	handleSocialLinkClick( link );
}
});

ssLinksParent?.addEventListener( 'click', function ( e ) {
var link = e.target.closest( '.uagb-ss__link' );
if ( link ) {
	handleSocialLinkClick( link );
}
});

function handleSocialLinkClick( link ) {
var social_url = link.dataset.href;
var target = "";
if ( social_url == "mailto:?body=" ) {
	target = "_self";
}
var request_url = "";
if ( social_url.indexOf("/pin/create/link/?url=") !== -1 ) {
	request_url = social_url + encodeURIComponent( window.location.href ) + "&media=" + '<?php echo esc_url( $thumbnail ); ?>';
} else {
	request_url = social_url + encodeURIComponent( window.location.href );
}
window.open( request_url, target );
}
<?php
return ob_get_clean();
?>
