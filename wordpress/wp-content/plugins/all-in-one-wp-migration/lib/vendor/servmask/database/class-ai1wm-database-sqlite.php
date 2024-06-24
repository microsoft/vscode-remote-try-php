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

class Ai1wm_Database_Sqlite extends Ai1wm_Database {

	/**
	 * Get views
	 *
	 * @return array
	 */
	protected function get_views() {
		if ( is_null( $this->views ) ) {
			$where_query = array();

			// Loop over table prefixes
			if ( $this->get_table_prefix_filters() ) {
				foreach ( $this->get_table_prefix_filters() as $prefix_filter ) {
					if ( isset( $prefix_filter[0], $prefix_filter[1] ) ) {
						$where_query[] = sprintf( "(`name` REGEXP '^%s' AND `name` NOT REGEXP '^%s')", $prefix_filter[0], $prefix_filter[1] );
					} else {
						$where_query[] = sprintf( "`name` REGEXP '^%s'", $prefix_filter[0] );
					}
				}
			} else {
				$where_query[] = 1;
			}

			$this->views = array();

			// Loop over views
			$result = $this->query( sprintf( "SELECT `name` FROM `sqlite_master` WHERE `type`='view' AND (%s)", implode( ' OR ', $where_query ) ) );
			while ( $row = $this->fetch_row( $result ) ) {
				if ( isset( $row[0] ) ) {
					$this->views[] = $row[0];
				}
			}

			$this->free_result( $result );
		}

		return $this->views;
	}

	/**
	 * Get base tables
	 *
	 * @return array
	 */
	protected function get_base_tables() {
		if ( is_null( $this->base_tables ) ) {
			$where_query = array();

			// Loop over table prefixes
			if ( $this->get_table_prefix_filters() ) {
				foreach ( $this->get_table_prefix_filters() as $prefix_filter ) {
					if ( isset( $prefix_filter[0], $prefix_filter[1] ) ) {
						$where_query[] = sprintf( "(`name` REGEXP '^%s' AND `name` NOT REGEXP '^%s')", $prefix_filter[0], $prefix_filter[1] );
					} else {
						$where_query[] = sprintf( "`name` REGEXP '^%s'", $prefix_filter[0] );
					}
				}
			} else {
				$where_query[] = 1;
			}

			$this->base_tables = array();

			// Loop over base tables
			$result = $this->query( sprintf( "SELECT `name` FROM `sqlite_master` WHERE `type`='table' AND (%s)", implode( ' OR ', $where_query ) ) );
			while ( $row = $this->fetch_row( $result ) ) {
				if ( isset( $row[0] ) ) {
					$this->base_tables[] = $row[0];
				}
			}

			$this->free_result( $result );
		}

		return $this->base_tables;
	}

	/**
	 * Run SQLite query
	 *
	 * @param  string $input SQL query
	 * @return mixed
	 */
	public function query( $input ) {
		return $this->wpdb->dbh->query( $input );
	}

	/**
	 * Escape string input for SQLite query
	 *
	 * @param  string $input String to escape
	 * @return string
	 */
	public function escape( $input ) {
		return $this->wpdb->_real_escape( $input );
	}

	/**
	 * Return the error code for the most recent function call
	 *
	 * @return integer
	 */
	public function errno() {
		return 0;
	}

	/**
	 * Return a string description of the last error
	 *
	 * @return string
	 */
	public function error() {
	}

	/**
	 * Return server info
	 *
	 * @return string
	 */
	public function server_info() {
		return $this->wpdb->db_server_info();
	}

	/**
	 * Return result as associative array
	 *
	 * @param  mixed $result SQLite resource
	 * @return array
	 */
	public function fetch_assoc( &$result ) {
		if ( key( $result ) === null ) {
			return false;
		}

		$current = current( $result );
		next( $result );

		return get_object_vars( $current );
	}

	/**
	 * Return the result from SQLite query as row
	 *
	 * @param  mixed $result SQLite resource
	 * @return array
	 */
	public function fetch_row( &$result ) {
		$current = $this->fetch_assoc( $result );
		if ( $current === false ) {
			return false;
		}

		return array_values( $current );
	}

	/**
	 * Return the number for rows from SQLite results
	 *
	 * @param  mixed $result SQLite resource
	 * @return integer
	 */
	public function num_rows( &$result ) {
		return count( $result );
	}

	/**
	 * Stub for free SQLite result memory
	 *
	 * @param  mixed $result SQLite resource
	 * @return void
	 */
	public function free_result( &$result ) {
		unset( $result );
	}

	/**
	 * Get SQLite create view
	 *
	 * @param  string $table_name View name
	 * @return string
	 */
	protected function get_create_view( $table_name ) {
		$result = $this->query( "SELECT `sql` FROM `sqlite_master` WHERE `name` = '{$table_name}'" );
		$row    = $this->fetch_assoc( $result );

		// Close result cursor
		$this->free_result( $result );

		// Get create table
		if ( isset( $row['sql'] ) ) {
			return $row['sql'];
		}
	}

	/**
	 * Get SQLite create table
	 *
	 * @param  string $table_name Table name
	 * @return string
	 */
	protected function get_create_table( $table_name ) {
		$result = $this->query( "SELECT `sql` FROM `sqlite_master` WHERE `name` = '{$table_name}'" );
		$row    = $this->fetch_assoc( $result );

		// Close result cursor
		$this->free_result( $result );

		// Get create table
		if ( isset( $row['sql'] ) ) {
			return $row['sql'];
		}
	}

	/**
	 * Replace table defaults
	 *
	 * @param  string $input SQL statement
	 * @return string
	 */
	protected function replace_table_defaults( $input ) {
		$pattern = array(
			'/DEFAULT(\s+)(\d+)/i',
			"/DEFAULT(\s+)'(.*?)'/i",
		);

		return preg_replace( $pattern, '', $input );
	}

	/**
	 * Replace view quotes
	 *
	 * @param  string $input View value
	 * @return string
	 */
	protected function replace_view_quotes( $input ) {
		return str_replace( '"', '`', $input );
	}

	/**
	 * Replace table quotes
	 *
	 * @param  string $input Table value
	 * @return string
	 */
	protected function replace_table_quotes( $input ) {
		return str_replace( '"', '`', $input );
	}

	/**
	 * Get SQLite primary keys
	 *
	 * @param  string $table_name Table name
	 * @return array
	 */
	protected function get_primary_keys( $table_name ) {
		$primary_keys = array();

		// Get primary keys
		$result = $this->query( "SELECT `table_info`.`name` FROM PRAGMA_TABLE_INFO('{$table_name}') AS table_info WHERE `table_info`.`pk` != 0" );
		while ( $row = $this->fetch_assoc( $result ) ) {
			if ( isset( $row['name'] ) ) {
				$primary_keys[] = $row['name'];
			}
		}

		// Close result cursor
		$this->free_result( $result );

		return $primary_keys;
	}

	/**
	 * Get SQLite column types
	 *
	 * @param  string $table_name Table name
	 * @return array
	 */
	protected function get_column_types( $table_name ) {
		$column_types = array();

		// Get column types
		$result = $this->query( "SELECT `name`, `type` FROM PRAGMA_TABLE_INFO('{$table_name}')" );
		while ( $row = $this->fetch_assoc( $result ) ) {
			if ( isset( $row['name'] ) ) {
				$column_types[ strtolower( $row['name'] ) ] = $row['type'];
			}
		}

		// Close result cursor
		$this->free_result( $result );

		return $column_types;
	}

	/**
	 * Get SQLite column names
	 *
	 * @param  string $table_name Table name
	 * @return array
	 */
	public function get_column_names( $table_name ) {
		$column_names = array();

		// Get column names
		$result = $this->query( "SELECT `name` FROM PRAGMA_TABLE_INFO('{$table_name}')" );
		while ( $row = $this->fetch_assoc( $result ) ) {
			if ( isset( $row['name'] ) ) {
				$column_names[ strtolower( $row['name'] ) ] = $row['name'];
			}
		}

		// Close result cursor
		$this->free_result( $result );

		return $column_names;
	}

	/**
	 * Get SQLite max allowed packet
	 *
	 * @return integer
	 */
	protected function get_max_allowed_packet() {
		return PHP_INT_MAX;
	}

	/**
	 * Use SQLite transactions
	 *
	 * @return bolean
	 */
	protected function use_transactions() {
		return false;
	}
}
