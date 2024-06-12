<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Listing;

if (!defined('ABSPATH')) exit;


use MailPoet\WP\Functions as WPFunctions;

class PageLimit {
  const DEFAULT_LIMIT_PER_PAGE = 20;

  /** @var WPFunctions */
  private $wp;

  public function __construct(
    WPFunctions $wp
  ) {
    $this->wp = $wp;
  }

  public function getLimitPerPage($model = null) {
    if ($model === null) {
      return self::DEFAULT_LIMIT_PER_PAGE;
    }

    $listingPerPage = $this->wp->getUserMeta(
      $this->wp->getCurrentUserId(),
      'mailpoet_' . $model . '_per_page',
      true
    );
    return (!empty($listingPerPage))
      ? (int)$listingPerPage
      : self::DEFAULT_LIMIT_PER_PAGE;
  }
}
