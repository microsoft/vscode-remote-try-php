<?php
if (!defined('ABSPATH')) exit;
require_once(dirname(__FILE__) . "/AbstractConsumer.php");
class ConsumerStrategies_FileConsumer extends ConsumerStrategies_AbstractConsumer {
 private $_file;
 function __construct($options) {
 parent::__construct($options);
 // what file to write to?
 $this->_file = isset($options['file']) ? $options['file'] : dirname(__FILE__)."/../../messages.txt";
 }
 public function persist($batch) {
 if (count($batch) > 0) {
 return file_put_contents($this->_file, json_encode($batch)."\n", FILE_APPEND | LOCK_EX) !== false;
 } else {
 return true;
 }
 }
}
