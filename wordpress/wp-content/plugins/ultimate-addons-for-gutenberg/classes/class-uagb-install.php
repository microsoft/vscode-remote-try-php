<?php
/**
 * UAGB Filesystem
 *
 * @package UAGB
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class UAGB_Install.
 */
class UAGB_Install {

	/**
	 * Member Variable
	 *
	 * @var instance
	 */
	private static $instance;

	/**
	 *  Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Create files/directories.
	 */
	public function create_files() {

		if ( ! defined( 'UAGB_UPLOAD_DIR_NAME' ) ) {
			define( 'UAGB_UPLOAD_DIR_NAME', 'uag-plugin' );
		}

		if ( ! defined( 'UAGB_UPLOAD_DIR' ) ) {
			$upload_dir = wp_upload_dir( null, false );
			define( 'UAGB_UPLOAD_DIR', $upload_dir['basedir'] . '/' . UAGB_UPLOAD_DIR_NAME . '/' );
		}

		$files = array(
			array(
				'base'    => UAGB_UPLOAD_DIR,
				'file'    => 'index.html',
				'content' => '',
			),
			array(
				'base'    => UAGB_UPLOAD_DIR . 'assets',
				'file'    => 'index.html',
				'content' => '',
			),
			array(
				'base' => UAGB_UPLOAD_DIR . 'assets/fonts',
			),
		);

		foreach ( $files as $file ) {

			if ( wp_mkdir_p( $file['base'] ) && ! empty( $file['file'] ) && ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) ) {

				$file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' ); // phpcs:ignore

				if ( $file_handle ) {
					fwrite( $file_handle, $file['content'] ); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_fwrite
					fclose( $file_handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose
				}
			}
		}
	}
}

/**
 *  Prepare if class 'UAGB_Install' exist.
 *  Kicking this off by calling 'get_instance()' method
 */
UAGB_Install::get_instance();

/**
 * Filesystem class
 *
 * @since 1.23.0
 */

/**
 * Install class
 *
 * @since 2.0.0
 *
 * @return object
 */
function uagb_install() {
	return UAGB_Install::get_instance();

}

