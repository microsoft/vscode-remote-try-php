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

class Ai1wm_Export_Enumerate_Themes {

	public static function execute( $params ) {

		$exclude_filters = array();

		// Get total themes files count
		if ( isset( $params['total_themes_files_count'] ) ) {
			$total_themes_files_count = (int) $params['total_themes_files_count'];
		} else {
			$total_themes_files_count = 1;
		}

		// Get total themes files size
		if ( isset( $params['total_themes_files_size'] ) ) {
			$total_themes_files_size = (int) $params['total_themes_files_size'];
		} else {
			$total_themes_files_size = 1;
		}

		// Set progress
		Ai1wm_Status::info( __( 'Retrieving a list of WordPress theme files...', AI1WM_PLUGIN_NAME ) );

		// Exclude inactive themes
		if ( isset( $params['options']['no_inactive_themes'] ) ) {
			foreach ( search_theme_directories() as $theme_name => $theme_info ) {
				if ( ! in_array( $theme_name, array( get_template(), get_stylesheet() ) ) ) {
					if ( isset( $theme_info['theme_root'] ) ) {
						$exclude_filters[] = $theme_info['theme_root'] . DIRECTORY_SEPARATOR . $theme_name;
					}
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

		// Create themes list file
		$themes_list = ai1wm_open( ai1wm_themes_list_path( $params ), 'w' );

		// Enumerate over themes directory
		if ( isset( $params['options']['no_themes'] ) === false ) {
			foreach ( ai1wm_get_themes_dirs() as $theme_dir ) {
				if ( is_dir( $theme_dir ) ) {

					// Iterate over themes directory
					$iterator = new Ai1wm_Recursive_Directory_Iterator( $theme_dir );

					// Exclude themes files
					$iterator = new Ai1wm_Recursive_Exclude_Filter( $iterator, apply_filters( 'ai1wm_exclude_themes_from_export', ai1wm_theme_filters( $exclude_filters ) ) );

					// Recursively iterate over themes directory
					$iterator = new Ai1wm_Recursive_Iterator_Iterator( $iterator, RecursiveIteratorIterator::LEAVES_ONLY, RecursiveIteratorIterator::CATCH_GET_CHILD );

					// Write path line
					foreach ( $iterator as $item ) {
						if ( $item->isFile() ) {
							if ( ai1wm_putcsv( $themes_list, array( $iterator->getPathname(), $iterator->getSubPathname(), $iterator->getSize(), $iterator->getMTime() ) ) ) {
								$total_themes_files_count++;

								// Add current file size
								$total_themes_files_size += $iterator->getSize();
							}
						}
					}
				}
			}
		}

		// Set progress
		Ai1wm_Status::info( __( 'Done retrieving a list of WordPress theme files.', AI1WM_PLUGIN_NAME ) );

		// Set total themes files count
		$params['total_themes_files_count'] = $total_themes_files_count;

		// Set total themes files size
		$params['total_themes_files_size'] = $total_themes_files_size;

		// Close the themes list file
		ai1wm_close( $themes_list );

		return $params;
	}
}
