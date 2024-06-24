<?php
/**
 * Copyright (C) 2014-2023 ServMask Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * ███████╗███████╗██████╗ ██╗   ██╗███╗   ███╗ █████╗ ███████╗██╗  ██╗
 * ██╔════╝██╔════╝██╔══██╗██║   ██║████╗ ████║██╔══██╗██╔════╝██║ ██╔╝
 * ███████╗█████╗  ██████╔╝██║   ██║██╔████╔██║███████║███████╗█████╔╝
 * ╚════██║██╔══╝  ██╔══██╗╚██╗ ██╔╝██║╚██╔╝██║██╔══██║╚════██║██╔═██╗
 * ███████║███████╗██║  ██║ ╚████╔╝ ██║ ╚═╝ ██║██║  ██║███████║██║  ██╗
 * ╚══════╝╚══════╝╚═╝  ╚═╝  ╚═══╝  ╚═╝     ╚═╝╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Kangaroos cannot jump here' );
}
?>

<p class="max-upload-size">
	<?php printf( __( 'Maximum upload file size: <strong>%s</strong>.', AI1WM_PLUGIN_NAME ), esc_html( ai1wm_size_format( wp_max_upload_size() ) ) ); ?>
</p>
<p>
	<a href="https://help.servmask.com/2018/10/27/how-to-increase-maximum-upload-file-size-in-wordpress/" target="_blank"><?php _e( 'How-to: Increase maximum upload file size', AI1WM_PLUGIN_NAME ); ?></a>
	<?php _e( 'or', AI1WM_PLUGIN_NAME ); ?>
	<a href="https://servmask.com/products/unlimited-extension" target="_blank" class="ai1wm-label">
		<i class="ai1wm-icon-notification"></i>
		<?php _e( 'Get unlimited', AI1WM_PLUGIN_NAME ); ?>
	</a>
</p>
