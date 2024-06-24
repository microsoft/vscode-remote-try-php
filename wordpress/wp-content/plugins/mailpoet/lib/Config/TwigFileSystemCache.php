<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config;

if (!defined('ABSPATH')) exit;


class TwigFileSystemCache extends \MailPoetVendor\Twig\Cache\FilesystemCache {

  private $directory;

  public function __construct(
    string $directory,
    int $options = 0
  ) {
    $this->directory = \rtrim($directory, '\\/') . '/';
    parent::__construct($directory, $options);
  }

  /**
   * The original FileSystemCache of twig generates the key depending on PHP_VERSION.
   * We need to produce the same key regardless of PHP_VERSION. Therefore, we
   * overwrite this method.
   **/
  public function generateKey(string $name, string $className): string {
    $hash = \hash('sha256', $className);
    return $this->directory . $hash[0] . $hash[1] . '/' . $hash . '.php';
  }
}
