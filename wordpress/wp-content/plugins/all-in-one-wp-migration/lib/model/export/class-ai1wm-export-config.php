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

class Ai1wm_Export_Config {

	public static function execute( $params ) {
		global $table_prefix, $wp_version;

		// Set progress
		Ai1wm_Status::info( __( 'Preparing configuration file...', AI1WM_PLUGIN_NAME ) );

		// Get options
		$options = wp_load_alloptions();

		// Get database client
		$db_client = Ai1wm_Database_Utility::create_client();

		$config = array();

		// Set site URL
		$config['SiteURL'] = site_url();

		// Set home URL
		$config['HomeURL'] = home_url();

		// Set internal site URL
		if ( isset( $options['siteurl'] ) ) {
			$config['InternalSiteURL'] = $options['siteurl'];
		}

		// Set internal home URL
		if ( isset( $options['home'] ) ) {
			$config['InternalHomeURL'] = $options['home'];
		}

		// Set replace old and new values
		if ( isset( $params['options']['replace'] ) && ( $replace = $params['options']['replace'] ) ) {
			for ( $i = 0; $i < count( $replace['old_value'] ); $i++ ) {
				if ( ! empty( $replace['old_value'][ $i ] ) && ! empty( $replace['new_value'][ $i ] ) ) {
					$config['Replace']['OldValues'][] = $replace['old_value'][ $i ];
					$config['Replace']['NewValues'][] = $replace['new_value'][ $i ];
				}
			}
		}

		// Set no spam comments
		if ( isset( $params['options']['no_spam_comments'] ) ) {
			$config['NoSpamComments'] = true;
		}

		// Set no post revisions
		if ( isset( $params['options']['no_post_revisions'] ) ) {
			$config['NoPostRevisions'] = true;
		}

		// Set no media
		if ( isset( $params['options']['no_media'] ) ) {
			$config['NoMedia'] = true;
		}

		// Set no themes
		if ( isset( $params['options']['no_themes'] ) ) {
			$config['NoThemes'] = true;
		}

		// Set no inactive themes
		if ( isset( $params['options']['no_inactive_themes'] ) ) {
			$config['NoInactiveThemes'] = true;
		}

		// Set no must-use plugins
		if ( isset( $params['options']['no_muplugins'] ) ) {
			$config['NoMustUsePlugins'] = true;
		}

		// Set no plugins
		if ( isset( $params['options']['no_plugins'] ) ) {
			$config['NoPlugins'] = true;
		}

		// Set no inactive plugins
		if ( isset( $params['options']['no_inactive_plugins'] ) ) {
			$config['NoInactivePlugins'] = true;
		}

		// Set no cache
		if ( isset( $params['options']['no_cache'] ) ) {
			$config['NoCache'] = true;
		}

		// Set no database
		if ( isset( $params['options']['no_database'] ) ) {
			$config['NoDatabase'] = true;
		}

		// Set no email replace
		if ( isset( $params['options']['no_email_replace'] ) ) {
			$config['NoEmailReplace'] = true;
		}

		// Set plugin version
		$config['Plugin'] = array( 'Version' => AI1WM_VERSION );

		// Set WordPress version and content
		$config['WordPress'] = array( 'Version' => $wp_version, 'Content' => WP_CONTENT_DIR, 'Plugins' => ai1wm_get_plugins_dir(), 'Themes' => ai1wm_get_themes_dirs(), 'Uploads' => ai1wm_get_uploads_dir(), 'UploadsURL' => ai1wm_get_uploads_url() );

		// Set database version
		$config['Database'] = array(
			'Version' => $db_client->server_info(),
			'Charset' => defined( 'DB_CHARSET' ) ? DB_CHARSET : 'undefined',
			'Collate' => defined( 'DB_COLLATE' ) ? DB_COLLATE : 'undefined',
			'Prefix'  => $table_prefix,
		);

		// Exclude selected db tables
		if ( isset( $params['options']['exclude_db_tables'], $params['excluded_db_tables'] ) ) {
			if ( ( $excluded_db_tables = explode( ',', $params['excluded_db_tables'] ) ) ) {
				$config['Database']['ExcludedTables'] = $excluded_db_tables;
			}
		}

		// Set PHP version
		$config['PHP'] = array( 'Version' => PHP_VERSION, 'System' => PHP_OS, 'Integer' => PHP_INT_SIZE );

		// Set active plugins
		$config['Plugins'] = array_values( array_diff( ai1wm_active_plugins(), ai1wm_active_servmask_plugins() ) );

		// Set active template
		$config['Template'] = ai1wm_active_template();

		// Set active stylesheet
		$config['Stylesheet'] = ai1wm_active_stylesheet();

		// Set upload path
		$config['Uploads'] = get_option( 'upload_path' );

		// Set upload URL path
		$config['UploadsURL'] = get_option( 'upload_url_path' );

		// Set server info
		$config['Server'] = array( '.htaccess' => base64_encode( ai1wm_get_htaccess() ), 'web.config' => base64_encode( ai1wm_get_webconfig() ) );

		if ( isset( $params['options']['encrypt_backups'] ) ) {
			$config['Encrypted']          = true;
			$config['EncryptedSignature'] = base64_encode( ai1wm_encrypt_string( AI1WM_SIGN_TEXT, $params['options']['encrypt_password'] ) );
		}

		// Save package.json file
		$handle = ai1wm_open( ai1wm_package_path( $params ), 'w' );
		ai1wm_write( $handle, json_encode( $config ) );
		ai1wm_close( $handle );

		// Set progress
		Ai1wm_Status::info( __( 'Done preparing configuration file.', AI1WM_PLUGIN_NAME ) );

		return $params;
	}
}
