<?php
/**
 * Plugin Name: Starter Templates Importer
 * Description: Library which interacts with Starter Templates and provide multiple useful modules.
 * Author: Brainstorm Force
 * Version: 1.0.17
 * License: GPL v2
 * Text Domain: st-importer
 *
 * @package Starter Templates Importer
 */

// Exit if Starter Templates Importer is already loaded.
if ( defined( 'ST_IMPORTER_DIR' ) ) {
	return;
}

// Load the Starter Templates Importer Loader.
if ( apply_filters( 'starter_templates_importer_load_library', true ) ) {
	require_once 'st-importer-loader.php';
}
