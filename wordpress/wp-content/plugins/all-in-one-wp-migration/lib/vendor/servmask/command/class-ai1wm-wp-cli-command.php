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
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Kangaroos cannot jump here' );
}

if ( defined( 'WP_CLI' ) ) {
	class Ai1wm_WP_CLI_Command extends WP_CLI_Command {

		/**
		 * Creates a new backup.
		 *
		 * ## EXAMPLES
		 *
		 * $ wp ai1wm export
		 *
		 * @subcommand export
		 */
		public function export( $args = array(), $assoc_args = array() ) {
			$this->info();
		}

		/**
		 * Creates a new backup.
		 *
		 * ## EXAMPLES
		 *
		 * $ wp ai1wm backup
		 *
		 * @subcommand backup
		 */
		public function backup( $args = array(), $assoc_args = array() ) {
			$this->info();
		}

		/**
		 * Imports a backup.
		 *
		 * ## EXAMPLES
		 *
		 * $ wp ai1wm import
		 *
		 * @subcommand import
		 */
		public function import( $args = array(), $assoc_args = array() ) {
			$this->info();
		}

		/**
		 * Restores a backup.
		 *
		 * ## EXAMPLES
		 *
		 * $ wp ai1wm restore
		 *
		 * @subcommand restore
		 */
		public function restore( $args = array(), $assoc_args = array() ) {
			$this->info();
		}

		/**
		 * Resets site to default WordPress installation.
		 *
		 * ## EXAMPLES
		 *
		 * $ wp ai1wm reset
		 *
		 * @subcommand reset
		 */
		public function reset( $args = array(), $assoc_args = array() ) {
			$this->info();
		}

		protected function info() {
			if ( is_multisite() ) {
				WP_CLI::error_multi_line(
					array(
						__( 'This feature is available in Multisite Extension.', AI1WM_PLUGIN_NAME ),
						__( 'You can purchase it from this address: https://servmask.com/products/multisite-extension', AI1WM_PLUGIN_NAME ),
					)
				);
				exit;
			}

			WP_CLI::error_multi_line(
				array(
					__( 'This feature is available in Unlimited Extension.', AI1WM_PLUGIN_NAME ),
					__( 'You can purchase it from this address: https://servmask.com/products/unlimited-extension', AI1WM_PLUGIN_NAME ),
				)
			);
		}
	}
}
