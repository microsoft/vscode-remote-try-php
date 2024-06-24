<?php
/**
 * Plugin Name: Gutenberg Starter Templates
 * Plugin URI: https://wpastra.com/
 * Description: Gutenberg single page templates, and blocks library to imported your website easily.
 * Version: 2.2.3
 * Author: Brainstorm Force
 * Author URI: https://www.brainstormforce.com
 * Text Domain: ast-block-templates
 *
 * @package Ast Block Templates
 */

/**
 * Set constants.
 */
if ( defined( 'Ast_Block_Templates' ) ) {
	return;
}

if ( apply_filters( 'ast_block_templates_disable', false ) ) {
	return;
}

// Set constants.
if ( ! defined( 'AST_BLOCK_TEMPLATES_LIBRARY_URL' ) ) {
	define( 'AST_BLOCK_TEMPLATES_LIBRARY_URL', trailingslashit( apply_filters( 'ast_block_templates_library_url', 'https://websitedemos.net/' ) ) );
}

define( 'AST_BLOCK_TEMPLATES_VER', '2.2.3' );
define( 'AST_BLOCK_TEMPLATES_FILE', __FILE__ );
define( 'AST_BLOCK_TEMPLATES_BASE', plugin_basename( AST_BLOCK_TEMPLATES_FILE ) );
define( 'AST_BLOCK_TEMPLATES_DIR', plugin_dir_path( AST_BLOCK_TEMPLATES_FILE ) );
define( 'AST_BLOCK_TEMPLATES_URI', plugins_url( '/', AST_BLOCK_TEMPLATES_FILE ) );

if ( ! defined( 'AST_BLOCK_TEMPLATES_JSON_DIR' ) ) {
	$upload_dir = wp_get_upload_dir();
	define( 'AST_BLOCK_TEMPLATES_JSON_DIR', trailingslashit( $upload_dir['basedir'] ) . 'ast-block-templates-json/' );
}

require_once 'class-ast-block-templates.php';
