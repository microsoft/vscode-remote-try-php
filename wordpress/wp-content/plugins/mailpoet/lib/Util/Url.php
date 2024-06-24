<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Util;

if (!defined('ABSPATH')) exit;


use MailPoet\WP\Functions as WPFunctions;

class Url {
  /** @var WPFunctions */
  private $wp;

  public function __construct(
    WPFunctions $wp
  ) {
    $this->wp = $wp;
  }

  public function getCurrentUrl() {
    $homeUrl = parse_url($this->wp->homeUrl());
    $queryArgs = $this->wp->addQueryArg(null, null);

    // Remove $this->wp->homeUrl() path from add_query_arg
    if (
      is_array($homeUrl)
      && isset($homeUrl['path'])
    ) {
      $queryArgs = str_replace($homeUrl['path'], '', $queryArgs);
    }

    return $this->wp->homeUrl($queryArgs);
  }

  public function redirectTo($url = null) {
    $this->wp->wpSafeRedirect($url);
    exit();
  }

  public function redirectBack($params = []) {
    // check mailpoet_redirect parameter
    $referer = (isset($_POST['mailpoet_redirect'])
      ? sanitize_text_field(wp_unslash($_POST['mailpoet_redirect']))
      : $this->wp->wpGetReferer()
    );

    // fallback: home_url
    if (!$referer) {
      $referer = $this->wp->homeUrl();
    }

    // append extra params to url
    if (!empty($params)) {
      $referer = $this->wp->addQueryArg($params, $referer);
    }

    $this->redirectTo($referer);
    exit();
  }

  public function redirectWithReferer($url = null) {
    $currentUrl = $this->getCurrentUrl();
    $url = $this->wp->addQueryArg(
      [
        'mailpoet_redirect' => urlencode($currentUrl),
      ],
      $url
    );

    if ($url !== $currentUrl) {
      $this->redirectTo($url);
    }
    exit();
  }

  public function isUsingHttps(string $url): bool {
    return $this->wp->wpParseUrl($url, PHP_URL_SCHEME) === 'https';
  }
}
