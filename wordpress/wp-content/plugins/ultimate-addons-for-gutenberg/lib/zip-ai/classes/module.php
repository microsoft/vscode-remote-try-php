<?php
/**
 * Zip AI - Module.
 *
 * This file is used to register and manage the Zip AI Modules.
 *
 * @package zip-ai
 */

namespace ZipAI\Classes;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Classes to be used, in alphabetical order.
use ZipAI\Classes\Helper;

/**
 * The Module Class.
 */
class Module {
	/**
	 * Private Variable of all the valid Zip AI Modules.
	 *
	 * @since 1.0.5
	 * @var array $valid_modules Array of all the available Zip AI Modules.
	 */
	private static $valid_modules = [
		'ai_assistant',
		'ai_design_copilot',
	];

	/**
	 * Update the status of Zip AI Module(s).
	 *
	 * @param string|array $module_name Name of the module or an array of module names.
	 * @param string       $status      Status of the module(s) to be updated.
	 * @since 1.0.5
	 * @return boolean True if Zip AI Module(s) status has been updated, false otherwise.
	 */
	private static function update_status( $module_name, $status ) {
		// If the status is not a valid status, return.
		if ( ! in_array( $status, [ 'enabled', 'disabled' ], true ) ) {
			return false;
		}

		// If the module name is a string, format it into an array.
		if ( is_string( $module_name ) && ! empty( trim( $module_name ) ) ) {
			$module_name = [ $module_name ];
		} elseif ( ! is_array( $module_name ) ) {
			return false;
		}

		// Get all the modules.
		$all_modules = self::get_all_modules();

		// Ensure that the modules that are to be updated are valid.
		$module_name = array_intersect( array_keys( $all_modules ), $module_name );

		// Modules from DB is an array of arrays, where the keys are the module names, and the values are an array of module data.
		// We need to update all the modules that are passed in the $module_name array, making their status as $status.
		array_walk(
			$all_modules,
			function( &$module, $module_key ) use ( $module_name, $status ) {
				// If the module is not in the module name array, return it as it is.
				if ( ! is_array( $module ) || ! in_array( $module_key, $module_name, true ) ) {
					return $module;
				}

				// If the module is in the module name array, update the status.
				$module['status'] = $status;

				// Return the updated module.
				return $module;
			}
		);

		// Update the modules array.
		return Helper::update_admin_settings_option( 'zip_ai_modules', $all_modules );
	}

	/**
	 * Function to migrate older Zip AI settings into the new modular format.
	 *
	 * @since 1.0.5
	 * @return void
	 */
	public static function migrate_options() {
		// Get the existing Zip AI settings option.
		$existing_settings = Helper::get_admin_settings_option( 'zip_ai_settings', [] );

		// If the chat enabled option is set, migrate it.
		if ( isset( $existing_settings['chat_enabled'] ) ) {
			// Set the new option value based on the chat enabled value.
			$ai_assistant_status = false === $existing_settings['chat_enabled'] ? 'disabled' : 'enabled';

			// Update the AI assistant module status.
			$ai_assistant_migrated = self::update_status( 'ai_assistant', $ai_assistant_status );

			// If the migration was successful, unset the chat enabled value and update the settings.
			if ( $ai_assistant_migrated ) {
				unset( $existing_settings['chat_enabled'] );
				Helper::update_admin_settings_option( 'zip_ai_settings', $existing_settings );
			}
		}
	}

	/**
	 * Function to get all the availabe Zip AI Modules, after applying the filter.
	 *
	 * First all the filtered modules and the modules from the database will be fetched.
	 * Then the database modules will be cross-checked against the valid filtered modules.
	 * This is done so that even if a value exists in the database, if the product that is adding the filter is disabled, the feature will be considered as non-existent.
	 * Finally the required data from the database will overwrite the filtered defaults, and only the valid modules will be returned for use.
	 *
	 * @since 1.0.5
	 * @return array Array of all the available Zip AI Modules and their details.
	 */
	public static function get_all_modules() {
		$filtered_modules = apply_filters( 'zip_ai_modules', [] );

		// Ensure that the modules are in the correct format.
		$filtered_modules = is_array( $filtered_modules ) ? $filtered_modules : [];

		// Get the existing Zip AI modules from the DB.
		$modules_from_db = Helper::get_admin_settings_option( 'zip_ai_modules', [] );

		// Ensure that the modules are in the correct format.
		$modules_from_db = is_array( $modules_from_db ) ? $modules_from_db : [];

		// Only load the modules from the database that have the same keys as the filtered modules.
		$modules_from_db = array_intersect_key( $modules_from_db, $filtered_modules );

		// Set the final modules array, where the database values override the filtered values.
		$filtered_modules = array_merge( $filtered_modules, $modules_from_db );

		// Ensure that only the valid modules are returned.
		return array_intersect_key( $filtered_modules, array_flip( self::$valid_modules ) );
	}

	/**
	 * Enable Zip AI Module(s).
	 *
	 * If a string is passed, that module will be enabled if valid.
	 * If an array is passed, all valid modules will be enabled.
	 *
	 * @param string|array $module_name Name of the module or an array of module names.
	 * @since 1.0.5
	 * @return boolean True if Zip AI module(s) has been enabled, false otherwise.
	 */
	public static function enable( $module_name ) {
		return self::update_status( $module_name, 'enabled' );
	}

	/**
	 * Function to disable Zip AI Module(s).
	 *
	 * If a string is passed, that module will be disabled if valid.
	 * If an array is passed, all valid modules will be disabled.
	 *
	 * @param string|array $module_name Name of the module or an array of module names.
	 * @since 1.0.5
	 * @return boolean True if Zip AI module(s) has been enabled, false otherwise.
	 */
	public static function disable( $module_name ) {
		return self::update_status( $module_name, 'disabled' );
	}

	/**
	 * Function to check if Zip AI Module is enabled.
	 *
	 * @param string $module_name Name of the module.
	 * @since 1.0.5
	 * @return boolean True if Zip AI is enabled, false otherwise.
	 */
	public static function is_enabled( $module_name ) {
		// If the module name is not a string, abandon ship.
		if ( ! is_string( $module_name ) ) {
			return false;
		}

		// Get all the modules.
		$all_modules = self::get_all_modules();

		// If the given module name is not a valid module or if the module does not have a status, abandon ship.
		if ( ! array_key_exists( $module_name, $all_modules ) || empty( $all_modules[ $module_name ]['status'] ) ) {
			return false;
		}

		// Return based on whether Zip AI is enabled or not.
		return 'enabled' === $all_modules[ $module_name ]['status'];
	}

	/**
	 * Enable the given Zip AI module if it exists, else create and enable it.
	 *
	 * @param array  $modules     The reference to the modules array that will be modified.
	 * @param string $module_name The module name.
	 * @since 1.1.0
	 * @return void
	 */
	public static function force_enabled( &$modules, $module_name ) {
		if ( empty( $modules[ $module_name ] ) || ! is_array( $modules[ $module_name ] ) ) {
			$modules[ $module_name ] = array( 'status' => 'enabled' );
		} else {
			$modules[ $module_name ]['status'] = 'enabled';
		}
	}
}
