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

class Ai1wm_Import_Database {

	public static function execute( $params, Ai1wm_Database $db_client = null ) {
		global $wpdb;

		// Skip database import
		if ( ! is_file( ai1wm_database_path( $params ) ) ) {
			return $params;
		}

		// Set query offset
		if ( isset( $params['query_offset'] ) ) {
			$query_offset = (int) $params['query_offset'];
		} else {
			$query_offset = 0;
		}

		// Set total queries size
		if ( isset( $params['total_queries_size'] ) ) {
			$total_queries_size = (int) $params['total_queries_size'];
		} else {
			$total_queries_size = 1;
		}

		// Read blogs.json file
		$handle = ai1wm_open( ai1wm_blogs_path( $params ), 'r' );

		// Parse blogs.json file
		$blogs = ai1wm_read( $handle, filesize( ai1wm_blogs_path( $params ) ) );
		$blogs = json_decode( $blogs, true );

		// Close handle
		ai1wm_close( $handle );

		// Read package.json file
		$handle = ai1wm_open( ai1wm_package_path( $params ), 'r' );

		// Parse package.json file
		$config = ai1wm_read( $handle, filesize( ai1wm_package_path( $params ) ) );
		$config = json_decode( $config, true );

		// Close handle
		ai1wm_close( $handle );

		// What percent of queries have we processed?
		$progress = (int) ( ( $query_offset / $total_queries_size ) * 100 );

		// Set progress
		Ai1wm_Status::info( sprintf( __( 'Restoring database...<br />%d%% complete', AI1WM_PLUGIN_NAME ), $progress ) );

		$old_replace_values = $old_replace_raw_values = array();
		$new_replace_values = $new_replace_raw_values = array();

		// Get Blog URLs
		foreach ( $blogs as $blog ) {

			// Handle old and new sites dir style
			if ( defined( 'UPLOADBLOGSDIR' ) ) {

				// Get plain Files Path
				if ( ! in_array( ai1wm_blog_files_url( $blog['Old']['BlogID'] ), $old_replace_values ) ) {
					$old_replace_values[] = ai1wm_blog_files_url( $blog['Old']['BlogID'] );
					$new_replace_values[] = ai1wm_blog_files_url( $blog['New']['BlogID'] );
				}

				// Get URL encoded Files Path
				if ( ! in_array( urlencode( ai1wm_blog_files_url( $blog['Old']['BlogID'] ) ), $old_replace_values ) ) {
					$old_replace_values[] = urlencode( ai1wm_blog_files_url( $blog['Old']['BlogID'] ) );
					$new_replace_values[] = urlencode( ai1wm_blog_files_url( $blog['New']['BlogID'] ) );
				}

				// Get URL raw encoded Files Path
				if ( ! in_array( rawurlencode( ai1wm_blog_files_url( $blog['Old']['BlogID'] ) ), $old_replace_values ) ) {
					$old_replace_values[] = rawurlencode( ai1wm_blog_files_url( $blog['Old']['BlogID'] ) );
					$new_replace_values[] = rawurlencode( ai1wm_blog_files_url( $blog['New']['BlogID'] ) );
				}

				// Get JSON escaped Files Path
				if ( ! in_array( addcslashes( ai1wm_blog_files_url( $blog['Old']['BlogID'] ), '/' ), $old_replace_values ) ) {
					$old_replace_values[] = addcslashes( ai1wm_blog_files_url( $blog['Old']['BlogID'] ), '/' );
					$new_replace_values[] = addcslashes( ai1wm_blog_files_url( $blog['New']['BlogID'] ), '/' );
				}

				// Get plain Sites Path
				if ( ! in_array( ai1wm_blog_sites_url( $blog['Old']['BlogID'] ), $old_replace_values ) ) {
					$old_replace_values[] = ai1wm_blog_sites_url( $blog['Old']['BlogID'] );
					$new_replace_values[] = ai1wm_blog_files_url( $blog['New']['BlogID'] );
				}

				// Get URL encoded Sites Path
				if ( ! in_array( urlencode( ai1wm_blog_sites_url( $blog['Old']['BlogID'] ) ), $old_replace_values ) ) {
					$old_replace_values[] = urlencode( ai1wm_blog_sites_url( $blog['Old']['BlogID'] ) );
					$new_replace_values[] = urlencode( ai1wm_blog_files_url( $blog['New']['BlogID'] ) );
				}

				// Get URL raw encoded Sites Path
				if ( ! in_array( rawurlencode( ai1wm_blog_sites_url( $blog['Old']['BlogID'] ) ), $old_replace_values ) ) {
					$old_replace_values[] = rawurlencode( ai1wm_blog_sites_url( $blog['Old']['BlogID'] ) );
					$new_replace_values[] = rawurlencode( ai1wm_blog_files_url( $blog['New']['BlogID'] ) );
				}

				// Get JSON escaped Sites Path
				if ( ! in_array( addcslashes( ai1wm_blog_sites_url( $blog['Old']['BlogID'] ), '/' ), $old_replace_values ) ) {
					$old_replace_values[] = addcslashes( ai1wm_blog_sites_url( $blog['Old']['BlogID'] ), '/' );
					$new_replace_values[] = addcslashes( ai1wm_blog_files_url( $blog['New']['BlogID'] ), '/' );
				}
			} else {

				// Get plain Files Path
				if ( ! in_array( ai1wm_blog_files_url( $blog['Old']['BlogID'] ), $old_replace_values ) ) {
					$old_replace_values[] = ai1wm_blog_files_url( $blog['Old']['BlogID'] );
					$new_replace_values[] = ai1wm_blog_uploads_url( $blog['New']['BlogID'] );
				}

				// Get URL encoded Files Path
				if ( ! in_array( urlencode( ai1wm_blog_files_url( $blog['Old']['BlogID'] ) ), $old_replace_values ) ) {
					$old_replace_values[] = urlencode( ai1wm_blog_files_url( $blog['Old']['BlogID'] ) );
					$new_replace_values[] = urlencode( ai1wm_blog_uploads_url( $blog['New']['BlogID'] ) );
				}

				// Get URL raw encoded Files Path
				if ( ! in_array( rawurlencode( ai1wm_blog_files_url( $blog['Old']['BlogID'] ) ), $old_replace_values ) ) {
					$old_replace_values[] = rawurlencode( ai1wm_blog_files_url( $blog['Old']['BlogID'] ) );
					$new_replace_values[] = rawurlencode( ai1wm_blog_uploads_url( $blog['New']['BlogID'] ) );
				}

				// Get JSON escaped Files Path
				if ( ! in_array( addcslashes( ai1wm_blog_files_url( $blog['Old']['BlogID'] ), '/' ), $old_replace_values ) ) {
					$old_replace_values[] = addcslashes( ai1wm_blog_files_url( $blog['Old']['BlogID'] ), '/' );
					$new_replace_values[] = addcslashes( ai1wm_blog_uploads_url( $blog['New']['BlogID'] ), '/' );
				}

				// Get plain Sites Path
				if ( ! in_array( ai1wm_blog_sites_url( $blog['Old']['BlogID'] ), $old_replace_values ) ) {
					$old_replace_values[] = ai1wm_blog_sites_url( $blog['Old']['BlogID'] );
					$new_replace_values[] = ai1wm_blog_uploads_url( $blog['New']['BlogID'] );
				}

				// Get URL encoded Sites Path
				if ( ! in_array( urlencode( ai1wm_blog_sites_url( $blog['Old']['BlogID'] ) ), $old_replace_values ) ) {
					$old_replace_values[] = urlencode( ai1wm_blog_sites_url( $blog['Old']['BlogID'] ) );
					$new_replace_values[] = urlencode( ai1wm_blog_uploads_url( $blog['New']['BlogID'] ) );
				}

				// Get URL raw encoded Sites Path
				if ( ! in_array( rawurlencode( ai1wm_blog_sites_url( $blog['Old']['BlogID'] ) ), $old_replace_values ) ) {
					$old_replace_values[] = rawurlencode( ai1wm_blog_sites_url( $blog['Old']['BlogID'] ) );
					$new_replace_values[] = rawurlencode( ai1wm_blog_uploads_url( $blog['New']['BlogID'] ) );
				}

				// Get JSON escaped Sites Path
				if ( ! in_array( addcslashes( ai1wm_blog_sites_url( $blog['Old']['BlogID'] ), '/' ), $old_replace_values ) ) {
					$old_replace_values[] = addcslashes( ai1wm_blog_sites_url( $blog['Old']['BlogID'] ), '/' );
					$new_replace_values[] = addcslashes( ai1wm_blog_uploads_url( $blog['New']['BlogID'] ), '/' );
				}
			}

			$site_urls = array();

			// Add Site URL
			if ( ! empty( $blog['Old']['SiteURL'] ) ) {
				$site_urls[] = $blog['Old']['SiteURL'];
			}

			// Add Internal Site URL
			if ( ! empty( $blog['Old']['InternalSiteURL'] ) ) {
				if ( parse_url( $blog['Old']['InternalSiteURL'], PHP_URL_SCHEME ) && parse_url( $blog['Old']['InternalSiteURL'], PHP_URL_HOST ) ) {
					$site_urls[] = $blog['Old']['InternalSiteURL'];
				}
			}

			// Get Site URL
			foreach ( $site_urls as $site_url ) {

				// Get www URL
				if ( stripos( $site_url, '//www.' ) !== false ) {
					$site_url_www_inversion = str_ireplace( '//www.', '//', $site_url );
				} else {
					$site_url_www_inversion = str_ireplace( '//', '//www.', $site_url );
				}

				// Replace Site URL
				foreach ( array( $site_url, $site_url_www_inversion ) as $url ) {

					// Get domain
					$old_domain = parse_url( $url, PHP_URL_HOST );
					$new_domain = parse_url( $blog['New']['SiteURL'], PHP_URL_HOST );

					// Get path
					$old_path = (string) parse_url( $url, PHP_URL_PATH );
					$new_path = (string) parse_url( $blog['New']['SiteURL'], PHP_URL_PATH );

					// Get scheme
					$new_scheme = parse_url( $blog['New']['SiteURL'], PHP_URL_SCHEME );

					// Add domain and path
					if ( ! in_array( sprintf( "'%s','%s'", $old_domain, trailingslashit( $old_path ) ), $old_replace_raw_values ) ) {
						$old_replace_raw_values[] = sprintf( "'%s','%s'", $old_domain, trailingslashit( $old_path ) );
						$new_replace_raw_values[] = sprintf( "'%s','%s'", $new_domain, trailingslashit( $new_path ) );
					}

					// Add domain and path with single quote
					if ( ! in_array( sprintf( "='%s%s", $old_domain, untrailingslashit( $old_path ) ), $old_replace_values ) ) {
						$old_replace_values[] = sprintf( "='%s%s", $old_domain, untrailingslashit( $old_path ) );
						$new_replace_values[] = sprintf( "='%s%s", $new_domain, untrailingslashit( $new_path ) );
					}

					// Add domain and path with double quote
					if ( ! in_array( sprintf( '="%s%s', $old_domain, untrailingslashit( $old_path ) ), $old_replace_values ) ) {
						$old_replace_values[] = sprintf( '="%s%s', $old_domain, untrailingslashit( $old_path ) );
						$new_replace_values[] = sprintf( '="%s%s', $new_domain, untrailingslashit( $new_path ) );
					}

					// Add Site URL scheme
					$old_schemes = array( 'http', 'https', '' );
					$new_schemes = array( $new_scheme, $new_scheme, '' );

					// Replace Site URL scheme
					for ( $i = 0; $i < count( $old_schemes ); $i++ ) {

						// Handle old and new sites dir style
						if ( ! defined( 'UPLOADBLOGSDIR' ) ) {

							// Add plain Uploads URL
							if ( ! in_array( ai1wm_url_scheme( sprintf( '%s/files/', untrailingslashit( $url ) ), $old_schemes[ $i ] ), $old_replace_values ) ) {
								$old_replace_values[] = ai1wm_url_scheme( sprintf( '%s/files/', untrailingslashit( $url ) ), $old_schemes[ $i ] );
								$new_replace_values[] = ai1wm_url_scheme( $blog['New']['WordPress']['UploadsURL'], $new_schemes[ $i ] );
							}

							// Add URL encoded Uploads URL
							if ( ! in_array( urlencode( ai1wm_url_scheme( sprintf( '%s/files/', untrailingslashit( $url ) ), $old_schemes[ $i ] ) ), $old_replace_values ) ) {
								$old_replace_values[] = urlencode( ai1wm_url_scheme( sprintf( '%s/files/', untrailingslashit( $url ) ), $old_schemes[ $i ] ) );
								$new_replace_values[] = urlencode( ai1wm_url_scheme( $blog['New']['WordPress']['UploadsURL'], $new_schemes[ $i ] ) );
							}

							// Add URL raw encoded Uploads URL
							if ( ! in_array( rawurlencode( ai1wm_url_scheme( sprintf( '%s/files/', untrailingslashit( $url ) ), $old_schemes[ $i ] ) ), $old_replace_values ) ) {
								$old_replace_values[] = rawurlencode( ai1wm_url_scheme( sprintf( '%s/files/', untrailingslashit( $url ) ), $old_schemes[ $i ] ) );
								$new_replace_values[] = rawurlencode( ai1wm_url_scheme( $blog['New']['WordPress']['UploadsURL'], $new_schemes[ $i ] ) );
							}

							// Add JSON escaped Uploads URL
							if ( ! in_array( addcslashes( ai1wm_url_scheme( sprintf( '%s/files/', untrailingslashit( $url ) ), $old_schemes[ $i ] ), '/' ), $old_replace_values ) ) {
								$old_replace_values[] = addcslashes( ai1wm_url_scheme( sprintf( '%s/files/', untrailingslashit( $url ) ), $old_schemes[ $i ] ), '/' );
								$new_replace_values[] = addcslashes( ai1wm_url_scheme( $blog['New']['WordPress']['UploadsURL'], $new_schemes[ $i ] ), '/' );
							}
						}

						// Add plain Site URL
						if ( ! in_array( ai1wm_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ), $old_replace_values ) ) {
							$old_replace_values[] = ai1wm_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] );
							$new_replace_values[] = ai1wm_url_scheme( untrailingslashit( $blog['New']['SiteURL'] ), $new_schemes[ $i ] );
						}

						// Add URL encoded Site URL
						if ( ! in_array( urlencode( ai1wm_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ) ), $old_replace_values ) ) {
							$old_replace_values[] = urlencode( ai1wm_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ) );
							$new_replace_values[] = urlencode( ai1wm_url_scheme( untrailingslashit( $blog['New']['SiteURL'] ), $new_schemes[ $i ] ) );
						}

						// Add URL raw encoded Site URL
						if ( ! in_array( rawurlencode( ai1wm_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ) ), $old_replace_values ) ) {
							$old_replace_values[] = rawurlencode( ai1wm_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ) );
							$new_replace_values[] = rawurlencode( ai1wm_url_scheme( untrailingslashit( $blog['New']['SiteURL'] ), $new_schemes[ $i ] ) );
						}

						// Add JSON escaped Site URL
						if ( ! in_array( addcslashes( ai1wm_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ), '/' ), $old_replace_values ) ) {
							$old_replace_values[] = addcslashes( ai1wm_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ), '/' );
							$new_replace_values[] = addcslashes( ai1wm_url_scheme( untrailingslashit( $blog['New']['SiteURL'] ), $new_schemes[ $i ] ), '/' );
						}
					}

					// Add email
					if ( ! isset( $config['NoEmailReplace'] ) ) {
						if ( ! in_array( sprintf( '@%s', $old_domain ), $old_replace_values ) ) {
							$old_replace_values[] = sprintf( '@%s', $old_domain );
							$new_replace_values[] = str_ireplace( '@www.', '@', sprintf( '@%s', $new_domain ) );
						}
					}
				}
			}

			$home_urls = array();

			// Add Home URL
			if ( ! empty( $blog['Old']['HomeURL'] ) ) {
				$home_urls[] = $blog['Old']['HomeURL'];
			}

			// Add Internal Home URL
			if ( ! empty( $blog['Old']['InternalHomeURL'] ) ) {
				if ( parse_url( $blog['Old']['InternalHomeURL'], PHP_URL_SCHEME ) && parse_url( $blog['Old']['InternalHomeURL'], PHP_URL_HOST ) ) {
					$home_urls[] = $blog['Old']['InternalHomeURL'];
				}
			}

			// Get Home URL
			foreach ( $home_urls as $home_url ) {

				// Get www URL
				if ( stripos( $home_url, '//www.' ) !== false ) {
					$home_url_www_inversion = str_ireplace( '//www.', '//', $home_url );
				} else {
					$home_url_www_inversion = str_ireplace( '//', '//www.', $home_url );
				}

				// Replace Home URL
				foreach ( array( $home_url, $home_url_www_inversion ) as $url ) {

					// Get domain
					$old_domain = parse_url( $url, PHP_URL_HOST );
					$new_domain = parse_url( $blog['New']['HomeURL'], PHP_URL_HOST );

					// Get path
					$old_path = (string) parse_url( $url, PHP_URL_PATH );
					$new_path = (string) parse_url( $blog['New']['HomeURL'], PHP_URL_PATH );

					// Get scheme
					$new_scheme = parse_url( $blog['New']['HomeURL'], PHP_URL_SCHEME );

					// Add domain and path
					if ( ! in_array( sprintf( "'%s','%s'", $old_domain, trailingslashit( $old_path ) ), $old_replace_raw_values ) ) {
						$old_replace_raw_values[] = sprintf( "'%s','%s'", $old_domain, trailingslashit( $old_path ) );
						$new_replace_raw_values[] = sprintf( "'%s','%s'", $new_domain, trailingslashit( $new_path ) );
					}

					// Add domain and path with single quote
					if ( ! in_array( sprintf( "='%s%s", $old_domain, untrailingslashit( $old_path ) ), $old_replace_values ) ) {
						$old_replace_values[] = sprintf( "='%s%s", $old_domain, untrailingslashit( $old_path ) );
						$new_replace_values[] = sprintf( "='%s%s", $new_domain, untrailingslashit( $new_path ) );
					}

					// Add domain and path with double quote
					if ( ! in_array( sprintf( '="%s%s', $old_domain, untrailingslashit( $old_path ) ), $old_replace_values ) ) {
						$old_replace_values[] = sprintf( '="%s%s', $old_domain, untrailingslashit( $old_path ) );
						$new_replace_values[] = sprintf( '="%s%s', $new_domain, untrailingslashit( $new_path ) );
					}

					// Set Home URL scheme
					$old_schemes = array( 'http', 'https', '' );
					$new_schemes = array( $new_scheme, $new_scheme, '' );

					// Replace Home URL scheme
					for ( $i = 0; $i < count( $old_schemes ); $i++ ) {

						// Handle old and new sites dir style
						if ( ! defined( 'UPLOADBLOGSDIR' ) ) {

							// Add plain Uploads URL
							if ( ! in_array( ai1wm_url_scheme( sprintf( '%s/files/', untrailingslashit( $url ) ), $old_schemes[ $i ] ), $old_replace_values ) ) {
								$old_replace_values[] = ai1wm_url_scheme( sprintf( '%s/files/', untrailingslashit( $url ) ), $old_schemes[ $i ] );
								$new_replace_values[] = ai1wm_url_scheme( $blog['New']['WordPress']['UploadsURL'], $new_schemes[ $i ] );
							}

							// Add URL encoded Uploads URL
							if ( ! in_array( urlencode( ai1wm_url_scheme( sprintf( '%s/files/', untrailingslashit( $url ) ), $old_schemes[ $i ] ) ), $old_replace_values ) ) {
								$old_replace_values[] = urlencode( ai1wm_url_scheme( sprintf( '%s/files/', untrailingslashit( $url ) ), $old_schemes[ $i ] ) );
								$new_replace_values[] = urlencode( ai1wm_url_scheme( $blog['New']['WordPress']['UploadsURL'], $new_schemes[ $i ] ) );
							}

							// Add URL raw encoded Uploads URL
							if ( ! in_array( rawurlencode( ai1wm_url_scheme( sprintf( '%s/files/', untrailingslashit( $url ) ), $old_schemes[ $i ] ) ), $old_replace_values ) ) {
								$old_replace_values[] = rawurlencode( ai1wm_url_scheme( sprintf( '%s/files/', untrailingslashit( $url ) ), $old_schemes[ $i ] ) );
								$new_replace_values[] = rawurlencode( ai1wm_url_scheme( $blog['New']['WordPress']['UploadsURL'], $new_schemes[ $i ] ) );
							}

							// Add JSON escaped Uploads URL
							if ( ! in_array( addcslashes( ai1wm_url_scheme( sprintf( '%s/files/', untrailingslashit( $url ) ), $old_schemes[ $i ] ), '/' ), $old_replace_values ) ) {
								$old_replace_values[] = addcslashes( ai1wm_url_scheme( sprintf( '%s/files/', untrailingslashit( $url ) ), $old_schemes[ $i ] ), '/' );
								$new_replace_values[] = addcslashes( ai1wm_url_scheme( $blog['New']['WordPress']['UploadsURL'], $new_schemes[ $i ] ), '/' );
							}
						}

						// Add plain Home URL
						if ( ! in_array( ai1wm_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ), $old_replace_values ) ) {
							$old_replace_values[] = ai1wm_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] );
							$new_replace_values[] = ai1wm_url_scheme( untrailingslashit( $blog['New']['HomeURL'] ), $new_schemes[ $i ] );
						}

						// Add URL encoded Home URL
						if ( ! in_array( urlencode( ai1wm_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ) ), $old_replace_values ) ) {
							$old_replace_values[] = urlencode( ai1wm_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ) );
							$new_replace_values[] = urlencode( ai1wm_url_scheme( untrailingslashit( $blog['New']['HomeURL'] ), $new_schemes[ $i ] ) );
						}

						// Add URL raw encoded Home URL
						if ( ! in_array( rawurlencode( ai1wm_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ) ), $old_replace_values ) ) {
							$old_replace_values[] = rawurlencode( ai1wm_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ) );
							$new_replace_values[] = rawurlencode( ai1wm_url_scheme( untrailingslashit( $blog['New']['HomeURL'] ), $new_schemes[ $i ] ) );
						}

						// Add JSON escaped Home URL
						if ( ! in_array( addcslashes( ai1wm_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ), '/' ), $old_replace_values ) ) {
							$old_replace_values[] = addcslashes( ai1wm_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ), '/' );
							$new_replace_values[] = addcslashes( ai1wm_url_scheme( untrailingslashit( $blog['New']['HomeURL'] ), $new_schemes[ $i ] ), '/' );
						}
					}

					// Add email
					if ( ! isset( $config['NoEmailReplace'] ) ) {
						if ( ! in_array( sprintf( '@%s', $old_domain ), $old_replace_values ) ) {
							$old_replace_values[] = sprintf( '@%s', $old_domain );
							$new_replace_values[] = str_ireplace( '@www.', '@', sprintf( '@%s', $new_domain ) );
						}
					}
				}
			}

			$uploads_urls = array();

			// Add Uploads URL
			if ( ! empty( $blog['Old']['WordPress']['UploadsURL'] ) ) {
				$uploads_urls[] = $blog['Old']['WordPress']['UploadsURL'];
			}

			// Get Uploads URL
			foreach ( $uploads_urls as $uploads_url ) {

				// Get www URL
				if ( stripos( $uploads_url, '//www.' ) !== false ) {
					$uploads_url_www_inversion = str_ireplace( '//www.', '//', $uploads_url );
				} else {
					$uploads_url_www_inversion = str_ireplace( '//', '//www.', $uploads_url );
				}

				// Replace Uploads URL
				foreach ( array( $uploads_url, $uploads_url_www_inversion ) as $url ) {

					// Get path
					$old_path = (string) parse_url( $url, PHP_URL_PATH );
					$new_path = (string) parse_url( $blog['New']['WordPress']['UploadsURL'], PHP_URL_PATH );

					// Get scheme
					$new_scheme = parse_url( $blog['New']['WordPress']['UploadsURL'], PHP_URL_SCHEME );

					// Replace Uploads URL Path
					if ( basename( $old_path ) ) {

						// Add path with single quote
						if ( ! in_array( sprintf( "='%s", trailingslashit( $old_path ) ), $old_replace_values ) ) {
							$old_replace_values[] = sprintf( "='%s", trailingslashit( $old_path ) );
							$new_replace_values[] = sprintf( "='%s", trailingslashit( $new_path ) );
						}

						// Add path with double quote
						if ( ! in_array( sprintf( '="%s', trailingslashit( $old_path ) ), $old_replace_values ) ) {
							$old_replace_values[] = sprintf( '="%s', trailingslashit( $old_path ) );
							$new_replace_values[] = sprintf( '="%s', trailingslashit( $new_path ) );
						}
					}

					// Set Uploads URL scheme
					$old_schemes = array( 'http', 'https', '' );
					$new_schemes = array( $new_scheme, $new_scheme, '' );

					// Replace Uploads URL scheme
					for ( $i = 0; $i < count( $old_schemes ); $i++ ) {

						// Add plain Uploads URL
						if ( ! in_array( ai1wm_url_scheme( $url, $old_schemes[ $i ] ), $old_replace_values ) ) {
							$old_replace_values[] = ai1wm_url_scheme( $url, $old_schemes[ $i ] );
							$new_replace_values[] = ai1wm_url_scheme( $blog['New']['WordPress']['UploadsURL'], $new_schemes[ $i ] );
						}

						// Add URL encoded Uploads URL
						if ( ! in_array( urlencode( ai1wm_url_scheme( $url, $old_schemes[ $i ] ) ), $old_replace_values ) ) {
							$old_replace_values[] = urlencode( ai1wm_url_scheme( $url, $old_schemes[ $i ] ) );
							$new_replace_values[] = urlencode( ai1wm_url_scheme( $blog['New']['WordPress']['UploadsURL'], $new_schemes[ $i ] ) );
						}

						// Add URL raw encoded Uploads URL
						if ( ! in_array( rawurlencode( ai1wm_url_scheme( $url, $old_schemes[ $i ] ) ), $old_replace_values ) ) {
							$old_replace_values[] = rawurlencode( ai1wm_url_scheme( $url, $old_schemes[ $i ] ) );
							$new_replace_values[] = rawurlencode( ai1wm_url_scheme( $blog['New']['WordPress']['UploadsURL'], $new_schemes[ $i ] ) );
						}

						// Add JSON escaped Uploads URL
						if ( ! in_array( addcslashes( ai1wm_url_scheme( $url, $old_schemes[ $i ] ), '/' ), $old_replace_values ) ) {
							$old_replace_values[] = addcslashes( ai1wm_url_scheme( $url, $old_schemes[ $i ] ), '/' );
							$new_replace_values[] = addcslashes( ai1wm_url_scheme( $blog['New']['WordPress']['UploadsURL'], $new_schemes[ $i ] ), '/' );
						}
					}
				}
			}
		}

		// Get plain Sites Path
		if ( ! in_array( ai1wm_blog_sites_url(), $old_replace_values ) ) {
			$old_replace_values[] = ai1wm_blog_sites_url();
			$new_replace_values[] = ai1wm_blog_uploads_url();
		}

		// Get URL encoded Sites Path
		if ( ! in_array( urlencode( ai1wm_blog_sites_url() ), $old_replace_values ) ) {
			$old_replace_values[] = urlencode( ai1wm_blog_sites_url() );
			$new_replace_values[] = urlencode( ai1wm_blog_uploads_url() );
		}

		// Get URL raw encoded Sites Path
		if ( ! in_array( rawurlencode( ai1wm_blog_sites_url() ), $old_replace_values ) ) {
			$old_replace_values[] = rawurlencode( ai1wm_blog_sites_url() );
			$new_replace_values[] = rawurlencode( ai1wm_blog_uploads_url() );
		}

		// Get JSON escaped Sites Path
		if ( ! in_array( addcslashes( ai1wm_blog_sites_url(), '/' ), $old_replace_values ) ) {
			$old_replace_values[] = addcslashes( ai1wm_blog_sites_url(), '/' );
			$new_replace_values[] = addcslashes( ai1wm_blog_uploads_url(), '/' );
		}

		$site_urls = array();

		// Add Site URL
		if ( ! empty( $config['SiteURL'] ) ) {
			$site_urls[] = $config['SiteURL'];
		}

		// Add Internal Site URL
		if ( ! empty( $config['InternalSiteURL'] ) ) {
			if ( parse_url( $config['InternalSiteURL'], PHP_URL_SCHEME ) && parse_url( $config['InternalSiteURL'], PHP_URL_HOST ) ) {
				$site_urls[] = $config['InternalSiteURL'];
			}
		}

		// Get Site URL
		foreach ( $site_urls as $site_url ) {

			// Get www URL
			if ( stripos( $site_url, '//www.' ) !== false ) {
				$site_url_www_inversion = str_ireplace( '//www.', '//', $site_url );
			} else {
				$site_url_www_inversion = str_ireplace( '//', '//www.', $site_url );
			}

			// Replace Site URL
			foreach ( array( $site_url, $site_url_www_inversion ) as $url ) {

				// Get domain
				$old_domain = parse_url( $url, PHP_URL_HOST );
				$new_domain = parse_url( site_url(), PHP_URL_HOST );

				// Get path
				$old_path = (string) parse_url( $url, PHP_URL_PATH );
				$new_path = (string) parse_url( site_url(), PHP_URL_PATH );

				// Get scheme
				$new_scheme = parse_url( site_url(), PHP_URL_SCHEME );

				// Add domain and path
				if ( ! in_array( sprintf( "'%s','%s'", $old_domain, trailingslashit( $old_path ) ), $old_replace_raw_values ) ) {
					$old_replace_raw_values[] = sprintf( "'%s','%s'", $old_domain, trailingslashit( $old_path ) );
					$new_replace_raw_values[] = sprintf( "'%s','%s'", $new_domain, trailingslashit( $new_path ) );
				}

				// Add domain and path with single quote
				if ( ! in_array( sprintf( "='%s%s", $old_domain, untrailingslashit( $old_path ) ), $old_replace_values ) ) {
					$old_replace_values[] = sprintf( "='%s%s", $old_domain, untrailingslashit( $old_path ) );
					$new_replace_values[] = sprintf( "='%s%s", $new_domain, untrailingslashit( $new_path ) );
				}

				// Add domain and path with double quote
				if ( ! in_array( sprintf( '="%s%s', $old_domain, untrailingslashit( $old_path ) ), $old_replace_values ) ) {
					$old_replace_values[] = sprintf( '="%s%s', $old_domain, untrailingslashit( $old_path ) );
					$new_replace_values[] = sprintf( '="%s%s', $new_domain, untrailingslashit( $new_path ) );
				}

				// Set Site URL scheme
				$old_schemes = array( 'http', 'https', '' );
				$new_schemes = array( $new_scheme, $new_scheme, '' );

				// Replace Site URL scheme
				for ( $i = 0; $i < count( $old_schemes ); $i++ ) {

					// Add plain Site URL
					if ( ! in_array( ai1wm_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ), $old_replace_values ) ) {
						$old_replace_values[] = ai1wm_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] );
						$new_replace_values[] = ai1wm_url_scheme( untrailingslashit( site_url() ), $new_schemes[ $i ] );
					}

					// Add URL encoded Site URL
					if ( ! in_array( urlencode( ai1wm_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ) ), $old_replace_values ) ) {
						$old_replace_values[] = urlencode( ai1wm_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ) );
						$new_replace_values[] = urlencode( ai1wm_url_scheme( untrailingslashit( site_url() ), $new_schemes[ $i ] ) );
					}

					// Add URL raw encoded Site URL
					if ( ! in_array( rawurlencode( ai1wm_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ) ), $old_replace_values ) ) {
						$old_replace_values[] = rawurlencode( ai1wm_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ) );
						$new_replace_values[] = rawurlencode( ai1wm_url_scheme( untrailingslashit( site_url() ), $new_schemes[ $i ] ) );
					}

					// Add JSON escaped Site URL
					if ( ! in_array( addcslashes( ai1wm_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ), '/' ), $old_replace_values ) ) {
						$old_replace_values[] = addcslashes( ai1wm_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ), '/' );
						$new_replace_values[] = addcslashes( ai1wm_url_scheme( untrailingslashit( site_url() ), $new_schemes[ $i ] ), '/' );
					}
				}

				// Add email
				if ( ! isset( $config['NoEmailReplace'] ) ) {
					if ( ! in_array( sprintf( '@%s', $old_domain ), $old_replace_values ) ) {
						$old_replace_values[] = sprintf( '@%s', $old_domain );
						$new_replace_values[] = str_ireplace( '@www.', '@', sprintf( '@%s', $new_domain ) );
					}
				}
			}
		}

		$home_urls = array();

		// Add Home URL
		if ( ! empty( $config['HomeURL'] ) ) {
			$home_urls[] = $config['HomeURL'];
		}

		// Add Internal Home URL
		if ( ! empty( $config['InternalHomeURL'] ) ) {
			if ( parse_url( $config['InternalHomeURL'], PHP_URL_SCHEME ) && parse_url( $config['InternalHomeURL'], PHP_URL_HOST ) ) {
				$home_urls[] = $config['InternalHomeURL'];
			}
		}

		// Get Home URL
		foreach ( $home_urls as $home_url ) {

			// Get www URL
			if ( stripos( $home_url, '//www.' ) !== false ) {
				$home_url_www_inversion = str_ireplace( '//www.', '//', $home_url );
			} else {
				$home_url_www_inversion = str_ireplace( '//', '//www.', $home_url );
			}

			// Replace Home URL
			foreach ( array( $home_url, $home_url_www_inversion ) as $url ) {

				// Get domain
				$old_domain = parse_url( $url, PHP_URL_HOST );
				$new_domain = parse_url( home_url(), PHP_URL_HOST );

				// Get path
				$old_path = (string) parse_url( $url, PHP_URL_PATH );
				$new_path = (string) parse_url( home_url(), PHP_URL_PATH );

				// Get scheme
				$new_scheme = parse_url( home_url(), PHP_URL_SCHEME );

				// Add domain and path
				if ( ! in_array( sprintf( "'%s','%s'", $old_domain, trailingslashit( $old_path ) ), $old_replace_raw_values ) ) {
					$old_replace_raw_values[] = sprintf( "'%s','%s'", $old_domain, trailingslashit( $old_path ) );
					$new_replace_raw_values[] = sprintf( "'%s','%s'", $new_domain, trailingslashit( $new_path ) );
				}

				// Add domain and path with single quote
				if ( ! in_array( sprintf( "='%s%s", $old_domain, untrailingslashit( $old_path ) ), $old_replace_values ) ) {
					$old_replace_values[] = sprintf( "='%s%s", $old_domain, untrailingslashit( $old_path ) );
					$new_replace_values[] = sprintf( "='%s%s", $new_domain, untrailingslashit( $new_path ) );
				}

				// Add domain and path with double quote
				if ( ! in_array( sprintf( '="%s%s', $old_domain, untrailingslashit( $old_path ) ), $old_replace_values ) ) {
					$old_replace_values[] = sprintf( '="%s%s', $old_domain, untrailingslashit( $old_path ) );
					$new_replace_values[] = sprintf( '="%s%s', $new_domain, untrailingslashit( $new_path ) );
				}

				// Add Home URL scheme
				$old_schemes = array( 'http', 'https', '' );
				$new_schemes = array( $new_scheme, $new_scheme, '' );

				// Replace Home URL scheme
				for ( $i = 0; $i < count( $old_schemes ); $i++ ) {

					// Add plain Home URL
					if ( ! in_array( ai1wm_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ), $old_replace_values ) ) {
						$old_replace_values[] = ai1wm_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] );
						$new_replace_values[] = ai1wm_url_scheme( untrailingslashit( home_url() ), $new_schemes[ $i ] );
					}

					// Add URL encoded Home URL
					if ( ! in_array( urlencode( ai1wm_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ) ), $old_replace_values ) ) {
						$old_replace_values[] = urlencode( ai1wm_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ) );
						$new_replace_values[] = urlencode( ai1wm_url_scheme( untrailingslashit( home_url() ), $new_schemes[ $i ] ) );
					}

					// Add URL raw encoded Home URL
					if ( ! in_array( rawurlencode( ai1wm_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ) ), $old_replace_values ) ) {
						$old_replace_values[] = rawurlencode( ai1wm_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ) );
						$new_replace_values[] = rawurlencode( ai1wm_url_scheme( untrailingslashit( home_url() ), $new_schemes[ $i ] ) );
					}

					// Add JSON escaped Home URL
					if ( ! in_array( addcslashes( ai1wm_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ), '/' ), $old_replace_values ) ) {
						$old_replace_values[] = addcslashes( ai1wm_url_scheme( untrailingslashit( $url ), $old_schemes[ $i ] ), '/' );
						$new_replace_values[] = addcslashes( ai1wm_url_scheme( untrailingslashit( home_url() ), $new_schemes[ $i ] ), '/' );
					}
				}

				// Add email
				if ( ! isset( $config['NoEmailReplace'] ) ) {
					if ( ! in_array( sprintf( '@%s', $old_domain ), $old_replace_values ) ) {
						$old_replace_values[] = sprintf( '@%s', $old_domain );
						$new_replace_values[] = str_ireplace( '@www.', '@', sprintf( '@%s', $new_domain ) );
					}
				}
			}
		}

		$uploads_urls = array();

		// Add Uploads URL
		if ( ! empty( $config['WordPress']['UploadsURL'] ) ) {
			$uploads_urls[] = $config['WordPress']['UploadsURL'];
		}

		// Get Uploads URL
		foreach ( $uploads_urls as $uploads_url ) {

			// Get www URL
			if ( stripos( $uploads_url, '//www.' ) !== false ) {
				$uploads_url_www_inversion = str_ireplace( '//www.', '//', $uploads_url );
			} else {
				$uploads_url_www_inversion = str_ireplace( '//', '//www.', $uploads_url );
			}

			// Replace Uploads URL
			foreach ( array( $uploads_url, $uploads_url_www_inversion ) as $url ) {

				// Get path
				$old_path = (string) parse_url( $url, PHP_URL_PATH );
				$new_path = (string) parse_url( ai1wm_get_uploads_url(), PHP_URL_PATH );

				// Get scheme
				$new_scheme = parse_url( ai1wm_get_uploads_url(), PHP_URL_SCHEME );

				// Replace Uploads URL Path
				if ( basename( $old_path ) ) {

					// Add path with single quote
					if ( ! in_array( sprintf( "='%s", trailingslashit( $old_path ) ), $old_replace_values ) ) {
						$old_replace_values[] = sprintf( "='%s", trailingslashit( $old_path ) );
						$new_replace_values[] = sprintf( "='%s", trailingslashit( $new_path ) );
					}

					// Add path with double quote
					if ( ! in_array( sprintf( '="%s', trailingslashit( $old_path ) ), $old_replace_values ) ) {
						$old_replace_values[] = sprintf( '="%s', trailingslashit( $old_path ) );
						$new_replace_values[] = sprintf( '="%s', trailingslashit( $new_path ) );
					}
				}

				// Add Uploads URL scheme
				$old_schemes = array( 'http', 'https', '' );
				$new_schemes = array( $new_scheme, $new_scheme, '' );

				// Replace Uploads URL scheme
				for ( $i = 0; $i < count( $old_schemes ); $i++ ) {

					// Add plain Uploads URL
					if ( ! in_array( ai1wm_url_scheme( $url, $old_schemes[ $i ] ), $old_replace_values ) ) {
						$old_replace_values[] = ai1wm_url_scheme( $url, $old_schemes[ $i ] );
						$new_replace_values[] = ai1wm_url_scheme( ai1wm_get_uploads_url(), $new_schemes[ $i ] );
					}

					// Add URL encoded Uploads URL
					if ( ! in_array( urlencode( ai1wm_url_scheme( $url, $old_schemes[ $i ] ) ), $old_replace_values ) ) {
						$old_replace_values[] = urlencode( ai1wm_url_scheme( $url, $old_schemes[ $i ] ) );
						$new_replace_values[] = urlencode( ai1wm_url_scheme( ai1wm_get_uploads_url(), $new_schemes[ $i ] ) );
					}

					// Add URL raw encoded Uploads URL
					if ( ! in_array( rawurlencode( ai1wm_url_scheme( $url, $old_schemes[ $i ] ) ), $old_replace_values ) ) {
						$old_replace_values[] = rawurlencode( ai1wm_url_scheme( $url, $old_schemes[ $i ] ) );
						$new_replace_values[] = rawurlencode( ai1wm_url_scheme( ai1wm_get_uploads_url(), $new_schemes[ $i ] ) );
					}

					// Add JSON escaped Uploads URL
					if ( ! in_array( addcslashes( ai1wm_url_scheme( $url, $old_schemes[ $i ] ), '/' ), $old_replace_values ) ) {
						$old_replace_values[] = addcslashes( ai1wm_url_scheme( $url, $old_schemes[ $i ] ), '/' );
						$new_replace_values[] = addcslashes( ai1wm_url_scheme( ai1wm_get_uploads_url(), $new_schemes[ $i ] ), '/' );
					}
				}
			}
		}

		// Get WordPress Content Dir
		if ( isset( $config['WordPress']['Content'] ) && ( $content_dir = $config['WordPress']['Content'] ) ) {

			// Add plain WordPress Content
			if ( ! in_array( $content_dir, $old_replace_values ) ) {
				$old_replace_values[] = $content_dir;
				$new_replace_values[] = WP_CONTENT_DIR;
			}

			// Add URL encoded WordPress Content
			if ( ! in_array( urlencode( $content_dir ), $old_replace_values ) ) {
				$old_replace_values[] = urlencode( $content_dir );
				$new_replace_values[] = urlencode( WP_CONTENT_DIR );
			}

			// Add URL raw encoded WordPress Content
			if ( ! in_array( rawurlencode( $content_dir ), $old_replace_values ) ) {
				$old_replace_values[] = rawurlencode( $content_dir );
				$new_replace_values[] = rawurlencode( WP_CONTENT_DIR );
			}

			// Add JSON escaped WordPress Content
			if ( ! in_array( addcslashes( $content_dir, '/' ), $old_replace_values ) ) {
				$old_replace_values[] = addcslashes( $content_dir, '/' );
				$new_replace_values[] = addcslashes( WP_CONTENT_DIR, '/' );
			}
		}

		// Get replace old and new values
		if ( isset( $config['Replace'] ) && ( $replace = $config['Replace'] ) ) {
			for ( $i = 0; $i < count( $replace['OldValues'] ); $i++ ) {
				if ( ! empty( $replace['OldValues'][ $i ] ) && ! empty( $replace['NewValues'][ $i ] ) ) {

					// Add plain replace values
					if ( ! in_array( $replace['OldValues'][ $i ], $old_replace_values ) ) {
						$old_replace_values[] = $replace['OldValues'][ $i ];
						$new_replace_values[] = $replace['NewValues'][ $i ];
					}

					// Add URL encoded replace values
					if ( ! in_array( urlencode( $replace['OldValues'][ $i ] ), $old_replace_values ) ) {
						$old_replace_values[] = urlencode( $replace['OldValues'][ $i ] );
						$new_replace_values[] = urlencode( $replace['NewValues'][ $i ] );
					}

					// Add URL raw encoded replace values
					if ( ! in_array( rawurlencode( $replace['OldValues'][ $i ] ), $old_replace_values ) ) {
						$old_replace_values[] = rawurlencode( $replace['OldValues'][ $i ] );
						$new_replace_values[] = rawurlencode( $replace['NewValues'][ $i ] );
					}

					// Add JSON Escaped replace values
					if ( ! in_array( addcslashes( $replace['OldValues'][ $i ], '/' ), $old_replace_values ) ) {
						$old_replace_values[] = addcslashes( $replace['OldValues'][ $i ], '/' );
						$new_replace_values[] = addcslashes( $replace['NewValues'][ $i ], '/' );
					}
				}
			}
		}

		// Get site URL
		$site_url = get_option( AI1WM_SITE_URL );

		// Get home URL
		$home_url = get_option( AI1WM_HOME_URL );

		// Get secret key
		$secret_key = get_option( AI1WM_SECRET_KEY );

		// Get HTTP user
		$auth_user = get_option( AI1WM_AUTH_USER );

		// Get HTTP password
		$auth_password = get_option( AI1WM_AUTH_PASSWORD );

		// Get auth header
		$auth_header = get_option( AI1WM_AUTH_HEADER );

		// Get Uploads Path
		$uploads_path = get_option( AI1WM_UPLOADS_PATH );

		// Get Uploads URL Path
		$uploads_url_path = get_option( AI1WM_UPLOADS_URL_PATH );

		// Get backups labels
		$backups_labels = get_option( AI1WM_BACKUPS_LABELS, array() );

		// Get sites links
		$sites_links = get_option( AI1WM_SITES_LINKS, array() );

		$old_table_prefixes = array();
		$new_table_prefixes = array();

		// Set site table prefixes
		foreach ( $blogs as $blog ) {
			if ( ai1wm_is_mainsite( $blog['Old']['BlogID'] ) === false ) {
				$old_table_prefixes[] = ai1wm_servmask_prefix( $blog['Old']['BlogID'] );
				$new_table_prefixes[] = ai1wm_table_prefix( $blog['New']['BlogID'] );
			}
		}

		// Set global table prefixes
		foreach ( $wpdb->global_tables as $table_name ) {
			$old_table_prefixes[] = ai1wm_servmask_prefix( 'mainsite' ) . $table_name;
			$new_table_prefixes[] = ai1wm_table_prefix() . $table_name;
		}

		// Set BuddyPress table prefixes
		if ( ai1wm_validate_plugin_basename( 'buddyboss-platform/bp-loader.php' ) || ai1wm_validate_plugin_basename( 'buddypress/bp-loader.php' ) ) {
			foreach ( array( 'signups', 'bp_activity', 'bp_activity_meta', 'bp_friends', 'bp_groups', 'bp_groups_groupmeta', 'bp_groups_members', 'bp_invitations', 'bp_messages_messages', 'bp_messages_meta', 'bp_messages_notices', 'bp_messages_recipients', 'bp_notifications', 'bp_notifications_meta', 'bp_optouts', 'bp_user_blogs', 'bp_user_blogs_blogmeta', 'bp_xprofile_data', 'bp_xprofile_fields', 'bp_xprofile_groups', 'bp_xprofile_meta' ) as $table_name ) {
				$old_table_prefixes[] = ai1wm_servmask_prefix( 'mainsite' ) . $table_name;
				$new_table_prefixes[] = ai1wm_table_prefix() . $table_name;
			}
		}

		// Set base table prefixes
		foreach ( $blogs as $blog ) {
			if ( ai1wm_is_mainsite( $blog['Old']['BlogID'] ) === true ) {
				$old_table_prefixes[] = ai1wm_servmask_prefix( 'basesite' );
				$new_table_prefixes[] = ai1wm_table_prefix( $blog['New']['BlogID'] );
			}
		}

		// Set main table prefixes
		foreach ( $blogs as $blog ) {
			if ( ai1wm_is_mainsite( $blog['Old']['BlogID'] ) === true ) {
				$old_table_prefixes[] = ai1wm_servmask_prefix( $blog['Old']['BlogID'] );
				$new_table_prefixes[] = ai1wm_table_prefix( $blog['New']['BlogID'] );
			}
		}

		// Set table prefixes
		$old_table_prefixes[] = ai1wm_servmask_prefix();
		$new_table_prefixes[] = ai1wm_table_prefix();

		// Get database client
		if ( is_null( $db_client ) ) {
			$db_client = Ai1wm_Database_Utility::create_client();
		}

		// Set database options
		$db_client->set_old_table_prefixes( $old_table_prefixes )
			->set_new_table_prefixes( $new_table_prefixes )
			->set_old_replace_values( $old_replace_values )
			->set_new_replace_values( $new_replace_values )
			->set_old_replace_raw_values( $old_replace_raw_values )
			->set_new_replace_raw_values( $new_replace_raw_values );

		// Set atomic tables (do not stop current request for all listed tables if timeout has been exceeded)
		$db_client->set_atomic_tables( array( ai1wm_table_prefix() . 'options' ) );

		// Set empty tables (do not populate current data for all listed tables)
		$db_client->set_empty_tables( array( ai1wm_table_prefix() . 'eum_logs' ) );

		// Set Visual Composer
		$db_client->set_visual_composer( ai1wm_validate_plugin_basename( 'js_composer/js_composer.php' ) );

		// Set Oxygen Builder
		$db_client->set_oxygen_builder( ai1wm_validate_plugin_basename( 'oxygen/functions.php' ) );

		// Set Optimize Press
		$db_client->set_optimize_press( ai1wm_validate_plugin_basename( 'optimizePressPlugin/optimizepress.php' ) );

		// Set Avada Fusion Builder
		$db_client->set_avada_fusion_builder( ai1wm_validate_plugin_basename( 'fusion-builder/fusion-builder.php' ) );

		// Set BeTheme Responsive
		$db_client->set_betheme_responsive( ai1wm_validate_theme_basename( 'betheme/style.css' ) );

		// Import database
		if ( $db_client->import( ai1wm_database_path( $params ), $query_offset ) ) {

			// Set progress
			Ai1wm_Status::info( __( 'Done restoring database.', AI1WM_PLUGIN_NAME ) );

			// Unset query offset
			unset( $params['query_offset'] );

			// Unset total queries size
			unset( $params['total_queries_size'] );

			// Unset completed flag
			unset( $params['completed'] );

		} else {

			// Get total queries size
			$total_queries_size = ai1wm_database_bytes( $params );

			// What percent of queries have we processed?
			$progress = (int) ( ( $query_offset / $total_queries_size ) * 100 );

			// Set progress
			Ai1wm_Status::info( sprintf( __( 'Restoring database...<br />%d%% complete', AI1WM_PLUGIN_NAME ), $progress ) );

			// Set query offset
			$params['query_offset'] = $query_offset;

			// Set total queries size
			$params['total_queries_size'] = $total_queries_size;

			// Set completed flag
			$params['completed'] = false;
		}

		// Flush WP cache
		ai1wm_cache_flush();

		// Reset active plugins
		update_option( AI1WM_ACTIVE_PLUGINS, array() );

		// Activate plugins
		ai1wm_activate_plugins( ai1wm_active_servmask_plugins() );

		// Set the new site URL
		update_option( AI1WM_SITE_URL, $site_url );

		// Set the new home URL
		update_option( AI1WM_HOME_URL, $home_url );

		// Set the new secret key value
		update_option( AI1WM_SECRET_KEY, $secret_key );

		// Set the new HTTP user
		update_option( AI1WM_AUTH_USER, $auth_user );

		// Set the new HTTP password
		update_option( AI1WM_AUTH_PASSWORD, $auth_password );

		// Set the new auth header
		update_option( AI1WM_AUTH_HEADER, $auth_header );

		// Set the new Uploads Path
		update_option( AI1WM_UPLOADS_PATH, $uploads_path );

		// Set the new Uploads URL Path
		update_option( AI1WM_UPLOADS_URL_PATH, $uploads_url_path );

		// Set the new backups labels
		update_option( AI1WM_BACKUPS_LABELS, $backups_labels );

		// Set the new sites links
		update_option( AI1WM_SITES_LINKS, $sites_links );

		// Set new backups path
		update_option( AI1WM_BACKUPS_PATH_OPTION, AI1WM_BACKUPS_PATH );

		return $params;
	}
}
