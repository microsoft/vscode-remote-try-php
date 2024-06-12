<?php
if (!defined('ABSPATH')) exit;
class ActionScheduler_FinishedAction extends ActionScheduler_Action {
 public function execute() {
 // don't execute
 }
 public function is_finished() {
 return TRUE;
 }
}
 