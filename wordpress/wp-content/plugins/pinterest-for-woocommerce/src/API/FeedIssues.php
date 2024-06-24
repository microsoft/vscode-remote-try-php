<?php
/**
 * Parse & return the Pinterest Feed issues
 *
 * @package     Pinterest_For_Woocommerce/API
 * @version     1.0.0s
 */

namespace Automattic\WooCommerce\Pinterest\API;

use Automattic\WooCommerce\Pinterest as Pinterest;
use Automattic\WooCommerce\Pinterest\FeedRegistration;

use \WP_REST_Server;
use \WP_REST_Request;
use \WP_REST_Response;
use Exception;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Endpoint returning the product-level issues of the XML feed.
 */
class FeedIssues extends VendorAPI {

	/**
	 * Array used to hold the cached filenames for the feed Issues files.
	 *
	 * @var array
	 */
	private $feed_data_files = array();

	/**
	 * Initialize class
	 */
	public function __construct() {

		$this->base              = 'feed_issues';
		$this->endpoint_callback = 'get_feed_issues';
		$this->methods           = WP_REST_Server::READABLE;
		$this->feed_data_files   = Pinterest_For_Woocommerce()::get_data( 'feed_data_cache' ) ?? array();

		$this->register_routes();
	}


	/**
	 * Get the feed issue lines for the last workflow of the current feed.
	 *
	 * @return array|WP_REST_Response|\WP_Error
	 *
	 * @param WP_REST_Request $request The request.
	 *
	 * @throws Exception PHP Exception.
	 */
	public function get_feed_issues( WP_REST_Request $request ) {

		try {
			$merchant_id = Pinterest_For_Woocommerce()::get_data( 'merchant_id' );
			$feed_id     = FeedRegistration::get_locally_stored_registered_feed_id();
			if ( ! Pinterest\ProductSync::is_product_sync_enabled() || ! $feed_id || ! $merchant_id ) {
				return array( 'lines' => array() );
			}

			$workflow        = false;
			$issues_file_url = $request->has_param( 'feed_issues_url' ) ? $request->get_param( 'feed_issues_url' ) : false;
			$paged           = $request->has_param( 'paged' ) ? (int) $request->get_param( 'paged' ) : 1;
			$per_page        = $request->has_param( 'per_page' ) ? (int) $request->get_param( 'per_page' ) : 25;

			if ( false === $issues_file_url ) {
				$workflow = Pinterest\Feeds::get_feed_latest_workflow( (string) $merchant_id, (string) $feed_id );

				if ( $workflow && isset( $workflow->s3_validation_url ) ) {
					$issues_file_url = $workflow->s3_validation_url;
				}
			}

			if ( empty( $issues_file_url ) ) {
				return array( 'lines' => array() );
			}

			// Get file.
			$issues_file = $this->get_remote_file( $issues_file_url, (array) $workflow );

			if ( empty( $issues_file ) ) {
				throw new Exception( esc_html__( 'Error downloading feed issues file from Pinterest.', 'pinterest-for-woocommerce' ), 400 );
			}

			$start_line  = ( ( $paged - 1 ) * $per_page );
			$end_line    = $start_line + $per_page - 1; // Starting from 0.
			$issues_data = self::parse_lines( $issues_file, $start_line, $end_line );

			if ( ! empty( $issues_data['lines'] ) ) {
				$issues_data['lines'] = array_map( array( __CLASS__, 'prepare_issue_lines' ), $issues_data['lines'] );
			}

			$response = new WP_REST_Response(
				array(
					'lines'      => $issues_data['lines'],
					'total_rows' => $issues_data['total'],
				)
			);

			$response->header( 'X-WP-Total', $issues_data['total'] );
			$response->header( 'X-WP-TotalPages', ceil( $issues_data['total'] / $per_page ) );

			return $response;

		} catch ( \Throwable $th ) {

			/* Translators: The error description as returned from the API */
			$error_message = sprintf( esc_html__( 'Could not get current feed\'s issues. [%s]', 'pinterest-for-woocommerce' ), $th->getMessage() );

			return new \WP_Error( \PINTEREST_FOR_WOOCOMMERCE_PREFIX . '_advertisers_error', $error_message, array( 'status' => $th->getCode() ) );
		}
	}


	/**
	 * Add product specific data to each line.
	 *
	 * @param array $line The array contaning each col value for the line.
	 *
	 * @return array
	 */
	private static function prepare_issue_lines( $line ) {

		$product      = wc_get_product( $line['ItemId'] );
		$edit_link    = '';
		$product_name = esc_html__( 'Invalid product', 'pinterest-for-woocommerce' );

		if ( $product ) {
			$product_name = $product->get_name();
		}

		if ( $product->get_parent_id() ) {
			$product_name .= ' ' . esc_html__( '(Variation)', 'pinterest-for-woocommerce' );
			$edit_link     = get_edit_post_link( $product->get_parent_id(), 'not_display' ); // get_edit_post_link() will return '&' instead of  '&amp;' for anything other than the 'display' context.
		}

		$edit_link = empty( $edit_link ) && $product ? get_edit_post_link( $product->get_id(), 'not_display' ) : $edit_link; // get_edit_post_link() will return '&' instead of  '&amp;' for anything other than the 'display' context.

		return array(
			'status'            => 'ERROR' === $line['Code'] ? 'error' : 'warning',
			'product_name'      => $product_name,
			'product_edit_link' => $edit_link,
			'issue_description' => $line['Message'],
		);
	}



	/**
	 * Reads the file given in $issues_file, parses and returns the content of lines
	 * from $start_line to $end_line as array items.
	 *
	 * @param string  $issues_file The file path to read from.
	 * @param int     $start_line  The first line to return.
	 * @param int     $end_line    The last line to return.
	 * @param boolean $has_keys    Whether or not the 1st line of the file holds the header keys.
	 *
	 * @return array
	 */
	private static function parse_lines( $issues_file, $start_line, $end_line, $has_keys = true ) {

		$lines      = array();
		$keys       = '';
		$delim      = "\t";
		$start_line = $has_keys ? $start_line + 1 : $start_line;
		$end_line   = $has_keys ? $end_line + 1 : $end_line;

		$spl = new \SplFileObject( $issues_file );

		// Get last line.
		$spl->seek( $spl->getSize() );
		$last_line = (int) $spl->key();

		if ( $has_keys ) {
			$spl->seek( 0 );
			$keys = $spl->current();
			$last_line--;
		}

		// Don't go over last line.
		$end_line = $end_line > $last_line ? $last_line : $end_line;

		for ( $i = $start_line; $i <= $end_line; $i++ ) {
			$spl->seek( $i );
			$lines[] = $spl->current();
		}

		if ( ! empty( $keys ) ) {
			$keys = array_map( 'trim', explode( $delim, $keys ) );
		}

		foreach ( $lines as &$line ) {
			$line = array_combine( $keys, array_map( 'trim', explode( $delim, $line ) ) );
		}

		return array(
			'lines' => $lines,
			'total' => $last_line,
		);
	}


	/**
	 * Get the file from $url and save it to a temporary location.
	 * Return the path of the temporary file.
	 *
	 * @param string $url             The URL to fetch the file from.
	 * @param mixed  $cache_variables The variables to use in order to populate the cache key.
	 *
	 * @return string|boolean
	 */
	private function get_remote_file( $url, $cache_variables ) {

		if ( is_array( $cache_variables ) && ! empty( $cache_variables ) ) {
			$ignore_for_cache = array( 's3_source_url', 's3_validation_url' ); // These 2 are different on every response.
			$cache_variables  = array_diff_key( $cache_variables, array_flip( $ignore_for_cache ) );
		}

		$cache_key = PINTEREST_FOR_WOOCOMMERCE_PREFIX . '_feed_file_' . md5( $cache_variables ? wp_json_encode( $cache_variables ) : $url );

		if ( isset( $this->feed_data_files[ $cache_key ] ) && file_exists( $this->feed_data_files[ $cache_key ] ) ) {
			return $this->feed_data_files[ $cache_key ];
		} elseif ( ! empty( $this->feed_data_files ) ) {

			// Cleanup previously stored files.
			foreach ( $this->feed_data_files as $key => $file ) {
				@unlink( $file ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged --- We don't care if the file is already gone.
				unset( $this->feed_data_files[ $key ] );
			}
		}

		if ( ! function_exists( 'wp_tempnam' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		$target_file = wp_tempnam();

		$response = wp_remote_get(
			$url,
			array(
				'stream'   => true,
				'filename' => $target_file,
				'timeout'  => 300,
			)
		);

		$result = $response && ! is_wp_error( $response ) ? $target_file : false;

		if ( $result ) {
			// Save to cache.
			$this->feed_data_files[ $cache_key ] = $result;
			$this->save_feed_data_cache();
		}

		return $result;
	}

	/**
	 * Save the current contents of feed_data_files to the options table.
	 *
	 * @return void
	 */
	private function save_feed_data_cache() {
		Pinterest_For_Woocommerce()::save_data( 'feed_data_cache', $this->feed_data_files );
	}

	/**
	 * Cleanup feed cached data.
	 */
	public static function deregister() {
		Pinterest_For_Woocommerce()::save_data( 'feed_data_cache', false );
	}

}
