<?php
namespace Action_Scheduler\WP_CLI;
if (!defined('ABSPATH')) exit;
class ProgressBar {
 protected $total_ticks;
 protected $count;
 protected $interval;
 protected $message;
 protected $progress_bar;
 public function __construct( $message, $count, $interval = 100 ) {
 if ( ! ( defined( 'WP_CLI' ) && WP_CLI ) ) {
 throw new \Exception( sprintf( __( 'The %s class can only be run within WP CLI.', 'action-scheduler' ), __CLASS__ ) );
 }
 $this->total_ticks = 0;
 $this->message = $message;
 $this->count = $count;
 $this->interval = $interval;
 }
 public function tick() {
 if ( null === $this->progress_bar ) {
 $this->setup_progress_bar();
 }
 $this->progress_bar->tick();
 $this->total_ticks++;
 do_action( 'action_scheduler/progress_tick', $this->total_ticks );
 }
 public function current() {
 return $this->progress_bar ? $this->progress_bar->current() : 0;
 }
 public function finish() {
 if ( null !== $this->progress_bar ) {
 $this->progress_bar->finish();
 }
 $this->progress_bar = null;
 }
 public function set_message( $message ) {
 $this->message = $message;
 }
 public function set_count( $count ) {
 $this->count = $count;
 $this->finish();
 }
 protected function setup_progress_bar() {
 $this->progress_bar = \WP_CLI\Utils\make_progress_bar(
 $this->message,
 $this->count,
 $this->interval
 );
 }
}
