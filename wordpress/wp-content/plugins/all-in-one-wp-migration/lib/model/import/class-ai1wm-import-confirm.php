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

class Ai1wm_Import_Confirm {

	public static function execute( $params ) {

		$messages = array();

		// Read package.json file
		$handle = ai1wm_open( ai1wm_package_path( $params ), 'r' );

		// Parse package.json file
		$package = ai1wm_read( $handle, filesize( ai1wm_package_path( $params ) ) );
		$package = json_decode( $package, true );

		// Close handle
		ai1wm_close( $handle );

		// Confirm message
		if ( defined( 'WP_CLI' ) ) {
			$messages[] = __(
				'The import process will overwrite your website including the database, media, plugins, and themes. ' .
				'Are you sure to proceed?',
				AI1WM_PLUGIN_NAME
			);
		} else {
			$messages[] = __(
				'The import process will overwrite your website including the database, media, plugins, and themes. ' .
				'Please ensure that you have a backup of your data before proceeding to the next step.',
				AI1WM_PLUGIN_NAME
			);
		}

		// Check compatibility of PHP versions
		if ( isset( $package['PHP']['Version'] ) ) {
			// Extract major and minor version numbers
			$source_versions = explode( '.', $package['PHP']['Version'] );
			$target_versions = explode( '.', PHP_VERSION );

			$source_major_version = intval( $source_versions[0] );
			$source_minor_version = intval( isset( $source_versions[1] ) ? $source_versions[1] : 0 );

			$target_major_version = intval( $target_versions[0] );
			$target_minor_version = intval( isset( $target_versions[1] ) ? $target_versions[1] : 0 );

			if ( $source_major_version !== $target_major_version ) {
				$from_php = $source_major_version;
				$to_php   = $target_major_version;
			} elseif ( $source_minor_version !== $target_minor_version ) {
				$from_php = sprintf( '%s.%s', $source_major_version, $source_minor_version );
				$to_php   = sprintf( '%s.%s', $target_major_version, $target_minor_version );
			}

			if ( isset( $from_php, $to_php ) ) {
				if ( defined( 'WP_CLI' ) ) {
					$message = __(
						'Your backup is from a PHP %s but the site that you are importing to is PHP %s. ' .
						'This could cause the import to fail. Technical details: https://help.servmask.com/knowledgebase/migrate-wordpress-from-php-5-to-php-7/',
						AI1WM_PLUGIN_NAME
					);
				} else {
					$message = __(
						'<i class="ai1wm-import-info">Your backup is from a PHP %s but the site that you are importing to is PHP %s. ' .
						'This could cause the import to fail. <a href="https://help.servmask.com/knowledgebase/migrate-wordpress-from-php-5-to-php-7/" target="_blank">Technical details</a></i>',
						AI1WM_PLUGIN_NAME
					);
				}

				$messages[] = sprintf( $message, $from_php, $to_php );
			}
		}

		if ( defined( 'WP_CLI' ) ) {
			$assoc_args = array();
			if ( isset( $params['cli_args'] ) ) {
				$assoc_args = $params['cli_args'];
			}

			WP_CLI::confirm( implode( PHP_EOL, $messages ), $assoc_args );

			return $params;
		}

		// Set progress
		Ai1wm_Status::confirm( implode( $messages ) );
		exit;
	}
}
