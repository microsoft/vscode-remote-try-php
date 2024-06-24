<?php
/**
 * Class PHPView
 *
 * @package Automattic\WooCommerce\Pinterest\View
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Pinterest\View;

use Automattic\WooCommerce\Pinterest\PluginHelper;
use Exception;

defined( 'ABSPATH' ) || exit;

/**
 * Class PHPView
 */
class PHPView implements View {

	use PluginHelper;

	/**
	 * Extension to use for view files.
	 */
	protected const VIEW_EXTENSION = 'php';

	/**
	 * Path to the view file to render.
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * Internal storage for passed-in context.
	 *
	 * @var array
	 */
	protected $context = array();

	/**
	 * View factory.
	 *
	 * @var ViewFactory
	 */
	protected $view_factory;

	/**
	 * PHPView constructor.
	 *
	 * @param string      $path         Path to the view file to render.
	 * @param ViewFactory $view_factory View factory instance to use.
	 *
	 * @throws ViewException If an invalid path was passed into the View.
	 */
	public function __construct( string $path, ViewFactory $view_factory ) {
		$this->path         = $this->validate( $path );
		$this->view_factory = $view_factory;
	}

	/**
	 * Render the current view with a given context.
	 *
	 * @param array $context Context in which to render.
	 *
	 * @return string Rendered HTML.
	 *
	 * @throws ViewException If the view could not be loaded.
	 */
	public function render( array $context = array() ): string {
		// Add entire context as array to the current instance to pass onto
		// partial views.
		$this->context = $context;

		// Save current buffering level so we can backtrack in case of an error.
		// This is needed because the view itself might also add an unknown
		// number of output buffering levels.
		$buffer_level = ob_get_level();
		ob_start();

		try {
			include $this->path;
		} catch ( Exception $exception ) {
			// Remove whatever levels were added up until now.
			while ( ob_get_level() > $buffer_level ) {
				ob_end_clean();
			}

			/**
			 * Exception hook.
			 * phpcs:disable WooCommerce.Commenting.CommentHooks.MissingSinceComment
			 */
			do_action( 'wc_pinterest_exception', $exception, __METHOD__ );

			throw ViewException::invalid_view_exception(
				$this->path,
				$exception
			);
		}

		$output = ob_get_clean();
		if ( ! $output ) {
			$output = '';
		}

		return $output;
	}

	/**
	 * Render a partial view.
	 *
	 * This can be used from within a currently rendered view, to include
	 * nested partials.
	 *
	 * The passed-in context is optional, and will fall back to the parent's
	 * context if omitted.
	 *
	 * @param string     $path    Path of the partial to render.
	 * @param array|null $context Context in which to render the partial.
	 *
	 * @return string Rendered HTML.
	 *
	 * @throws ViewException If the view could not be loaded or the provided path was not valid.
	 */
	public function render_partial( string $path, array $context = null ): string {
		if ( ! $context ) {
			$context = $this->context;
		}

		return $this->view_factory->create( $path )->render( $context );
	}

	/**
	 * Return the raw value of a context property.
	 *
	 * By default, properties are automatically escaped when accessing them
	 * within the view. This method allows direct access to the raw value
	 * instead to bypass this automatic escaping.
	 *
	 * @param string $property Property for which to return the raw value.
	 *
	 * @return mixed Raw context property value.
	 *
	 * @throws ViewException If a requested property is not recognized (only in debugging mode).
	 */
	public function raw( string $property ) {
		if ( array_key_exists( $property, $this->context ) ) {
			return $this->context[ $property ];
		}

		/**
		 * Displays an error message when a view property is missing.
		 * phpcs:disable WooCommerce.Commenting.CommentHooks.MissingSinceComment
		 */
		do_action( 'wc_pinterest_error', sprintf( 'View property "%s" is missing or undefined.', $property ), __METHOD__ );

		/*
		 * We only throw an exception here if we are in debugging mode, as we
		 * don't want to take the server down when trying to render a missing
		 * property.
		 */
		if ( $this->is_debug_mode() ) {
			throw ViewException::invalid_context_property( $property );
		}

		return null;
	}

	/**
	 * Validate a path.
	 *
	 * @param string $path Path to validate.
	 *
	 * @return string Validated path.
	 *
	 * @throws ViewException If an invalid path was passed into the View.
	 */
	protected function validate( string $path ): string {
		$path = $this->check_extension( $path, static::VIEW_EXTENSION );
		$path = path_join( $this->get_views_base_path(), $path );

		if ( ! is_readable( $path ) ) {
			/**
			 * View not found error.
			 * phpcs:disable WooCommerce.Commenting.CommentHooks.MissingSinceComment
			 */
			do_action( 'wc_pinterest_error', sprintf( 'View not found in path "%s".', $path ), __METHOD__ );

			throw ViewException::invalid_path( $path );
		}

		return $path;
	}

	/**
	 * Check that the path has the correct extension.
	 *
	 * Optionally adds the extension if none was detected.
	 *
	 * @param string $path      Path to check the extension of.
	 * @param string $extension Extension to use.
	 *
	 * @return string Path with correct extension.
	 */
	protected function check_extension( string $path, string $extension ): string {
		$detected_extension = pathinfo( $path, PATHINFO_EXTENSION );

		if ( $extension !== $detected_extension ) {
			$path .= '.' . $extension;
		}

		return $path;
	}

	/**
	 * Use magic getter to provide automatic escaping by default.
	 *
	 * Use the raw() method to skip automatic escaping.
	 *
	 * @param string $property Property to get.
	 *
	 * @return mixed
	 *
	 * @throws ViewException If a requested property is not recognized (only in debugging mode).
	 */
	public function __get( string $property ) {
		if ( array_key_exists( $property, $this->context ) ) {
			return $this->sanitize_context_variable( $this->context[ $property ] );
		}

		/**
		 * View property missing error.
		 * phpcs:disable WooCommerce.Commenting.CommentHooks.MissingSinceComment
		 */
		do_action( 'wc_pinterest_error', sprintf( 'View property "%s" is missing or undefined.', $property ), __METHOD__ );

		/*
		 * We only throw an exception here if we are in debugging mode, as we
		 * don't want to take the server down when trying to render a missing
		 * property.
		 */
		if ( $this->is_debug_mode() ) {
			throw ViewException::invalid_context_property( $property );
		}

		return null;
	}

	/**
	 * Sanitize context variable.
	 *
	 * @param mixed $var Variable to sanitize.
	 */
	protected function sanitize_context_variable( $var ) {
		if ( is_array( $var ) ) {
			return array_map( array( $this, 'sanitize_context_variable' ), $var );
		} else {
			return ! is_bool( $var ) && is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
		}
	}

	/**
	 * Get base path for the view.
	 *
	 * @return string
	 */
	protected function get_views_base_path(): string {
		return path_join( dirname( __DIR__, 2 ), 'views' );
	}
}
