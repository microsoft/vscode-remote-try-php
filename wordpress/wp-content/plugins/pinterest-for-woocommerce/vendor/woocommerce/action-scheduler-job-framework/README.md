# Action Scheduler Job Framework

## Requirements

- PHP 7.0+
- [Jetpack Autoloader](https://github.com/Automattic/jetpack-autoloader) - Required to ensure the latest version of the framework is used in case multiple plugins have it as a dependency


## Versioning & breaking changes

This package follows [Semver](https://semver.org/) for versioning.

This framework may receive breaking changes at the moment while it is only used in the [Facebook for WooCommerce](https://github.com/woocommerce/facebook-for-woocommerce) plugin.
However, once we use it in another plugin we will need to reject all breaking changes.

## Installation

The framework should be installed via [Composer](https://getcomposer.org/). 

1. Add the following to your project's `composer.json` file:
```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/woocommerce/action-scheduler-job-framework"
    }
  ],
  "require": {
    "woocommerce/action-scheduler-job-framework": "1.0.0"
  }
}
```

2. Then run `composer update`

## Chained Jobs

A "chained job" is a kind of batched job that creates follow-up actions in a chain until a set of items has been processed.

Each "batch" in the job is a separate "scheduled action". Each batch is numbered and should be limited to process a set number of items.

### Methods

- `::handle_start()` - Runs before a job instance starts.
- `::handle_end()` - Runs after a job instance ends.
- `::get_items_for_batch()` - Gets a set of items for the batch to process.
- `::process_item()` - Processes a single item.
- `::get_name()` - Get the unique name/ID of the job.
- `::get_number_of_items_processed()` - Get the number of items that have been processed by the currently running instance of the job.
- `::is_running()` - Check if this job is currently running. Checks if there is any "start" or "batch" actions `pending` or `in-progress` for the job.

### Example:

```php

class GenerateProductFeed extends Automattic\WooCommerce\ActionSchedulerJobFramework\AbstractChainedJob {

	use Automattic\WooCommerce\ActionSchedulerJobFramework\Utilities\BatchQueryOffset;

	/**
	 * Runs before starting the job.
	 */
	protected function handle_start() {
		// Optionally do something when starting the job.
	}

	/**
	 * Runs after the finishing the job.
	 */
	protected function handle_end() {
		// Optionally do something when ending the job.
	}

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
	protected function get_items_for_batch( int $batch_number, array $args ): array {
		$product_args = [
			'fields'         => 'ids',
			'post_status'    => 'publish',
			'post_type'      => [ 'product', 'product_variation' ],
			'posts_per_page' => $this->get_batch_size(),
			'offset'         => $this->get_query_offset( $batch_number ),
			'orderby'        => 'ID',
			'order'          => 'ASC',
		];

		$query = new WP_Query( $product_args );
		return $query->posts;
	}

	/**
	 * Process a single item.
	 *
	 * @param string|int|array $item A single item from the get_items_for_batch() method.
	 * @param array            $args The args for the job.
	 *
	 * @throws Exception On error. The failure will be logged by Action Scheduler and the job chain will stop.
	 */
	protected function process_item( $item, array $args ) {
		// Process each item here.
	}

	/**
	 * Get the name/slug of the job.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return 'generate_feed';
	}

	/**
	 * Get the name/slug of the plugin that owns the job.
	 *
	 * @return string
	 */
	public function get_plugin_name(): string {
		return 'facebook_for_woocommerce';
	}

}


add_action( 'init', function() {
    $job = new GenerateProductFeed(
        new \Automattic\WooCommerce\ActionSchedulerJobFramework\Proxies\ActionScheduler()
    );
    $job->init();
    
    // Start the job if it's not already running
    if ( ! $job->is_running() ) {
        $job->queue_start();
    }
});

```
