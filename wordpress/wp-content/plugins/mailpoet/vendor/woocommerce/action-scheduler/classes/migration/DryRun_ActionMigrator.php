<?php
namespace Action_Scheduler\Migration;
if (!defined('ABSPATH')) exit;
class DryRun_ActionMigrator extends ActionMigrator {
 public function migrate( $source_action_id ) {
 do_action( 'action_scheduler/migrate_action_dry_run', $source_action_id );
 return 0;
 }
}
