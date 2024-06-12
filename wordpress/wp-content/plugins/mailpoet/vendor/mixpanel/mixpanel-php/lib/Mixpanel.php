<?php
if (!defined('ABSPATH')) exit;
require_once(dirname(__FILE__) . "/Base/MixpanelBase.php");
require_once(dirname(__FILE__) . "/Producers/MixpanelPeople.php");
require_once(dirname(__FILE__) . "/Producers/MixpanelEvents.php");
require_once(dirname(__FILE__) . "/Producers/MixpanelGroups.php");
class Mixpanel extends Base_MixpanelBase {
 public $people;
 private $_events;
 public $group;
 private static $_instances = array();
 public function __construct($token, $options = array()) {
 parent::__construct($options);
 $this->people = new Producers_MixpanelPeople($token, $options);
 $this->_events = new Producers_MixpanelEvents($token, $options);
 $this->group = new Producers_MixpanelGroups($token, $options);
 }
 public static function getInstance($token, $options = array()) {
 if(!isset(self::$_instances[$token])) {
 self::$_instances[$token] = new Mixpanel($token, $options);
 }
 return self::$_instances[$token];
 }
 public function enqueue($message = array()) {
 $this->_events->enqueue($message);
 }
 public function enqueueAll($messages = array()) {
 $this->_events->enqueueAll($messages);
 }
 public function flush($desired_batch_size = 50) {
 $this->_events->flush($desired_batch_size);
 }
 public function reset() {
 $this->_events->reset();
 }
 public function identify($user_id, $anon_id = null) {
 $this->_events->identify($user_id, $anon_id);
 }
 public function track($event, $properties = array()) {
 $this->_events->track($event, $properties);
 }
 public function register($property, $value) {
 $this->_events->register($property, $value);
 }
 public function registerAll($props_and_vals = array()) {
 $this->_events->registerAll($props_and_vals);
 }
 public function registerOnce($property, $value) {
 $this->_events->registerOnce($property, $value);
 }
 public function registerAllOnce($props_and_vals = array()) {
 $this->_events->registerAllOnce($props_and_vals);
 }
 public function unregister($property) {
 $this->_events->unregister($property);
 }
 public function unregisterAll($properties) {
 $this->_events->unregisterAll($properties);
 }
 public function getProperty($property)
 {
 return $this->_events->getProperty($property);
 }
 public function createAlias($distinct_id, $alias) {
 $this->_events->createAlias($distinct_id, $alias);
 }
}
