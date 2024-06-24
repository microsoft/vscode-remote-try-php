<?php
/**
 * Evaluates the spec and returns a status.
 */

namespace Automattic\WooCommerce\Admin\Features\PaymentGatewaySuggestions;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\RemoteSpecs\RuleProcessors\RuleEvaluator;

/**
 * Evaluates the spec and returns the evaluated suggestion.
 */
class EvaluateSuggestion {
	/**
	 * Evaluates the spec and returns the suggestion.
	 *
	 * @param object|array $spec The suggestion to evaluate.
	 * @return object The evaluated suggestion.
	 */
	public static function evaluate( $spec ) {
		$rule_evaluator = new RuleEvaluator();
		$suggestion     = is_array( $spec ) ? (object) $spec : clone $spec;

		if ( isset( $suggestion->is_visible ) ) {
			$is_visible             = $rule_evaluator->evaluate( $suggestion->is_visible );
			$suggestion->is_visible = $is_visible;
		}

		return $suggestion;
	}

	/**
	 * Evaluates the specs and returns the visible suggestions.
	 *
	 * @param array $specs payment suggestion spec array.
	 * @return array The visible suggestions and errors.
	 */
	public static function evaluate_specs( $specs ) {
		$suggestions = array();
		$errors      = array();

		foreach ( $specs as $spec ) {
			try {
				$suggestion = self::evaluate( $spec );
				if ( ! property_exists( $suggestion, 'is_visible' ) || $suggestion->is_visible ) {
					$suggestions[] = $suggestion;
				}
			} catch ( \Throwable $e ) {
				$errors[] = $e;
			}
		}

		return array(
			'suggestions' => $suggestions,
			'errors'      => $errors,
		);
	}
}
