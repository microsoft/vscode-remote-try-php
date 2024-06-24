<?php
namespace Action_Scheduler\Migration;
if (!defined('ABSPATH')) exit;
class DryRun_LogMigrator extends LogMigrator {
 public function migrate( $source_action_id, $destination_action_id ) {
 // no-op
 }
}