<?php
if (!defined('ABSPATH')) exit;
interface ActionScheduler_Schedule {
 public function next( DateTime $after = NULL );
 public function is_recurring();
}
 