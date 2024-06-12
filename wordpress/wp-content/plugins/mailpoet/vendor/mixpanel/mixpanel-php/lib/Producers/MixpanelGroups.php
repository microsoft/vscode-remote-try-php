<?php
if (!defined('ABSPATH')) exit;
require_once(dirname(__FILE__) . "/MixpanelBaseProducer.php");
class Producers_MixpanelGroups extends Producers_MixpanelBaseProducer {
 private function _constructPayload($group_key, $group_id, $operation, $value, $ignore_time = false) {
 $payload = array(
 '$token' => $this->_token,
 '$group_key' => $group_key,
 '$group_id' => $group_id,
 '$time' => microtime(true),
 $operation => $value
 );
 if ($ignore_time === true) $payload['$ignore_time'] = true;
 return $payload;
 }
 public function set($group_key, $group_id, $props, $ignore_time = false) {
 $payload = $this->_constructPayload($group_key, $group_id, '$set', $props, $ignore_time);
 $this->enqueue($payload);
 }
 public function setOnce($group_key, $group_id, $props, $ignore_time = false) {
 $payload = $this->_constructPayload($group_key, $group_id, '$set_once', $props, $ignore_time);
 $this->enqueue($payload);
 }
 public function remove($group_key, $group_id, $props, $ignore_time = false) {
 $payload = $this->_constructPayload($group_key, $group_id, '$remove', $props, $ignore_time);
 $this->enqueue($payload);
 }
 public function union($group_key, $group_id, $prop, $val, $ignore_time = false) {
 $payload = $this->_constructPayload($group_key, $group_id, '$union', array("$prop" => $val), $ignore_time);
 $this->enqueue($payload);
 }
 public function deleteGroup($group_key, $group_id, $ignore_time = false) {
 $payload = $this->_constructPayload($group_key, $group_id, '$delete', "", $ignore_time);
 $this->enqueue($payload);
 }
 function _getEndpoint() {
 return $this->_options['groups_endpoint'];
 }
}
