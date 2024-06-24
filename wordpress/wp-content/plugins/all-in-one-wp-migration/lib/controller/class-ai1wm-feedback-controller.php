<?php
/**
 * Copyright (C) 2014-2023 ServMask Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * ███████╗███████╗██████╗ ██╗   ██╗███╗   ███╗ █████╗ ███████╗██╗  ██╗
 * ██╔════╝██╔════╝██╔══██╗██║   ██║████╗ ████║██╔══██╗██╔════╝██║ ██╔╝
 * ███████╗█████╗  ██████╔╝██║   ██║██╔████╔██║███████║███████╗█████╔╝
 * ╚════██║██╔══╝  ██╔══██╗╚██╗ ██╔╝██║╚██╔╝██║██╔══██║╚════██║██╔═██╗
 * ███████║███████╗██║  ██║ ╚████╔╝ ██║ ╚═╝ ██║██║  ██║███████║██║  ██╗
 * ╚══════╝╚══════╝╚═╝  ╚═╝  ╚═══╝  ╚═╝     ╚═╝╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Kangaroos cannot jump here' );
}

class Ai1wm_Feedback_Controller {

	public static function feedback( $params = array() ) {
		ai1wm_setup_environment();

		// Set params
		if ( empty( $params ) ) {
			$params = stripslashes_deep( $_POST );
		}

		// Set secret key
		$secret_key = null;
		if ( isset( $params['secret_key'] ) ) {
			$secret_key = trim( $params['secret_key'] );
		}

		// Set type
		$type = null;
		if ( isset( $params['ai1wm_type'] ) ) {
			$type = trim( $params['ai1wm_type'] );
		}

		// Set e-mail
		$email = null;
		if ( isset( $params['ai1wm_email'] ) ) {
			$email = trim( $params['ai1wm_email'] );
		}

		// Set message
		$message = null;
		if ( isset( $params['ai1wm_message'] ) ) {
			$message = trim( $params['ai1wm_message'] );
		}

		// Set terms
		$terms = false;
		if ( isset( $params['ai1wm_terms'] ) ) {
			$terms = (bool) $params['ai1wm_terms'];
		}

		try {
			// Ensure that unauthorized people cannot access feedback action
			ai1wm_verify_secret_key( $secret_key );
		} catch ( Ai1wm_Not_Valid_Secret_Key_Exception $e ) {
			exit;
		}

		$extensions = Ai1wm_Extensions::get();

		// Exclude File Extension
		if ( defined( 'AI1WMTE_PLUGIN_NAME' ) ) {
			unset( $extensions[ AI1WMTE_PLUGIN_NAME ] );
		}

		$purchases = array();
		foreach ( $extensions as $extension ) {
			if ( ( $uuid = get_option( $extension['key'] ) ) ) {
				$purchases[] = $uuid;
			}
		}

		try {
			Ai1wm_Feedback::add( $type, $email, $message, $terms, implode( PHP_EOL, $purchases ) );
		} catch ( Ai1wm_Feedback_Exception $e ) {
			ai1wm_json_response( array( 'errors' => array( $e->getMessage() ) ) );
			exit;
		}

		ai1wm_json_response( array( 'errors' => array() ) );
		exit;
	}
}
