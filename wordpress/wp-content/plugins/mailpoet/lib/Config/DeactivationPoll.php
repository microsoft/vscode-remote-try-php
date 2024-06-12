<?php declare(strict_types = 1);

namespace MailPoet\Config;

if (!defined('ABSPATH')) exit;


use MailPoet\WP\Functions as WPFunctions;

class DeactivationPoll {

  /** @var WPFunctions */
  private $wp;

  /** @var Renderer */
  private $renderer;

  public function __construct(
    WPFunctions $wp,
    Renderer $renderer
  ) {
    $this->wp = $wp;
    $this->renderer = $renderer;
  }

  public function init() {
    $this->wp->addAction('admin_print_scripts', [$this, 'css']);
    $this->wp->addAction('admin_footer', [$this, 'modal']);
  }

  private function shouldShow(): bool {
    if (!function_exists('get_current_screen')) {
      return false;
    }
    $screen = $this->wp->getCurrentScreen();
    if (is_null($screen)) {
      return false;
    }
    return in_array($screen->id, ['plugins', 'plugins-network'], true);
  }

  public function css(): void {
    if (!$this->shouldShow()) {
      return;
    }
    $this->render('deactivationPoll/css.html');
  }

  public function modal(): void {
    if (!$this->shouldShow()) {
      return;
    }
    $this->render('deactivationPoll/index.html');
  }

  private function render($template): void {
    try {
      // phpcs:disable -- because we use echo here, WordPress sniffs reported a warning
      echo $this->renderer->render($template);
      // phpcs:enable
    } catch (\Exception $e) {
      // if the website fails to render we have other places to catch and display the error
    }
  }
}
