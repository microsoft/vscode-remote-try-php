<?php
/**
 * Plugin Name: Zip AI Assistant
 * Description: Library which interacts with SCS and provide multiple useful modules.
 * Author: Brainstorm Force
 * Version: 1.1.6
 * License: GPL v2
 * Text Domain: zip-ai
 *
 * @package zip-ai
 */

// Exit if Zip AI is already loaded.
if ( defined( 'ZIP_AI_DIR' ) ) {
	return;
}

// Load the Zip AI Loader.
if ( apply_filters( 'zip_ai_load_library', true ) ) {
	require_once 'loader.php';
}
