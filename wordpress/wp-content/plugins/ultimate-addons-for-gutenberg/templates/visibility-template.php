<?php
/**
 * UAGB Visibility Template.
 *
 * @package UAGB
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta name="viewport" content="width=device-width,initial-scale=1.0" charset="<?php bloginfo( 'charset' ); ?>">
	<?php if ( ! current_theme_supports( 'title-tag' ) ) : ?>
		<title><?php echo wp_get_document_title(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></title>
		<?php endif; ?>
		<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<?php
		the_content();

	wp_footer();
	?>
	</body>
</html>
