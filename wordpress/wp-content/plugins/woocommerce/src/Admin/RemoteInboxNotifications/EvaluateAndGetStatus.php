<?php
/**
 * Evaluates the spec and returns a status.
 */

namespace Automattic\WooCommerce\Admin\RemoteInboxNotifications;

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\DeprecatedClassFacade;

/**
 * Evaluates the spec and returns a status.
 *
 * @deprecated 8.8.0
 */
class EvaluateAndGetStatus extends DeprecatedClassFacade {
	/**
	 * The name of the non-deprecated class that this facade covers.
	 *
	 * @var string
	 */
	protected static $facade_over_classname = 'Automattic\WooCommerce\Admin\RemoteSpecs\RuleProcessors\EvaluateAndGetStatus';

	/**
	 * The version that this class was deprecated in.
	 *
	 * @var string
	 */
	protected static $deprecated_in_version = '8.8.0';
}
