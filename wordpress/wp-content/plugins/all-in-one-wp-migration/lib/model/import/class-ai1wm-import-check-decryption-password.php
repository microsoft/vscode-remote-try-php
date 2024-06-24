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

class Ai1wm_Import_Check_Decryption_Password {

	public static function execute( $params ) {
		global $ai1wm_params;

		// Read package.json file
		$handle = ai1wm_open( ai1wm_package_path( $params ), 'r' );

		// Parse package.json file
		$package = ai1wm_read( $handle, filesize( ai1wm_package_path( $params ) ) );
		$package = json_decode( $package, true );

		// Close handle
		ai1wm_close( $handle );

		if ( ! empty( $params['decryption_password'] ) ) {
			if ( ai1wm_is_decryption_password_valid( $package['EncryptedSignature'], $params['decryption_password'] ) ) {
				$params['is_decryption_password_valid'] = true;

				$archive = new Ai1wm_Extractor( ai1wm_archive_path( $params ), $params['decryption_password'] );
				$archive->extract_by_files_array( ai1wm_storage_path( $params ), array( AI1WM_MULTISITE_NAME, AI1WM_DATABASE_NAME ), array(), array() );

				Ai1wm_Status::info( __( 'Done validating the decryption password.', AI1WM_PLUGIN_NAME ) );

				$ai1wm_params = $params;

				return $params;
			}

			$decryption_password_error = __( 'The decryption password is not valid.', AI1WM_PLUGIN_NAME );

			if ( defined( 'WP_CLI' ) ) {
				WP_CLI::error( $decryption_password_error );
			} else {
				Ai1wm_Status::backup_is_encrypted( $decryption_password_error );
				exit;
			}
		}

		return $params;
	}
}
