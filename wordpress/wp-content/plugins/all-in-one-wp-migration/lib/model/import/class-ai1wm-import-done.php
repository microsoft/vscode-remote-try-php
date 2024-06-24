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

class Ai1wm_Import_Done {

	public static function execute( $params ) {
		global $wp_rewrite;

		// Check multisite.json file
		if ( is_file( ai1wm_multisite_path( $params ) ) ) {

			// Read multisite.json file
			$handle = ai1wm_open( ai1wm_multisite_path( $params ), 'r' );

			// Parse multisite.json file
			$multisite = ai1wm_read( $handle, filesize( ai1wm_multisite_path( $params ) ) );
			$multisite = json_decode( $multisite, true );

			// Close handle
			ai1wm_close( $handle );

			// Activate WordPress plugins
			if ( isset( $multisite['Plugins'] ) && ( $plugins = $multisite['Plugins'] ) ) {
				ai1wm_activate_plugins( $plugins );
			}

			// Deactivate WordPress SSL plugins
			if ( ! is_ssl() ) {
				ai1wm_deactivate_plugins(
					array(
						ai1wm_discover_plugin_basename( 'really-simple-ssl/rlrsssl-really-simple-ssl.php' ),
						ai1wm_discover_plugin_basename( 'wordpress-https/wordpress-https.php' ),
						ai1wm_discover_plugin_basename( 'wp-force-ssl/wp-force-ssl.php' ),
						ai1wm_discover_plugin_basename( 'force-https-littlebizzy/force-https.php' ),
					)
				);

				ai1wm_woocommerce_force_ssl( false );
			}

			// Deactivate WordPress plugins
			ai1wm_deactivate_plugins(
				array(
					ai1wm_discover_plugin_basename( 'invisible-recaptcha/invisible-recaptcha.php' ),
					ai1wm_discover_plugin_basename( 'wps-hide-login/wps-hide-login.php' ),
					ai1wm_discover_plugin_basename( 'hide-my-wp/index.php' ),
					ai1wm_discover_plugin_basename( 'hide-my-wordpress/index.php' ),
					ai1wm_discover_plugin_basename( 'mycustomwidget/my_custom_widget.php' ),
					ai1wm_discover_plugin_basename( 'lockdown-wp-admin/lockdown-wp-admin.php' ),
					ai1wm_discover_plugin_basename( 'rename-wp-login/rename-wp-login.php' ),
					ai1wm_discover_plugin_basename( 'wp-simple-firewall/icwp-wpsf.php' ),
					ai1wm_discover_plugin_basename( 'join-my-multisite/joinmymultisite.php' ),
					ai1wm_discover_plugin_basename( 'multisite-clone-duplicator/multisite-clone-duplicator.php' ),
					ai1wm_discover_plugin_basename( 'wordpress-mu-domain-mapping/domain_mapping.php' ),
					ai1wm_discover_plugin_basename( 'wordpress-starter/siteground-wizard.php' ),
					ai1wm_discover_plugin_basename( 'pro-sites/pro-sites.php' ),
					ai1wm_discover_plugin_basename( 'wpide/WPide.php' ),
					ai1wm_discover_plugin_basename( 'page-optimize/page-optimize.php' ),
					ai1wm_discover_plugin_basename( 'update-services/update-services.php' ),
				)
			);

			// Deactivate Swift Optimizer rules
			ai1wm_deactivate_swift_optimizer_rules(
				array(
					ai1wm_discover_plugin_basename( 'all-in-one-wp-migration/all-in-one-wp-migration.php' ),
					ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-azure-storage-extension/all-in-one-wp-migration-azure-storage-extension.php' ),
					ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-b2-extension/all-in-one-wp-migration-b2-extension.php' ),
					ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-backup/all-in-one-wp-migration-backup.php' ),
					ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-box-extension/all-in-one-wp-migration-box-extension.php' ),
					ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-digitalocean-extension/all-in-one-wp-migration-digitalocean-extension.php' ),
					ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-direct-extension/all-in-one-wp-migration-direct-extension.php' ),
					ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-dropbox-extension/all-in-one-wp-migration-dropbox-extension.php' ),
					ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-file-extension/all-in-one-wp-migration-file-extension.php' ),
					ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-ftp-extension/all-in-one-wp-migration-ftp-extension.php' ),
					ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-gcloud-storage-extension/all-in-one-wp-migration-gcloud-storage-extension.php' ),
					ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-gdrive-extension/all-in-one-wp-migration-gdrive-extension.php' ),
					ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-glacier-extension/all-in-one-wp-migration-glacier-extension.php' ),
					ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-mega-extension/all-in-one-wp-migration-mega-extension.php' ),
					ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-multisite-extension/all-in-one-wp-migration-multisite-extension.php' ),
					ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-onedrive-extension/all-in-one-wp-migration-onedrive-extension.php' ),
					ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-pcloud-extension/all-in-one-wp-migration-pcloud-extension.php' ),
					ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-pro/all-in-one-wp-migration-pro.php' ),
					ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-s3-client-extension/all-in-one-wp-migration-s3-client-extension.php' ),
					ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-s3-extension/all-in-one-wp-migration-s3-extension.php' ),
					ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-unlimited-extension/all-in-one-wp-migration-unlimited-extension.php' ),
					ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-url-extension/all-in-one-wp-migration-url-extension.php' ),
					ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-webdav-extension/all-in-one-wp-migration-webdav-extension.php' ),
				)
			);

			// Deactivate Revolution Slider
			ai1wm_deactivate_revolution_slider( ai1wm_discover_plugin_basename( 'revslider/revslider.php' ) );

			// Deactivate Jetpack modules
			ai1wm_deactivate_jetpack_modules( array( 'photon', 'sso' ) );

			// Flush Elementor cache
			ai1wm_elementor_cache_flush();

			// Initial DB version
			ai1wm_initial_db_version();

		} else {

			// Check package.json file
			if ( is_file( ai1wm_package_path( $params ) ) ) {

				// Read package.json file
				$handle = ai1wm_open( ai1wm_package_path( $params ), 'r' );

				// Parse package.json file
				$package = ai1wm_read( $handle, filesize( ai1wm_package_path( $params ) ) );
				$package = json_decode( $package, true );

				// Close handle
				ai1wm_close( $handle );

				// Activate WordPress plugins
				if ( isset( $package['Plugins'] ) && ( $plugins = $package['Plugins'] ) ) {
					ai1wm_activate_plugins( $plugins );
				}

				// Activate WordPress template
				if ( isset( $package['Template'] ) && ( $template = $package['Template'] ) ) {
					ai1wm_activate_template( $template );
				}

				// Activate WordPress stylesheet
				if ( isset( $package['Stylesheet'] ) && ( $stylesheet = $package['Stylesheet'] ) ) {
					ai1wm_activate_stylesheet( $stylesheet );
				}

				// Deactivate WordPress SSL plugins
				if ( ! is_ssl() ) {
					ai1wm_deactivate_plugins(
						array(
							ai1wm_discover_plugin_basename( 'really-simple-ssl/rlrsssl-really-simple-ssl.php' ),
							ai1wm_discover_plugin_basename( 'wordpress-https/wordpress-https.php' ),
							ai1wm_discover_plugin_basename( 'wp-force-ssl/wp-force-ssl.php' ),
							ai1wm_discover_plugin_basename( 'force-https-littlebizzy/force-https.php' ),
						)
					);

					ai1wm_woocommerce_force_ssl( false );
				}

				// Deactivate WordPress plugins
				ai1wm_deactivate_plugins(
					array(
						ai1wm_discover_plugin_basename( 'invisible-recaptcha/invisible-recaptcha.php' ),
						ai1wm_discover_plugin_basename( 'wps-hide-login/wps-hide-login.php' ),
						ai1wm_discover_plugin_basename( 'hide-my-wp/index.php' ),
						ai1wm_discover_plugin_basename( 'hide-my-wordpress/index.php' ),
						ai1wm_discover_plugin_basename( 'mycustomwidget/my_custom_widget.php' ),
						ai1wm_discover_plugin_basename( 'lockdown-wp-admin/lockdown-wp-admin.php' ),
						ai1wm_discover_plugin_basename( 'rename-wp-login/rename-wp-login.php' ),
						ai1wm_discover_plugin_basename( 'wp-simple-firewall/icwp-wpsf.php' ),
						ai1wm_discover_plugin_basename( 'join-my-multisite/joinmymultisite.php' ),
						ai1wm_discover_plugin_basename( 'multisite-clone-duplicator/multisite-clone-duplicator.php' ),
						ai1wm_discover_plugin_basename( 'wordpress-mu-domain-mapping/domain_mapping.php' ),
						ai1wm_discover_plugin_basename( 'wordpress-starter/siteground-wizard.php' ),
						ai1wm_discover_plugin_basename( 'pro-sites/pro-sites.php' ),
						ai1wm_discover_plugin_basename( 'wpide/WPide.php' ),
						ai1wm_discover_plugin_basename( 'page-optimize/page-optimize.php' ),
						ai1wm_discover_plugin_basename( 'update-services/update-services.php' ),
					)
				);

				// Deactivate Swift Optimizer rules
				ai1wm_deactivate_swift_optimizer_rules(
					array(
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration/all-in-one-wp-migration.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-azure-storage-extension/all-in-one-wp-migration-azure-storage-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-b2-extension/all-in-one-wp-migration-b2-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-backup/all-in-one-wp-migration-backup.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-box-extension/all-in-one-wp-migration-box-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-digitalocean-extension/all-in-one-wp-migration-digitalocean-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-direct-extension/all-in-one-wp-migration-direct-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-dropbox-extension/all-in-one-wp-migration-dropbox-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-file-extension/all-in-one-wp-migration-file-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-ftp-extension/all-in-one-wp-migration-ftp-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-gcloud-storage-extension/all-in-one-wp-migration-gcloud-storage-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-gdrive-extension/all-in-one-wp-migration-gdrive-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-glacier-extension/all-in-one-wp-migration-glacier-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-mega-extension/all-in-one-wp-migration-mega-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-multisite-extension/all-in-one-wp-migration-multisite-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-onedrive-extension/all-in-one-wp-migration-onedrive-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-pcloud-extension/all-in-one-wp-migration-pcloud-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-pro/all-in-one-wp-migration-pro.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-s3-client-extension/all-in-one-wp-migration-s3-client-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-s3-extension/all-in-one-wp-migration-s3-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-unlimited-extension/all-in-one-wp-migration-unlimited-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-url-extension/all-in-one-wp-migration-url-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-webdav-extension/all-in-one-wp-migration-webdav-extension.php' ),
					)
				);

				// Deactivate Revolution Slider
				ai1wm_deactivate_revolution_slider( ai1wm_discover_plugin_basename( 'revslider/revslider.php' ) );

				// Deactivate Jetpack modules
				ai1wm_deactivate_jetpack_modules( array( 'photon', 'sso' ) );

				// Flush Elementor cache
				ai1wm_elementor_cache_flush();

				// Initial DB version
				ai1wm_initial_db_version();
			}
		}

		// Check blogs.json file
		if ( is_file( ai1wm_blogs_path( $params ) ) ) {

			// Read blogs.json file
			$handle = ai1wm_open( ai1wm_blogs_path( $params ), 'r' );

			// Parse blogs.json file
			$blogs = ai1wm_read( $handle, filesize( ai1wm_blogs_path( $params ) ) );
			$blogs = json_decode( $blogs, true );

			// Close handle
			ai1wm_close( $handle );

			// Loop over blogs
			foreach ( $blogs as $blog ) {

				// Activate WordPress plugins
				if ( isset( $blog['New']['Plugins'] ) && ( $plugins = $blog['New']['Plugins'] ) ) {
					ai1wm_activate_plugins( $plugins );
				}

				// Activate WordPress template
				if ( isset( $blog['New']['Template'] ) && ( $template = $blog['New']['Template'] ) ) {
					ai1wm_activate_template( $template );
				}

				// Activate WordPress stylesheet
				if ( isset( $blog['New']['Stylesheet'] ) && ( $stylesheet = $blog['New']['Stylesheet'] ) ) {
					ai1wm_activate_stylesheet( $stylesheet );
				}

				// Deactivate WordPress SSL plugins
				if ( ! is_ssl() ) {
					ai1wm_deactivate_plugins(
						array(
							ai1wm_discover_plugin_basename( 'really-simple-ssl/rlrsssl-really-simple-ssl.php' ),
							ai1wm_discover_plugin_basename( 'wordpress-https/wordpress-https.php' ),
							ai1wm_discover_plugin_basename( 'wp-force-ssl/wp-force-ssl.php' ),
							ai1wm_discover_plugin_basename( 'force-https-littlebizzy/force-https.php' ),
						)
					);

					ai1wm_woocommerce_force_ssl( false );
				}

				// Deactivate WordPress plugins
				ai1wm_deactivate_plugins(
					array(
						ai1wm_discover_plugin_basename( 'invisible-recaptcha/invisible-recaptcha.php' ),
						ai1wm_discover_plugin_basename( 'wps-hide-login/wps-hide-login.php' ),
						ai1wm_discover_plugin_basename( 'hide-my-wp/index.php' ),
						ai1wm_discover_plugin_basename( 'hide-my-wordpress/index.php' ),
						ai1wm_discover_plugin_basename( 'mycustomwidget/my_custom_widget.php' ),
						ai1wm_discover_plugin_basename( 'lockdown-wp-admin/lockdown-wp-admin.php' ),
						ai1wm_discover_plugin_basename( 'rename-wp-login/rename-wp-login.php' ),
						ai1wm_discover_plugin_basename( 'wp-simple-firewall/icwp-wpsf.php' ),
						ai1wm_discover_plugin_basename( 'join-my-multisite/joinmymultisite.php' ),
						ai1wm_discover_plugin_basename( 'multisite-clone-duplicator/multisite-clone-duplicator.php' ),
						ai1wm_discover_plugin_basename( 'wordpress-mu-domain-mapping/domain_mapping.php' ),
						ai1wm_discover_plugin_basename( 'wordpress-starter/siteground-wizard.php' ),
						ai1wm_discover_plugin_basename( 'pro-sites/pro-sites.php' ),
						ai1wm_discover_plugin_basename( 'wpide/WPide.php' ),
						ai1wm_discover_plugin_basename( 'page-optimize/page-optimize.php' ),
						ai1wm_discover_plugin_basename( 'update-services/update-services.php' ),
					)
				);

				// Deactivate Swift Optimizer rules
				ai1wm_deactivate_swift_optimizer_rules(
					array(
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration/all-in-one-wp-migration.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-azure-storage-extension/all-in-one-wp-migration-azure-storage-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-b2-extension/all-in-one-wp-migration-b2-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-backup/all-in-one-wp-migration-backup.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-box-extension/all-in-one-wp-migration-box-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-digitalocean-extension/all-in-one-wp-migration-digitalocean-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-direct-extension/all-in-one-wp-migration-direct-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-dropbox-extension/all-in-one-wp-migration-dropbox-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-file-extension/all-in-one-wp-migration-file-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-ftp-extension/all-in-one-wp-migration-ftp-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-gcloud-storage-extension/all-in-one-wp-migration-gcloud-storage-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-gdrive-extension/all-in-one-wp-migration-gdrive-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-glacier-extension/all-in-one-wp-migration-glacier-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-mega-extension/all-in-one-wp-migration-mega-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-multisite-extension/all-in-one-wp-migration-multisite-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-onedrive-extension/all-in-one-wp-migration-onedrive-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-pcloud-extension/all-in-one-wp-migration-pcloud-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-pro/all-in-one-wp-migration-pro.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-s3-client-extension/all-in-one-wp-migration-s3-client-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-s3-extension/all-in-one-wp-migration-s3-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-unlimited-extension/all-in-one-wp-migration-unlimited-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-url-extension/all-in-one-wp-migration-url-extension.php' ),
						ai1wm_discover_plugin_basename( 'all-in-one-wp-migration-webdav-extension/all-in-one-wp-migration-webdav-extension.php' ),
					)
				);

				// Deactivate Revolution Slider
				ai1wm_deactivate_revolution_slider( ai1wm_discover_plugin_basename( 'revslider/revslider.php' ) );

				// Deactivate Jetpack modules
				ai1wm_deactivate_jetpack_modules( array( 'photon', 'sso' ) );

				// Flush Elementor cache
				ai1wm_elementor_cache_flush();

				// Initial DB version
				ai1wm_initial_db_version();
			}
		}

		// Clear auth cookie (WP Cerber)
		if ( ai1wm_validate_plugin_basename( 'wp-cerber/wp-cerber.php' ) ) {
			wp_clear_auth_cookie();
		}

		$should_reset_permalinks = false;

		// Switch to default permalink structure
		if ( ( $should_reset_permalinks = ai1wm_should_reset_permalinks( $params ) ) ) {
			$wp_rewrite->set_permalink_structure( '' );
		}

		// Set progress
		if ( ai1wm_validate_plugin_basename( 'fusion-builder/fusion-builder.php' ) ) {
			Ai1wm_Status::done( __( 'Your site has been imported successfully!', AI1WM_PLUGIN_NAME ), Ai1wm_Template::get_content( 'import/avada', array( 'should_reset_permalinks' => $should_reset_permalinks ) ) );
		} elseif ( ai1wm_validate_plugin_basename( 'oxygen/functions.php' ) ) {
			Ai1wm_Status::done( __( 'Your site has been imported successfully!', AI1WM_PLUGIN_NAME ), Ai1wm_Template::get_content( 'import/oxygen', array( 'should_reset_permalinks' => $should_reset_permalinks ) ) );
		} else {
			Ai1wm_Status::done( __( 'Your site has been imported successfully!', AI1WM_PLUGIN_NAME ), Ai1wm_Template::get_content( 'import/done', array( 'should_reset_permalinks' => $should_reset_permalinks ) ) );
		}

		do_action( 'ai1wm_status_import_done', $params );

		return $params;
	}
}
