<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Doctrine;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Doctrine\Common\Cache\CacheProvider;
use ReflectionClass;
use ReflectionException;

// Simple filesystem-based cache storage for Doctrine Metadata.
//
// Needed because Doctrine's FilesystemCache doesn't work read-only (when metadata dumped)
// and it calls realpath() that could fail on some hostings due to filesystem permissions.
class MetadataCache extends CacheProvider {
  /** @var bool */
  private $isDevMode;

  /** @var string */
  private $directory;

  public function __construct(
    $dir,
    $isReadOnly
  ) {
    $this->isDevMode = defined('WP_DEBUG') && WP_DEBUG && !$isReadOnly;
    $this->directory = rtrim($dir, '/\\');
    if (!file_exists($this->directory)) {
      mkdir($this->directory);
    }
  }

  protected function doFetch($id) {
    if (!$this->doContains($id)) {
      return false;
    }
    return unserialize((string)file_get_contents($this->getFilename($id)));
  }

  protected function doContains($id) {
    $filename = $this->getFilename($id);
    $fileExists = file_exists($filename);

    // in dev mode invalidate cache if source file has changed
    if ($fileExists && $this->isDevMode) {
      /** @var \stdClass $classMetadata */
      $classMetadata = unserialize((string)file_get_contents($filename));
      if (!isset($classMetadata->name) || (!class_exists($classMetadata->name) && !interface_exists($classMetadata->name))) {
        return false;
      }
      try {
        $reflection = new ReflectionClass($classMetadata->name);
      } catch (ReflectionException $e) {
        return false;
      }
      clearstatcache();
      return filemtime((string)$filename) >= filemtime((string)$reflection->getFileName());
    }

    return $fileExists;
  }

  protected function doSave($id, $data, $lifeTime = 0) {
    $filename = $this->getFilename($id);
    $result = @file_put_contents($filename, serialize($data));
    if ($result === false) {
      throw new \RuntimeException("Error while writing to '$filename'");
    }
    return true;
  }

  protected function doDelete($id) {
    @unlink($this->getFilename($id));
    return true;
  }

  protected function doFlush() {
    $directoryContent = glob($this->directory . DIRECTORY_SEPARATOR . '*');
    if ($directoryContent === false) {
      return false;
    }
    foreach ($directoryContent as $filename) {
      if (is_file($filename)) {
        @unlink($filename);
      }
    }
    return true;
  }

  protected function doGetStats() {
    return null;
  }

  private function getFilename($id) {
    return $this->directory . DIRECTORY_SEPARATOR . md5($id);
  }
}
