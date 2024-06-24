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

class Ai1wm_Import_Check_Encryption {

	public static function execute( $params ) {
		// Read package.json file
		$handle = ai1wm_open( ai1wm_package_path( $params ), 'r' );

		// Parse package.json file
		$package = ai1wm_read( $handle, filesize( ai1wm_package_path( $params ) ) );
		$package = json_decode( $package, true );

		// Close handle
		ai1wm_close( $handle );

		if ( empty( $package['Encrypted'] ) || empty( $package['EncryptedSignature'] ) || ! empty( $params['is_decryption_password_valid'] ) ) {
			return $params;
		}

		if ( ! ai1wm_can_decrypt() ) {
			$message = __( 'Importing an encrypted backup is not supported on this server. <a href="https://help.servmask.com/knowledgebase/unable-to-encrypt-and-decrypt-backups/" target="_blank">Technical details</a>', AI1WM_PLUGIN_NAME );

			if ( defined( 'WP_CLI' ) ) {
				WP_CLI::error( $message );
			} else {
				Ai1wm_Status::server_cannot_decrypt( $message );
				exit;
			}
		}

		if ( defined( 'WP_CLI' ) ) {
			$message = __(
				'Backup is encrypted. Please provide decryption password: ',
				AI1WM_PLUGIN_NAME
			);

			$params['decryption_password'] = readline( $message );

			return $params;
		}

		Ai1wm_Status::backup_is_encrypted( null );
		exit;
	}
}
