<?php
declare( strict_types=1 );

namespace Automattic\WooCommerce\ActionSchedulerJobFramework;

defined( 'ABSPATH' ) || exit;

/**
 * Interface JobInterface.
 *
 * @since 1.0.0
 */
interface JobInterface {

	/**
	 * Init the job, register necessary WP actions.
	 */
	public function init();

	/**
	 * Get the name/slug of the job.
	 *
	 * @return string
	 */
	public function get_name(): string;

	/**
	 * Get the name/slug of the plugin that owns the job.
	 *
	 * @return string
	 */
	public function get_plugin_name(): string;

	/**
	 * Get the action group name/slug for the job.
	 *
	 * @return string
	 */
	public function get_group_name(): string;

	/**
	 * Check if this job is running.
	 *
	 * @return bool
	 */
	public function is_running(): bool;

}
