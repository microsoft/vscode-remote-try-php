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

class Ai1wm_Import_Users {

	public static function execute( $params ) {

		// Check multisite.json file
		if ( is_file( ai1wm_multisite_path( $params ) ) ) {

			// Set progress
			Ai1wm_Status::info( __( 'Preparing users...', AI1WM_PLUGIN_NAME ) );

			// Read multisite.json file
			$handle = ai1wm_open( ai1wm_multisite_path( $params ), 'r' );

			// Parse multisite.json file
			$multisite = ai1wm_read( $handle, filesize( ai1wm_multisite_path( $params ) ) );
			$multisite = json_decode( $multisite, true );

			// Close handle
			ai1wm_close( $handle );

			ai1wm_populate_roles();

			// Set WordPress super admins
			if ( isset( $multisite['Admins'] ) && ( $admins = $multisite['Admins'] ) ) {
				foreach ( $admins as $username ) {
					if ( ( $user = get_user_by( 'login', $username ) ) ) {
						if ( $user->exists() ) {
							$user->set_role( 'administrator' );
						}
					}
				}
			}

			// Set progress
			Ai1wm_Status::info( __( 'Done preparing users.', AI1WM_PLUGIN_NAME ) );
		}

		return $params;
	}
}
