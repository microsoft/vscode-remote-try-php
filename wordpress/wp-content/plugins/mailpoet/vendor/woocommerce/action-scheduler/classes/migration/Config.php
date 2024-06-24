<?php
namespace Action_Scheduler\Migration;
if (!defined('ABSPATH')) exit;
use Action_Scheduler\WP_CLI\ProgressBar;
use ActionScheduler_Logger as Logger;
use ActionScheduler_Store as Store;
class Config {
 private $source_store;
 private $source_logger;
 private $destination_store;
 private $destination_logger;
 private $progress_bar;
 private $dry_run = false;
 public function __construct() {
 }
 public function get_source_store() {
 if ( empty( $this->source_store ) ) {
 throw new \RuntimeException( __( 'Source store must be configured before running a migration', 'action-scheduler' ) );
 }
 return $this->source_store;
 }
 public function set_source_store( Store $store ) {
 $this->source_store = $store;
 }
 public function get_source_logger() {
 if ( empty( $this->source_logger ) ) {
 throw new \RuntimeException( __( 'Source logger must be configured before running a migration', 'action-scheduler' ) );
 }
 return $this->source_logger;
 }
 public function set_source_logger( Logger $logger ) {
 $this->source_logger = $logger;
 }
 public function get_destination_store() {
 if ( empty( $this->destination_store ) ) {
 throw new \RuntimeException( __( 'Destination store must be configured before running a migration', 'action-scheduler' ) );
 }
 return $this->destination_store;
 }
 public function set_destination_store( Store $store ) {
 $this->destination_store = $store;
 }
 public function get_destination_logger() {
 if ( empty( $this->destination_logger ) ) {
 throw new \RuntimeException( __( 'Destination logger must be configured before running a migration', 'action-scheduler' ) );
 }
 return $this->destination_logger;
 }
 public function set_destination_logger( Logger $logger ) {
 $this->destination_logger = $logger;
 }
 public function get_dry_run() {
 return $this->dry_run;
 }
 public function set_dry_run( $dry_run ) {
 $this->dry_run = (bool) $dry_run;
 }
 public function get_progress_bar() {
 return $this->progress_bar;
 }
 public function set_progress_bar( ProgressBar $progress_bar ) {
 $this->progress_bar = $progress_bar;
 }
}
