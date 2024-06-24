<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Newsletter\Renderer\PostProcess;

if (!defined('ABSPATH')) exit;


use MailPoet\Newsletter\Links\Links;
use MailPoet\Newsletter\Renderer\Renderer;
use MailPoet\Util\pQuery\pQuery;
use MailPoet\WP\Functions as WPFunctions;

class OpenTracking {
  public static function process($template) {
    $DOM = new pQuery();
    $DOM = $DOM->parseStr($template);
    $template = $DOM->query('body');
    // url is a temporary data tag that will be further replaced with
    // the proper track API URL during sending
    $url = Links::DATA_TAG_OPEN;
    $openTrackingImage = sprintf(
      '<img alt="" class="" src="%s"/>',
      $url
    );
    $template->html($template->html() . $openTrackingImage);
    return $DOM->__toString();
  }

  public static function addTrackingImage() {
    WPFunctions::get()->addFilter(Renderer::FILTER_POST_PROCESS, function ($template) {
      return OpenTracking::process($template);
    });
    return true;
  }
}
