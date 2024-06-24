<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Util;

if (!defined('ABSPATH')) exit;


use MailPoet\Config\Env;

if (!class_exists('ProgressBar', false)) {

  /**
   * The Progress Bar class
   *
   */
  class ProgressBar {

    private $totalCount = 0;
    private $currentCount = 0;
    private $filename;
    public $url;

    /**
     * Initialize the class and set its properties.
     *
     */
    public function __construct(
      $progressBarId
    ) {
      $filename = $progressBarId . '-progress.json';
      $this->filename = Env::$tempPath . '/' . $filename;
      $this->url = Env::$tempUrl . '/' . $filename;
      $counters = $this->readProgress();
      if (isset($counters['total'])) {
        $this->totalCount = $counters['total'];
      }
      if (isset($counters['current'])) {
        $this->currentCount = $counters['current'];
      }
    }

    /**
     * Get the progress file URL
     *
     * @return string Progress file URL
     */
    public function getUrl() {
      return $this->url;
    }

    /**
     * Read the progress counters
     *
     * @return array|false Array of counters
     */
    private function readProgress() {
      if (!file_exists($this->filename)) {
        return false;
      }
      $jsonContent = file_get_contents($this->filename);
      if (is_string($jsonContent)) {
        /** @var array $data */
        $data = json_decode($jsonContent, true);
        return $data;
      }
      return false;
    }

    /**
     * Set the total count
     *
     * @param int $count Count
     */
    public function setTotalCount($count) {
      if (($count != $this->totalCount) || ($count == 0)) {
        $this->totalCount = $count;
        $this->currentCount = 0;
        $this->saveProgress();
      }
    }

    /**
     * Increment the current count
     *
     * @param int $count Count
     */
    public function incrementCurrentCount($count) {
      $this->currentCount += $count;
      $this->saveProgress();
    }

    /**
     * Save the progress counters
     *
     */
    private function saveProgress() {
      file_put_contents($this->filename, json_encode([
        'total' => $this->totalCount,
        'current' => $this->currentCount,
      ]));
    }

    /**
     * Delete the progress file
     *
     */
    public function deleteProgressFile() {
      unlink($this->filename);
    }
  }

}
