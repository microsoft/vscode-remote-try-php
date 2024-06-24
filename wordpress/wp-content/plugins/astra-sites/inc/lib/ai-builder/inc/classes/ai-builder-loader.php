<?php
/**
 * Ai Builder
 *
 * @since  1.0.0
 * @package Ai Builder
 */

namespace AiBuilder\Inc\Classes;

use AiBuilder\Inc\Traits\Instance;

/**
 * Ai_Builder
 */
class Ai_Builder_Loader {

	use Instance;

	/**
	 * Constructor.
	 *
	 * @since  1.0.0
	 */
	private function __construct() {
		$this->includes_files();
	}

	/**
	 * Load all the required files in the importer.
	 *
	 * @since  1.0.0
	 */
	private function includes_files() {

		require_once AI_BUILDER_DIR . 'inc/classes/ai-builder-importer-log.php';
		require_once AI_BUILDER_DIR . 'inc/classes/zipwp/class-ai-builder-zipwp-integration.php';
		require_once AI_BUILDER_DIR . 'inc/classes/zipwp/class-ai-builder-zipwp-api.php';
		require_once AI_BUILDER_DIR . 'inc/classes/importer/class-ai-builder-importer.php';
	}
}

/**
 * Kicking this off by calling 'get_instance()' method
 */
Ai_Builder_Loader::Instance();

