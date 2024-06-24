<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config;

if (!defined('ABSPATH')) exit;


use MailPoetVendor\Twig\Loader\FilesystemLoader as TwigFileSystem;

class RendererFactory {

  /** @var Renderer|null */
  private $renderer;

  public function getRenderer() {
    if (!$this->renderer) {
      $debugging = WP_DEBUG;
      $autoReload = defined('MAILPOET_DEVELOPMENT') && MAILPOET_DEVELOPMENT;
      $this->renderer = new Renderer(
        $debugging,
        Env::$cachePath,
        new TwigFileSystem(Env::$viewsPath),
        $autoReload
      );
    }
    return $this->renderer;
  }
}
