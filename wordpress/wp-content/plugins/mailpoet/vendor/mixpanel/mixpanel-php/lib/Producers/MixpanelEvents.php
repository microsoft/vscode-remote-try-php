<?php
if (!defined('ABSPATH')) exit;
require_once(dirname(__FILE__) . "/MixpanelBaseProducer.php");
require_once(dirname(__FILE__) . "/MixpanelPeople.php");
require_once(dirname(__FILE__) . "/../ConsumerStrategies/CurlConsumer.php");
class Producers_MixpanelEvents extends Producers_MixpanelBaseProducer {
 private $_super_properties = array("mp_lib" => "php");
 public function track($event, $properties = array()) {
 // if no token is passed in, use current token
 if (!isset($properties["token"])) $properties['token'] = $this->_token;
 // if no time is passed in, use the current time
 if (!isset($properties["time"])) $properties['time'] = microtime(true);
 $params['event'] = $event;
 $params['properties'] = array_merge($this->_super_properties, $properties);
 $this->enqueue($params);
 }
 public function register($property, $value) {
 $this->_super_properties[$property] = $value;
 }
 public function registerAll($props_and_vals = array()) {
 foreach($props_and_vals as $property => $value) {
 $this->register($property, $value);
 }
 }
 public function registerOnce($property, $value) {
 if (!isset($this->_super_properties[$property])) {
 $this->register($property, $value);
 }
 }
 public function registerAllOnce($props_and_vals = array()) {
 foreach($props_and_vals as $property => $value) {
 if (!isset($this->_super_properties[$property])) {
 $this->register($property, $value);
 }
 }
 }
 public function unregister($property) {
 unset($this->_super_properties[$property]);
 }
 public function unregisterAll($properties) {
 foreach($properties as $property) {
 $this->unregister($property);
 }
 }
 public function getProperty($property) {
 return $this->_super_properties[$property];
 }
 public function identify($user_id, $anon_id = null) {
 $this->register("distinct_id", $user_id);
 $UUIDv4 = '/^(\$device:)?[a-zA-Z0-9]*-[a-zA-Z0-9]*-[a-zA-Z0-9]*-[a-zA-Z0-9]*-[a-zA-Z0-9]*$/i';
 if (!empty($anon_id)) {
 if (preg_match($UUIDv4, $anon_id) !== 1) {
 error_log("Running Identify method (identified_id: $user_id, anon_id: $anon_id) failed, anon_id not in UUID v4 format");
 } else {
 $this->track('$identify', array(
 '$identified_id' => $user_id,
 '$anon_id' => $anon_id
 ));
 }
 }
 }
 public function createAlias($distinct_id, $alias) {
 $msg = array(
 "event" => '$create_alias',
 "properties" => array("distinct_id" => $distinct_id, "alias" => $alias, "token" => $this->_token)
 );
 // Save the current fork/async options
 $old_fork = isset($this->_options['fork']) ? $this->_options['fork'] : false;
 $old_async = isset($this->_options['async']) ? $this->_options['async'] : false;
 // Override fork/async to make the new consumer synchronous
 $this->_options['fork'] = false;
 $this->_options['async'] = false;
 // The name is ambiguous, but this creates a new consumer with current $this->_options
 $consumer = $this->_getConsumer();
 $success = $consumer->persist(array($msg));
 // Restore the original fork/async settings
 $this->_options['fork'] = $old_fork;
 $this->_options['async'] = $old_async;
 if (!$success) {
 error_log("Creating Mixpanel Alias (distinct id: $distinct_id, alias: $alias) failed");
 throw new Exception("Tried to create an alias but the call was not successful");
 } else {
 return $msg;
 }
 }
 function _getEndpoint() {
 return $this->_options['events_endpoint'];
 }
}
