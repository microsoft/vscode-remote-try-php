<?php
/**
 * Zipwp Images API
 *
 * @since  1.0.0
 * @package Zipwp Images API
 */

namespace ZipWP_Images\Classes;

/**
 * Ai_Builder
 */
class Zipwp_Images_Api {

	/**
	 * Instance
	 *
	 * @access private
	 * @var object Class Instance.
	 * @since 1.0.0
	 */
	private static $instance = null;

	/**
	 * Initiator
	 *
	 * @since 1.0.0
	 * @return object initialized object of class.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_route' ) );
		add_action( 'wp_ajax_zipwp_images_insert_image', array( $this, 'zipwp_insert_image' ) );
	}

	/**
	 * Get api domain
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_api_domain() {
		return trailingslashit( defined( 'ZIPWP_API' ) ? ZIPWP_API : 'https://api.zipwp.com/api/' );
	}

	/**
	 * Get api namespace
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_api_namespace() {
		return 'zipwp-images/v1';
	}

	/**
	 * Get API headers
	 *
	 * @since 1.0.0
	 * @return array<string, string>
	 */
	public function get_api_headers() {
		return array(
			'Content-Type' => 'application/json',
			'Accept'       => 'application/json',
		);
	}

	/**
	 * Check whether a given request has permission to read notes.
	 *
	 * @param  object $request WP_REST_Request Full details about the request.
	 * @return object|boolean
	 */
	public function get_item_permissions_check( $request ) {

		if ( ! current_user_can( 'manage_options' ) ) {
			return new \WP_Error(
				'gt_rest_cannot_access',
				__( 'Sorry, you are not allowed to do that.', 'zipwp-images', 'astra-sites' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}
		return true;
	}


	/**
	 * Load all the required files in the importer.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function register_route() {

		register_rest_route(
			$this->get_api_namespace(),
			'/images/',
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'get_images' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'keywords'    => array(
							'type'     => 'string',
							'required' => true,
						),
						'per_page'    => array(
							'type'     => 'string',
							'required' => false,
						),
						'page'        => array(
							'type'     => 'string',
							'required' => false,
						),
						'orientation' => array(
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_text_field',
							'required'          => false,
						),
						'engine'      => array(
							'type'              => 'string',
							'required'          => false,
							'sanitize_callback' => 'sanitize_text_field',
						),
						'filter'      => array(
							'type'              => 'string',
							'required'          => false,
							'sanitize_callback' => 'sanitize_text_field',
						),
						'color'       => array(
							'type'              => 'string',
							'required'          => false,
							'sanitize_callback' => 'sanitize_text_field',
						),
					),
				),
			)
		);
	}

	/**
	 * Get Images.
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return mixed
	 */
	public function get_images( $request ) {

		$nonce = $request->get_header( 'X-WP-Nonce' );

		// Verify the nonce.
		if ( ! wp_verify_nonce( sanitize_text_field( (string) $nonce ), 'wp_rest' ) ) {
			wp_send_json_error(
				array(
					'data'   => __( 'Nonce verification failed.', 'zipwp-images', 'astra-sites' ),
					'status' => false,

				)
			);
		}

		$api_endpoint = $this->get_api_domain() . 'images/';

		$post_data = array(
			'keywords'    => isset( $request['keywords'] ) && ! empty( $request['keywords'] ) ? [ $request['keywords'] ] : [ 'flowers' ],
			'per_page'    => isset( $request['per_page'] ) ? $request['per_page'] : 20,
			'page'        => isset( $request['page'] ) ? sanitize_text_field( $request['page'] ) : '1',
			// Expected orientation values are all, landscape, portrait.
			'orientation' => isset( $request['orientation'] ) ? sanitize_text_field( $request['orientation'] ) : '',
			'color'       => isset( $request['color'] ) ? sanitize_text_field( $request['color'] ) : '',
			// Expected filter values are newest, popular.
			'filter'      => isset( $request['filter'] ) ? sanitize_text_field( $request['filter'] ) : 'popular',
			'engine'      => isset( $request['engine'] ) ? sanitize_text_field( $request['engine'] ) : 'pexels',
			'details'     => true,
		);

		switch ( $post_data['engine'] ) {

			case 'pexels':
				// sort=popular.
				$post_data['filter'] = 'popular' === $post_data['filter'] ? 'popular' : 'desc';
				break;

			case 'pixabay':
				// order=popular.
				$post_data['filter'] = 'popular' === $post_data['filter'] ? 'popular' : 'latest';
				break;

		}

		$request_args = array(
			'body'    => wp_json_encode( $post_data ),
			'headers' => $this->get_api_headers(),
			'timeout' => 100,
		);
		$response     = wp_safe_remote_post( $api_endpoint, $request_args ); // @phpstan-ignore-line

		if ( is_wp_error( $response ) ) {
			// There was an error in the request.
			wp_send_json_error(
				array(
					'data'   => 'Failed ' . $response->get_error_message(),
					'status' => false,

				)
			);
		}
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );
		if ( 200 === $response_code ) {
			$response_data = json_decode( $response_body, true );

			// Get image sizes and add to the each image.
			$images = is_array( $response_data ) && isset( $response_data['images'] ) ? $response_data['images'] : [];
			foreach ( $images as $key => $image ) {
				$images[ $key ]['sizes'] = $this->get_image_size( $image );
			}

			wp_send_json_success(
				array(
					'data'   => $images,
					'status' => true,
				)
			);

		} else {

			wp_send_json_error(
				array(
					'data'   => 'Failed',
					'status' => false,

				)
			);
		}
	}

	/**
	 * Download and save the image in the media library.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function zipwp_insert_image() {
		// Verify Nonce.
		check_ajax_referer( 'zipwp-images', '_ajax_nonce' );

		if ( ! current_user_can( 'upload_files' ) ) {
			wp_send_json_error( __( 'You are not allowed to perform this action', 'zipwp-images', 'astra-sites' ) );
		}

		$url      = isset( $_POST['url'] ) ? sanitize_url( $_POST['url'] ) : false; // phpcs:ignore -- We need to remove this ignore once the WPCS has released this issue fix - https://github.com/WordPress/WordPress-Coding-Standards/issues/2189.
		$name     = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : false;
		$photo_id = isset( $_POST['id'] ) ? absint( sanitize_key( $_POST['id'] ) ) : 0;

		if ( 0 === $photo_id ) {
			wp_send_json_error( __( 'Need to send photo ID', 'zipwp-images', 'astra-sites' ) );
		}

		if ( false === $url ) {
			wp_send_json_error( __( 'Need to send URL of the image to be downloaded', 'zipwp-images', 'astra-sites' ) );
		}

		$image  = '';
		$result = array();

		$name  = preg_replace( '/\.[^.]+$/', '', (string) $name ) . '-' . $photo_id . '.jpg';
		$image = $this->create_image_from_url( $url, $name, (string) $photo_id );

		if ( empty( $image ) ) {
			wp_send_json_error( __( 'Could not download the image.', 'zipwp-images', 'astra-sites' ) );
		}

		$image                    = intval( $image );
		$result['attachmentData'] = wp_prepare_attachment_for_js( $image );

		if ( did_action( 'elementor/loaded' ) ) {
			$result['data'] = $this->get_attachment_data( $image );
		}

		// Save downloaded image reference to an option.
		if ( 0 !== $photo_id ) {
			$saved_images = get_option( 'zipwp-images-saved-images', array() );

			if ( empty( $saved_images ) ) {
				$saved_images = array();
			}

			$saved_images[] = $photo_id;
			update_option( 'zipwp-images-saved-images', $saved_images, 'no' );
		}

		$result['updated-saved-images'] = get_option( 'zipwp-images-saved-images', array() );

		wp_send_json_success( $result );
	}

	/**
	 * Create the image and return the new media upload id.
	 *
	 * @param String $url URL to pixabay image.
	 * @param String $name Name to pixabay image.
	 * @param String $photo_id Photo ID to pixabay image.
	 * @param String $description Description to pixabay image.
	 * @see http://codex.wordpress.org/Function_Reference/wp_insert_attachment#Example
	 *
	 * @return mixed
	 */
	public function create_image_from_url( $url, $name, $photo_id, $description = '' ) {
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$file_array         = array();
		$file_array['name'] = wp_basename( $name );

		// Download file to temp location.
		$file_array['tmp_name'] = download_url( $url );

		// If error storing temporarily, return the error.
		if ( is_wp_error( $file_array['tmp_name'] ) ) {
			return $file_array;
		}

		// Do the validation and storage stuff.
		$id = media_handle_sideload( $file_array, 0, null );

		// If error storing permanently, unlink.
		if ( is_wp_error( $id ) ) {
			@unlink( $file_array['tmp_name'] ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_unlink -- Deleting the file from temp location.
			return $id;
		}

		$alt = ( '' === $description ) ? $name : $description;

		// Store the original attachment source in meta.
		add_post_meta( $id, '_source_url', $url );

		update_post_meta( $id, 'zipwp-images', $photo_id );
		update_post_meta( $id, '_wp_attachment_image_alt', $alt );
		return $id;
	}


	/**
	 * Import Image.
	 *
	 * @since  1.0.0
	 * @param int $image Downloaded Image id.
	 * @return array<string, array<int, array<string, mixed>>>
	 */
	public function get_attachment_data( $image ) {
		if ( empty( $image ) || ! class_exists( 'Elementor\Utils' ) ) {
			return array();
		}

		return array(
			'content' => array(
				array(
					'id'       => \Elementor\Utils::generate_random_string(),
					'elType'   => 'section',
					'settings' => array(),
					'isInner'  => false,
					'elements' => array(
						array(
							'id'       => \Elementor\Utils::generate_random_string(),
							'elType'   => 'column',
							'elements' => array(
								array(
									'id'         => \Elementor\Utils::generate_random_string(),
									'elType'     => 'widget',
									'settings'   => array(
										'image'      => array(
											'url' => wp_get_attachment_url( $image ),
											'id'  => $image,
										),
										'image_size' => 'full',
									),
									'widgetType' => 'image',
								),
							),
							'isInner'  => false,
						),
					),
				),
			),
		);
	}

	/**
	 * Image size.
	 *
	 * @since 1.0.0
	 * @param array<string, array<string, mixed>> $image Image Array.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function get_image_size( $image ) {
		$sizes = array();

		if ( empty( $image['details'] ) ) {
			return $sizes;
		}

		switch ( $image['engine'] ) {
			case 'pexels':
				$available_image_sizes = $image['details']['src'];

				if ( is_array( $available_image_sizes ) ) {
					foreach ( $available_image_sizes as $size_key => $url ) {
						$dimensions = $this->get_image_dimensions( $url );
						$value      = array(
							'id'     => $size_key,
							'url'    => $url,
							'width'  => $dimensions['width'],
							'height' => $dimensions['height'],
						);
						$sizes[]    = $value;
					}
				}

				return $sizes;
			case 'pixabay':
				$sizes = array(
					array(
						'id'     => 'original',
						'url'    => $image['details']['largeImageURL'],
						'width'  => $image['details']['webformatWidth'],
						'height' => $image['details']['webformatHeight'],
					),
					array(
						'id'     => 'medium',
						'url'    => $image['details']['webformatURL'],
						'width'  => $image['details']['webformatWidth'],
						'height' => $image['details']['webformatHeight'],
					),
					array(
						'id'     => 'small',
						'url'    => $image['details']['previewURL'],
						'width'  => $image['details']['previewWidth'],
						'height' => $image['details']['previewHeight'],
					),
				);

				return $sizes;
			default:
				return $sizes;
		}
	}

	/**
	 * Get width and height of the image.
	 *
	 * @since 1.0.0
	 * @param string $url Image URL.
	 * @return array<string, array<string, string>|string>
	 */
	public function get_image_dimensions( $url ) {
		$clean_url = esc_url_raw( $url );
		parse_str( explode( '?', $clean_url )[1], $query_params );
		return array(
			'width'  => $query_params['w'] ?? '',
			'height' => $query_params['h'] ?? '',
		);
	}
}

/**
 * Kicking this off by calling 'get_instance()' method
 */
Zipwp_Images_Api::get_instance();

