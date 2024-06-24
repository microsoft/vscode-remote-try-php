<?php
/**
 * AI Builder Compatibility for 3rd party plugins.
 *
 * @package AI Builder
 * @since 1.0.11
 */

if ( ! class_exists( 'Ai_Builder_Compatibility' ) ) :

	/**
	 * AI Builder Compatibility
	 *
	 * @since 1.0.11
	 */
	class Ai_Builder_Compatibility {

		/**
		 * Instance
		 *
		 * @access private
		 * @var object Class object.
		 * @since 1.0.11
		 */
		private static $instance;

		/**
		 * Initiator
		 *
		 * @since 1.0.11
		 * @return object initialized object of class.
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @since 1.0.11
		 */
		public function __construct() {

			// Plugin - Spectra.
			require_once AI_BUILDER_DIR . 'inc/compatibility/uag/ai-builder-compatibility-uag.php';

			// Plugin - WooCommerce.
			require_once AI_BUILDER_DIR . 'inc/compatibility/surecart/ai-builder-compatibility-surecart.php';

			// Plugin - Cartflows.
			require_once AI_BUILDER_DIR . 'inc/compatibility/cartflows/ai-builder-compatibility-cartflows.php';

			// Plugin - Suretriggers.
			require_once AI_BUILDER_DIR . 'inc/compatibility/suretriggers/ai-builder-compatibility-suretriggers.php';
		}

	}

	/**
	 * Kicking this off by calling 'instance()' method
	 */
	Ai_Builder_Compatibility::instance();

endif;


