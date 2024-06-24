<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Twig;

if (!defined('ABSPATH')) exit;


use MailPoet\WP\Functions as WPFunctions;
use MailPoetVendor\Twig\Extension\AbstractExtension;
use MailPoetVendor\Twig\TwigFilter;

class Filters extends AbstractExtension {
  public function getName() {
    return 'filters';
  }

  public function getFilters() {
    return [
      new TwigFilter(
        'intval',
        'intval'
      ),
      new TwigFilter(
        'replaceLinkTags',
        'MailPoet\Util\Helpers::replaceLinkTags'
      ),
      new TwigFilter(
        'wpKses',
        [$this, 'wpKses'],
        ['is_safe' => ['html']]
      ),
    ];
  }

  public function wpKses($content, $allowedHtml) {
    $wp = WPFunctions::get();
    return $wp->wpKses($content, $allowedHtml);
  }
}
