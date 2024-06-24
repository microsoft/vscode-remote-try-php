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

class Ai1wm_Status {

	public static function error( $title, $message ) {
		self::log( array( 'type' => 'error', 'title' => $title, 'message' => $message ) );
	}

	public static function info( $message ) {
		self::log( array( 'type' => 'info', 'message' => $message ) );
	}

	public static function download( $message ) {
		self::log( array( 'type' => 'download', 'message' => $message ) );
	}

	public static function disk_space_confirm( $message ) {
		self::log( array( 'type' => 'disk_space_confirm', 'message' => $message ) );
	}

	public static function confirm( $message ) {
		self::log( array( 'type' => 'confirm', 'message' => $message ) );
	}

	public static function done( $title, $message = null ) {
		self::log( array( 'type' => 'done', 'title' => $title, 'message' => $message ) );
	}

	public static function blogs( $title, $message ) {
		self::log( array( 'type' => 'blogs', 'title' => $title, 'message' => $message ) );
	}

	public static function progress( $percent ) {
		self::log( array( 'type' => 'progress', 'percent' => $percent ) );
	}

	public static function backup_is_encrypted( $error ) {
		self::log( array( 'type' => 'backup_is_encrypted', 'error' => $error ) );
	}

	public static function server_cannot_decrypt( $message ) {
		self::log( array( 'type' => 'server_cannot_decrypt', 'message' => $message ) );
	}

	public static function log( $data ) {
		if ( ! ai1wm_is_scheduled_backup() ) {
			update_option( AI1WM_STATUS, $data );
		}
	}
}
