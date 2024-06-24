<?php // phpcs:ignore SlevomatCodingStandard.TypeHints.DeclareStrictTypes.DeclareStrictTypesMissing

namespace MailPoet\Config;

if (!defined('ABSPATH')) exit;


use MailPoet\DI\ContainerWrapper;
use MailPoet\Subscribers\SubscriberPersonalDataEraser;
use MailPoet\WP\Functions as WPFunctions;

class PersonalDataErasers {
  public function init() {
    WPFunctions::get()->addFilter('wp_privacy_personal_data_erasers', [$this, 'registerSubscriberEraser']);
  }

  public function registerSubscriberEraser($erasers) {
    $erasers['mailpet-subscriber'] = [
      'eraser_friendly_name' => __('MailPoet Subscribers', 'mailpoet'),
      'callback' => [ContainerWrapper::getInstance()->get(SubscriberPersonalDataEraser::class), 'erase'],
    ];

    return $erasers;
  }
}
