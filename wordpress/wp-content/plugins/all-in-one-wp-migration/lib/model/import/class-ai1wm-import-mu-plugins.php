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

class Ai1wm_Import_Mu_Plugins {

	public static function execute( $params ) {

		// Set progress
		Ai1wm_Status::info( __( 'Activating mu-plugins...', AI1WM_PLUGIN_NAME ) );

		$exclude_files = array(
			AI1WM_MUPLUGINS_NAME . DIRECTORY_SEPARATOR . AI1WM_ENDURANCE_PAGE_CACHE_NAME,
			AI1WM_MUPLUGINS_NAME . DIRECTORY_SEPARATOR . AI1WM_ENDURANCE_PHP_EDGE_NAME,
			AI1WM_MUPLUGINS_NAME . DIRECTORY_SEPARATOR . AI1WM_ENDURANCE_BROWSER_CACHE_NAME,
			AI1WM_MUPLUGINS_NAME . DIRECTORY_SEPARATOR . AI1WM_GD_SYSTEM_PLUGIN_NAME,
			AI1WM_MUPLUGINS_NAME . DIRECTORY_SEPARATOR . AI1WM_WP_STACK_CACHE_NAME,
			AI1WM_MUPLUGINS_NAME . DIRECTORY_SEPARATOR . AI1WM_WP_COMSH_LOADER_NAME,
			AI1WM_MUPLUGINS_NAME . DIRECTORY_SEPARATOR . AI1WM_WP_COMSH_HELPER_NAME,
			AI1WM_MUPLUGINS_NAME . DIRECTORY_SEPARATOR . AI1WM_WP_ENGINE_SYSTEM_PLUGIN_NAME,
			AI1WM_MUPLUGINS_NAME . DIRECTORY_SEPARATOR . AI1WM_WPE_SIGN_ON_PLUGIN_NAME,
			AI1WM_MUPLUGINS_NAME . DIRECTORY_SEPARATOR . AI1WM_WP_ENGINE_SECURITY_AUDITOR_NAME,
			AI1WM_MUPLUGINS_NAME . DIRECTORY_SEPARATOR . AI1WM_WP_CERBER_SECURITY_NAME,
			AI1WM_MUPLUGINS_NAME . DIRECTORY_SEPARATOR . AI1WM_SQLITE_DATABASE_INTEGRATION_NAME,
			AI1WM_MUPLUGINS_NAME . DIRECTORY_SEPARATOR . AI1WM_SQLITE_DATABASE_ZERO_NAME,
		);

		// Open the archive file for reading
		$archive = new Ai1wm_Extractor( ai1wm_archive_path( $params ) );

		// Unpack mu-plugins files
		$archive->extract_by_files_array( WP_CONTENT_DIR, array( AI1WM_MUPLUGINS_NAME ), $exclude_files );

		// Close the archive file
		$archive->close();

		// Set progress
		Ai1wm_Status::info( __( 'Done activating mu-plugins.', AI1WM_PLUGIN_NAME ) );

		return $params;
	}
}
