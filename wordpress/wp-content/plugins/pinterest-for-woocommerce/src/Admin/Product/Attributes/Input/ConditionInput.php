<?php
/**
 * Class Condition
 *
 * @package Automattic\WooCommerce\Pinterest\Product\Attributes
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Pinterest\Admin\Product\Attributes\Input;

use Automattic\WooCommerce\Pinterest\Admin\Input\Select;

defined( 'ABSPATH' ) || exit;

/**
 * Class Condition
 */
class ConditionInput extends Select {

	/**
	 * ConditionInput constructor.
	 */
	public function __construct() {
		parent::__construct();

		$this->set_label( __( 'Condition', 'pinterest-for-woocommerce' ) );
		$this->set_description( __( 'Condition or state of the item.', 'pinterest-for-woocommerce' ) );
	}

}
