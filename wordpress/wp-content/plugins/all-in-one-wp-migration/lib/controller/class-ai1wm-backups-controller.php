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

class Ai1wm_Backups_Controller {

	public static function index() {
		Ai1wm_Template::render(
			'backups/index',
			array(
				'backups'      => Ai1wm_Backups::get_files(),
				'labels'       => Ai1wm_Backups::get_labels(),
				'downloadable' => Ai1wm_Backups::are_downloadable(),
			)
		);
	}

	public static function delete( $params = array() ) {
		ai1wm_setup_environment();

		// Set params
		if ( empty( $params ) ) {
			$params = stripslashes_deep( $_POST );
		}

		// Set secret key
		$secret_key = null;
		if ( isset( $params['secret_key'] ) ) {
			$secret_key = trim( $params['secret_key'] );
		}

		// Set archive
		$archive = null;
		if ( isset( $params['archive'] ) ) {
			$archive = trim( $params['archive'] );
		}

		try {
			// Ensure that unauthorized people cannot access delete action
			ai1wm_verify_secret_key( $secret_key );
		} catch ( Ai1wm_Not_Valid_Secret_Key_Exception $e ) {
			exit;
		}

		try {
			Ai1wm_Backups::delete_file( $archive );
			Ai1wm_Backups::delete_label( $archive );
		} catch ( Ai1wm_Backups_Exception $e ) {
			ai1wm_json_response( array( 'errors' => array( $e->getMessage() ) ) );
			exit;
		}

		ai1wm_json_response( array( 'errors' => array() ) );
		exit;
	}

	public static function add_label( $params = array() ) {
		ai1wm_setup_environment();

		// Set params
		if ( empty( $params ) ) {
			$params = stripslashes_deep( $_POST );
		}

		// Set secret key
		$secret_key = null;
		if ( isset( $params['secret_key'] ) ) {
			$secret_key = trim( $params['secret_key'] );
		}

		// Set archive
		$archive = null;
		if ( isset( $params['archive'] ) ) {
			$archive = trim( $params['archive'] );
		}

		// Set backup label
		$label = null;
		if ( isset( $params['label'] ) ) {
			$label = trim( $params['label'] );
		}

		try {
			// Ensure that unauthorized people cannot access add label action
			ai1wm_verify_secret_key( $secret_key );
		} catch ( Ai1wm_Not_Valid_Secret_Key_Exception $e ) {
			exit;
		}

		try {
			Ai1wm_Backups::set_label( $archive, $label );
		} catch ( Ai1wm_Backups_Exception $e ) {
			ai1wm_json_response( array( 'errors' => array( $e->getMessage() ) ) );
			exit;
		}

		ai1wm_json_response( array( 'errors' => array() ) );
		exit;
	}

	public static function backup_list( $params = array() ) {
		ai1wm_setup_environment();

		// Set params
		if ( empty( $params ) ) {
			$params = stripslashes_deep( $_GET );
		}

		// Set secret key
		$secret_key = null;
		if ( isset( $params['secret_key'] ) ) {
			$secret_key = trim( $params['secret_key'] );
		}

		try {
			// Ensure that unauthorized people cannot access backups list action
			ai1wm_verify_secret_key( $secret_key );
		} catch ( Ai1wm_Not_Valid_Secret_Key_Exception $e ) {
			exit;
		}

		Ai1wm_Template::render(
			'backups/backups-list',
			array(
				'backups'      => Ai1wm_Backups::get_files(),
				'labels'       => Ai1wm_Backups::get_labels(),
				'downloadable' => Ai1wm_Backups::are_downloadable(),
			)
		);
		exit;
	}

	public static function backup_list_content( $params = array() ) {
		ai1wm_setup_environment();

		// Set params
		if ( empty( $params ) ) {
			$params = stripslashes_deep( $_POST );
		}

		// Set secret key
		$secret_key = null;
		if ( isset( $params['secret_key'] ) ) {
			$secret_key = trim( $params['secret_key'] );
		}

		try {
			// Ensure that unauthorized people cannot access backups list action
			ai1wm_verify_secret_key( $secret_key );
		} catch ( Ai1wm_Not_Valid_Secret_Key_Exception $e ) {
			exit;
		}

		try {
			$archive = new Ai1wm_Extractor( ai1wm_backup_path( $params ) );
			ai1wm_json_response( $archive->list_files() );
		} catch ( Exception $e ) {
			ai1wm_json_response(
				array(
					'error' => __( 'Unable to list backup content', AI1WM_PLUGIN_NAME ),
				)
			);
		}

		exit;
	}

	public static function download_file( $params = array() ) {
		ai1wm_setup_environment();

		// Set params
		if ( empty( $params ) ) {
			$params = stripslashes_deep( $_POST );
		}

		// Set secret key
		$secret_key = null;
		if ( isset( $params['secret_key'] ) ) {
			$secret_key = trim( $params['secret_key'] );
		}

		try {
			// Ensure that unauthorized people cannot access backups list action
			ai1wm_verify_secret_key( $secret_key );
		} catch ( Ai1wm_Not_Valid_Secret_Key_Exception $e ) {
			exit;
		}

		$chunk_size = 1024 * 1024;
		$file_bytes = 0;

		try {
			if ( $handle  = ai1wm_open( ai1wm_backup_path( $params ), 'rb' ) ) {
				if ( ! isset( $params['file_size'] ) ) {
					$params['file_size'] = filesize( ai1wm_backup_path( $params ) );
				}

				if ( ! isset( $params['offset'] ) ) {
					$params['offset'] = 0;
				}

				ai1wm_seek( $handle, $params['offset'] );
				while ( ! feof( $handle ) && $file_bytes < $params['file_size'] ) {
					$buffer = ai1wm_read( $handle, min( $chunk_size, $params['file_size'] - $file_bytes ) );
					echo $buffer;
					ob_flush();
					flush();
					$file_bytes += strlen( $buffer );
				}

				ai1wm_close( $handle );
			}
		} catch ( Exception $exception ) {
		}

		exit;
	}
}
