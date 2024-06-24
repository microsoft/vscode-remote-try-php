<?php
/**
 * WordPress Importer
 * https://github.com/humanmade/WordPress-Importer
 *
 * Released under the GNU General Public License v2.0
 * https://github.com/humanmade/WordPress-Importer/blob/master/LICENSE
 *
 * Describes a logger instance
 *
 * Based on PSR-3: http://www.php-fig.org/psr/psr-3/
 *
 * The message MUST be a string or object implementing __toString().
 *
 * The message MAY contain placeholders in the form: {foo} where foo
 * will be replaced by the context data in key "foo".
 *
 * The context array can contain arbitrary data, the only assumption that
 * can be made by implementors is that if an Exception instance is given
 * to produce a stack trace, it MUST be in a key named "exception".
 *
 * See https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md
 * for the full interface specification.
 *
 * @package WordPress Importer
 */

if ( ! class_exists( 'WP_Importer_Logger' ) ) :

	/**
	 * WP Importer Log
	 */
	class WP_Importer_Logger {

		/**
		 * System is unusable.
		 *
		 * @param string $message Error message.
		 * @param array  $context Error context.
		 * @return null
		 */
		public function emergency( $message, array $context = array() ) {
			return $this->log( 'emergency', $message, $context );
		}

		/**
		 * Action must be taken immediately.
		 *
		 * Example: Entire website down, database unavailable, etc. This should
		 * trigger the SMS alerts and wake you up.
		 *
		 * @param string $message Error message.
		 * @param array  $context Error context.
		 * @return null
		 */
		public function alert( $message, array $context = array() ) {
			return $this->log( 'alert', $message, $context );
		}

		/**
		 * Critical conditions.
		 *
		 * Example: Application component unavailable, unexpected exception.
		 *
		 * @param string $message Error message.
		 * @param array  $context Error context.
		 * @return null
		 */
		public function critical( $message, array $context = array() ) {
			return $this->log( 'critical', $message, $context );
		}

		/**
		 * Runtime errors that do not require immediate action but should typically
		 * be logged and monitored.
		 *
		 * @param string $message Error message.
		 * @param array  $context Error context.
		 * @return null
		 */
		public function error( $message, array $context = array() ) {
			return $this->log( 'error', $message, $context );
		}

		/**
		 * Exceptional occurrences that are not errors.
		 *
		 * Example: Use of deprecated APIs, poor use of an API, undesirable things
		 * that are not necessarily wrong.
		 *
		 * @param string $message Error message.
		 * @param array  $context Error context.
		 * @return null
		 */
		public function warning( $message, array $context = array() ) {
			return $this->log( 'warning', $message, $context );
		}

		/**
		 * Normal but significant events.
		 *
		 * @param string $message Error message.
		 * @param array  $context Error context.
		 * @return null
		 */
		public function notice( $message, array $context = array() ) {
			return $this->log( 'notice', $message, $context );
		}

		/**
		 * Interesting events.
		 *
		 * Example: User logs in, SQL logs.
		 *
		 * @param string $message Error message.
		 * @param array  $context Error context.
		 * @return null
		 */
		public function info( $message, array $context = array() ) {
			return $this->log( 'info', $message, $context );
		}

		/**
		 * Detailed debug information.
		 *
		 * @param string $message Error message.
		 * @param array  $context Error context.
		 * @return null
		 */
		public function debug( $message, array $context = array() ) {
			return $this->log( 'debug', $message, $context );
		}

		/**
		 * Logs with an arbitrary level.
		 *
		 * @param mixed  $level Error level.
		 * @param string $message Error message.
		 * @param array  $context Error context.
		 * @return void
		 */
		public function log( $level, $message, array $context = array() ) {

			$this->messages[] = array(
				'timestamp' => time(),
				'level'     => $level,
				'message'   => $message,
				'context'   => $context,
			);
		}
	}
endif;
