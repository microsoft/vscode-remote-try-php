<?php
declare( strict_types=1 );

namespace Automattic\WooCommerce\ActionSchedulerJobFramework\Utilities;

/**
 * Trait BatchQueryOffset
 *
 * @since 1.0.0
 */
trait BatchQueryOffset {

	use BatchSize;

	/**
	 * Get the query offset based on a given batch number and the specified batch size.
	 *
	 * @param int $batch_number
	 *
	 * @return int
	 */
	protected function get_query_offset( int $batch_number ): int {
		return $this->get_batch_size() * ( $batch_number - 1 );
	}

}
