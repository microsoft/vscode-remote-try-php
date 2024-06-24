<?php
/**
 * Class GoogleCategoryInput
 *
 * @package Automattic\WooCommerce\Pinterest\Product\Attributes
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Pinterest\Admin\Product\Attributes\Input;

use Automattic\WooCommerce\Pinterest\Admin\Input\GoogleCategory;

defined( 'ABSPATH' ) || exit;

/**
 * Class GoogleCategoryInput
 */
class GoogleCategoryInput extends GoogleCategory {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();

		$this->set_label( __( 'Google Category', 'pinterest-for-woocommerce' ) );
		$this->set_description( __( 'Categorization of the product based on the standardized Google Product Taxonomy.', 'pinterest-for-woocommerce' ) );
	}

}
