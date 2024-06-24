<?php
if (!defined('ABSPATH')) exit;
require_once(dirname(__FILE__) . "/../Base/MixpanelBase.php");
abstract class ConsumerStrategies_AbstractConsumer extends Base_MixpanelBase {
 function __construct($options = array()) {
 parent::__construct($options);
 if ($this->_debug()) {
 $this->_log("Instantiated new Consumer");
 }
 }
 protected function _encode($params) {
 return base64_encode(json_encode($params));
 }
 protected function _handleError($code, $msg) {
 if (isset($this->_options['error_callback'])) {
 $handler = $this->_options['error_callback'];
 call_user_func($handler, $code, $msg);
 }
 if ($this->_debug()) {
 $arr = debug_backtrace();
 $class = get_class($arr[0]['object']);
 $line = $arr[0]['line'];
 error_log ( "[ $class - line $line ] : " . print_r($msg, true) );
 }
 }
 public function getNumThreads() {
 return 1;
 }
 abstract function persist($batch);
}