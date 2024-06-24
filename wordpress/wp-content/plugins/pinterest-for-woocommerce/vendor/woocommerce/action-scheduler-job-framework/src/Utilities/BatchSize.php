<?php
declare( strict_types=1 );

namespace Automattic\WooCommerce\ActionSchedulerJobFramework\Utilities;

/**
 * Trait BatchSize
 *
 * @since 1.0.0
 */
trait BatchSize {

	/**
	 * Get the job's batch size.
	 *
	 * @return int
	 */
	protected function get_batch_size(): int {
		return 10;
	}

}
