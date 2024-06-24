<?php
/**
 * Standard log levels
 *
 * @version 3.2.0
 * @package WooCommerce\Classes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Log levels class.
 */
abstract class WC_Log_Levels {
	/**
	 * Log Levels
	 *
	 * Description of levels:
	 *     'emergency': System is unusable.
	 *     'alert': Action must be taken immediately.
	 *     'critical': Critical conditions.
	 *     'error': Error conditions.
	 *     'warning': Warning conditions.
	 *     'notice': Normal but significant condition.
	 *     'info': Informational messages.
	 *     'debug': Debug-level messages.
	 *
	 * @see @link {https://tools.ietf.org/html/rfc5424}
	 */
	const EMERGENCY = 'emergency';
	const ALERT     = 'alert';
	const CRITICAL  = 'critical';
	const ERROR     = 'error';
	const WARNING   = 'warning';
	const NOTICE    = 'notice';
	const INFO      = 'info';
	const DEBUG     = 'debug';

	/**
	 * Level strings mapped to integer severity.
	 *
	 * @var array
	 */
	protected static $level_to_severity = array(
		self::EMERGENCY => 800,
		self::ALERT     => 700,
		self::CRITICAL  => 600,
		self::ERROR     => 500,
		self::WARNING   => 400,
		self::NOTICE    => 300,
		self::INFO      => 200,
		self::DEBUG     => 100,
	);

	/**
	 * Severity integers mapped to level strings.
	 *
	 * This is the inverse of $level_severity.
	 *
	 * @var array
	 */
	protected static $severity_to_level = array(
		800 => self::EMERGENCY,
		700 => self::ALERT,
		600 => self::CRITICAL,
		500 => self::ERROR,
		400 => self::WARNING,
		300 => self::NOTICE,
		200 => self::INFO,
		100 => self::DEBUG,
	);


	/**
	 * Validate a level string.
	 *
	 * @param string $level Log level.
	 * @return bool True if $level is a valid level.
	 */
	public static function is_valid_level( $level ) {
		return is_string( $level ) && array_key_exists( strtolower( $level ), self::$level_to_severity );
	}

	/**
	 * Translate level string to integer.
	 *
	 * @param string $level Log level, options: emergency|alert|critical|error|warning|notice|info|debug.
	 * @return int 100 (debug) - 800 (emergency) or 0 if not recognized
	 */
	public static function get_level_severity( $level ) {
		return self::is_valid_level( $level ) ? self::$level_to_severity[ strtolower( $level ) ] : 0;
	}

	/**
	 * Get an associative array with `level name => numerical severity` key/value pairs.
	 *
	 * @return int[]
	 */
	public static function get_all_level_severities() {
		return self::$level_to_severity;
	}

	/**
	 * Translate severity integer to level string.
	 *
	 * @param int $severity Severity level.
	 * @return bool|string False if not recognized. Otherwise string representation of level.
	 */
	public static function get_severity_level( $severity ) {
		if ( ! array_key_exists( $severity, self::$severity_to_level ) ) {
			return false;
		}
		return self::$severity_to_level[ $severity ];
	}

	/**
	 * Get an associative array with `numerical severity => level name` key/value pairs.
	 *
	 * @return string[]
	 */
	public static function get_all_severity_levels() {
		return self::$severity_to_level;
	}

	/**
	 * Get the UI label for a log level.
	 *
	 * @param string $level Log level, options: emergency|alert|critical|error|warning|notice|info|debug.
	 *
	 * @return string
	 */
	public static function get_level_label( $level ) {
		$labels = self::get_all_level_labels();

		if ( ! array_key_exists( $level, $labels ) ) {
			return '';
		}

		return $labels[ $level ];
	}

	/**
	 * Get the UI labels for all log levels.
	 *
	 * @return string[]
	 */
	public static function get_all_level_labels() {
		return array(
			self::EMERGENCY => __( 'Emergency', 'woocommerce' ),
			self::ALERT     => __( 'Alert', 'woocommerce' ),
			self::CRITICAL  => __( 'Critical', 'woocommerce' ),
			self::ERROR     => __( 'Error', 'woocommerce' ),
			self::WARNING   => __( 'Warning', 'woocommerce' ),
			self::NOTICE    => __( 'Notice', 'woocommerce' ),
			self::INFO      => __( 'Info', 'woocommerce' ),
			self::DEBUG     => __( 'Debug', 'woocommerce' ),
		);
	}
}
