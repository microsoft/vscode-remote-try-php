<?php
if (!defined('ABSPATH')) exit;
require_once(dirname(__FILE__) . "/AbstractConsumer.php");
class ConsumerStrategies_CurlConsumer extends ConsumerStrategies_AbstractConsumer {
 protected $_host;
 protected $_endpoint;
 protected $_connect_timeout;
 protected $_timeout;
 protected $_protocol;
 protected $_fork = null;
 protected $_num_threads;
 function __construct($options) {
 parent::__construct($options);
 $this->_host = $options['host'];
 $this->_endpoint = $options['endpoint'];
 $this->_connect_timeout = isset($options['connect_timeout']) ? $options['connect_timeout'] : 5;
 $this->_timeout = isset($options['timeout']) ? $options['timeout'] : 30;
 $this->_protocol = isset($options['use_ssl']) && $options['use_ssl'] == true ? "https" : "http";
 $this->_fork = isset($options['fork']) ? ($options['fork'] == true) : false;
 $this->_num_threads = isset($options['num_threads']) ? max(1, intval($options['num_threads'])) : 1;
 // ensure the environment is workable for the given settings
 if ($this->_fork == true) {
 $exists = function_exists('exec');
 if (!$exists) {
 throw new Exception('The "exec" function must exist to use the cURL consumer in "fork" mode. Try setting fork = false or use another consumer.');
 }
 $disabled = explode(', ', ini_get('disable_functions'));
 $enabled = !in_array('exec', $disabled);
 if (!$enabled) {
 throw new Exception('The "exec" function must be enabled to use the cURL consumer in "fork" mode. Try setting fork = false or use another consumer.');
 }
 } else {
 if (!function_exists('curl_init')) {
 throw new Exception('The cURL PHP extension is required to use the cURL consumer with fork = false. Try setting fork = true or use another consumer.');
 }
 }
 }
 public function persist($batch) {
 if (count($batch) > 0) {
 $url = $this->_protocol . "://" . $this->_host . $this->_endpoint;
 if ($this->_fork) {
 $data = "data=" . $this->_encode($batch);
 return $this->_execute_forked($url, $data);
 } else {
 return $this->_execute($url, $batch);
 }
 } else {
 return true;
 }
 }
 protected function _execute($url, $batch) {
 if ($this->_debug()) {
 $this->_log("Making blocking cURL call to $url");
 }
 $mh = curl_multi_init();
 $chs = array();
 $batch_size = ceil(count($batch) / $this->_num_threads);
 for ($i=0; $i<$this->_num_threads && !empty($batch); $i++) {
 $ch = curl_init();
 $chs[] = $ch;
 $data = "data=" . $this->_encode(array_splice($batch, 0, $batch_size));
 curl_setopt($ch, CURLOPT_URL, $url);
 curl_setopt($ch, CURLOPT_HEADER, 0);
 curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->_connect_timeout);
 curl_setopt($ch, CURLOPT_TIMEOUT, $this->_timeout);
 curl_setopt($ch, CURLOPT_POST, 1);
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
 curl_multi_add_handle($mh,$ch);
 }
 $running = 0;
 do {
 curl_multi_exec($mh, $running);
 curl_multi_select($mh);
 } while ($running > 0);
 $info = curl_multi_info_read($mh);
 $error = false;
 foreach ($chs as $ch) {
 $response = curl_multi_getcontent($ch);
 if (false === $response) {
 $this->_handleError(curl_errno($ch), curl_error($ch));
 $error = true;
 }
 elseif ("1" != trim($response)) {
 $this->_handleError(0, $response);
 $error = true;
 }
 curl_multi_remove_handle($mh, $ch);
 }
 if (CURLE_OK != $info['result']) {
 $this->_handleError($info['result'], "cURL error with code=".$info['result']);
 $error = true;
 }
 curl_multi_close($mh);
 return !$error;
 }
 protected function _execute_forked($url, $data) {
 if ($this->_debug()) {
 $this->_log("Making forked cURL call to $url");
 }
 $exec = 'curl -X POST -H "Content-Type: application/x-www-form-urlencoded" -d ' . $data . ' "' . $url . '"';
 if(!$this->_debug()) {
 $exec .= " >/dev/null 2>&1 &";
 }
 exec($exec, $output, $return_var);
 if ($return_var != 0) {
 $this->_handleError($return_var, $output);
 }
 return $return_var == 0;
 }
 public function getConnectTimeout()
 {
 return $this->_connect_timeout;
 }
 public function getEndpoint()
 {
 return $this->_endpoint;
 }
 public function getFork()
 {
 return $this->_fork;
 }
 public function getHost()
 {
 return $this->_host;
 }
 public function getOptions()
 {
 return $this->_options;
 }
 public function getProtocol()
 {
 return $this->_protocol;
 }
 public function getTimeout()
 {
 return $this->_timeout;
 }
 public function getNumThreads() {
 return $this->_num_threads;
 }
}
