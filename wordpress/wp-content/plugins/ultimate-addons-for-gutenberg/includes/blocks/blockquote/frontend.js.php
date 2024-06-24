<?php
/**
 * Frontend JS File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

/**
 * Adding this comment to avoid PHPStan errors of undefined variable as these variables are defined else where.
 *
 * @var mixed[] $attr
 */

if ( ! $attr['enableTweet'] ) {
	return '';
}

$target = $attr['iconTargetUrl'];

$base_selector = ( isset( $attr['classMigrate'] ) && $attr['classMigrate'] ) ? '.uagb-block-' : '#uagb-blockquote-';
$selector      = $base_selector . $id;

$share_link = 'https://twitter.com/intent/tweet';
$text       = rawurlencode( $attr['descriptionText'] );

if ( ! empty( trim( $attr['author'] ) ) ) {
	$text .= ' — ' . $attr['author'];
}

$share_link = add_query_arg( 'text', $text, $share_link );

if ( 'current' === $target ) {
	$share_link = add_query_arg( 'url', rawurlencode( home_url() . add_query_arg( false, false ) ), $share_link );
} else {
	$share_link = add_query_arg( 'url', rawurlencode( $attr['customUrl'] ), $share_link );
}

if ( ! empty( trim( $attr['iconShareVia'] ) ) ) {
	$user_name = $attr['iconShareVia'];
	if ( '@' === substr( $user_name, 0, 1 ) ) {
		$user_name = substr( $user_name, 1 );
	}
	$share_link = add_query_arg( 'via', rawurlencode( $user_name ), $share_link );
}
ob_start();
?>
var selector = document.querySelectorAll( '<?php echo esc_attr( $selector ); ?>' );
if ( selector.length > 0 ) {

	var blockquote__tweet = selector[0].getElementsByClassName("uagb-blockquote__tweet-button");

	if ( blockquote__tweet.length > 0 ) {

		blockquote__tweet[0].addEventListener("click",function(){	
			var request_url = "<?php echo esc_url_raw( $share_link ); ?>";
			window.open( request_url );
		});
	}
}
<?php
return ob_get_clean();
?>
