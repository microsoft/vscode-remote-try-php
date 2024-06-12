<?php
/**
 * ST Importer File System
 *
 * @since  1.0.1
 * @package ST Importer
 */

namespace STImporter\Importer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ST_Importer_File_System' ) ) {

	/**
	 * ST_Importer_File_System
	 */
	class ST_Importer_File_System {

		/**
		 * Instance of this class.
		 *
		 * @since 1.0.0
		 * @var object Class object.
		 */
		private static $instance;

		/**
		 * Initiator of this class.
		 *
		 * @since 1.0.0
		 * @return self initialized object of this class.
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Folder name for the json files.
		 *
		 * @var string
		 * @since 1.0.1
		 */
		public static $folder_name = 'json';

		/**
		 * Create files for demo content.
		 *
		 * @return void
		 * @since 1.0.1
		 */
		public function create_file() {
			$upload_dir = wp_upload_dir();
			$file       = array(
				'file_base'    => $upload_dir['basedir'] . '/astra-sites/' . self::$folder_name,
				'file_name'    => 'astra_sites_import_data.json',
				'file_content' => array(),
			);

			if ( wp_mkdir_p( $file['file_base'] ) && ! file_exists( trailingslashit( $file['file_base'] ) . $file['file_name'] ) ) {
				$file_handle = @fopen( trailingslashit( $file['file_base'] ) . $file['file_name'], 'w' ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_system_read_fopen
				if ( $file_handle ) {
					if ( is_string( wp_json_encode( $file['file_content'] ) ) ) {
						fwrite( $file_handle, wp_json_encode( $file['file_content'] ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fwrite, WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_fwrite
						fclose( $file_handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose
						astra_sites_error_log( 'File: ' . $file['file_name'] . ' Created Successfully!' );
					}
				}
			}
		}

		/**
		 * Delete a JSON file from the uploads directory.
		 *
		 * @param string $file_name File name to be deleted.
		 * @return void True on success, false on failure.
		 */
		public function delete_json_file( $file_name ) {
			$upload_dir = wp_upload_dir();
			$path       = $upload_dir['basedir'] . '/astra-sites/' . self::$folder_name . '/';
			$file_name  = $path . $file_name;

			if ( file_exists( $path ) ) {
				wp_delete_file( $file_name );
			} else {
				astra_sites_error_log( 'File not found: ' . $file_name );
			}
		}

		/**
		 * Getting json file for templates from uploads.
		 *
		 * @param string $file_name  File data.
		 * @param bool   $array_format  Is The file content array.
		 *
		 * @return mixed
		 */
		public function get_json_file_content( $file_name, $array_format = true ) {
			$upload_dir = wp_upload_dir();
			$path       = $upload_dir['basedir'] . '/astra-sites/' . self::$folder_name . '/';
			$file_name  = $path . $file_name;

			if ( file_exists( $file_name ) ) {
				// Ignoring the rule as it is not a remote file.
				$file_content = file_get_contents( $file_name ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

				if ( $array_format ) {
					return json_decode( (string) $file_content, true );
				} else {
					return $file_content;
				}
			}

			return '';
		}

		/**
		 * Getter for get_json_file_content
		 *
		 * @since  1.0.1
		 *
		 * @return mixed
		 */
		public function get_demo_content() {
			return $this->get_json_file_content( 'astra_sites_import_data.json' );
		}

		/**
		 * Delete for get_json_file_content
		 *
		 * @since  1.0.1
		 *
		 * @return mixed
		 */
		public function delete_demo_content() {
			$this->delete_json_file( 'astra_sites_import_data.json' );
		}

		/**
		 * Update files/directories.
		 *
		 * @param string     $file_name    The file name.
		 * @param string|int $file_content The file content.
		 *
		 * @return void
		 */
		public function update_json_file( $file_name, $file_content ) {
			$upload_dir = wp_upload_dir();
			$dir_info   = array(
				'path' => $upload_dir['basedir'] . '/astra-sites/' . self::$folder_name . '/',
			);
			$this->create_file();
			if ( file_exists( $dir_info['path'] . $file_name ) && file_put_contents( $dir_info['path'] . $file_name, wp_json_encode( $file_content ) ) !== false ) { //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents
				astra_sites_error_log( 'File: ' . $file_name . ' Updated Successfully!' );
			} else {
				astra_sites_error_log( 'File: ' . $file_name . ' Not Updated!' );
			}
		}

		/**
		 * Setter for update_json_file()
		 *
		 * @param string|int $file_content The file content.
		 *
		 * @return mixed
		 */
		public function update_demo_data( $file_content ) {
			$this->update_json_file( 'astra_sites_import_data.json', $file_content );
		}
	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	ST_Importer_File_System::get_instance();

}
