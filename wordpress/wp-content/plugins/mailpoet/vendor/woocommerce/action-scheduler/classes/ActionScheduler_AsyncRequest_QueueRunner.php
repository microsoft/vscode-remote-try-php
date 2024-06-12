<?php
if (!defined('ABSPATH')) exit;
defined( 'ABSPATH' ) || exit;
class ActionScheduler_AsyncRequest_QueueRunner extends WP_Async_Request {
 protected $store;
 protected $prefix = 'as';
 protected $action = 'async_request_queue_runner';
 public function __construct( ActionScheduler_Store $store ) {
 parent::__construct();
 $this->store = $store;
 }
 protected function handle() {
 do_action( 'action_scheduler_run_queue', 'Async Request' ); // run a queue in the same way as WP Cron, but declare the Async Request context
 $sleep_seconds = $this->get_sleep_seconds();
 if ( $sleep_seconds ) {
 sleep( $sleep_seconds );
 }
 $this->maybe_dispatch();
 }
 public function maybe_dispatch() {
 if ( ! $this->allow() ) {
 return;
 }
 $this->dispatch();
 ActionScheduler_QueueRunner::instance()->unhook_dispatch_async_request();
 }
 protected function allow() {
 if ( ! has_action( 'action_scheduler_run_queue' ) || ActionScheduler::runner()->has_maximum_concurrent_batches() || ! $this->store->has_pending_actions_due() ) {
 $allow = false;
 } else {
 $allow = true;
 }
 return apply_filters( 'action_scheduler_allow_async_request_runner', $allow );
 }
 protected function get_sleep_seconds() {
 return apply_filters( 'action_scheduler_async_request_sleep_seconds', 5, $this );
 }
}
