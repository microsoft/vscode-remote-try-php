<?php
/**
 * Astra Sites Compatibility for 'LearnDash LMS'
 *
 * @see  https://www.learndash.com/
 *
 * @package Astra Sites
 * @since 1.3.13
 */

if ( ! class_exists( 'Astra_Sites_Compatibility_SFWD_LMS' ) ) :

	/**
	 * Astra_Sites_Compatibility_SFWD_LMS
	 *
	 * @since 1.3.13
	 */
	class Astra_Sites_Compatibility_SFWD_LMS {

		/**
		 * Instance
		 *
		 * @access private
		 * @var object Class object.
		 * @since 1.3.13
		 */
		private static $instance;

		/**
		 * Initiator
		 *
		 * @since 1.3.13
		 * @return object initialized object of class.
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
		 * @since 1.3.13
		 */
		public function __construct() {
			add_filter( 'astra_sites_gutenberg_batch_process_post_types', array( $this, 'set_post_types' ) );
			add_action( 'astra_sites_import_complete', array( $this, 'process_landing_pages_mapping' ) );
		}

		/**
		 * Set LearnDash Landing pages with respect to Cartflows.
		 *
		 * @since 2.3.2
		 */
		public function process_landing_pages_mapping() {
			$demo_data = Astra_Sites_File_System::get_instance()->get_demo_content();
			if ( ! isset( $demo_data['astra-post-data-mapping'] ) || ! isset( $demo_data['astra-post-data-mapping']['ld_landing_pages'] ) ) {
				return;
			}

			$index = 'ld_landing_pages';
			$posts = ( isset( $demo_data['astra-post-data-mapping'][ $index ] ) ) ? $demo_data['astra-post-data-mapping'][ $index ] : array();

			if ( ! empty( $posts ) ) {
				foreach ( $posts as $key => $post ) {

					if ( '' !== $post['landing_page'] ) {
						// Get course by Title.
						$course = Astra_Site_Options_Import::instance()->get_page_by_title( $post['course'], 'sfwd-courses' );
						// Get landing step by Title.
						$landing_page = Astra_Site_Options_Import::instance()->get_page_by_title( $post['landing_page'], 'cartflows_step' );

						if ( is_object( $course ) && is_object( $landing_page ) ) {
							if ( defined( 'WP_CLI' ) ) {
								WP_CLI::line( 'Setting LearnDash - CartFlows Landing page - ' . $course->post_title . ' - ( ' . $course->ID . ' )' );
							}

							$ld_meta                                     = get_post_meta( $course->ID, '_sfwd-courses', true );
							$ld_meta['sfwd-courses_wcf_course_template'] = $landing_page->ID;

							// Update the imported landing step to the course.
							update_post_meta( $course->ID, '_sfwd-courses', $ld_meta );
						}
					}
				}
			}
		}

		/**
		 * Set post types
		 *
		 * @since 1.3.13
		 *
		 * @param array $post_types Post types.
		 */
		public function set_post_types( $post_types = array() ) {
			return array_merge( $post_types, array( 'sfwd-courses', 'sfwd-lessons', 'sfwd-topic', 'sfwd-quiz', 'sfwd-certificates', 'sfwd-assignment' ) );
		}
	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	Astra_Sites_Compatibility_SFWD_LMS::get_instance();

endif;
