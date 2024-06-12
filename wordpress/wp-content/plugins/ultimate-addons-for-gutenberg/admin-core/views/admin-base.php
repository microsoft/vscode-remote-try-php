<?php
/**
 * Admin Base HTML.
 *
 * @package uag
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="uag-menu-page-wrapper">
	<div id="uag-menu-page">
		<div class="uag-menu-page-content uag-clear">
		<?php

			do_action( 'uag_render_admin_page_content', $menu_page_slug, $page_action );
		?>
		</div>
	</div>
</div>
