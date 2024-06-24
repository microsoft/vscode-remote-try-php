<?php
declare( strict_types=1 );

namespace Automattic\WooCommerce\ActionSchedulerJobFramework;

use Exception;

defined( 'ABSPATH' ) || exit;

/**
 * Interface ChainedJobInterface.
 *
 * @see AbstractChainedJob
 *
 * @since 1.0.0
 */
interface ChainedJobInterface extends JobInterface {

	const CHAIN_START = 'chain_start';
	const CHAIN_BATCH = 'chain_batch';
	const CHAIN_END   = 'chain_end';

	/**
	 * Queue the job to be started in the background.
	 *
	 * @param array $args Set args to be available during the job.
	 */
	public function queue_start( array $args = [] );

	/**
	 * Handles job start action.
	 *
	 * @hooked {plugin_name}/jobs/{job_name}/chain_start
	 *
	 * @param array $args The args for the job.
	 *
	 * @throws Exception If an error occurs. Exceptions will be logged by Action Scheduler.
	 */
	public function handle_start_action( array $args );

	/**
	 * Handles job process batch action.
	 *
	 * @hooked {plugin_name}/jobs/{job_name}/chain_batch
	 *
	 * @param int   $batch_number The batch number for the new batch.
	 * @param array $args The args for the job.
	 *
	 * @throws Exception If an error occurs. Exceptions will be logged by Action Scheduler.
	 */
	public function handle_batch_action( int $batch_number, array $args );

	/**
	 * Handles job end action.
	 *
	 * @hooked {plugin_name}/jobs/{job_name}/chain_end
	 *
	 * @param array $args The args for the job.
	 *
	 * @throws Exception If an error occurs. Exceptions will be logged by Action Scheduler.
	 */
	public function handle_end_action( array $args );

	/**
	 * Get the number of items processed by the currently running job.
	 *
	 * @return int Returns the number of items processed. Will return zero if the job isn't running.
	 */
	public function get_number_of_items_processed(): int;


}
