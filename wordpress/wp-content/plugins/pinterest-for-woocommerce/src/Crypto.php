<?php
/**
 * Pinterest for WooCommerce Crypto Wrapper
 *
 * @class       WP_Salesforce_Crypto
 * @version     1.0.0
 * @package     Pinterest_For_WooCommerce/Classes/
 */

namespace Automattic\WooCommerce\Pinterest;

use Defuse\Crypto\KeyProtectedByPassword;
use Defuse\Crypto\Crypto as DefuseCrypto;
use Defuse\Crypto\Key;
use Defuse\Crypto\Exception as DefuseException;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class handling encryption and decryption of sensitive data.
 */
class Crypto {

	/**
	 * The key used in place of a password for the encryption.
	 *
	 * @var string
	 */
	private static $key;

	/**
	 * Initiate class.
	 */
	public function __construct() {
		self::$key = \PINTEREST_FOR_WOOCOMMERCE_PREFIX . '_' . md5( is_multisite() ? \AUTH_KEY . get_current_blog_id() : \AUTH_KEY );
	}


	/**
	 * Create the Protected encoded key.
	 *
	 * @return mixed
	 */
	private static function create_key() {

		$protected_key         = KeyProtectedByPassword::createRandomPasswordProtectedKey( self::$key );
		$protected_key_encoded = $protected_key->saveToAsciiSafeString();

		if ( ! empty( $protected_key_encoded ) ) {
			Pinterest_For_Woocommerce()::save_data( 'crypto_encoded_key', $protected_key_encoded );
			return $protected_key_encoded;
		}

		return false;

	}


	/**
	 * Get the encoded user key.
	 *
	 * @param string $retry The retry attempt #.
	 */
	private static function get_key( $retry = null ) {

		static $user_key_encoded;

		if ( ! is_null( $user_key_encoded ) ) {
			return $user_key_encoded;
		}

		if ( empty( self::$key ) ) {
			new self();
		}

		$protected_key_encoded = Pinterest_For_Woocommerce()::get_data( 'crypto_encoded_key' );

		if ( empty( $protected_key_encoded ) ) {
			$protected_key_encoded = self::create_key();
		}

		try {
			$protected_key    = KeyProtectedByPassword::loadFromAsciiSafeString( $protected_key_encoded );
			$user_key         = $protected_key->unlockKey( self::$key );
			$user_key_encoded = $user_key->saveToAsciiSafeString();
		} catch ( DefuseException\WrongKeyOrModifiedCiphertextException | DefuseException\BadFormatException $ex ) {

			if ( is_null( $retry ) ) {
				Pinterest_For_Woocommerce()::save_data( 'crypto_encoded_key', null );
				return self::get_key( 1 );
			}

			Logger::log( esc_html__( 'Could not decrypt key value. Try reconnecting to Pinterest.', 'pinterest-for-woocommerce' ), 'error' );
			Pinterest_For_Woocommerce()::save_data( 'crypto_encoded_key', false ); // Reset base key.
			return false;
		}

		return $user_key_encoded;
	}


	/**
	 * Encrypt the given value and return the encrypted data.
	 *
	 * @param string $value the value to encrypt.
	 *
	 * @return string
	 */
	public static function encrypt( $value ) {

		$user_key_encoded = self::get_key();
		$user_key         = Key::loadFromAsciiSafeString( $user_key_encoded );

		return DefuseCrypto::encrypt( $value, $user_key );
	}


	/**
	 * Decrypt the given value and return the unencrypted data.
	 *
	 * @param string $encrypted_value the value to decrypt.
	 *
	 * @return string
	 */
	public static function decrypt( $encrypted_value ) {

		$user_key_encoded = self::get_key();
		$user_key         = Key::loadFromAsciiSafeString( $user_key_encoded );
		$value            = false;

		try {
			$value = DefuseCrypto::decrypt( $encrypted_value, $user_key );
		} catch ( DefuseException\WrongKeyOrModifiedCiphertextException $ex ) {
			// Either there's a bug in our code, we're trying to decrypt with the
			// wrong key, or the encrypted credit card number was corrupted in the
			// database.
			Logger::log( esc_html__( 'Could not decrypt key value. Try reconnecting to Pinterest.', 'pinterest-for-woocommerce' ), 'error' );
		}

		return $value;
	}
}
