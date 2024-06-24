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

class Ai1wm_Export_Enumerate_Media {

	public static function execute( $params ) {

		$exclude_filters = array();

		// Get total media files count
		if ( isset( $params['total_media_files_count'] ) ) {
			$total_media_files_count = (int) $params['total_media_files_count'];
		} else {
			$total_media_files_count = 1;
		}

		// Get total media files size
		if ( isset( $params['total_media_files_size'] ) ) {
			$total_media_files_size = (int) $params['total_media_files_size'];
		} else {
			$total_media_files_size = 1;
		}

		// Set progress
		Ai1wm_Status::info( __( 'Retrieving a list of WordPress media files...', AI1WM_PLUGIN_NAME ) );

		// Exclude selected files
		if ( isset( $params['options']['exclude_files'], $params['excluded_files'] ) ) {
			if ( ( $excluded_files = explode( ',', $params['excluded_files'] ) ) ) {
				foreach ( $excluded_files as $excluded_path ) {
					$exclude_filters[] = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . untrailingslashit( $excluded_path );
				}
			}
		}

		// Create media list file
		$media_list = ai1wm_open( ai1wm_media_list_path( $params ), 'w' );

		// Enumerate over media directory
		if ( isset( $params['options']['no_media'] ) === false ) {
			if ( is_dir( ai1wm_get_uploads_dir() ) ) {

				// Iterate over media directory
				$iterator = new Ai1wm_Recursive_Directory_Iterator( ai1wm_get_uploads_dir() );

				// Exclude media files
				$iterator = new Ai1wm_Recursive_Exclude_Filter( $iterator, apply_filters( 'ai1wm_exclude_media_from_export', ai1wm_media_filters( $exclude_filters ) ) );

				// Recursively iterate over content directory
				$iterator = new Ai1wm_Recursive_Iterator_Iterator( $iterator, RecursiveIteratorIterator::LEAVES_ONLY, RecursiveIteratorIterator::CATCH_GET_CHILD );

				// Write path line
				foreach ( $iterator as $item ) {
					if ( $item->isFile() ) {
						if ( ai1wm_putcsv( $media_list, array( $iterator->getPathname(), $iterator->getSubPathname(), $iterator->getSize(), $iterator->getMTime() ) ) ) {
							$total_media_files_count++;

							// Add current file size
							$total_media_files_size += $iterator->getSize();
						}
					}
				}
			}
		}

		// Set progress
		Ai1wm_Status::info( __( 'Done retrieving a list of WordPress media files.', AI1WM_PLUGIN_NAME ) );

		// Set total media files count
		$params['total_media_files_count'] = $total_media_files_count;

		// Set total media files size
		$params['total_media_files_size'] = $total_media_files_size;

		// Close the media list file
		ai1wm_close( $media_list );

		return $params;
	}
}
