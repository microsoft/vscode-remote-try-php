<?php
if (!defined('ABSPATH')) exit;
require_once(dirname(__FILE__) . "/MixpanelBaseProducer.php");
class Producers_MixpanelPeople extends Producers_MixpanelBaseProducer {
 private function _constructPayload($distinct_id, $operation, $value, $ip = null, $ignore_time = false, $ignore_alias = false) {
 $payload = array(
 '$token' => $this->_token,
 '$distinct_id' => $distinct_id,
 '$time' => microtime(true),
 $operation => $value
 );
 if ($ip !== null) $payload['$ip'] = $ip;
 if ($ignore_time === true) $payload['$ignore_time'] = true;
 if ($ignore_alias === true) $payload['$ignore_alias'] = true;
 return $payload;
 }
 public function set($distinct_id, $props, $ip = null, $ignore_time = false, $ignore_alias = false) {
 $payload = $this->_constructPayload($distinct_id, '$set', $props, $ip, $ignore_time, $ignore_alias);
 $this->enqueue($payload);
 }
 public function setOnce($distinct_id, $props, $ip = null, $ignore_time = false, $ignore_alias = false) {
 $payload = $this->_constructPayload($distinct_id, '$set_once', $props, $ip, $ignore_time, $ignore_alias);
 $this->enqueue($payload);
 }
 public function remove($distinct_id, $props, $ip = null, $ignore_time = false, $ignore_alias = false) {
 $payload = $this->_constructPayload($distinct_id, '$unset', $props, $ip, $ignore_time, $ignore_alias);
 $this->enqueue($payload);
 }
 public function increment($distinct_id, $prop, $val, $ip = null, $ignore_time = false, $ignore_alias = false) {
 $payload = $this->_constructPayload($distinct_id, '$add', array("$prop" => $val), $ip, $ignore_time, $ignore_alias);
 $this->enqueue($payload);
 }
 public function append($distinct_id, $prop, $val, $ip = null, $ignore_time = false, $ignore_alias = false) {
 $operation = gettype($val) == "array" ? '$union' : '$append';
 $payload = $this->_constructPayload($distinct_id, $operation, array("$prop" => $val), $ip, $ignore_time, $ignore_alias);
 $this->enqueue($payload);
 }
 public function trackCharge($distinct_id, $amount, $timestamp = null, $ip = null, $ignore_time = false, $ignore_alias = false) {
 $timestamp = $timestamp == null ? time() : $timestamp;
 $date_iso = date("c", $timestamp);
 $transaction = array(
 '$time' => $date_iso,
 '$amount' => $amount
 );
 $val = array('$transactions' => $transaction);
 $payload = $this->_constructPayload($distinct_id, '$append', $val, $ip, $ignore_time, $ignore_alias);
 $this->enqueue($payload);
 }
 public function clearCharges($distinct_id, $ip = null, $ignore_time = false, $ignore_alias = false) {
 $payload = $this->_constructPayload($distinct_id, '$set', array('$transactions' => array()), $ip, $ignore_time, $ignore_alias);
 $this->enqueue($payload);
 }
 public function deleteUser($distinct_id, $ip = null, $ignore_time = false, $ignore_alias = false) {
 $payload = $this->_constructPayload($distinct_id, '$delete', "", $ip, $ignore_time, $ignore_alias);
 $this->enqueue($payload);
 }
 function _getEndpoint() {
 return $this->_options['people_endpoint'];
 }
}
