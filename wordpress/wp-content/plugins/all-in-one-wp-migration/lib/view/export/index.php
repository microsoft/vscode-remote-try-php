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

<div class="ai1wm-container">
	<div class="ai1wm-row">
		<div class="ai1wm-left">
			<div class="ai1wm-holder">
				<h1>
					<i class="ai1wm-icon-export"></i>
					<?php _e( 'Export Site', AI1WM_PLUGIN_NAME ); ?>
				</h1>

				<?php if ( is_readable( AI1WM_STORAGE_PATH ) && is_writable( AI1WM_STORAGE_PATH ) ) : ?>

					<form action="" method="post" id="ai1wm-export-form" class="ai1wm-clear">

						<?php include AI1WM_TEMPLATES_PATH . '/export/find-replace.php'; ?>

						<?php do_action( 'ai1wm_export_left_options' ); ?>

						<?php include AI1WM_TEMPLATES_PATH . '/export/advanced-settings.php'; ?>

						<?php include AI1WM_TEMPLATES_PATH . '/export/export-buttons.php'; ?>

						<input type="hidden" name="ai1wm_manual_export" value="1" />

					</form>

					<?php do_action( 'ai1wm_export_left_end' ); ?>

				<?php else : ?>

					<?php include AI1WM_TEMPLATES_PATH . '/export/export-permissions.php'; ?>

				<?php endif; ?>
			</div>
		</div>

		<?php include AI1WM_TEMPLATES_PATH . '/common/sidebar-right.php'; ?>

	</div>
</div>
