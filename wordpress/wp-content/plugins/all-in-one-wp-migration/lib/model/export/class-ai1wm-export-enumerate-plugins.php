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

class Ai1wm_Export_Enumerate_Plugins {

	public static function execute( $params ) {

		$exclude_filters = array( AI1WM_SQLITE_DATABASE_INTEGRATION_NAME );

		// Get total plugins files count
		if ( isset( $params['total_plugins_files_count'] ) ) {
			$total_plugins_files_count = (int) $params['total_plugins_files_count'];
		} else {
			$total_plugins_files_count = 1;
		}

		// Get total plugins files size
		if ( isset( $params['total_plugins_files_size'] ) ) {
			$total_plugins_files_size = (int) $params['total_plugins_files_size'];
		} else {
			$total_plugins_files_size = 1;
		}

		// Set progress
		Ai1wm_Status::info( __( 'Retrieving a list of WordPress plugin files...', AI1WM_PLUGIN_NAME ) );

		// Exclude inactive plugins
		if ( isset( $params['options']['no_inactive_plugins'] ) ) {
			foreach ( get_plugins() as $plugin_name => $plugin_info ) {
				if ( is_plugin_inactive( $plugin_name ) ) {
					$exclude_filters[] = ( dirname( $plugin_name ) === '.' ? basename( $plugin_name ) : dirname( $plugin_name ) );
				}
			}
		}

		// Exclude selected files
		if ( isset( $params['options']['exclude_files'], $params['excluded_files'] ) ) {
			if ( ( $excluded_files = explode( ',', $params['excluded_files'] ) ) ) {
				foreach ( $excluded_files as $excluded_path ) {
					$exclude_filters[] = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . untrailingslashit( $excluded_path );
				}
			}
		}

		// Create plugins list file
		$plugins_list = ai1wm_open( ai1wm_plugins_list_path( $params ), 'w' );

		// Enumerate over plugins directory
		if ( isset( $params['options']['no_plugins'] ) === false ) {

			// Iterate over plugins directory
			$iterator = new Ai1wm_Recursive_Directory_Iterator( ai1wm_get_plugins_dir() );

			// Exclude plugins files
			$iterator = new Ai1wm_Recursive_Exclude_Filter( $iterator, apply_filters( 'ai1wm_exclude_plugins_from_export', ai1wm_plugin_filters( $exclude_filters ) ) );

			// Recursively iterate over plugins directory
			$iterator = new Ai1wm_Recursive_Iterator_Iterator( $iterator, RecursiveIteratorIterator::LEAVES_ONLY, RecursiveIteratorIterator::CATCH_GET_CHILD );

			// Write path line
			foreach ( $iterator as $item ) {
				if ( $item->isFile() ) {
					if ( ai1wm_putcsv( $plugins_list, array( $iterator->getPathname(), $iterator->getSubPathname(), $iterator->getSize(), $iterator->getMTime() ) ) ) {
						$total_plugins_files_count++;

						// Add current file size
						$total_plugins_files_size += $iterator->getSize();
					}
				}
			}
		}

		// Set progress
		Ai1wm_Status::info( __( 'Done retrieving a list of WordPress plugin files.', AI1WM_PLUGIN_NAME ) );

		// Set total plugins files count
		$params['total_plugins_files_count'] = $total_plugins_files_count;

		// Set total plugins files size
		$params['total_plugins_files_size'] = $total_plugins_files_size;

		// Close the plugins list file
		ai1wm_close( $plugins_list );

		return $params;
	}
}
