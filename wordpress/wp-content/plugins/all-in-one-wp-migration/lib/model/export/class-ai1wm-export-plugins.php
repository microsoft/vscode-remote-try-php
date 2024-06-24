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

class Ai1wm_Export_Plugins {

	public static function execute( $params ) {

		// Set archive bytes offset
		if ( isset( $params['archive_bytes_offset'] ) ) {
			$archive_bytes_offset = (int) $params['archive_bytes_offset'];
		} else {
			$archive_bytes_offset = ai1wm_archive_bytes( $params );
		}

		// Set file bytes offset
		if ( isset( $params['file_bytes_offset'] ) ) {
			$file_bytes_offset = (int) $params['file_bytes_offset'];
		} else {
			$file_bytes_offset = 0;
		}

		// Set plugins bytes offset
		if ( isset( $params['plugins_bytes_offset'] ) ) {
			$plugins_bytes_offset = (int) $params['plugins_bytes_offset'];
		} else {
			$plugins_bytes_offset = 0;
		}

		// Get processed files size
		if ( isset( $params['processed_files_size'] ) ) {
			$processed_files_size = (int) $params['processed_files_size'];
		} else {
			$processed_files_size = 0;
		}

		// Get total plugins files size
		if ( isset( $params['total_plugins_files_size'] ) ) {
			$total_plugins_files_size = (int) $params['total_plugins_files_size'];
		} else {
			$total_plugins_files_size = 1;
		}

		// Get total plugins files count
		if ( isset( $params['total_plugins_files_count'] ) ) {
			$total_plugins_files_count = (int) $params['total_plugins_files_count'];
		} else {
			$total_plugins_files_count = 1;
		}

		// What percent of files have we processed?
		$progress = (int) min( ( $processed_files_size / $total_plugins_files_size ) * 100, 100 );

		// Set progress
		Ai1wm_Status::info( sprintf( __( 'Archiving %d plugin files...<br />%d%% complete', AI1WM_PLUGIN_NAME ), $total_plugins_files_count, $progress ) );

		// Flag to hold if file data has been processed
		$completed = true;

		// Start time
		$start = microtime( true );

		// Get plugins list file
		$plugins_list = ai1wm_open( ai1wm_plugins_list_path( $params ), 'r' );

		// Set the file pointer at the current index
		if ( fseek( $plugins_list, $plugins_bytes_offset ) !== -1 ) {

			// Open the archive file for writing
			$archive = new Ai1wm_Compressor( ai1wm_archive_path( $params ) );

			// Set the file pointer to the one that we have saved
			$archive->set_file_pointer( $archive_bytes_offset );

			// Loop over files
			while ( list( $file_abspath, $file_relpath, $file_size, $file_mtime ) = fgetcsv( $plugins_list ) ) {
				$file_bytes_written = 0;

				// Add file to archive
				if ( ( $completed = $archive->add_file( $file_abspath, 'plugins' . DIRECTORY_SEPARATOR . $file_relpath, $file_bytes_written, $file_bytes_offset ) ) ) {
					$file_bytes_offset = 0;

					// Get plugins bytes offset
					$plugins_bytes_offset = ftell( $plugins_list );
				}

				// Increment processed files size
				$processed_files_size += $file_bytes_written;

				// What percent of files have we processed?
				$progress = (int) min( ( $processed_files_size / $total_plugins_files_size ) * 100, 100 );

				// Set progress
				Ai1wm_Status::info( sprintf( __( 'Archiving %d plugin files...<br />%d%% complete', AI1WM_PLUGIN_NAME ), $total_plugins_files_count, $progress ) );

				// More than 10 seconds have passed, break and do another request
				if ( ( $timeout = apply_filters( 'ai1wm_completed_timeout', 10 ) ) ) {
					if ( ( microtime( true ) - $start ) > $timeout ) {
						$completed = false;
						break;
					}
				}
			}

			// Get archive bytes offset
			$archive_bytes_offset = $archive->get_file_pointer();

			// Truncate the archive file
			$archive->truncate();

			// Close the archive file
			$archive->close();
		}

		// End of the plugins list?
		if ( feof( $plugins_list ) ) {

			// Unset archive bytes offset
			unset( $params['archive_bytes_offset'] );

			// Unset file bytes offset
			unset( $params['file_bytes_offset'] );

			// Unset plugins bytes offset
			unset( $params['plugins_bytes_offset'] );

			// Unset processed files size
			unset( $params['processed_files_size'] );

			// Unset total plugins files size
			unset( $params['total_plugins_files_size'] );

			// Unset total plugins files count
			unset( $params['total_plugins_files_count'] );

			// Unset completed flag
			unset( $params['completed'] );

		} else {

			// Set archive bytes offset
			$params['archive_bytes_offset'] = $archive_bytes_offset;

			// Set file bytes offset
			$params['file_bytes_offset'] = $file_bytes_offset;

			// Set plugins bytes offset
			$params['plugins_bytes_offset'] = $plugins_bytes_offset;

			// Set processed files size
			$params['processed_files_size'] = $processed_files_size;

			// Set total plugins files size
			$params['total_plugins_files_size'] = $total_plugins_files_size;

			// Set total plugins files count
			$params['total_plugins_files_count'] = $total_plugins_files_count;

			// Set completed flag
			$params['completed'] = $completed;
		}

		// Close the plugins list file
		ai1wm_close( $plugins_list );

		return $params;
	}
}
