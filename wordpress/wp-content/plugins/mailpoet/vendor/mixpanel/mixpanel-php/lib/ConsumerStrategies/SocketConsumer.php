<?php
if (!defined('ABSPATH')) exit;
require_once(dirname(__FILE__) . "/AbstractConsumer.php");
class ConsumerStrategies_SocketConsumer extends ConsumerStrategies_AbstractConsumer {
 private $_host;
 private $_endpoint;
 private $_connect_timeout;
 private $_protocol;
 private $_socket;
 private $_async;
 private $_port;
 public function __construct($options = array()) {
 parent::__construct($options);
 $this->_host = $options['host'];
 $this->_endpoint = $options['endpoint'];
 $this->_connect_timeout = isset($options['connect_timeout']) ? $options['connect_timeout'] : 5;
 $this->_async = isset($options['async']) && $options['async'] === false ? false : true;
 if (array_key_exists('use_ssl', $options) && $options['use_ssl'] == true) {
 $this->_protocol = "ssl";
 $this->_port = 443;
 } else {
 $this->_protocol = "tcp";
 $this->_port = 80;
 }
 }
 public function persist($batch) {
 $socket = $this->_getSocket();
 if (!is_resource($socket)) {
 return false;
 }
 $data = "data=".$this->_encode($batch);
 $body = "";
 $body.= "POST ".$this->_endpoint." HTTP/1.1\r\n";
 $body.= "Host: " . $this->_host . "\r\n";
 $body.= "Content-Type: application/x-www-form-urlencoded\r\n";
 $body.= "Accept: application/json\r\n";
 $body.= "Content-length: " . strlen($data) . "\r\n";
 $body.= "\r\n";
 $body.= $data;
 return $this->_write($socket, $body);
 }
 private function _getSocket() {
 if(is_resource($this->_socket)) {
 if ($this->_debug()) {
 $this->_log("Using existing socket");
 }
 return $this->_socket;
 } else {
 if ($this->_debug()) {
 $this->_log("Creating new socket at ".time());
 }
 return $this->_createSocket();
 }
 }
 private function _createSocket($retry = true) {
 try {
 $socket = pfsockopen($this->_protocol . "://" . $this->_host, $this->_port, $err_no, $err_msg, $this->_connect_timeout);
 if ($this->_debug()) {
 $this->_log("Opening socket connection to " . $this->_protocol . "://" . $this->_host . ":" . $this->_port);
 }
 if ($err_no != 0) {
 $this->_handleError($err_no, $err_msg);
 return $retry == true ? $this->_createSocket(false) : false;
 } else {
 // cache the socket
 $this->_socket = $socket;
 return $socket;
 }
 } catch (Exception $e) {
 $this->_handleError($e->getCode(), $e->getMessage());
 return $retry == true ? $this->_createSocket(false) : false;
 }
 }
 private function _destroySocket() {
 $socket = $this->_socket;
 $this->_socket = null;
 fclose($socket);
 }
 private function _write($socket, $data, $retry = true) {
 $bytes_sent = 0;
 $bytes_total = strlen($data);
 $socket_closed = false;
 $success = true;
 $max_bytes_per_write = 8192;
 // if we have no data to write just return true
 if ($bytes_total == 0) {
 return true;
 }
 // try to write the data
 while (!$socket_closed && $bytes_sent < $bytes_total) {
 try {
 $bytes = fwrite($socket, $data, $max_bytes_per_write);
 if ($this->_debug()) {
 $this->_log("Socket wrote ".$bytes." bytes");
 }
 // if we actually wrote data, then remove the written portion from $data left to write
 if ($bytes > 0) {
 $data = substr($data, $max_bytes_per_write);
 }
 } catch (Exception $e) {
 $this->_handleError($e->getCode(), $e->getMessage());
 $socket_closed = true;
 }
 if (isset($bytes) && $bytes) {
 $bytes_sent += $bytes;
 } else {
 $socket_closed = true;
 }
 }
 // create a new socket if the current one is closed and retry the message
 if ($socket_closed) {
 $this->_destroySocket();
 if ($retry) {
 if ($this->_debug()) {
 $this->_log("Retrying socket write...");
 }
 $socket = $this->_getSocket();
 if ($socket) return $this->_write($socket, $data, false);
 }
 return false;
 }
 // only wait for the response in debug mode or if we explicitly want to be synchronous
 if ($this->_debug() || !$this->_async) {
 $res = $this->handleResponse(fread($socket, 2048));
 if ($res["status"] != "200") {
 $this->_handleError($res["status"], $res["body"]);
 $success = false;
 }
 }
 return $success;
 }
 private function handleResponse($response) {
 $lines = explode("\n", $response);
 // extract headers
 $headers = array();
 foreach($lines as $line) {
 $kvsplit = explode(":", $line);
 if (count($kvsplit) == 2) {
 $header = $kvsplit[0];
 $value = $kvsplit[1];
 $headers[$header] = trim($value);
 }
 }
 // extract status
 $line_one_exploded = explode(" ", $lines[0]);
 $status = $line_one_exploded[1];
 // extract body
 $body = $lines[count($lines) - 1];
 // if the connection has been closed lets kill the socket
 if (isset($headers["Connection"]) and $headers['Connection'] == "close") {
 $this->_destroySocket();
 if ($this->_debug()) {
 $this->_log("Server told us connection closed so lets destroy the socket so it'll reconnect on next call");
 }
 }
 $ret = array(
 "status" => $status,
 "body" => $body,
 );
 return $ret;
 }
}
