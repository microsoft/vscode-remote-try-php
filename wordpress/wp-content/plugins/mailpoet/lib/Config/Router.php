<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config;

if (!defined('ABSPATH')) exit;


use MailPoet\WP\Functions as WPFunctions;

class Router {
  /** @var WPFunctions */
  private $wp;

  public function __construct(
    WPFunctions $wp
  ) {
    $this->wp = $wp;
  }

  public function checkRedirects(): void {
    $url = null;
    if (isset($_GET['page']) && sanitize_text_field(wp_unslash($_GET['page'])) === 'mailpoet-newsletters') {
      $url = $this->checkNewslettersRedirect();
    }
    if (!$url) return;

    $this->redirect($url);
  }

  private function checkNewslettersRedirect(): ?string {
    if (isset($_GET['stats'])) {
      return '/wp-admin/admin.php?page=mailpoet-newsletters#/stats/' . sanitize_text_field(wp_unslash($_GET['stats']));
    }

    return null;
  }

  private function redirect(string $url): void {
    $this->wp->wpSafeRedirect(
      $this->wp->getSiteUrl(null, $url)
    );
    exit;
  }
}
