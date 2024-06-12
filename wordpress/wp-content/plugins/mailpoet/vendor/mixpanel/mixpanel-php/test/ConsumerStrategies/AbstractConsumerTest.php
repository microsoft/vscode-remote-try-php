<?php
if (!defined('ABSPATH')) exit;
class ConsumerStrategies_AbstractConsumerTest extends PHPUnit_Framework_TestCase {
 protected $_instance = null;
 protected function setUp()
 {
 parent::setUp();
 $this->_instance = new AbstractConsumer();
 }
 protected function tearDown()
 {
 parent::tearDown();
 $this->_instance = null;
 }
 public function test_encode() {
 $encoded = base64_encode(json_encode(array("1" => "one")));
 $this->assertEquals($encoded, $this->_instance->encode(array("1" => "one")));
 }
}
class AbstractConsumer extends ConsumerStrategies_AbstractConsumer {
 function persist($batch)
 {
 // TODO: Implement persist() method.
 }
 function encode($msg) {
 return $this->_encode($msg);
 }
}
