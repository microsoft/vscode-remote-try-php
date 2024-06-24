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

class Ai1wm_Cron {

	/**
	 * Schedules a hook which will be executed by the WordPress
	 * actions core on a specific interval
	 *
	 * @param  string  $hook       Event hook
	 * @param  string  $recurrence How often the event should reoccur
	 * @param  integer $timestamp  Preferred timestamp (when the event shall be run)
	 * @param  array   $args       Arguments to pass to the hook function(s)
	 * @return mixed
	 */
	public static function add( $hook, $recurrence, $timestamp, $args = array() ) {
		$schedules = wp_get_schedules();

		// Schedule event
		if ( isset( $schedules[ $recurrence ] ) && ( $current = $schedules[ $recurrence ] ) ) {
			if ( $timestamp <= ( $current_timestamp = time() ) ) {
				while ( $timestamp <= $current_timestamp ) {
					$timestamp += $current['interval'];
				}
			}

			return wp_schedule_event( $timestamp, $recurrence, $hook, $args );
		}
	}

	/**
	 * Un-schedules all previously-scheduled cron jobs using a particular
	 * hook name or a specific combination of hook name and arguments.
	 *
	 * @param  string  $hook Event hook
	 * @return boolean
	 */
	public static function clear( $hook ) {
		$cron = get_option( AI1WM_CRON, array() );
		if ( empty( $cron ) ) {
			return false;
		}

		foreach ( $cron as $timestamp => $hooks ) {
			if ( isset( $hooks[ $hook ] ) ) {
				unset( $cron[ $timestamp ][ $hook ] );

				// Unset empty timestamps
				if ( empty( $cron[ $timestamp ] ) ) {
					unset( $cron[ $timestamp ] );
				}
			}
		}

		return update_option( AI1WM_CRON, $cron );
	}

	/**
	 * Checks whether cronjob already exists
	 *
	 * @param  string $hook Event hook
	 * @param  array  $args Event callback arguments
	 * @return boolean
	 */
	public static function exists( $hook, $args = array() ) {
		$cron = get_option( AI1WM_CRON, array() );
		if ( empty( $cron ) ) {
			return false;
		}

		foreach ( $cron as $timestamp => $hooks ) {
			if ( empty( $args ) ) {
				if ( isset( $hooks[ $hook ] ) ) {
					return true;
				}
			} else {
				if ( isset( $hooks[ $hook ][ md5( serialize( $args ) ) ] ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Deletes cron event(s) if it exists
	 *
	 * @param  string $hook Event hook
	 * @param  array  $args Event callback arguments
	 * @return boolean
	 */
	public static function delete( $hook, $args = array() ) {
		$cron = get_option( AI1WM_CRON, array() );
		if ( empty( $cron ) ) {
			return false;
		}

		$key = md5( serialize( $args ) );
		foreach ( $cron as $timestamp => $hooks ) {
			if ( isset( $cron[ $timestamp ][ $hook ][ $key ] ) ) {
				unset( $cron[ $timestamp ][ $hook ][ $key ] );
			}
			if ( isset( $cron[ $timestamp ][ $hook ] ) && empty( $cron[ $timestamp ][ $hook ] ) ) {
				unset( $cron[ $timestamp ][ $hook ] );
			}
			if ( empty( $cron[ $timestamp ] ) ) {
				unset( $cron[ $timestamp ] );
			}
		}

		return update_option( AI1WM_CRON, $cron );
	}
}
