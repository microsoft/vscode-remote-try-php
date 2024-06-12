<?php declare(strict_types = 1);

namespace MailPoet\NewsletterTemplates;

if (!defined('ABSPATH')) exit;


use MailPoet\WP\Functions as WPFunctions;

class BrandStyles {

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    WPFunctions $wp
  ) {
    $this->wp = $wp;
  }

  public function isAvailable(): bool {
    return $this->wp->wpIsBlockTheme();
  }
}
