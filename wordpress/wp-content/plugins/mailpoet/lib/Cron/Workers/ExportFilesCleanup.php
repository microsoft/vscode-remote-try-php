<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Cron\Workers;

if (!defined('ABSPATH')) exit;


use MailPoet\Entities\ScheduledTaskEntity;
use MailPoet\Subscribers\ImportExport\Export\Export;
use MailPoetVendor\Carbon\Carbon;

class ExportFilesCleanup extends SimpleWorker {
  const TASK_TYPE = 'export_files_cleanup';
  const DELETE_FILES_AFTER_X_DAYS = 1;

  public function processTaskStrategy(ScheduledTaskEntity $task, $timer) {
    $iterator = new \GlobIterator(Export::getExportPath() . '/' . Export::getFilePrefix() . '*.*');
    foreach ($iterator as $file) {
      if (is_string($file)) {
        continue;
      }
      $name = $file->getPathname();
      $created = $file->getMTime();
      $now = new Carbon();
      if (Carbon::createFromTimestamp((int)$created)->lessThan($now->subDays(self::DELETE_FILES_AFTER_X_DAYS))) {
        unlink($name);
      };
    }
    return true;
  }
}
