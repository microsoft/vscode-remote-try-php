<?php
if (!defined('ABSPATH')) exit;
class MixpanelTest extends PHPUnit_Framework_TestCase {
 protected $_instance = null;
 protected function setUp() {
 parent::setUp();
 $this->_instance = Mixpanel::getInstance("token");
 }
 protected function tearDown() {
 parent::tearDown();
 $this->_instance->reset();
 $this->_instance = null;
 }
 public function testGetInstance() {
 $instance = Mixpanel::getInstance("token");
 $this->assertInstanceOf("Mixpanel", $instance);
 $this->assertEquals($this->_instance, $instance);
 $this->assertInstanceOf("Producers_MixpanelPeople", $this->_instance->people);
 }
}
