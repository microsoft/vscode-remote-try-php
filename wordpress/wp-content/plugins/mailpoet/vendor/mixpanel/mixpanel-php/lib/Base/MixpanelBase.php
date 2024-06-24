<?php
if (!defined('ABSPATH')) exit;
class Base_MixpanelBase {
 private $_defaults = array(
 "max_batch_size" => 50, // the max batch size Mixpanel will accept is 50,
 "max_queue_size" => 1000, // the max num of items to hold in memory before flushing
 "debug" => false, // enable/disable debug mode
 "consumer" => "curl", // which consumer to use
 "host" => "api.mixpanel.com", // the host name for api calls
 "events_endpoint" => "/track", // host relative endpoint for events
 "people_endpoint" => "/engage", // host relative endpoint for people updates
 "groups_endpoint" => "/groups", // host relative endpoint for groups updates
 "use_ssl" => true, // use ssl when available
 "error_callback" => null // callback to use on consumption failures
 );
 protected $_options = array();
 public function __construct($options = array()) {
 $options = array_merge($this->_defaults, $options);
 $this->_options = $options;
 }
 protected function _log($msg) {
 $arr = debug_backtrace();
 $class = $arr[0]['class'];
 $line = $arr[0]['line'];
 error_log ( "[ $class - line $line ] : " . $msg );
 }
 protected function _debug() {
 return isset($this->_options["debug"]) && $this->_options["debug"] == true;
 }
}
