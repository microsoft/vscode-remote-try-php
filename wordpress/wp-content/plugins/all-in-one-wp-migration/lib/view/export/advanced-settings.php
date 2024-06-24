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

<div class="ai1wm-field-set">
	<div class="ai1wm-accordion ai1wm-expandable">
		<h4>
			<i class="ai1wm-icon-arrow-right"></i>
			<?php _e( 'Advanced options', AI1WM_PLUGIN_NAME ); ?>
			<small><?php _e( '(click to expand)', AI1WM_PLUGIN_NAME ); ?></small>
		</h4>
		<ul>
			<?php if ( ai1wm_can_encrypt() ) : ?>
				<li class="ai1wm-encrypt-backups-container">
					<label for="ai1wm-encrypt-backups">
						<input type="checkbox" id="ai1wm-encrypt-backups" name="options[encrypt_backups]" />
						<?php _e( 'Protect this backup with a password', AI1WM_PLUGIN_NAME ); ?>
					</label>
					<div class="ai1wm-encrypt-backups-passwords-toggle">
						<div class="ai1wm-encrypt-backups-passwords-container">
							<div class="ai1wm-input-password-container">
								<input type="password" placeholder="<?php _e( 'Enter a password', AI1WM_PLUGIN_NAME ); ?>" name="options[encrypt_password]" id="ai1wm-backup-encrypt-password">
								<a href="#ai1wm-backup-encrypt-password" class="ai1wm-toggle-password-visibility ai1wm-icon-eye-blocked"></a>
								<div class="ai1wm-error-message"><?php _e( 'A password is required', AI1WM_PLUGIN_NAME ); ?></div>
							</div>
							<div class="ai1wm-input-password-container">
								<input type="password" name="options[encrypt_password_confirmation]" placeholder="<?php _e( 'Repeat the password', AI1WM_PLUGIN_NAME ); ?>" id="ai1wm-backup-encrypt-password-confirmation">
								<a href="#ai1wm-backup-encrypt-password-confirmation" class="ai1wm-toggle-password-visibility ai1wm-icon-eye-blocked"></a>
								<div class="ai1wm-error-message"><?php _e( 'The passwords do not match', AI1WM_PLUGIN_NAME ); ?></div>
							</div>
						</div>
					</div>
				</li>
			<?php else : ?>
				<li class="ai1wm-encrypt-backups-container-disabled">
					<input type="checkbox" id="ai1wm-encrypt-backups" name="options[encrypt_backups]" disabled />
					<?php _e( 'Password-protect and encrypt backups', AI1WM_PLUGIN_NAME ); ?>
					<a href="https://help.servmask.com/knowledgebase/unable-to-encrypt-and-decrypt-backups/" target="_blank"><span class="ai1wm-icon-help"></span></a>
				</li>
			<?php endif; ?>
			<li>
				<label for="ai1wm-no-spam-comments">
					<input type="checkbox" id="ai1wm-no-spam-comments" name="options[no_spam_comments]" />
					<?php _e( 'Do <strong>not</strong> export spam comments', AI1WM_PLUGIN_NAME ); ?>
				</label>
			</li>
			<li>
				<label for="ai1wm-no-post-revisions">
					<input type="checkbox" id="ai1wm-no-post-revisions" name="options[no_post_revisions]" />
					<?php _e( 'Do <strong>not</strong> export post revisions', AI1WM_PLUGIN_NAME ); ?>
				</label>
			</li>
			<li>
				<label for="ai1wm-no-media">
					<input type="checkbox" id="ai1wm-no-media" name="options[no_media]" />
					<?php _e( 'Do <strong>not</strong> export media library (files)', AI1WM_PLUGIN_NAME ); ?>
				</label>
			</li>
			<li>
				<label for="ai1wm-no-themes">
					<input type="checkbox" id="ai1wm-no-themes" name="options[no_themes]" />
					<?php _e( 'Do <strong>not</strong> export themes (files)', AI1WM_PLUGIN_NAME ); ?>
				</label>
			</li>

			<?php do_action( 'ai1wm_export_inactive_themes' ); ?>

			<li>
				<label for="ai1wm-no-muplugins">
					<input type="checkbox" id="ai1wm-no-muplugins" name="options[no_muplugins]" />
					<?php _e( 'Do <strong>not</strong> export must-use plugins (files)', AI1WM_PLUGIN_NAME ); ?>
				</label>
			</li>

			<li>
				<label for="ai1wm-no-plugins">
					<input type="checkbox" id="ai1wm-no-plugins" name="options[no_plugins]" />
					<?php _e( 'Do <strong>not</strong> export plugins (files)', AI1WM_PLUGIN_NAME ); ?>
				</label>
			</li>

			<?php do_action( 'ai1wm_export_inactive_plugins' ); ?>

			<?php do_action( 'ai1wm_export_cache_files' ); ?>

			<li>
				<label for="ai1wm-no-database">
					<input type="checkbox" id="ai1wm-no-database" name="options[no_database]" />
					<?php _e( 'Do <strong>not</strong> export database (sql)', AI1WM_PLUGIN_NAME ); ?>
				</label>
			</li>
			<li>
				<label for="ai1wm-no-email-replace">
					<input type="checkbox" id="ai1wm-no-email-replace" name="options[no_email_replace]" />
					<?php _e( 'Do <strong>not</strong> replace email domain (sql)', AI1WM_PLUGIN_NAME ); ?>
				</label>
			</li>

			<?php do_action( 'ai1wm_export_advanced_settings' ); ?>

			<?php do_action( 'ai1wm_export_exclude_db_tables' ); ?>
		</ul>
	</div>
</div>
