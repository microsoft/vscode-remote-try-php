<?php
if (!defined('ABSPATH')) exit;
require_once(dirname(__FILE__) . "/../Base/MixpanelBase.php");
require_once(dirname(__FILE__) . "/../ConsumerStrategies/FileConsumer.php");
require_once(dirname(__FILE__) . "/../ConsumerStrategies/CurlConsumer.php");
require_once(dirname(__FILE__) . "/../ConsumerStrategies/SocketConsumer.php");
if (!function_exists('json_encode')) {
 throw new Exception('The JSON PHP extension is required.');
}
abstract class Producers_MixpanelBaseProducer extends Base_MixpanelBase {
 protected $_token;
 private $_queue = array();
 private $_consumer = null;
 private $_consumers = array(
 "file" => "ConsumerStrategies_FileConsumer",
 "curl" => "ConsumerStrategies_CurlConsumer",
 "socket" => "ConsumerStrategies_SocketConsumer"
 );
 protected $_max_queue_size = 1000;
 public function __construct($token, $options = array()) {
 parent::__construct($options);
 // register any customer consumers
 if (isset($options["consumers"])) {
 $this->_consumers = array_merge($this->_consumers, $options['consumers']);
 }
 // set max queue size
 if (isset($options["max_queue_size"])) {
 $this->_max_queue_size = $options['max_queue_size'];
 }
 // associate token
 $this->_token = $token;
 if ($this->_debug()) {
 $this->_log("Using token: ".$this->_token);
 }
 // instantiate the chosen consumer
 $this->_consumer = $this->_getConsumer();
 }
 public function __destruct() {
 $attempts = 0;
 $max_attempts = 10;
 $success = false;
 while (!$success && $attempts < $max_attempts) {
 if ($this->_debug()) {
 $this->_log("destruct flush attempt #".($attempts+1));
 }
 $success = $this->flush();
 $attempts++;
 }
 }
 public function flush($desired_batch_size = 50) {
 $queue_size = count($this->_queue);
 $succeeded = true;
 $num_threads = $this->_consumer->getNumThreads();
 if ($this->_debug()) {
 $this->_log("Flush called - queue size: ".$queue_size);
 }
 while($queue_size > 0 && $succeeded) {
 $batch_size = min(array($queue_size, $desired_batch_size*$num_threads, $this->_options['max_batch_size']*$num_threads));
 $batch = array_splice($this->_queue, 0, $batch_size);
 $succeeded = $this->_persist($batch);
 if (!$succeeded) {
 if ($this->_debug()) {
 $this->_log("Batch consumption failed!");
 }
 $this->_queue = array_merge($batch, $this->_queue);
 if ($this->_debug()) {
 $this->_log("added batch back to queue, queue size is now $queue_size");
 }
 }
 $queue_size = count($this->_queue);
 if ($this->_debug()) {
 $this->_log("Batch of $batch_size consumed, queue size is now $queue_size");
 }
 }
 return $succeeded;
 }
 public function reset() {
 $this->_queue = array();
 }
 public function getQueue() {
 return $this->_queue;
 }
 public function getToken() {
 return $this->_token;
 }
 protected function _getConsumer() {
 $key = $this->_options['consumer'];
 $Strategy = $this->_consumers[$key];
 if ($this->_debug()) {
 $this->_log("Using consumer: " . $key . " -> " . $Strategy);
 }
 $this->_options['endpoint'] = $this->_getEndpoint();
 return new $Strategy($this->_options);
 }
 public function enqueue($message = array()) {
 array_push($this->_queue, $message);
 // force a flush if we've reached our threshold
 if (count($this->_queue) > $this->_max_queue_size) {
 $this->flush();
 }
 if ($this->_debug()) {
 $this->_log("Queued message: ".json_encode($message));
 }
 }
 public function enqueueAll($messages = array()) {
 foreach($messages as $message) {
 $this->enqueue($message);
 }
 }
 protected function _persist($message) {
 return $this->_consumer->persist($message);
 }
 abstract function _getEndpoint();
}
