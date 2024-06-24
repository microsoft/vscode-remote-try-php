<?php
/**
 * Starter Templates WXR Importer - Module.
 *
 * This file is used to register and manage the Zip AI Modules.
 *
 * @package Starter Templates Importer
 */

namespace STImporter\Importer\WXR_Importer;

use STImporter\Importer\ST_Importer_Helper;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * The Module Class.
 */
class ST_WXR_Importer {

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
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		add_filter( 'upload_mimes', array( $this, 'custom_upload_mimes' ) ); //phpcs:ignore WordPressVIPMinimum.Hooks.RestrictedHooks.upload_mimes -- Added this to allow upload of SVG files.
		add_action( 'wp_ajax_astra-wxr-import', array( $this, 'sse_import' ) );
		add_filter( 'wxr_importer.pre_process.user', '__return_null' );
		add_filter( 'wp_import_post_data_processed', array( $this, 'pre_post_data' ), 10, 2 );
		add_filter( 'wxr_importer.pre_process.post', array( $this, 'pre_process_post' ), 10, 4 );
		if ( version_compare( get_bloginfo( 'version' ), '5.1.0', '>=' ) ) {
			add_filter( 'wp_check_filetype_and_ext', array( $this, 'real_mime_types_5_1_0' ), 10, 5 );
		} else {
			add_filter( 'wp_check_filetype_and_ext', array( $this, 'real_mime_types' ), 10, 4 );
		}
		add_action( 'wp_import_insert_post', array( $this, 'after_imported_post' ), 10, 4 );

	}

	/**
	 * After Post import action.
	 *
	 * @param int                   $post_id post id.
	 * @param int                   $original_id post id.
	 * @param array<string, string> $postdata post id.
	 * @param array<string, string> $data post id.
	 *
	 * @return void
	 */
	public function after_imported_post( $post_id, $original_id, $postdata, $data ) {
		if ( in_array( $data['post_type'], [ 'post', 'page' ], true ) && 'ai' === get_transient( 'astra_sites_current_import_template_type' ) ) {
			$imports                         = get_option(
				'astra_sites_ai_imports',
				array(
					'post' => [],
					'page' => [],
				)
			);
			$imports[ $data['post_type'] ][] = $post_id;
			update_option( 'astra_sites_ai_imports', $imports );
		}
	}

	/**
	 * Add .xml files as supported format in the uploader.
	 *
	 * @since 1.1.5 Added SVG file support.
	 *
	 * @since 1.0.0
	 *
	 * @param array $mimes Already supported mime types.
	 */
	public function custom_upload_mimes( $mimes ) {

		// Allow SVG files.
		$mimes['svg']  = 'image/svg+xml';
		$mimes['svgz'] = 'image/svg+xml';

		// Allow XML files.
		$mimes['xml'] = 'text/xml';

		// Allow JSON files.
		$mimes['json'] = 'application/json';

		return $mimes;
	}

	/**
	 * Constructor.
	 *
	 * @since  1.1.0
	 * @since  1.4.0 The `$xml_url` was added.
	 *
	 * @param  string $xml_url XML file URL.
	 */
	public function sse_import( $xml_url = '' ) {

		if ( wp_doing_ajax() ) {

			// Verify Nonce.
			check_ajax_referer( 'astra-sites', '_ajax_nonce' );

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error(
					array(
						'error' => __( 'Permission Denied!', 'st-importer', 'astra-sites' ),
					)
				);
			}

			// Start the event stream.
			header( 'Content-Type: text/event-stream, charset=UTF-8' );
			// Turn off PHP output compression.
			$previous = error_reporting( error_reporting() ^ E_WARNING ); //phpcs:ignore WordPress.PHP.DevelopmentFunctions, WordPress.PHP.DiscouragedPHPFunctions -- 3rd party library.
			ini_set( 'output_buffering', 'off' ); //phpcs:ignore WordPress.PHP.IniSet.Risky -- 3rd party library.
			ini_set( 'zlib.output_compression', false ); //phpcs:ignore WordPress.PHP.IniSet.Risky -- 3rd party library.
			error_reporting( $previous ); //phpcs:ignore WordPress.PHP.DevelopmentFunctions, WordPress.PHP.DiscouragedPHPFunctions -- 3rd party library.

			if ( $GLOBALS['is_nginx'] ) {
				// Setting this header instructs Nginx to disable fastcgi_buffering
				// and disable gzip for this request.
				header( 'X-Accel-Buffering: no' );
				header( 'Content-Encoding: none' );
			}

			// 2KB padding for IE.
			echo esc_html( ':' . str_repeat( ' ', 2048 ) . "\n\n" );
		}

		$xml_id = isset( $_REQUEST['xml_id'] ) ? absint( $_REQUEST['xml_id'] ) : '';
		if ( ! empty( $xml_id ) ) {
			$xml_url = get_attached_file( $xml_id );
		}

		if ( empty( $xml_url ) ) {
			exit;
		}

		// Time to run the import!
		set_time_limit( 0 );

		// Ensure we're not buffered.
		wp_ob_end_flush_all();
		flush();

		do_action( 'astra_sites_before_sse_import' );

		// Enable default GD library.
		add_filter( 'wp_image_editors', array( $this, 'enable_wp_image_editor_gd' ) );

		// Change GUID image URL.
		add_filter( 'wxr_importer.pre_process.post', array( $this, 'fix_image_duplicate_issue' ), 10, 4 );

		// Are we allowed to create users?
		add_filter( 'wxr_importer.pre_process.user', '__return_null' );

		// Keep track of our progress.
		add_action( 'wxr_importer.processed.post', array( $this, 'imported_post' ), 10, 2 );
		add_action( 'wxr_importer.process_failed.post', array( $this, 'imported_post' ), 10, 2 );
		add_action( 'wxr_importer.process_already_imported.post', array( $this, 'already_imported_post' ), 10, 2 );
		add_action( 'wxr_importer.process_skipped.post', array( $this, 'already_imported_post' ), 10, 2 );
		add_action( 'wxr_importer.processed.comment', array( $this, 'imported_comment' ) );
		add_action( 'wxr_importer.process_already_imported.comment', array( $this, 'imported_comment' ) );
		add_action( 'wxr_importer.processed.term', array( $this, 'imported_term' ) );
		add_action( 'wxr_importer.process_failed.term', array( $this, 'imported_term' ) );
		add_action( 'wxr_importer.process_already_imported.term', array( $this, 'imported_term' ) );
		add_action( 'wxr_importer.processed.user', array( $this, 'imported_user' ) );
		add_action( 'wxr_importer.process_failed.user', array( $this, 'imported_user' ) );

		// Keep track of our progress.
		add_action( 'wxr_importer.processed.post', array( $this, 'track_post' ), 10, 2 );
		add_action( 'wxr_importer.processed.term', array( $this, 'track_term' ) );

		// Flush once more.
		flush();

		$importer = $this->get_importer();
		$response = $importer->import( $xml_url );

		// Let the browser know we're done.
		$complete = array(
			'action' => 'complete',
			'error'  => false,
		);
		if ( is_wp_error( $response ) ) {
			$complete['error'] = $response->get_error_message();
		}

		$this->emit_sse_message( $complete );
		if ( wp_doing_ajax() ) {
			exit;
		}
	}

	/**
	 * Set GUID as per the attachment URL which avoid duplicate images issue due to the different GUID.
	 *
	 * @param array $data Post data. (Return empty to skip).
	 * @param array $meta Meta data.
	 * @param array $comments Comments on the post.
	 * @param array $terms Terms on the post.
	 */
	public function fix_image_duplicate_issue( $data, $meta, $comments, $terms ) {

		if ( empty( $data ) ) {
			return $data;
		}

		$remote_url   = ! empty( $data['attachment_url'] ) ? $data['attachment_url'] : $data['guid'];
		$data['guid'] = $remote_url;

		return $data;
	}

		/**
		 * Enable the WP_Image_Editor_GD library.
		 *
		 * @since 2.2.3
		 * @param  array $editors Image editors library list.
		 * @return array
		 */
	public function enable_wp_image_editor_gd( $editors ) {
		$gd_editor = 'WP_Image_Editor_GD';
		$editors   = array_diff( $editors, array( $gd_editor ) );
		array_unshift( $editors, $gd_editor );
		return $editors;
	}

	/**
	 * Track Imported Term
	 *
	 * @param  int $term_id Term ID.
	 * @return void
	 */
	public function track_term( $term_id ) {
		$term = get_term( $term_id );
		update_term_meta( $term_id, '_astra_sites_imported_term', true );
	}

	/**
	 * Pre Post Data
	 *
	 * @since 2.1.0
	 *
	 * @param  array $postdata Post data.
	 * @param  array $data     Post data.
	 * @return array           Post data.
	 */
	public function pre_post_data( $postdata, $data ) {

		// Skip GUID field which point to the https://websitedemos.net.
		$postdata['guid'] = '';

		return $postdata;
	}

	/**
	 * Pre Process Post
	 *
	 * @since 1.2.12
	 *
	 * @param array $data Post data. (Return empty to skip.).
	 * @param array $meta Meta data.
	 * @param array $comments Comments on the post.
	 * @param array $terms Terms on the post.
	 */
	public function pre_process_post( $data, $meta, $comments, $terms ) {

		if ( isset( $data['post_content'] ) ) {

			$meta_data = wp_list_pluck( $meta, 'key' );

			$is_attachment          = ( 'attachment' === $data['post_type'] ) ? true : false;
			$is_elementor_page      = in_array( '_elementor_version', $meta_data, true );
			$is_beaver_builder_page = in_array( '_fl_builder_enabled', $meta_data, true );
			$is_brizy_page          = in_array( 'brizy_post_uid', $meta_data, true );

			$disable_post_content = apply_filters( 'astra_sites_pre_process_post_disable_content', ( $is_attachment || $is_elementor_page || $is_beaver_builder_page || $is_brizy_page ) );

			// If post type is `attachment OR
			// If page contain Elementor, Brizy or Beaver Builder meta then skip this page.
			if ( $disable_post_content ) {
				$data['post_content'] = '';
			} else {
				/**
				 * Gutenberg Content Data Fix
				 *
				 * Gutenberg encode the page content. In import process the encoded characterless e.g. <, > are
				 * decoded into HTML tag and it break the Gutenberg render markup.
				 *
				 * Note: We have not check the post is created with Gutenberg or not. We have imported other sites
				 * and confirm that this works for every other page builders too.
				 */
				$data['post_content'] = wp_slash( $data['post_content'] );
			}
		}

		/**
		 * Setting the publish date to current date.
		 */
		if ( isset( $data['post_date'] ) ) {
			$post_modified         = current_time( 'mysql' );
			$post_modified_gmt     = current_time( 'mysql', 1 );
			$data['post_date']     = $post_modified;
			$data['post_date_gmt'] = $post_modified_gmt;
		}

		return $data;
	}

	/**
	 * Different MIME type of different PHP version
	 *
	 * Filters the "real" file type of the given file.
	 *
	 * @since 1.2.9
	 *
	 * @param array  $defaults File data array containing 'ext', 'type', and
	 *                                          'proper_filename' keys.
	 * @param string $file                      Full path to the file.
	 * @param string $filename                  The name of the file (may differ from $file due to
	 *                                          $file being in a tmp directory).
	 * @param array  $mimes                     Key is the file extension with value as the mime type.
	 */
	public function real_mime_types( $defaults, $file, $filename, $mimes ) {
		return $this->real_mimes( $defaults, $filename, $file );
	}

	/**
	 * Real Mime Type
	 *
	 * @since 1.2.15
	 *
	 * @param array  $defaults File data array containing 'ext', 'type', and
	 *                                          'proper_filename' keys.
	 * @param string $filename                  The name of the file (may differ from $file due to
	 *                                          $file being in a tmp directory).
	 * @param string $file file content.
	 */
	public function real_mimes( $defaults, $filename, $file ) {

		// Set EXT and real MIME type only for the file name `wxr.xml`.
		if ( strpos( $filename, 'wxr' ) !== false ) {
			$defaults['ext']  = 'xml';
			$defaults['type'] = 'text/xml';
		}

		// Set EXT and real MIME type only for the file name `wpforms.json` or `wpforms-{page-id}.json`.
		if ( ( strpos( $filename, 'wpforms' ) !== false ) || ( strpos( $filename, 'cartflows' ) !== false ) || ( strpos( $filename, 'spectra' ) !== false ) ) {
			$defaults['ext']  = 'json';
			$defaults['type'] = 'text/plain';
		}

		if ( 'svg' === pathinfo( $filename, PATHINFO_EXTENSION ) ) {
			// Perform SVG sanitization using the sanitize_svg function.
			$svg_content           = file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			$sanitized_svg_content = $this->sanitize_svg( $svg_content );
			// phpcs:disable WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents
			file_put_contents( $file, $sanitized_svg_content );
			// phpcs:enable WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents

			// Update mime type and extension.
			$defaults['type'] = 'image/svg+xml';
			$defaults['ext']  = 'svg';
		}

		return $defaults;
	}
	/**
	 * Different MIME type of different PHP version
	 *
	 * Filters the "real" file type of the given file.
	 *
	 * @since 1.2.9
	 *
	 * @param array  $defaults File data array containing 'ext', 'type', and
	 *                                          'proper_filename' keys.
	 * @param string $file                      Full path to the file.
	 * @param string $filename                  The name of the file (may differ from $file due to
	 *                                          $file being in a tmp directory).
	 * @param array  $mimes                     Key is the file extension with value as the mime type.
	 * @param string $real_mime                Real MIME type of the uploaded file.
	 */
	public function real_mime_types_5_1_0( $defaults, $file, $filename, $mimes, $real_mime ) {
		return $this->real_mimes( $defaults, $filename, $file );
	}

	/**
	 * Sanitizes SVG Code string.
	 *
	 * @param string $original_content SVG code to sanitize.
	 * @return string|bool
	 * @since 1.0.7
	 * @phpstan-ignore-next-line
	 * */
	public function sanitize_svg( $original_content ) {

		if ( ! $original_content ) {
			return '';
		}

		// Define allowed tags and attributes.
		$allowed_tags = array(
			'a',
			'circle',
			'clippath',
			'defs',
			'style',
			'desc',
			'ellipse',
			'fegaussianblur',
			'filter',
			'foreignobject',
			'g',
			'image',
			'line',
			'lineargradient',
			'marker',
			'mask',
			'metadata',
			'path',
			'pattern',
			'polygon',
			'polyline',
			'radialgradient',
			'rect',
			'stop',
			'svg',
			'switch',
			'symbol',
			'text',
			'textpath',
			'title',
			'tspan',
			'use',
		);

		$allowed_attributes = array(
			'class',
			'clip-path',
			'clip-rule',
			'fill',
			'fill-opacity',
			'fill-rule',
			'filter',
			'id',
			'mask',
			'opacity',
			'stroke',
			'stroke-dasharray',
			'stroke-dashoffset',
			'stroke-linecap',
			'stroke-linejoin',
			'stroke-miterlimit',
			'stroke-opacity',
			'stroke-width',
			'style',
			'systemlanguage',
			'transform',
			'href',
			'xlink:href',
			'xlink:title',
			'cx',
			'cy',
			'r',
			'requiredfeatures',
			'clippathunits',
			'type',
			'rx',
			'ry',
			'color-interpolation-filters',
			'stddeviation',
			'filterres',
			'filterunits',
			'height',
			'primitiveunits',
			'width',
			'x',
			'y',
			'font-size',
			'display',
			'font-family',
			'font-style',
			'font-weight',
			'text-anchor',
			'marker-end',
			'marker-mid',
			'marker-start',
			'x1',
			'x2',
			'y1',
			'y2',
			'gradienttransform',
			'gradientunits',
			'spreadmethod',
			'markerheight',
			'markerunits',
			'markerwidth',
			'orient',
			'preserveaspectratio',
			'refx',
			'refy',
			'viewbox',
			'maskcontentunits',
			'maskunits',
			'd',
			'patterncontentunits',
			'patterntransform',
			'patternunits',
			'points',
			'fx',
			'fy',
			'offset',
			'stop-color',
			'stop-opacity',
			'xmlns',
			'xmlns:se',
			'xmlns:xlink',
			'xml:space',
			'method',
			'spacing',
			'startoffset',
			'dx',
			'dy',
			'rotate',
			'textlength',
		);

		$is_encoded = false;

		$needle = "\x1f\x8b\x08";
		// phpcs:disable PHPCompatibility.ParameterValues.NewIconvMbstringCharsetDefault.NotSet
		if ( function_exists( 'mb_strpos' ) ) {
			$is_encoded = 0 === mb_strpos( $original_content, $needle );
		} else {
			$is_encoded = 0 === strpos( $original_content, $needle );
		}
		// phpcs:enable PHPCompatibility.ParameterValues.NewIconvMbstringCharsetDefault.NotSet

		// phpcs:disable WordPress.PHP.YodaConditions.NotYoda
		if ( $is_encoded ) {
			$original_content = gzdecode( $original_content );
			if ( $original_content === false ) {
				return '';
			}
		}
		// phpcs:enable WordPress.PHP.YodaConditions.NotYoda

		// Strip php tags.
		$content = preg_replace( '/<\?(=|php)(.+?)\?>/i', '', $original_content );
		$content = preg_replace( '/<\?(.*)\?>/Us', '', $content );
		$content = preg_replace( '/<\%(.*)\%>/Us', '', $content );

		if ( ( false !== strpos( $content, '<?' ) ) || ( false !== strpos( $content, '<%' ) ) ) {
			return '';
		}

		// Strip comments.
		$content = preg_replace( '/<!--(.*)-->/Us', '', $content );
		$content = preg_replace( '/\/\*(.*)\*\//Us', '', $content );

		if ( ( false !== strpos( $content, '<!--' ) ) || ( false !== strpos( $content, '/*' ) ) ) {
			return '';
		}

		// Strip line breaks.
		$content = preg_replace( '/\r|\n/', '', $content );

		// Find the start and end tags so we can cut out miscellaneous garbage.
		$start = strpos( $content, '<svg' );
		$end   = strrpos( $content, '</svg>' );
		if ( false === $start || false === $end ) {
			return '';
		}

		$content = substr( $content, $start, ( $end - $start + 6 ) );

		// If the server's PHP version is 8 or up, make sure to disable the ability to load external entities.
		$php_version_under_eight = version_compare( PHP_VERSION, '8.0.0', '<' );
		if ( $php_version_under_eight ) {
			// phpcs:disable Generic.PHP.DeprecatedFunctions.Deprecated
			$libxml_disable_entity_loader = libxml_disable_entity_loader( true );
			// phpcs:enable Generic.PHP.DeprecatedFunctions.Deprecated
		}
		// Suppress the errors.
		$libxml_use_internal_errors = libxml_use_internal_errors( true );

		// Create DOMDocument instance.
		$dom = new \DOMDocument();
		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$dom->formatOutput        = false;
		$dom->preserveWhiteSpace  = false;
		$dom->strictErrorChecking = false;
		// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

		$open_svg = ! ! $content ? $dom->loadXML( $content ) : false;
		if ( ! $open_svg ) {
			return '';
		}

		// Strip Doctype.
		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		foreach ( $dom->childNodes as $child ) {
			if ( XML_DOCUMENT_TYPE_NODE === $child->nodeType && ! ! $child->parentNode ) {
				$child->parentNode->removeChild( $child );
		// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			}
		}

		// Sanitize elements.
		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase, WordPress.PHP.StrictInArray
		$elements = $dom->getElementsByTagName( '*' );
		for ( $index = $elements->length - 1; $index >= 0; $index-- ) {
			$current_element = $elements->item( $index );
			if ( ! in_array( strtolower( $current_element->tagName ), $allowed_tags ) ) {
				$current_element->parentNode->removeChild( $current_element );
				// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				continue;
			}

			// Validate allowed attributes.
			for ( $i = $current_element->attributes->length - 1; $i >= 0; $i-- ) {
				$attr_name           = $current_element->attributes->item( $i )->name;
				$attr_name_lowercase = strtolower( $attr_name );
				if ( ! in_array( $attr_name_lowercase, $allowed_attributes ) &&
					! preg_match( '/^aria-/', $attr_name_lowercase ) &&
					! preg_match( '/^data-/', $attr_name_lowercase ) ) {
					$current_element->removeAttribute( $attr_name );
					continue;
				}

				$attr_value = $current_element->attributes->item( $i )->value;
				if ( ! empty( $attr_value ) &&
					( preg_match( '/^((https?|ftp|file):)?\/\//i', $attr_value ) ||
					preg_match( '/base64|data|(?:java)?script|alert\(|window\.|document/i', $attr_value ) ) ) {
					$current_element->removeAttribute( $attr_name );
					continue;
				}
			}

			// Strip xlink:href.
			$xlink_href = $current_element->getAttributeNS( 'http://www.w3.org/1999/xlink', 'href' );
			if ( $xlink_href && strpos( $xlink_href, '#' ) !== 0 ) {
				$current_element->removeAttributeNS( 'http://www.w3.org/1999/xlink', 'href' );
			}

			// Strip use tag with external references.
			// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			if ( strtolower( $current_element->tagName ) === 'use' ) {
				$xlink_href = $current_element->getAttributeNS( 'http://www.w3.org/1999/xlink', 'href' );
				if ( $current_element->parentNode && $xlink_href && strpos( $xlink_href, '#' ) !== 0 ) {
					$current_element->parentNode->removeChild( $current_element );
					// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				}
			}
		}

		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		$sanitized = $dom->saveXML( $dom->documentElement, LIBXML_NOEMPTYTAG );
		// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

		// Restore defaults.
		if ( $php_version_under_eight && isset( $libxml_disable_entity_loader ) ) {
			// phpcs:disable Generic.PHP.DeprecatedFunctions.Deprecated
			libxml_disable_entity_loader( $libxml_disable_entity_loader );
			// phpcs:enable Generic.PHP.DeprecatedFunctions.Deprecated, WordPress.PHP.StrictInArray
		}
		libxml_use_internal_errors( $libxml_use_internal_errors );

		return $sanitized;
	}

	/**
	 * Download File Into Uploads Directory
	 *
	 * @since 1.0.0 Added $overrides argument to override the uploaded file actions.
	 *
	 * @param  string $file Download File URL.
	 * @param  array  $overrides Upload file arguments.
	 * @param  int    $timeout_seconds Timeout in downloading the XML file in seconds.
	 * @return array        Downloaded file data.
	 */
	public static function download_file( $file = '', $overrides = array(), $timeout_seconds = 300 ) {

		// Gives us access to the download_url() and wp_handle_sideload() functions.
		require_once ABSPATH . 'wp-admin/includes/file.php';

		// Download file to temp dir.
		$temp_file = download_url( $file, $timeout_seconds );

		// WP Error.
		if ( is_wp_error( $temp_file ) ) {
			return array(
				'success' => false,
				'data'    => $temp_file->get_error_message(),
			);
		}

		// Array based on $_FILE as seen in PHP file uploads.
		$file_args = array(
			'name'     => basename( $file ),
			'tmp_name' => $temp_file,
			'error'    => 0,
			'size'     => filesize( $temp_file ),
		);

		$defaults = array(

			// Tells WordPress to not look for the POST form
			// fields that would normally be present as
			// we downloaded the file from a remote server, so there
			// will be no form fields
			// Default is true.
			'test_form'   => false,

			// Setting this to false lets WordPress allow empty files, not recommended.
			// Default is true.
			'test_size'   => true,

			// A properly uploaded file will pass this test. There should be no reason to override this one.
			'test_upload' => true,

			'mimes'       => array(
				'xml'  => 'text/xml',
				'json' => 'text/plain',
			),
		);

		$overrides = wp_parse_args( $overrides, $defaults );

		// Move the temporary file into the uploads directory.
		$results = wp_handle_sideload( $file_args, $overrides );

		if ( isset( $results['error'] ) ) {
			return array(
				'success' => false,
				'data'    => $results,
			);
		}

		// Success.
		return array(
			'success' => true,
			'data'    => $results,
		);
	}

	/**
	 * Start the xml import.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $path Absolute path to the XML file.
	 * @param  int    $post_id Uploaded XML file ID.
	 */
	public static function get_xml_data( $path, $post_id ) {

		$args = array(
			'action'      => 'astra-wxr-import',
			'id'          => '1',
			'_ajax_nonce' => wp_create_nonce( 'astra-sites' ),
			'xml_id'      => $post_id,
		);
		$url  = add_query_arg( urlencode_deep( $args ), admin_url( 'admin-ajax.php', 'relative' ) );

		$data = self::get_data( $path );

		return array(
			'count'   => array(
				'posts'    => $data->post_count,
				'media'    => $data->media_count,
				'users'    => count( $data->users ),
				'comments' => $data->comment_count,
				'terms'    => $data->term_count,
			),
			'url'     => $url,
			'strings' => array(
				'complete' => __( 'Import complete!', 'st-importer', 'astra-sites' ),
			),
		);
	}

	/**
	 * Get XML data.
	 *
	 * @since 1.0.0
	 * @param  string $url Downloaded XML file absolute URL.
	 * @return object  XML file data.
	 */
	public static function get_data( $url ) {
		$importer = self::get_importer();
		$data     = $importer->get_preliminary_information( $url );
		if ( is_wp_error( $data ) ) {
			return $data;
		}
		return $data;
	}

	/**
	 * Get Importer
	 *
	 * @since 1.0.0
	 * @return object   Importer object.
	 */
	public static function get_importer() {
		$options = apply_filters(
			'astra_sites_xml_import_options',
			array(
				'update_attachment_guids' => true,
				'fetch_attachments'       => true,
				'default_author'          => get_current_user_id(),
			)
		);

		$importer = new \WXR_Importer( $options );
		$logger   = new \WP_Importer_Logger_ServerSentEvents();

		$importer->set_logger( $logger );
		return $importer;
	}

	/**
	 * Check is valid URL
	 *
	 * @param string $url  The site URL.
	 *
	 * @since 2.7.1
	 * @return string
	 */
	public static function is_valid_wxr_url( $url = '' ) {
		if ( empty( $url ) ) {
			return false;
		}

		$parse_url = wp_parse_url( $url );
		if ( empty( $parse_url ) || ! is_array( $parse_url ) ) {
			return false;
		}

		$valid_hosts = apply_filters(
			'astra_sites_valid_url',
			array(
				'lh3.googleusercontent.com',
				'pixabay.com',
			)
		);

		$ai_site_url = get_option( 'ast_ai_import_current_url', '' );

		if ( '' !== $ai_site_url ) {
			$url           = wp_parse_url( $ai_site_url );
			$valid_hosts[] = $url ? $url['host'] : '';
		}

		$api_domain_parse_url = wp_parse_url( ST_Importer_Helper::get_api_domain() );
		$valid_hosts[]        = $api_domain_parse_url['host'];

		// Validate host.
		if ( in_array( $parse_url['host'], $valid_hosts, true ) ) {
			return true;
		}

		return false;
	}

		/**
		 * Send message when a post has been imported.
		 *
		 * @since 1.1.0
		 * @param int   $id Post ID.
		 * @param array $data Post data saved to the DB.
		 */
	public function imported_post( $id, $data ) {
		$this->emit_sse_message(
			array(
				'action' => 'updateDelta',
				'type'   => ( 'attachment' === $data['post_type'] ) ? 'media' : 'posts',
				'delta'  => 1,
			)
		);
	}

	/**
	 * Send message when a post is marked as already imported.
	 *
	 * @since 1.1.0
	 * @param array $data Post data saved to the DB.
	 */
	public function already_imported_post( $data ) {
		$this->emit_sse_message(
			array(
				'action' => 'updateDelta',
				'type'   => ( 'attachment' === $data['post_type'] ) ? 'media' : 'posts',
				'delta'  => 1,
			)
		);
	}

	/**
	 * Send message when a comment has been imported.
	 *
	 * @since 1.1.0
	 */
	public function imported_comment() {
		$this->emit_sse_message(
			array(
				'action' => 'updateDelta',
				'type'   => 'comments',
				'delta'  => 1,
			)
		);
	}

	/**
	 * Send message when a term has been imported.
	 *
	 * @since 1.1.0
	 */
	public function imported_term() {
		$this->emit_sse_message(
			array(
				'action' => 'updateDelta',
				'type'   => 'terms',
				'delta'  => 1,
			)
		);
	}

	/**
	 * Send message when a user has been imported.
	 *
	 * @since 1.1.0
	 */
	public function imported_user() {
		$this->emit_sse_message(
			array(
				'action' => 'updateDelta',
				'type'   => 'users',
				'delta'  => 1,
			)
		);
	}

	/**
	 * Emit a Server-Sent Events message.
	 *
	 * @since 1.1.0
	 * @param mixed $data Data to be JSON-encoded and sent in the message.
	 */
	public function emit_sse_message( $data ) {

		if ( wp_doing_ajax() ) {
			echo "event: message\n";
			echo 'data: ' . wp_json_encode( $data ) . "\n\n";

			// Extra padding.
			echo esc_html( ':' . str_repeat( ' ', 2048 ) . "\n\n" );
		}

		flush();
	}
	/**
	 * Track Imported Post
	 *
	 * @param  int   $post_id Post ID.
	 * @param array $data Raw data imported for the post.
	 * @return void
	 */
	public function track_post( $post_id = 0, $data = array() ) {
		ST_Importer_Helper::track_post( $post_id, $data );
	}
}
