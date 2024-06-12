<?php
/**
 * Class GoogleCategorySearch
 *
 * @package Automattic\WooCommerce\Pinterest\Product
 */

declare( strict_types=1 );

namespace Automattic\WooCommerce\Pinterest\Product;

/**
 * Class GoogleCategorySearch
 */
class GoogleCategorySearch {

	/**
	 * Register.
	 */
	public function register(): void {
		add_action(
			'wp_ajax_woocommerce_json_search_google_category',
			function() {
				$this->search_category();
			}
		);
	}

	/**
	 * Search for a Google Category.
	 *
	 * @return void
	 */
	protected function search_category() {
		ob_start();

		check_ajax_referer( 'search-products', 'security' );

		if ( ! current_user_can( 'edit_products' ) ) {
			wp_die( -1 );
		}

		$search_text = isset( $_GET['term'] ) ? wc_clean( wp_unslash( $_GET['term'] ) ) : '';

		if ( ! $search_text ) {
			wp_die();
		}

		wp_send_json( $this->matching_categories( $search_text ) );
	}

	/**
	 * Return list of matching Google Categories
	 *
	 * @param string $search_text Search text to match.
	 * @return array List of categories.
	 */
	protected function matching_categories( string $search_text ): array {
		$matching = array_filter(
			GoogleProductTaxonomy::TAXONOMY,
			function( $category ) use ( $search_text ) {
				return isset( $category['label'] ) && false !== stripos( $category['label'], $search_text );
			}
		);

		$categories = array_map( array( $this, 'full_category_name' ), $matching );
		return array_combine( $categories, $categories );
	}

	/**
	 * Returns the category name including it's parents.
	 *
	 * @param array $category Google Category information.
	 * @return string
	 */
	protected function full_category_name( array $category ): string {
		if ( ! empty( $category['parent'] ) ) {
			$parent = $this->full_category_name( GoogleProductTaxonomy::TAXONOMY[ $category['parent'] ] );

			return "{$parent} > {$category['label']}";
		}

		return $category['label'];
	}

}
