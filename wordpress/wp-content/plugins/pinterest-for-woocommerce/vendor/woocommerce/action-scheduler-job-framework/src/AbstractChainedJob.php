<?php
declare( strict_types=1 );

namespace Automattic\WooCommerce\ActionSchedulerJobFramework;

use ActionScheduler_Action;
use Automattic\WooCommerce\ActionSchedulerJobFramework\Utilities\BatchSize;
use Exception;

defined( 'ABSPATH' ) || exit;

/**
 * Class AbstractChainedJob.
 *
 * A "chained job" is a kind of batched job that creates follow-up actions until all items in the job have been processed.
 *
 * Each "batch" in the job is a separate "scheduled action". Each batch is numbered and should be limited to process a
 * set number of items.
 *
 * Only a single chained job can run at any one time.
 *
 * @since 1.0.0
 */
abstract class AbstractChainedJob extends AbstractJob implements ChainedJobInterface {

	use BatchSize;

	/**
	 * Get a set of items for the batch.
	 *
	 * NOTE: when using an OFFSET based query to retrieve items it's recommended to order by the item ID while
	 * ASCENDING. This is so that any newly added items will not disrupt the query offset.
	 *
	 * @param int   $batch_number The batch number increments for each new batch in the job cycle.
	 * @param array $args         The args for the job.
	 *
	 * @throws Exception On error. The failure will be logged by Action Scheduler and the job chain will stop.
	 */
	abstract protected function get_items_for_batch( int $batch_number, array $args ): array;

	/**
	 * Process a single item.
	 *
	 * @param string|int|array $item A single item from the get_items_for_batch() method.
	 * @param array            $args The args for the job.
	 *
	 * @throws Exception On error. The failure will be logged by Action Scheduler and the job chain will stop.
	 */
	abstract protected function process_item( $item, array $args );

	/**
	 * Called before starting the job.
	 */
	protected function handle_start() {
		// Optionally override this method in child class.
	}

	/**
	 * Called after the finishing the job.
	 */
	protected function handle_end() {
		// Optionally override this method in child class.
	}

	/**
	 * Init the job, register necessary WP actions.
	 */
	public function init() {
		add_action( $this->get_action_full_name( self::CHAIN_START ), [ $this, 'handle_start_action' ] );
		add_action( $this->get_action_full_name( self::CHAIN_BATCH ), [ $this, 'handle_batch_action' ], 10, 2 );
		add_action( $this->get_action_full_name( self::CHAIN_END ), [ $this, 'handle_end_action' ] );
	}

	/**
	 * Queue the job to be started in the background.
	 *
	 * @param array $args The args for the job.
	 */
	public function queue_start( array $args = [] ) {
		$this->schedule_immediate_action( self::CHAIN_START, [ $args ] );
	}

	/**
	 * Queue a batch to be processed immediately.
	 *
	 * @param int   $batch_number The batch number for the new batch.
	 * @param array $args         The args for the job.
	 */
	protected function queue_batch( int $batch_number, array $args ) {
		$this->schedule_immediate_action( self::CHAIN_BATCH, [ $batch_number, $args ] );
	}

	/**
	 * Queue the job to be ended.
	 *
	 * Should be called once all items are processed.
	 *
	 * @param array $args The args for the job.
	 */
	protected function queue_end( array $args = [] ) {
		$this->schedule_immediate_action( self::CHAIN_END, [ $args ] );
	}

	/**
	 * Handles job start action.
	 *
	 * @hooked {plugin_name}/jobs/{job_name}/chain_start
	 *
	 * @param array $args The args for the job.
	 *
	 * @throws Exception On error. The failure will be logged by Action Scheduler and the job chain will stop.
	 */
	public function handle_start_action( array $args ) {
		// Prevent starting if a job already has scheduled batch actions
		$batch_action_name = $this->get_action_full_name( self::CHAIN_BATCH );
		if ( $this->action_scheduler->next_scheduled_action( $batch_action_name, null, $this->get_group_name() ) ) {
			throw new Exception( 'This job is already running.' );
		}

		$this->handle_start();
		$this->queue_batch( 1, $args );
	}

	/**
	 * Handle processing a chain batch.
	 *
	 * @hooked {plugin_name}/jobs/{job_name}/chain_batch
	 *
	 * @param int   $batch_number The batch number for the new batch.
	 * @param array $args         The args for the job.
	 *
	 * @throws Exception On error. The failure will be logged by Action Scheduler and the job chain will stop.
	 */
	public function handle_batch_action( int $batch_number, array $args ) {
		$items = $this->get_items_for_batch( $batch_number, $args );

		if ( empty( $items ) ) {
			// No more items to process so end the job chain
			$this->queue_end( $args );
		} else {
			$this->process_items( $items, $args );

			// If there were items, queue another batch.
			$this->queue_batch( $batch_number + 1, $args );
		}
	}

	/**
	 * Processes a batch of items.
	 *
	 * @since 1.1.0
	 *
	 * @param array $items The items of the current batch.
	 * @param array $args  The args for the job.
	 *
	 * @throws Exception On error. The failure will be logged by Action Scheduler and the job chain will stop.
	 */
	protected function process_items( array $items, array $args ) {
		foreach ( $items as $item ) {
			$this->process_item( $item, $args );
		}
	}

	/**
	 * Handles job end action.
	 *
	 * @hooked {plugin_name}/jobs/{job_name}/chain_end
	 *
	 * @param array $args The args for the job.
	 *
	 * @throws Exception On error. The failure will be logged by Action Scheduler.
	 */
	public function handle_end_action( array $args ) {
		$this->handle_end();
	}

	/**
	 * Check if this job is running.
	 *
	 * Checks if there is any "start" or "batch" actions pending or in-progress for this job.
	 *
	 * @return bool
	 */
	public function is_running(): bool {
		$start_action = $this->get_action_full_name( self::CHAIN_START );
		$batch_action = $this->get_action_full_name( self::CHAIN_BATCH );

		if ( $this->action_scheduler->next_scheduled_action( $start_action, null, $this->get_group_name() ) ) {
			return true;
		}
		if ( $this->action_scheduler->next_scheduled_action( $batch_action, null, $this->get_group_name() ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get the number of items processed by the currently running job.
	 *
	 * @return int Returns the number of items processed. Will return zero if the job isn't running.
	 */
	public function get_number_of_items_processed(): int {
		$batch_action_name = $this->get_action_full_name( self::CHAIN_BATCH );

		$in_progress_actions = $this->action_scheduler->search(
			[
				'hook'     => $batch_action_name,
				'per_page' => 1,
				'status'   => $this->action_scheduler::STATUS_RUNNING,
			]
		);

		if ( $in_progress_actions ) {
			return $this->calculate_items_processed_from_batch_action( current( $in_progress_actions ) );
		}

		$pending_actions = $this->action_scheduler->search(
			[
				'hook'     => $batch_action_name,
				'per_page' => 1,
				'status'   => $this->action_scheduler::STATUS_PENDING,
			]
		);

		if ( $pending_actions ) {
			return $this->calculate_items_processed_from_batch_action( current( $pending_actions ) );
		}

		return 0;
	}

	/**
	 * Calculate the number of items processed by the job based on a given scheduled batch action.
	 *
	 * @param ActionScheduler_Action $action The most recent batch action.
	 *
	 * @return int
	 */
	protected function calculate_items_processed_from_batch_action( ActionScheduler_Action $action ): int {
		$args = $action->get_args();

		// The batch number is the first action arg, take 1 because it's not been fully processed yet
		$number_of_batches_processed = $args[0] - 1;

		// Use max() to not allow a negative value
		return max( 0, $this->get_batch_size() * $number_of_batches_processed );
	}

}
