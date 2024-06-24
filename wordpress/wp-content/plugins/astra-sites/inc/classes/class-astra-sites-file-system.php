<?php
/**
 * Astra Sites File System
 *
 * @since  4.2.0
 * @package Astra Sites
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Astra_Sites_File_System' ) ) {

	/**
	 * Astra_Sites_File_System
	 */
	class Astra_Sites_File_System {

		/**
		 * Instance of Astra_Sites
		 *
		 * @since  4.2.0
		 * @var (self) Astra_Sites
		 */
		private static $instance = null;

		/**
		 * Folder name for the json files.
		 * 
		 * @var string
		 * @since 4.2.0
		 */
		public static $folder_name = 'json';

		/**
		 * Instance of Astra_Sites.
		 *
		 * @since  4.2.0
		 * @return self Class object.
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Create files for demo content.
		 * 
		 * @return void
		 * @since 4.2.0
		 */
		public function create_file() { 
			$upload_dir = wp_upload_dir();
			$file = array(
				'file_base' => $upload_dir['basedir'] . '/astra-sites/' . self::$folder_name,
				'file_name' => 'astra_sites_import_data.json',
				'file_content' => array(),
			);

			$this->create_single_file( $file );
		}

		/**
		 * Delete a JSON file from the uploads directory.
		 *
		 * @param string $file_name File name to be deleted.
		 * @return void True on success, false on failure.
		 */
		public function delete_json_file( $file_name ) {
			$upload_dir = wp_upload_dir();
			$file_name = $upload_dir['basedir'] . '/astra-sites/' . self::$folder_name . '/' . $file_name;
			
			if ( file_exists( $file_name ) ) {
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
			$file_name = $upload_dir['basedir'] . '/astra-sites/' . self::$folder_name . '/' . $file_name;

			if ( file_exists( $file_name ) ) {
				// Ignoring the rule as it is not a remote file.
				$file_content = file_get_contents( $file_name ); //phpcs:ignore WordPressVIPMinimum.Performance.FetchingRemoteData.FileGetContentsUnknown
		
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
		 * @since  4.2.0
		 * 
		 * @return mixed
		 */
		public function get_demo_content() {
			return $this->get_json_file_content( 'astra_sites_import_data.json' );
		}

		/**
		 * Delete for get_json_file_content
		 *
		 * @since  4.2.0
		 * 
		 * @return mixed
		 */
		public function delete_demo_content() {
			$this->delete_json_file( 'astra_sites_import_data.json' );
		}

		/**
		 * Create single json file.
		 *
		 * @since 4.2.2
		 * @param array<string, mixed> $file file data.
		 * 
		 * @return void
		 */
		public function create_single_file( $file ) {

			if ( wp_mkdir_p( $file['file_base'] ) ) {
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
		 * Update files/directories.
		 * 
		 * @param string $file_name    The file name.
		 * @param mixed  $file_content The file content.
		 * 
		 * @return void
		 */
		public function update_json_file( $file_name, $file_content ) {
			$upload_dir = wp_upload_dir();
			$dir_info = array(
				'path' => $upload_dir['basedir'] . '/astra-sites/' . self::$folder_name . '/',
			);
			
			if ( ! file_exists( $dir_info['path'] . $file_name ) ) {
				$file = array(
					'file_base' => $dir_info['path'],
					'file_name' => $file_name,
					'file_content' => '',
				);

				$this->create_single_file( $file );
			}

			if ( file_exists( $dir_info['path'] . $file_name ) && file_put_contents( $dir_info['path'] . $file_name, wp_json_encode( $file_content ) ) !== false ) { //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_file_put_contents
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
	Astra_Sites_File_System::get_instance();

}
